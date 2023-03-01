<?php

class AdminOrdersController extends AdminOrdersControllerCore {
    
    public function __construct() 
    {
        //calls the original core file
        parent::__construct();

        if (version_compare(_PS_VERSION_, '1.5') > 0) {
            $this->_join .= 'LEFT JOIN ps_sellermania_order smo ON smo.id_order = a.id_order';

            $this->_select .= ', smo.ref_order as `smo_ref_order`';

            $tmp_params = $this->fields_list;
            $this->fields_list = [];
            $prev_key = '';
            foreach ($tmp_params as $key => $tmp_param) {
                if ($prev_key === "reference") {
                    $this->fields_list['smo_ref_order'] = array(
                        'title' => $this->l('MP Reference'),
                        'havingFilter' => true,
                    );
                }

                $this->fields_list += [
                    $key => $tmp_param
                ];
                $prev_key = $key;
            }
        }
    }
}