<?php
/*
* 2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to team@froggy-commerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade your module to newer
* versions in the future.
*
*  @author         Froggy Commerce <team@froggy-commerce.com>
*  @copyright      2010-2016 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Define if not defined
if (!defined('_PS_BASE_URL_'))
    define('_PS_BASE_URL_', Tools::getShopDomain(true));

class SellermaniaExportController
{
    /**
     * @var array fields to export
     */
    private $fields_to_export = array(
        'id_product' => 'int', 'id_product_attribute' => 'int', 'id_unique' => 'string', 'ean13' => 'string', 'upc' => 'string', 'ecotax' => 'float',
        'quantity' => 'int', 'price' => 'float', 'crossed_price' => 'float', 'wholesale_price' => 'float', 'reference' => 'string',
        'supplier' => 'string', 'supplier_reference' => 'string',
        'width' => 'float', 'height' => 'float', 'depth' => 'float', 'weight' => 'float', 'location' => 'string',
        'name' => 'string', 'category_default' => 'string', 'category_default_full_path' => 'string',
        'description' => 'string', 'description_short' => 'string', 'manufacturer_name' => 'string',
        'meta_title' => 'string', 'meta_description' => 'string', 'meta_keywords' => 'string', 'product_url' => 'string',
        'id_category_default' => 'int', 'condition' => 'string', 'date_add' => 'string', 'date_upd' => 'string',
    );

    /**
     * @var array attribute groups
     */
    private $attribute_groups = array();

    /**
     * @var array features
     */
    private $features = array();

    /**
     * Controller constructor
     */
    public function __construct()
    {
        $this->context = Context::getContext();

        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (Tools::getValue('l') != '')
            $id_lang = Language::getIdByIso(Tools::getValue('l'));
        $tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'attribute_group_lang` WHERE `id_lang` = '.(int)$id_lang.' ORDER BY `id_attribute_group`');
        foreach ($tmp as $t)
            $this->attribute_groups[$t['id_attribute_group']] = $t['name'];
        $tmp = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'feature_lang` WHERE `id_lang` = '.(int)$id_lang.' ORDER BY `id_feature`');
        foreach ($tmp as $t)
            $this->features[$t['id_feature']] = $t['name'];
    }

    /**
     * Get export filename
     * @param string $iso_lang
     * @return string export file name
     */
    public function get_export_filename($iso_lang, $web_path = false)
    {
        $sellermania_key = Configuration::get('SELLERMANIA_KEY');
        if ($web_path)
            return 'export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
        return dirname(__FILE__).'/../../export/export-'.strtolower($iso_lang).'-'.$sellermania_key.'.csv';
    }


    /**
     * Delete old exported files
     * @param string $iso_lang
     */
    public function delete_export_files($iso_lang)
    {
        // Init
        $languages_list = Language::getLanguages();
        $sellermania_key = Configuration::get('SELLERMANIA_KEY');

        // Delete all export files or only export file of the selected language
        if (!empty($iso_lang))
            @unlink($this->get_export_filename($iso_lang));
        else
            foreach ($languages_list as $language)
                @unlink($this->get_export_filename($language['iso_code']));
    }

    /**
     * Export method
     * @param string $output (display|file)
     * @param string $iso_lang
     * @param integer $start
     * @param integer $limit
     */
    public function export($output, $iso_lang = '', $start = 0, $limit = 0)
    {
        // Check if context is set, if not we set it
        if (!isset($this->context) || empty($this->context)) {
            $this->context = Context::getContext();
        }
        if (!isset($this->context->language)) {
            $this->context->language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        }

        // If output is file, we delete old export files
        if ($output == 'file')
            $this->delete_export_files($iso_lang);

        // If output is displayed, we force format download
        if ($output == 'display' && Tools::getValue('display') != 'inline' && Tools::getValue('display') != 'debug')
        {
            header('Content-type: application/vnd.ms-excel');
            header('Content-disposition: attachment; filename="sellermania.csv"');
        }

        // If debug display is enabled
        if (Tools::getValue('display') == 'debug') {
            echo '<table border="1"><tr><td>';
        }

        // Init
        if (!empty($iso_lang))
            $languages_list = array(array('iso_code' => $iso_lang));
        else
            $languages_list = Language::getLanguages();

        // Get products list for each lang
        foreach ($languages_list as $language)
        {
            $iso_lang = strtolower($language['iso_code']);
            $id_lang = Language::getIdByIso($iso_lang);
            $this->renderExportHeader($iso_lang, $output);

            $offset = $start;
            $items_per_page = $limit;
            if ($start == 0 || $limit = 0) {
                $page = 1;
                $items_per_page = 10;
                $offset = (($page - 1) * $items_per_page) + 1;
            }

            $result = SellermaniaProduct::getProductsRequest($id_lang, $offset, $items_per_page);
            while ($result) {

                $nb_rows = 0;
                while ($row = Db::getInstance()->nextRow($result))
                {
                    $row['location'] = SellermaniaProduct::getLocation($row['id_product'], $row['location']);
                    $row['tags'] = SellermaniaProduct::getProductTags($row['id_product'], $id_lang);
                    $row['features'] = SellermaniaProduct::getFeatures($row['id_product'], $id_lang);
                    $row['declinations'] = SellermaniaProduct::getProductDeclinations($row['id_product'], $id_lang);
                    $row['images'] = SellermaniaProduct::getImages($row['id_product']);
                    $this->renderExport($row, $iso_lang, $output);
                    $nb_rows++;
                }

                if ($start == 0 || $limit = 0) {
                    $page++;
                    $offset = (($page - 1) * $items_per_page) + 1;
                }

                if ($nb_rows > 0) {
                    $result = SellermaniaProduct::getProductsRequest($id_lang, $offset, $items_per_page);
                } else {
                    $result = false;
                }
            }
        }
    }

    /**
     * Render export header
     * @param string $iso_lang
     * @param string $output (display|file)
     */
    public function renderExportHeader($iso_lang, $output)
    {
        $line = '';
        foreach ($this->fields_to_export as $field => $field_type) {
            $line .= '"'.$field.'";';
        }
        for ($i = 1; $i <= 5; $i++) {
            $line .= '"tag '.$i.'";';
        }
        for ($i = 1; $i <= 12; $i++) {
            if ($i == 1) {
                $line .= '"images";';
            } else {
                $line .= '"image '.$i.'";';
            }
        }
        foreach ($this->attribute_groups as $id_attribute_group => $group_name) {
            $line .= '"Attr '.$id_attribute_group.' - '.$group_name.'";';
        }
        foreach ($this->features as $id_feature => $name) {
            $line .= '"Feature '.$id_feature.' - '.$name.'";';
        }
        $line .= "\n";
        $this->renderLine($line, $iso_lang, $output);
    }

    /**
     * Render export
     * @param array $row
     * @param string $iso_lang
     * @param string $output (display|file)
     */
    public function renderExport($row, $iso_lang, $output)
    {
        // If declination duplicate row for each declination
        if ($row['declinations'] && is_array($row['declinations'])) {

            $rows = array();
            foreach ($row['declinations'] as $id_product_attribute => $declination) {

                $rowCopy = $row;
                $rowCopy['id_product_attribute'] = $id_product_attribute;

                // Disable combination concatenation if this advanced option is disabled
                // Some merchants only fill one combination
                if (Configuration::get('SM_ENABLE_EXPORT_COMB_NAME') == 'yes') {
                    $rowCopy['name'] = $rowCopy['name'].' '.implode(' ', $declination['attributes_values']);
                }

                $rowCopy['price'] = Product::getPriceStatic($rowCopy['id_product'], true, $id_product_attribute, 2);
                $rowCopy['crossed_price'] = Product::getPriceStatic($rowCopy['id_product'], true, $id_product_attribute, 2, null, false, false);
                if ($declination['wholesale_price'] > 0) {
                    $rowCopy['wholesale_price'] = $declination['wholesale_price'];
                }
                if ($rowCopy['crossed_price'] == $rowCopy['price']) {
                    unset($rowCopy['crossed_price']);
                }
                $rowCopy['ecotax'] = $declination['ecotax'];
                $rowCopy['quantity'] = $declination['quantity'];
                $rowCopy['reference'] = (!empty($declination['reference']) ? $declination['reference'] : '');
                $rowCopy['ean13'] = (!empty($declination['ean13']) ? $declination['ean13'] : '');
                $rowCopy['upc'] = (!empty($declination['upc']) ? $declination['upc'] : '');
                $rowCopy['location'] = (!empty($declination['location']) ? $declination['location'] : '');
                if (isset($declination['images']) && count($declination['images']) >= 1) {
                    $rowCopy['images'] = $declination['images'];
                }
                $rowCopy['attributes_values'] = $declination['attributes_values'];
                if ($rowCopy['active'] != 1) {
                    $rowCopy['quantity'] = 0;
                }
                $rowCopy['supplier'] = $this->getSupplierData($rowCopy, 'name');
                $rowCopy['supplier_reference'] = $this->getSupplierData($rowCopy, 'reference');
                $rows[] = $rowCopy;
            }
        }
        else
        {
            if ($row['active'] != 1) {
                $row['quantity'] = 0;
            }
            $row['price'] = Product::getPriceStatic($row['id_product'], true, null, 2);
            $row['crossed_price'] = Product::getPriceStatic($row['id_product'], true, null, 2, null, false, false);
            if ($row['crossed_price'] == $row['price']) {
                unset($row['crossed_price']);
            }
            $row['id_product_attribute'] = 0;
            $row['supplier'] = $this->getSupplierData($row, 'name');
            $row['supplier_reference'] = $this->getSupplierData($row, 'reference');
            $rows = array($row);
        }

        // Filter ref without EAN13
        if (Tools::getValue('filter') == 'without_ean13') {
            foreach ($rows as $krow => $row) {
                if (empty($row['ean13'])) {
                    unset($rows[$krow]);
                }
            }
        }
        if (empty($rows)) {
            return false;
        }

        // Begin rendering
        $line = '';
        foreach ($rows as $row) {
            if ($row['id_product'] != Configuration::get('SM_DEFAULT_PRODUCT_ID') && $row['name'] != '') {

                foreach ($this->fields_to_export as $field => $field_type) {

                    if ($field == 'id_unique') {
                        $row[$field] = $row['id_product'] . '-' . $row['id_product_attribute'];
                    } else if ($field == 'product_url') {
                        $row[$field] = $this->context->link->getProductLink($row['id_product'], null, null, null, Language::getIdByIso($iso_lang));
                    } else if ($field == 'category_default_full_path') {
                        $category = new Category((int)$row['id_category_default'], $this->context->language->id);
                        $full_path = $category->name;
                        while ($category->id_parent > 0) {
                            $category = new Category((int)$category->id_parent, $this->context->language->id);
                            $full_path = $category->name.' > '.$full_path;
                        }
                        $row[$field] = $full_path;
                    } else if (!isset($row[$field])) {
                        $row[$field] = '';
                    } else if ($field_type == 'int') {
                        $row[$field] = (int)$row[$field];
                    } else if ($field_type == 'float') {
                        $row[$field] = number_format($row[$field], 2, '.', '');
                    }
                    $line .= '"'.str_replace(array("\r\n", "\n", '"'), '', $row[$field]).'";';
                }
                for ($i = 1; $i <= 5; $i++) {
                    $line .= '"'.(isset($row['tags'][$i - 1]) ? $row['tags'][$i - 1] : '').'";';
                }
                for ($i = 1; $i <= 12; $i++) {
                    $line .= '"'.(isset($row['images'][$i - 1]) ? $row['images'][$i - 1] : '').'";';
                }
                foreach ($this->attribute_groups as $id_attribute_group => $group_name) {
                    $line .= '"'.(isset($row['attributes_values'][$id_attribute_group]) ? $row['attributes_values'][$id_attribute_group] : '').'";';
                }
                foreach ($this->features as $id_feature => $name) {
                    $line .= '"'.(isset($row['features'][$id_feature]) ? $row['features'][$id_feature] : '').'";';
                }
                $line .= "\n";
            }
        }

        // Free memory
        $row = null;

        $this->renderLine($line, $iso_lang, $output);
    }

    /**
     * Render line
     * @param string $line
     * @param string $iso_lang
     * @param string $output (display|file)
     */
    public function renderLine($line, $iso_lang, $output)
    {
        if ($output == 'file')
        {
            $real_path_file = $this->get_export_filename($iso_lang);
            file_put_contents($real_path_file, $line, FILE_APPEND);
        }
        else {
            if (Tools::getValue('display') == 'debug') {
                $line = str_replace(array(';', "\n"), array('</td><td>', '</td></tr><tr><td>'), html_entity_decode($line));
            }
            echo $line;
        }
    }


    public function getSupplierData($row, $variable)
    {
        if (version_compare(_PS_VERSION_, '1.5.0') >= 0)
        {
            $suppliers = ProductSupplier::getSupplierCollection($row['id_product']);
            foreach ($suppliers as $product_supplier)
            {
                $row['id_supplier'] = $product_supplier->id_supplier;
                $id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier($row['id_product'], $row['id_product_attribute'], $row['id_supplier']);
                $product_supplier = new ProductSupplier($id_product_supplier);

                if ($variable == 'name' && !empty($product_supplier->product_supplier_reference)) {
                    $new_supplier = new Supplier($row['id_supplier']);
                    return $new_supplier->name;
                }

                if ($variable == 'reference' && !empty($product_supplier->product_supplier_reference))
                    return $product_supplier->product_supplier_reference;
            }

        }
        return '';
    }


    /**
     * Run method
     */
    public function run()
    {
        // Init
        global $argv;
        $argument_key = '';
        if (isset($argv[0]))
            $argument_key = Configuration::get('SELLERMANIA_KEY');

        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        if (empty($this->context->shop->id))
            $this->context->shop->setContext(4);

        // Check if Sellermania key exists
        if (Configuration::get('SELLERMANIA_KEY') == '')
            die('ERROR1');
        if (Tools::getValue('k') == '' && $argument_key == '')
            die('ERROR2');
        if (Tools::getValue('k') == Configuration::get('SELLERMANIA_KEY') || $argument_key == Configuration::get('SELLERMANIA_KEY'))
        {
            // Up time and memory limit
            set_time_limit(600);
            ini_set('memory_limit', '256M');

            // If no cart, we create one
            if (!is_object($this->context->cart))
            {
                global $cart;
                $cart = new Cart();
                $this->context->cart = $cart;
            }

            // Run export method
            $this->export((empty($argument_key) ? 'display' : 'file'), Tools::getValue('l'), Tools::getValue('s'), Tools::getValue('e'));
        }
        else
            die('ERROR3');
    }
}

