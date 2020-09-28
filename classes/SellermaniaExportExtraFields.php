<?php

class SellermaniaExportExtraFields
{
    public static function getNewFields($fields)
    {
        if (Configuration::get('SM_EXPORT_EXTRA_FIELDS') != '')
        {
            $extra_fields_config = json_decode(Configuration::get('SM_EXPORT_EXTRA_FIELDS'), true);
            if (json_last_error() == JSON_ERROR_NONE) {
                if (isset($extra_fields_config['join'])) {
                    foreach ($extra_fields_config['join'] as $k => $join) {
                        foreach ($join['fields'] as $field) {
                            $fields[$field] = 'string';
                        }
                    }
                }
            }
        }

        return $fields;
    }

    public static function getSQLSelectors(&$extra_select, &$extra_join)
    {
        if (Configuration::get('SM_EXPORT_EXTRA_FIELDS') != '')
        {
            $extra_fields_config = json_decode(Configuration::get('SM_EXPORT_EXTRA_FIELDS'), true);
            if (json_last_error() == JSON_ERROR_NONE) {
                if (isset($extra_fields_config['join'])) {
                    foreach ($extra_fields_config['join'] as $k => $join) {
                        foreach ($join['fields'] as $field) {
                            $extra_select .= ', j'.$k.'.`'.bqSQL($field).'`';
                        }
                        $extra_join .= "\n".'LEFT JOIN `'._DB_PREFIX_.bqSQL($join['table']).'` j'.$k.' ON (j'.$k.'.`'.bqSQL($join['link']).'` = p.`'.bqSQL($join['link']).'`)';
                    }
                }
            }
        }
    }
}


