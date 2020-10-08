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

class SellermaniaProduct
{
    /**
     * Make products MySQL request
     * @param integer $id_lang
     * @param integer $start
     * @param integer $limit
     * @return mysql ressource
     */
    public static function getProductsRequest($id_lang, $start = 0, $limit = 0)
    {
        // Retrieve context
        $context = Context::getContext();

        // Init
        $limitSQL = '';
        if ((int)$start > 0 && (int)$limit > 0)
            $limitSQL = ' LIMIT '.(int)$start.','.(int)$limit;

        $where = '';
        if (Configuration::get('SM_EXPORT_ALL') == 'no')
        {
            $categories = json_decode(Configuration::get('SM_EXPORT_CATEGORIES'), true);
            if (empty($categories))
                $categories[] = 0;
            foreach ($categories as $kc => $vc)
                $categories[(int)$kc] = (int)$vc;
            $where = ' AND p.`id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` IN ('.implode(',', $categories).'))';
        }

        $extra_select = '';
        $extra_join = '';
        SellermaniaExportExtraFields::getSQLSelectors($extra_select, $extra_join);

        // SQL request
        $sql = 'SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
            (p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice, p.`active`, p.`id_category_default`
            '.$extra_select.'
        FROM `'._DB_PREFIX_.'product` p
        LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
        LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
                                                   AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
                                                      AND tr.`id_state` = 0)
        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
        LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
        LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
        '.$extra_join.'
        WHERE (
            p.`active` = 1 OR
            p.`date_upd` > \''.pSQL(date('Y-m-d', strtotime('-'.(int)Configuration::get('SM_EXPORT_STAY_NB_DAYS').' days'))).'\'
        ) '.$where.'
        ORDER BY p.`id_product`';

        // Return query
        return Db::getInstance()->Execute($sql);
    }

    /**
     * Get product declinations
     * @param integer $id_product
     * @param integer $id_lang
     * @return boolean|array
     */
    public static function getProductDeclinations($id_product, $id_lang)
    {
        $sql = '
        SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name,
        a.`color` AS attribute_color, pa.*
        FROM `'._DB_PREFIX_.'product_attribute` pa
        LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
        LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
        LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
        LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON a.`id_attribute` = al.`id_attribute`
        LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON ag.`id_attribute_group` = agl.`id_attribute_group`
        WHERE pa.`id_product` = '.(int)($id_product).'
        AND al.`id_lang` = '.(int)($id_lang).'
        AND agl.`id_lang` = '.(int)($id_lang).'
        ORDER BY agl.`public_name`, al.`name`';
        $attributes_groups = Db::getInstance()->executeS($sql);

        $combinations = false;
        if (is_array($attributes_groups) AND $attributes_groups)
        {
            // Init
            $link = new Link();

            // Retrieve images corresponding to each declination
            $ids = array();
            foreach ($attributes_groups as $pa)
                $ids[] = (int)$pa['id_product_attribute'];
            if ($result = Db::getInstance()->ExecuteS('
            SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
            FROM `'._DB_PREFIX_.'product_attribute_image` pai
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.`id_image` = pai.`id_image`)
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
            WHERE pai.`id_product_attribute` IN ('.implode(', ', $ids).') AND il.`id_lang` = '.(int)($id_lang).' ORDER by i.`position`'))
            {
                $images = array();
                foreach ($result AS $row)
                    if ($row['id_image'] > 0)
                        $images[$row['id_product_attribute']][] = 'http://'.$link->getImageLink('product', $id_product.'-'.$row['id_image'], 'thickbox');
            }

            // Retrieve infos for each declination
            foreach ($attributes_groups AS $k => $row)
            {
                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['price'] = (float)($row['price']);
                $combinations[$row['id_product_attribute']]['ecotax'] = (float)($row['ecotax']);
                $combinations[$row['id_product_attribute']]['weight'] = (float)($row['weight']);
                $combinations[$row['id_product_attribute']]['quantity'] = (int)($row['quantity']);
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['supplier_reference'] = $row['supplier_reference'];
                $combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
                $combinations[$row['id_product_attribute']]['upc'] = $row['upc'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $combinations[$row['id_product_attribute']]['wholesale_price'] = $row['wholesale_price'];
                if (isset($images[$row['id_product_attribute']]))
                    $combinations[$row['id_product_attribute']]['images'] = $images[$row['id_product_attribute']];
            }
        }

        return $combinations;
    }

    /**
     * Get product tags
     * @param integer $id_product
     * @param integer $id_lang
     * @return string $tags
     */
    public static function getProductTags($id_product, $id_lang)
    {
        $sql = 'SELECT t.`name` FROM `'._DB_PREFIX_.'product_tag` pt
                LEFT JOIN `'._DB_PREFIX_.'tag` t ON (t.`id_tag` = pt.`id_tag` AND t.`id_lang` = '.(int)$id_lang.')
                WHERE pt.`id_product` = '.(int)$id_product;
        $tags_list = Db::getInstance()->executeS($sql);

        $tags = array();
        $tags_tmp = array();
        foreach ($tags_list as $t)
        {
            if (count($tags_tmp) == 5)
            {
                $tags[] = implode(',', $tags_tmp);
                $tags_tmp = array();
            }
            if (!empty($t['name']))
                $tags_tmp[] = $t['name'];
        }
        $tags[] = implode(',', $tags_tmp);

        return $tags;
    }

    /**
     * Get product features
     * @param integer $id_product
     * @param integer $id_lang
     * @return array $features
     */
    public static function getFeatures($id_product, $id_lang)
    {
        $tmp = Db::getInstance()->executeS('
        SELECT fp.`id_feature`, fvl.`value` as feature_value_name
        FROM `'._DB_PREFIX_.'feature_product` fp
        LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fvl.`id_feature_value` = fp.`id_feature_value` AND fvl.`id_lang` = '.(int)$id_lang.')
        WHERE fp.`id_product` = '.(int)$id_product);
        $result = array();
        foreach ($tmp as $t)
            $result[$t['id_feature']] = $t['feature_value_name'];
        return $result;
    }

    /**
     * Get images
     * @param integer $id_product
     * @return array
     */
    public static function getImages($id_product)
    {
        // Retrieves images
        $images = array();
        $sql = 'SELECT i.`cover`, i.`id_image`, i.`position`
        FROM `'._DB_PREFIX_.'image` i
        WHERE i.`id_product` = '.(int)$id_product.'
        ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);

        $link = new Link();
        foreach ($result as $row)
        {
            $image_link = 'http://'.$link->getImageLink('product', $id_product.'-'.$row['id_image'], 'thickbox');
            if (Tools::getHttpHost() == '')
            {
                $image_link = str_replace('http:///', 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/', $image_link);
                $image_link = str_replace('http://html/', 'http://'.Configuration::get('PS_SHOP_DOMAIN').'/', $image_link);
            }
            $images[] = $image_link;
        }

        return $images;
    }

    /**
     * Get product location
     * @param $id_product
     * @return string
     */
    public static function getLocation($id_product)
    {
        return '';
    }
}

