<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'sellermania'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SellermaniaFieldError.php');

class AdminSellermaniaDiagnosticsController extends ModuleAdminController
{
    private $check_api_link = "https://doc.sellermania.com/prestashop_version";
    private $download_button_link = "https://doc.sellermania.com/#/prestashop/";
    private $log_file_path = 'sellermania/log/log.txt';

    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.6') >= 0) {
            $this->bootstrap = true;
        } else {
            $this->bootstrap = false;
        }
        $this->display = 'view';
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        $sections = [
            "module_version" => "",
            "database_schema" => "",
            "hooks" => "",
            "hosting" => "",
        ];

        if ($this->bootstrap) {
            $template_file = _PS_MODULE_DIR_ . 'sellermania/views/templates/admin/diagnostics/content.bootstrap.tpl';
        } else {
            $template_file = _PS_MODULE_DIR_ . 'sellermania/views/templates/admin/diagnostics/content.tpl';
        }
        $smarty = $this->context->smarty;

        $smarty->assign("is_wizard_launched", Configuration::get('SM_WIZARD_LAUNCHED'));
        $smarty->assign('sellermania_module_path', $this->module->getPathUri());

        // module version
        $smarty->assign("current_module_version", $this->module->version);
        $latest_module_version = file_get_contents($this->check_api_link);
        $smarty->assign("download_button_link", $this->download_button_link);
        if (version_compare($latest_module_version, $this->module->version, '>')) {
            $new_version_available = true;
            $sections["module_version"] = "show";
        } else {
            $new_version_available = false;
        }
        $smarty->assign("new_version_available", $new_version_available);

        // database schema
        $database_schema_xml = file_get_contents(_PS_MODULE_DIR_."sellermania/database_schema.xml");
        $xml = simplexml_load_string($database_schema_xml, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $database_schema = json_decode($json,true);
        $schema_ok = true;
        foreach ($database_schema["table"] as $table_key => $table) {
            $table_name = $table["name"];
            foreach ($table["fields"]["field"] as $field_key => $field) {
                $column_name = $field["name"];
                $column_type = $field["type"];
                $sql = "
                    SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = '"._DB_NAME_."'
                        AND TABLE_NAME = '"._DB_PREFIX_.$table_name."'
                        AND COLUMN_NAME = '".$column_name."'
                ";
                $column_result = Db::getInstance()->executeS($sql);
                if (empty($column_result)) {
                    $database_schema["table"][$table_key]["fields"]["field"][$field_key]["error"] = $this->module->l("not found");
                    $schema_ok = false;
                } else {
                    if ($column_result[0]["DATA_TYPE"] != $column_type) {
                        $database_schema["table"][$table_key]["fields"]["field"][$field_key]["error"] = $this->module->l("column data type not ok - expected ").$column_type.$this->module->l(", got ").$column_result[0]["DATA_TYPE"];
                        $schema_ok = false;
                    } elseif ($column_result[0]["IS_NULLABLE"] == "YES" and (!isset($field["is_nullable"]) || $field["is_nullable"] == 0)) {
                        $database_schema["table"][$table_key]["fields"]["field"][$field_key]["error"] = $this->module->l("column should be not nullable");
                        $schema_ok = false;
                    }
                }
            }
        }
        if (!$schema_ok) {
            $sections["database_schema"] = "show";
        }
        $smarty->assign("schema_ok", $schema_ok);
        $smarty->assign("database_schema", $database_schema["table"]);

        // hooks
        $hooks = $this->getHooksFromPsVersion();
        $unregistered_hooks = [];
        foreach ($hooks as $hook) {
            if (!$this->module->isHookRegistered($hook)) {
                $unregistered_hooks[] = $hook;
                $sections["hooks"] = "show";
            }
        }
        $smarty->assign('unregistered_hooks', $unregistered_hooks);
        $smarty->assign('hooks_to_register', $hooks);

        // server info
        $smarty->assign('server_info', php_uname());
        $smarty->assign('server_version', $_SERVER['SERVER_SOFTWARE']);
        $smarty->assign('php_version', phpversion());
        $smarty->assign('memory_limit', ini_get('memory_limit'));
        $smarty->assign('max_execution_time', ini_get('max_execution_time'));
        $minimal_sys_requirements_alerts = [];
        if (in_array(version_compare(phpversion(), $this->module->getMinimalSystemRequirements("php_version")), [0.1])) {
            $minimal_sys_requirements_alerts = [
                $this->module->l("Minimal PHP version not satisfied: should be at least ".$this->module->getMinimalSystemRequirements("php_version")),
            ];
            $sections["hosting"] = "show";
        }
        $smarty->assign('minimal_sys_requirements_alerts', $minimal_sys_requirements_alerts);

        // database info
        $database_version = "-";
        $result = Db::getInstance()->executeS('SHOW VARIABLES LIKE "version"');
        if (!empty($result)) {
            foreach ($result as $entry) {
                if (isset($entry['Variable_name']) && "version" == $entry['Variable_name']) {
                    if (isset($entry['Value'])) {
                        $database_version = $entry['Value'];
                    }
                }
            }
        }
        $smarty->assign("database_version", $database_version);
        $smarty->assign("database_server", _DB_SERVER_);
        $smarty->assign("database_name", _DB_NAME_);
        $smarty->assign("table_prefix", _DB_PREFIX_);

        //store information
        $smarty->assign('ps_version', _PS_VERSION_);
        $smarty->assign('shop_url', _PS_BASE_URL_);
        $theme_dir = explode('/', _THEME_DIR_);
        $index = count($theme_dir) - 2;
        $smarty->assign('current_theme', $theme_dir[$index]);

        //mail configuration
        if ( function_exists( 'mail' ) ) {
            $smarty->assign('mail_config', "mail");
        } else {
            $smarty->assign('mail_config', "smtp");
        }

        // browser information
        $smarty->assign('browser_info', $_SERVER['HTTP_USER_AGENT']);

        // module configuration
        $smarty->assign("field_errors", SellermaniaFieldError::getAllActiveFieldErrors());

        // config parameters
        $sql = "SELECT * FROM `"._DB_PREFIX_."configuration` WHERE `name` LIKE 'SM_%'";
        $configuration_parameters = Db::getInstance()->executeS($sql);
        $smarty->assign("configuration_parameters", $configuration_parameters);

        // logs
        if (file_exists(_PS_MODULE_DIR_ . $this->log_file_path)) {
            $logs = file_get_contents(_PS_MODULE_DIR_ . $this->log_file_path);
        } else {
            $logs = "";
        }
        $smarty->assign('logs', $logs);

        $smarty->assign('sections', $sections);
        $content = $smarty->fetch($template_file);
        $smarty->assign(['content' => $content]);
    }

    private function getHooksFromPsVersion()
    {
        if (version_compare(_PS_VERSION_, '1.7') >= 0) {
            return  $this->module->getHooksForVersion("1.7");
        } elseif (version_compare(_PS_VERSION_, '1.6') >= 0) {
            return  $this->module->getHooksForVersion("1.6");
        } elseif (version_compare(_PS_VERSION_, '1.5') >= 0) {
            return $this->module->getHooksForVersion("1.5");
        } else {
            return  $this->module->getHooksForVersion("default");
        }
    }
}