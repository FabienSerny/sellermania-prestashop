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

use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SellermaniaActionOrderGridDefinitionModifierController
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
        /** @var GridDefinitionInterface $definition */
        $definition = $this->params['definition'];

        $definition
            ->getColumns()
            ->addAfter(
                'reference',
                (new DataColumn('sm_id_order'))
                    ->setName($this->module->l('MP Reference'))
                    ->setOptions([
                        'field' => 'sm_id_order',
                    ])
            )
        ;

        // For search filter
        $definition->getFilters()
            ->add(
                (new Filter('sm_id_order', TextType::class))
                    ->setAssociatedColumn('sm_id_order')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Marketplace Order ID',
                        ],
                    ])
            )
        ;
    }
}

