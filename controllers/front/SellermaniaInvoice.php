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

// Define if not defined
if (!defined('_PS_BASE_URL_'))
    define('_PS_BASE_URL_', Tools::getShopDomain(true));


class SellermaniaInvoiceController
{
    /**
     * SellermaniaInvoiceController constructor.
     * @param object $module
     * @param string $dir_path
     * @param string $web_path
     */
    public function __construct($module, $dir_path, $web_path)
    {
        $this->module = $module;
        $this->web_path = $web_path;
        $this->dir_path = $dir_path;
        $this->context = Context::getContext();
        $this->ps_version = str_replace('.', '', substr(_PS_VERSION_, 0, 3));
    }

    public function loadInvoiceData($id_order)
    {
        // Display Sellermania invoice
        $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($id_order);
        if (isset($sellermania_order->details) && ($sellermania_order->details['User'][0]['InvoiceUrl'] || $sellermania_order->details['User'][1]['InvoiceUrl'])) {
            $invoice_url = '';
            if (isset($sellermania_order->details['User'][0]['InvoiceUrl'])) {
                $invoice_url = $sellermania_order->details['User'][0]['InvoiceUrl'];
            }
            if (isset($sellermania_order->details['User'][1]['InvoiceUrl'])) {
                $invoice_url = $sellermania_order->details['User'][1]['InvoiceUrl'];
            }
            if (!empty($invoice_url)) {
                header('location:'.$invoice_url);
                exit;
            }
        }
        die('No invoice available yet');

        // Init
        $order_invoices = array();
        $id_lang = $this->context->language->id;

        // Retrieve order
        $order = new Order($id_order);

        // Retrieve invoices
        if (version_compare(_PS_VERSION_, '1.5.0') >= 0) {

            // Retrieve invoices
            $invoices_list = $order->getInvoicesCollection();
            foreach ($invoices_list as $order_invoice) {
                $order_invoices[] = $order_invoice;
            }

            // If more than one invoice, display warning (will fix this later)
            if (count($order_invoices) > 1) {
                die('Sellermania module does not handle multiple invoice yet');
            }

            // Set invoice date and number
            $order_invoice_date = $order_invoice->date_add;
            $order_invoice_number = $order_invoice->number;

            // Get configuration
            $invoice_prefix = Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, (int)$order->id_shop);
            $shop_contact = Configuration::getMultiple(array(
                'PS_SHOP_NAME', 'PS_SHOP_EMAIL', 'PS_SHOP_DETAILS', 'PS_SHOP_ADDR1', 'PS_SHOP_ADDR2',
                'PS_SHOP_CODE', 'PS_SHOP_CITY', 'PS_SHOP_COUNTRY_ID', 'PS_SHOP_PHONE', 'PS_SHOP_FAX',
            ), null, null, (int)$order->id_shop);

        } else {

            // Set invoice date and number
            $order_invoice_date = $order->invoice_date;
            $order_invoice_number = $order->invoice_number;

            // Get configuration
            $invoice_prefix = Configuration::get('PS_INVOICE_PREFIX', $id_lang);
            $shop_contact = Configuration::getMultiple(array(
                'PS_SHOP_NAME', 'PS_SHOP_EMAIL', 'PS_SHOP_DETAILS', 'PS_SHOP_ADDR1', 'PS_SHOP_ADDR2',
                'PS_SHOP_CODE', 'PS_SHOP_CITY', 'PS_COUNTRY_DEFAULT', 'PS_SHOP_PHONE', 'PS_SHOP_FAX',
            ));
            $shop_contact['PS_SHOP_COUNTRY_ID'] = $shop_contact['PS_COUNTRY_DEFAULT'];

            // Ob clean
            ob_clean();
        }

        // Retrieve data
        $shop_contact['PS_SHOP_COUNTRY'] = new Country((int)$shop_contact['PS_SHOP_COUNTRY_ID'], $id_lang);

        // Add debug tool
        $sellermania_order = SellermaniaOrder::getSellermaniaOrderFromOrderId($id_order);
        if (Tools::getIsset('debug')) {
            d($sellermania_order);
        }

        // Assign data
        $logo_path = dirname(__FILE__).'/../../../../img/';
        $picture_logo_path = 'logo.jpg';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $picture_logo_path = Configuration::get('PS_LOGO');
        }
        if (!file_exists($logo_path.$picture_logo_path) && $logo_path.'logo.png') {
            $picture_logo_path = 'logo.png';
        }
        $data = array(
            'logo_path' => $logo_path.$picture_logo_path,
            'shop_name' => $shop_contact['PS_SHOP_NAME'],
            'shop_contact' => $shop_contact,
            'title' => $this->module->l('Invoice number').' #'.$invoice_prefix.sprintf('%06d', $order_invoice_number),
            'date' => Tools::displayDate($order_invoice_date, $this->context->language->id),
            'sellermania_order' => $sellermania_order,
            'sellermania_conditions_list' => $this->module->sellermania_conditions_list,
        );

        return $data;
    }

    /**
     * @param integer $id_order
     */
    public function generate($id_order)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sellermania');
        $pdf->SetTitle('Invoice #'.$id_order);
        $pdf->SetSubject('Invoice #'.$id_order);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();

        $this->context->smarty->assign($this->loadInvoiceData($id_order));
        $html = $this->module->compliantDisplay('../pdf/invoice.tpl');

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('invoice.pdf', 'I');
    }


    /**
     * Run method
     */
    public function run()
    {
        // Set _PS_ADMIN_DIR_ define and set default Shop
        if (!defined('_PS_ADMIN_DIR_'))
            define('_PS_ADMIN_DIR_', getcwd());

        // Check if Order ID
        if (Tools::getValue('id_order') < 1) {
            die('ERROR: No Order ID');
        }

        // Generate invoice
        $this->generate(Tools::getValue('id_order'));
    }
}

