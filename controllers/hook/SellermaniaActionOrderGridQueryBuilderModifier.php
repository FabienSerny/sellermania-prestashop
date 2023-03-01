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

class SellermaniaActionOrderGridQueryBuilderModifierController
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
        /** @var QueryBuilder $searchQueryBuilder */
        $searchQueryBuilder = $this->params['search_query_builder'];

        /** @var CustomerFilters $searchCriteria */
        $searchCriteria = $this->params['search_criteria'];


        $searchQueryBuilder->addSelect(
            ' IF(sm.`ref_order` IS NULL, "#####", sm.`ref_order`) AS `sm_id_order`'
        );

        $searchQueryBuilder->leftJoin(
            'o',
            '`' . pSQL(_DB_PREFIX_) . 'sellermania_order`',
            'sm',
            'sm.`id_order` = o.`id_order`'
        );

        /** @var QueryBuilder  $countQueryBuilder */
        $countQueryBuilder = $this->params['count_query_builder'];



        if ('sm_id_order' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('sm.`id_order`', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('sm_id_order' === $filterName) {
                $searchQueryBuilder->andWhere('sm.`ref_order` = :ref_order');
                $searchQueryBuilder->setParameter('ref_order', $filterValue);
                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('sm.`ref_order` IS NULL');
                }

                $countQueryBuilder->leftJoin(
                    'o',
                    '`' . pSQL(_DB_PREFIX_) . 'sellermania_order`',
                    'sm',
                    'sm.`id_order` = o.`id_order`'
                );

                $countQueryBuilder->andWhere('sm.`ref_order` = :ref_order');
                $countQueryBuilder->setParameter('ref_order', $filterValue);
            }
        }
    }
}

