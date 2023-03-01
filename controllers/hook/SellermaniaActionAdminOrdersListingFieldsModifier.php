<?php
/*
* 2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
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
*  @copyright      2010-2020 Sellermania / Froggy Commerce / 23Prod SARL
*  @version        1.0
*  @license        http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/*
 * Security
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class SellermaniaActionAdminOrdersListingFieldsModifierController
{
    /**
     * Controller constructor
     */
    public function __construct($module, $dir_path, $web_path)
    {
        $this->module = $module;
        $this->web_path = $web_path;
        $this->dir_path = $dir_path;
        $this->context = Context::getContext();
        $this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
    }

    /**
     * Run method
     * @return string $html
     */
    public function run()
    {
        // If hook is called in AdminController::processFilter() we have to check existence
        if (isset($this->params['select'])) {
            $this->params['select'] .= ', sm.`ref_order` AS `sm_id_order`';
        }

        // If hook is called in AdminController::processFilter() we have to check existence
        if (isset($this->params['join'])) {
            $this->params['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'sellermania_order` AS sm ON (a.`id_order` = sm.`id_order`)';
        }

        if (!array_key_exists("sm_id_order", $this->params['fields'])) {
            $tmp_params = $this->params['fields'];
            $this->params['fields'] = [];
            $prev_key = '';
            foreach ($tmp_params as $key => $tmp_param) {
                if ($prev_key === "reference") {
                    $this->params['fields'][] = [
                        'sm_id_order' => [
                            'title' => $this->module->l('MP Reference'),
                            'align' => 'text-center',
                            'class' => 'fixed-width-xs',
                            'filter_key' => 'sm!ref_order',
                            'order_key' => 'sm!ref_order',
                        ],
                    ];
                }

                $this->params['fields'] += [
                    $key => $tmp_param
                ];
                $prev_key = $key;
            }
        }
    }
}

