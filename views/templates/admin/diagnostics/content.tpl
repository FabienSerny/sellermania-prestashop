<div class="card title-card panel">
    <div class="container text-center">
        <img src="{$sellermania_module_path}views/img/sellermania-logo.png" width="320" style="max-width:100%" alt="{l s='Sellermania' mod='sellermania'}" />
        <h4>{l s="A way to make sure your Sellermania module is working properly" mod='sellermania'}</h4>
    </div>
</div>

<div class="panel card {$sections["module_version"]}">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Module Version" mod='sellermania'}: {$current_module_version}</strong>
            </div>
            {if $new_version_available }
                <small class="badge badge-warning">{l s="New version available" mod='sellermania'}</small>
            {else}
                <small class="badge badge-success">{l s="Up to date" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        {if $new_version_available }
            <h2 class="text-center">{l s="Download the latest version of the module to get the newest updates" mod='sellermania'}</h2>
            <p class="text-center"><a target="_blank" href="{$download_button_link}" class="btn btn-warning">{l s="Download it from here" mod='sellermania'}</a></p>
        {else}
            <div class="alert alert-success">
                <p>{l s="Your module is up to date with the latest version of our module" mod='sellermania'}</p>
            </div>
        {/if}
    </div>
</div>

<div class="panel card {$sections["database_schema"]}">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Database integrity" mod='sellermania'}</strong>
            </div>
            {if $schema_ok}
                <small class="badge badge-success">{l s="All tables are valid" mod='sellermania'}</small>
            {else}
                <small class="badge badge-danger">{l s="Some errors were found in your database schema" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        {foreach from=$database_schema item=table}
            <h4>Table: <strong>{$table.name}</strong></h4>
            <table class="table database-table">
                <tr>
                    <th scope="col">{l s="Column name"}</th>
                    <th scope="col">{l s="Error"}</th>
                </tr>
                {foreach from=$table.fields.field item=field}
                    <tr>
                        <td>{$field.name}</td>
                        <td>
                            {if isset($field.error) and $field.error != ""}
                                <small class="badge badge-danger">{$field.error}</small>
                            {else}
                                <small class="badge badge-success">ok</small>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
            <hr>
        {/foreach}
    </div>
</div>

<div class="panel card {$sections["hooks"]}">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Hooks" mod='sellermania'}</strong>
            </div>
            {if empty($unregistered_hooks)}
                <small class="badge badge-success">{l s="All hooks are registered" mod='sellermania'}</small>
            {else}
                <small class="badge badge-danger">{l s="Some hooks are not registered" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        {if empty($unregistered_hooks)}
            <div class="alert alert-success">
                <p>
                    {l s="All hooks are registered in your module !" mod='sellermania'}
                </p>
            </div>
        {else}
            {foreach from=$unregistered_hooks item=hook}
                <div class="alert alert-danger">
                    {$hook} {l s="is not registered ! you need to re-install your module." mod='sellermania'}
                </div>
            {/foreach}
        {/if}

        <table class="table">
            <thead>
                <tr>
                    <th>{l s="Hook name"}</th>
                    <th>{l s="Status"}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$hooks_to_register item=hook}
                    <tr>
                        <td>{$hook}</td>
                        {if $hook|in_array:$unregistered_hooks}
                            <td><small class="badge badge-warning">{l s="Not found" mod='sellermania'}</small></td>
                        {else}
                            <td><small class="badge badge-success">OK</small></td>
                        {/if}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>

<div class="panel card {$sections["hosting"]}">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Hosting conditions" mod='sellermania'}</strong>
            </div>
            {if empty($minimal_sys_requirements_alerts)}
                <small class="badge badge-success">{l s="Minimal system requirements are satisfied" mod='sellermania'}</small>
            {else}
                <small class="badge badge-danger">{l s="Minimal system requirements are not satisfied" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        {foreach from=$minimal_sys_requirements_alerts item=alert_message}
            <div class="alert alert-danger">{$alert_message}</div>
        {/foreach}
        <div class="row">
            <div class="col-lg-5">
                <h2>{l s="Server information" mod='sellermania'}</h2>
                <table class="table border">
                    <tr>
                        <th>{l s="Server information:" mod='sellermania'}</th>
                        <td>{$server_info}</td>
                    </tr>
                    <tr>
                        <th>{l s="Server software version:" mod='sellermania'}</th>
                        <td>{$server_version}</td>
                    </tr>
                    <tr>
                        <th>{l s="PHP version:" mod='sellermania'}</th>
                        <td>{$php_version}</td>
                    </tr>
                    <tr>
                        <th>{l s="Memory limit:" mod='sellermania'}</th>
                        <td>{$memory_limit}</td>
                    </tr>
                    <tr>
                        <th>{l s="Max execution time" mod='sellermania'}</th>
                        <td>{$max_execution_time}</td>
                    </tr>
                </table>

                <hr>

                <h2>{l s="Database information" mod='sellermania'}</h2>
                <table class="table border">
                    <tr>
                        <th>{l s="Mysql version" mod='sellermania'}</th>
                        <td>{$database_version}</td>
                    </tr>
                    <tr>
                        <th>{l s="Mysql server" mod='sellermania'}</th>
                        <td>{$database_server}</td>
                    </tr>
                    <tr>
                        <th>{l s="Mysql database name" mod='sellermania'}</th>
                        <td>{$database_name}</td>
                    </tr>
                    <tr>
                        <th>{l s="Mysql database prefix" mod='sellermania'}</th>
                        <td>{$table_prefix}</td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-6">
                <h2>{l s="Store information" mod='sellermania'}</h2>
                <table class="table border">
                    <tr>
                        <th>{l s="Prestashop version" mod='sellermania'}</th>
                        <td>{$ps_version}</td>
                    </tr>
                    <tr>
                        <th>{l s="Shop URL" mod='sellermania'}</th>
                        <td>{$shop_url}</td>
                    </tr>
                    <tr>
                        <th>{l s="Current Theme" mod='sellermania'}</th>
                        <td>{$current_theme}</td>
                    </tr>
                </table>

                <hr>

                <h2>{l s="Mail configuration" mod='sellermania'}</h2>
                <table class="table border">
                    <tr>
                        <th>{l s="Mail method" mod='sellermania'}</th>
                        <td>
                            {if $mail_config eq "mail"}
                                {l s="You are using the PHP mail() function." mod='sellermania'}
                            {else}
                                {l s="You are using SMTP parameters." mod='sellermania'}
                            {/if}
                        </td>
                    </tr>
                </table>

                <hr>

                <h2>{l s="Your browser information" mod='sellermania'}</h2>
                <table class="table border">
                    <tr>
                        <th>{l s="Your web browser" mod='sellermania'}</th>
                        <td>{$browser_info}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="panel card">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Module Configuration" mod='sellermania'}</strong>
            </div>
            {if $field_errors|@count > 0}
                <small class="badge badge-danger">{l s="Module missconfigured" mod='sellermania'}</small>
            {else}
                <small class="badge badge-success">{l s="Module configuration is OK" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        {if $field_errors|@count > 0}
            <table class="table">
                <tr>
                    <th>Field name</th>
                    <th>Error message</th>
                    <th>Section</th>
                    <th>Configuration saved at</th>
                </tr>
                {foreach from=$field_errors item=error}
                    <tr>
                        <td>{$error.field_name}</td>
                        <td>{$error.error_message}</td>
                        <td>
                            {if $error.section == "import-orders"}
                                {l s="Import Orders"}
                            {else}
                                {l s="Export catalog"}
                            {/if}
                        </td>
                        <td>{$error.date_add}</td>
                    </tr>
                {/foreach}
            </table>
        {else}
            <div class="alert alert-success">
                {l s="Your module configuration is OK" mod='sellermania'}
            </div>
        {/if}
    </div>
</div>

<div class="panel card">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>{l s="Configuration parameters" mod='sellermania'}</strong>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table" id="sm_config_table">
            <thead>
            <tr>
                <th>{l s="ID configuration" mod='sellermania'}</th>
                <th>{l s="Name" mod='sellermania'}</th>
                <th>{l s="Value" mod='sellermania'}</th>
                <th>{l s="ID Shop" mod='sellermania'}</th>
                <th>{l s="ID Shop group" mod='sellermania'}</th>
                <th>{l s="Date add" mod='sellermania'}</th>
                <th>{l s="Date update" mod='sellermania'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$configuration_parameters item=sm_parameter}
                <tr>
                    <td>{$sm_parameter["id_configuration"]}</td>
                    <td>{$sm_parameter["name"]}</td>
                    <td>{$sm_parameter["value"]}</td>
                    <td>{$sm_parameter["id_shop_group"]}</td>
                    <td>{$sm_parameter["id_shop"]}</td>
                    <td>{$sm_parameter["date_add"]}</td>
                    <td>{$sm_parameter["date_upd"]}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>

<div class="panel card">
    <div class="card-header">
        <div class="diagnostic-item-wrapper">
            <div class="diagnostic-item-header-flex">
                <strong>Logs</strong>
            </div>
            {if $logs}
                <small class="badge badge-warning">{l s="Logs found" mod='sellermania'}</small>
            {else}
                <small class="badge badge-success">{l s="No logs found" mod='sellermania'}</small>
            {/if}
        </div>
    </div>
    <div class="card-body">
        <code style="max-height: 250px; overflow: auto;">{$logs}</code>
    </div>
</div>

<style>
    .diagnostic-item-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header {
        cursor: pointer;
        padding: 20px;
    }
    .card-body {
        padding: 20px;
        border-top: 1px solid #eee;
    }
    .badge {
        border-radius: 0 !important;
        padding: 3px 5px !important;
    }
    .table.border, .table.border td, .table.border th {
        border-collapse: collapse;
        border: 1px solid #333 !important;
    }
    .panel:not(.title-card) {
        padding: 0 !important;
    }
    .diagnostic-item-header-flex {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .diagnostic-item-header-flex i {
        margin-right: 5px;
    }
    th {
        font-weight: bold !important;
    }
    .panel .card-body {
        display: none;
    }
    .panel.show .card-body {
        display: block;
    }
    .database-table td, .database-table th {
        width: 50%;
    }
</style>

<link rel="stylesheet" href="{$sellermania_module_path}views/css/ps15.css">
<link rel="stylesheet" href="{$sellermania_module_path}lib/bootstrap/bootstrap.css">
<link rel="stylesheet" href="{$sellermania_module_path}lib/datatables/datatables.min.css">
<script src="{$sellermania_module_path}lib/datatables/datatables.min.js"></script>

<script>
    $(function () {
        $('.card-header').click(function (e) {
            e.preventDefault();
            $('.card-body', $(this).parent()).toggle();
        })

        $('#sm_config_table').DataTable({
            ordering: false,
            order: false,
            lengthChange: false,
            language: { search: "", searchPlaceholder: "Search..." },
        });
    })
</script>