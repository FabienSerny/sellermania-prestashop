class SmWizard {

    _apiConnectionIsOk = false;

    _ajax_path = $('#sm-wizard-login').attr('data-module-web-path') + "wizard_ajax.php"
    _lang = $('#sm-wizard-login').attr('data-lang');
    _key = $('#sm-wizard-login').attr('data-key');

    _validatorErrors = [];

    _marketplaces_loaded = false;
    _carriers_loaded = false;

    _marketplaceToImportFrom = [];

    _GENERAL_INFO_STEP_NUMBER = 1;
    _MARKETPLACE_LIST_STEP_NUMBER = 2;
    _CARRIERS_FOR_MARKETPLACE_STEP_NUMBER = 3;
    _FEED_EXPORT_STEP_NUMBER = 4;
    _ORDER_STATES_MAPPING_STEP_NUMBER = 5;

    _translationMessages = {
        "Connection could not be established": {"fr": "La connexion n'a pas pu être établie"},
        "Connection established successfully": {"fr": "Connexion réussie"},

        "A configured shipping carrier for": {"fr": "Un transporteur configuré pour"},
        "should have a shipping service": {"fr": "doit avoir un service de livraison"},
        "You are importing orders from": {"fr": "Vous avez choisi d'importer des commandes de"},
        "but you haven't configured one shipping carrier at least for it": {"fr": "mais vous n'avez pas configuré au moins un transporteur pour cette marketplace"},
        "You need to define a positive number of days": {"fr": "Vous devez définir un nombre de jours positif"},
        "You need to choose the way you want to export your catalog": {"fr": "Vous devez choisir la manière dont vous voulez exporter votre catalogue"},
        "Sellermania specific order status created and matched successfully !": {"fr": "Les états de commandes spécifiques à Sellermania ont été créé avec succès"},
        "The following order states need to be matched": {"fr": "Vous devez correspondre ces états de commandes avec ceux de Prestashop"},
        "You need to provide a positive number for last X days": {"fr": "Vous devez indiquer un nombre positif pour les X derniers jours"},
        "You need to provide a positive number of orders to import": {"fr": "Vous devez indiquer un nombre positif pour les commandes à importer"},
    }

    _l ($k) {
        if (typeof this._lang === undefined) {
            this._lang = "en"
        }
        if (typeof this._translationMessages[$k][this._lang] != "undefined") {
            return this._translationMessages[$k][this._lang];
        }
        return $k;
    }

    start () {
        $('#btn-launch-wizard').click(function (e) {
            e.preventDefault();
            $('#sm-wz-welcome-page').fadeOut(500, function () {
                $('.sellermaniaWrapper').fadeIn();
            })
        })

        return this;
    }

    testApi ($parent, $testApiWrapper, email, token, orderEndpoint) {
        $parent.prepend('<span class="sm-loader"></span>');
        $testApiWrapper.html("");
        let sm_key = this._key;

        $.ajax({
            type: 'POST',
            url: this._ajax_path,
            data: {
                ajax: true,
                key: sm_key,
                method: "test_api",
                email: email,
                token: token,
                endpoint: orderEndpoint,
            },
            cache: false,
            context: this,
        }).done(function (jqxhr) {
            let result = JSON.parse(jqxhr);
            if ("ok" === result.status) {
                $testApiWrapper.append('<div class="alert alert-success">'+this._l("Connection established successfully")+' !</div>');
                this._apiConnectionIsOk = true;
                setTimeout(function () {
                    $("#sm-wizard-login").fadeOut(500, function () {
                        $('#sellermania-module-wizard').fadeIn();
                    })
                }, 1000)
            } else {
                $testApiWrapper.append('<div class="alert alert-danger">'+this._l("Connection could not be established")+'<br>' + result.message + '</div>');
                this._apiConnectionIsOk = false;
            }
            $(".sm-loader", $parent).remove();
        });
    }

    createCustomStatus ($parent) {
        let that = this;
        let sm_key = this._key;
        $parent.append('<span class="sm-loader"></span>');
        $.ajax({
            type: 'POST',
            url: this._ajax_path,
            data: {
                ajax: true,
                key: sm_key,
                method: "order_status"
            },
            cache: false,
        }).done(function (jqxhr) {
            setTimeout(function () {
                let $orderStatesTable = $('tbody', '#order_states_table');
                if (!$orderStatesTable.length) {
                    $orderStatesTable = $('#order_states_table');
                }

                $($orderStatesTable).html(jqxhr);

                $('#custom-status-creation-wrapper').slideUp(500, function () {
                    $(this).remove();
                });
                $("#custom-status-creation-wrapper").after('<div class="alert alert-success">'+that._l("Sellermania specific order status created and matched successfully !")+'</div>')
            }, 1000);
        });
    }

    getAvailableMarketplaces ($marketplacesListWrapper) {
        if (!this._marketplaces_loaded) {
            let sm_key = this._key;
            $marketplacesListWrapper.html('<span class="sm-loader"></span>');
            $.ajax({
                type: 'POST',
                url: this._ajax_path,
                data: {
                    ajax: true,
                    key: sm_key,
                    method: "get_available_marketplaces",
                    email: $('input[name=sm_order_email]').val(),
                    token: $('input[name=sm_order_token]').val(),
                    endpoint: $('input[name=sm_order_endpoint]').val(),
                },
                cache: false,
            }).done(function (jqxhr) {
                $(".sm-loader", $marketplacesListWrapper).remove();
                $marketplacesListWrapper.html(jqxhr);
            });
            this._marketplaces_loaded = true;
        }
    }

    getCarriersForMarketplaces ($marketplacesListWrapper) {
        if (!this._carriers_loaded) {
            let sm_key = this._key;
            $marketplacesListWrapper.html('<span class="sm-loader"></span>');

            $.ajax({
                type: 'POST',
                url: this._ajax_path,
                data: {
                    ajax: true,
                    key: sm_key,
                    method: "get_carriers_for_marketplaces",
                    email: $('input[name=sm_order_email]').val(),
                    token: $('input[name=sm_order_token]').val(),
                    endpoint: $('input[name=sm_order_endpoint]').val(),
                },
                cache: false,
            }).done(function (jqxhr) {
                $(".sm-loader", $marketplacesListWrapper).remove();
                $marketplacesListWrapper.html(jqxhr);
                $("body").trigger("domChanged");
            });
            this._carriers_loaded = true;
        }
    }

    navigate () {
        $('.btn-navigate-form-step').click({_this: this}, function (e) {
            e.preventDefault();
            let step = Number($(this).attr('step_number'));

            if (e.data._this._MARKETPLACE_LIST_STEP_NUMBER === step) {
                let $marketplacesListWrapper = $('#wz-marketplaces-list');
                e.data._this.getAvailableMarketplaces($marketplacesListWrapper);

            } else if (e.data._this._CARRIERS_FOR_MARKETPLACE_STEP_NUMBER === step) {
                let $marketplacesListWrapper = $('#wz-carriers-for-marketplaces-list');
                e.data._this.getCarriersForMarketplaces($marketplacesListWrapper)
            }
        })

        return this;
    }

    showDefaultValues () {
        $('input[name=sm_import_method]').change(function (e) {
            e.preventDefault();
            let $cronConfigWrapper = $('#sm_import_method_cron_configuration');
            $cronConfigWrapper.hide();
            if ($(this).val() === "cron") {
                $cronConfigWrapper.show();
            }
        })

        $('input[type=radio][name=sm_product_to_include_in_feed]').change(function () {
            if ("all" === this.value) {
                $('#sm_last_days_to_include_in_feed').val("")
            } else if ("without_oos" === this.value) {
                $('#sm_last_days_to_include_in_feed').val("7")
            }
        })

        return this;
    }

    catchClicksAndEvents () {
        $('#create-custom-status').click({_this: this}, function (e) {
            e.preventDefault();
            let $parent = $(this).parent();
            e.data._this.createCustomStatus($parent);
        })

        $('#btn-test-api').click({_this: this}, function (e) {
            e.preventDefault();
            $('.error-message').remove();
            let $testApiWrapper = $("#test-api-connectivity-result");
            let $parent = $(this).parent();

            let email = $('input[name=sm_order_email]').val();
            let token = $('input[name=sm_order_token]').val();
            let orderEndpoint = $('input[name=sm_order_endpoint]').val();
            let confirmOrderEndpoint = $('input[name=sm_confirm_order_endpoint]').val();

            if (email === "" || token === "" || orderEndpoint === "" || confirmOrderEndpoint === "") {
                $(this).after('<p class="error-message">Please fill in all the requested information</p>');
                return;
            }
            e.data._this.testApi($parent, $testApiWrapper, email, token, orderEndpoint);
        })

        $('.api-connection-field').on('input', {_this: this},  function (e) {
            e.data._this._apiConnectionIsOk = false;
            e.data._this._marketplaces_loaded = false;
            e.data._this._carriers_loaded = false;
        })

        return this;
    }

    navigateToFormStep (stepNumber) {
        document.getElementById('sellermania-module-wizard').scrollIntoView();

        $('.form-step').each(function () {
            $(this).addClass('d-none').removeClass('animated');
        })

        $('.form-stepper-list').each(function () {
            $(this).addClass('form-stepper-unfinished').removeClass('form-stepper-active form-stepper-completed')
        })

        $('#step-' + stepNumber).removeClass('d-none').addClass('animated');

        let $formStepCircle = $('li[step="' + stepNumber + '"]');

        $formStepCircle.removeClass('form-stepper-unfinished form-stepper-completed').addClass('form-stepper-active')

        for (let index = 0; index < stepNumber; index++) {
            $formStepCircle = $('li[step="' + index + '"]');
            if ($formStepCircle.length) {
                $formStepCircle.removeClass('form-stepper-unfinished form-stepper-active').addClass('form-stepper-completed');
            }
        }
    }

    validateFormStep (formStep) {
        let isOk = true;
        this._validatorErrors = [];

        switch (formStep) {
            case this._GENERAL_INFO_STEP_NUMBER:
                isOk = this._validateStepGeneralInfo();
                break;
            case this._MARKETPLACE_LIST_STEP_NUMBER:
                isOk = this._validateStepMarketplaceList();
                break;
            case this._CARRIERS_FOR_MARKETPLACE_STEP_NUMBER:
                isOk = this._validateCarriersForMarketplaces();
                break;
            case this._FEED_EXPORT_STEP_NUMBER:
                isOk = this._validateFeedExport();
                break;
            case this._ORDER_STATES_MAPPING_STEP_NUMBER:
                isOk = this._validateOrderStatesMapping();
                break;
            default:
                return false;
        }

        return isOk;
    }

    _validateStepGeneralInfo () {
        let isOk = true;

        let sm_order_import_past_days = $("input[name=sm_order_import_past_days]").val()
        let sm_order_import_limit = $("input[name=sm_order_import_limit]").val()

        if (isNaN(sm_order_import_past_days) || (sm_order_import_past_days - Math.floor(sm_order_import_past_days)) !== 0 || '' === sm_order_import_past_days || sm_order_import_past_days <= 0) {
            this._validatorErrors.push(this._l("You need to provide a positive number for last X days"))
            isOk = false;
        }
        if (isNaN(sm_order_import_limit) || (sm_order_import_limit - Math.floor(sm_order_import_limit)) !== 0 || '' === sm_order_import_limit || sm_order_import_limit <= 0) {
            this._validatorErrors.push(this._l("You need to provide a positive number of orders to import"))
            isOk = false;
        }

        if (true !== this._apiConnectionIsOk) {
            this._validatorErrors.push("Please test your API connection before proceeding to the next step");
            isOk = false;
        }

        return isOk;
    }

    _validateStepMarketplaceList () {
        //let isOk = false;
        let marketplaceToImportFrom = [];
        $('select[data-connected=1]', '#wz-marketplaces-list').each(function () {
            if ($(this).val() !== "NO") {
                //isOk = true;
                let fieldName = $(this).attr("name");
                let marketplaceCode = fieldName.replace("SM_MKP_", "");
                marketplaceToImportFrom.push(marketplaceCode);
            }
        })
        /*if (!isOk) {
            this._validatorErrors.push("You should import orders from at least one Marketplace");
        }*/
        this._marketplaceToImportFrom = marketplaceToImportFrom;
        //return isOk;
        return true;
    }

    _validateCarriersForMarketplaces () {
        let isOk = true;
        let errors = [];
        let that = this;
        for (let i in this._marketplaceToImportFrom) {
            let marketplaceConfigIsGood = false;
            let marketplaceCode = this._marketplaceToImportFrom[i];
            $("[name^='SM_MKP_DELIVERY_"+marketplaceCode+"_']").each(function () {
                let fieldName = $(this).attr('name');
                if ("" !== $(this).val()) {
                    marketplaceConfigIsGood = true;
                    if (marketplaceCode.includes("AMAZON_")) {
                        let psCarrierId = fieldName.replace('SM_MKP_DELIVERY_'+marketplaceCode+'_', '');
                        if ("" === $('input[name=SM_MKP_SHIPPING_SERVICE_'+marketplaceCode+'_'+psCarrierId+']').val()) {
                            isOk = false;
                            errors.push(that._l("A configured shipping carrier for") + " " + marketplaceCode.replace("_", ".") + " " + that._l("should have a shipping service"));
                        }
                    }
                }
            })
            if (!marketplaceConfigIsGood) {
                isOk = false;
                errors.push(that._l("You are importing orders from") + " " + (marketplaceCode.replace("_", ".")) + " " + that._l("but you haven't configured one shipping carrier at least for it"));
            }
        }
        this._validatorErrors = errors;
        return isOk;
    }

    _validateFeedExport () {
        let isOk = true;
        let val = $('input[name=sm_product_to_include_in_feed]:checked').val();
        if (undefined !== val) {
            if ("without_oos" === val) {
                let nbDays = $('input[name=sm_last_days_to_include_in_feed]').val();
                if (isNaN(nbDays) || (nbDays - Math.floor(nbDays)) !== 0 || '' === nbDays || nbDays < 1) {
                    isOk = false;
                    this._validatorErrors.push(this._l("You need to define a positive number of days"));
                }
            }
        } else {
            isOk = false;
            this._validatorErrors.push(this._l("You need to choose the way you want to export your catalog"));
        }
        return isOk;
    }

    _validateOrderStatesMapping () {
        let isOk = true;
        let mandatoryOrderStatesToMap = [
            {smStatus: 6, enLabel: "To be confirmed", frLabel: "A confirmer"},
            {smStatus: 1, enLabel: "To dispatch", frLabel: "A expédier"},
            {smStatus: 2, enLabel: "Dispatched", frLabel: "Expédiée"},
            {smStatus: 4, enLabel: "Canceled", frLabel: "Annulée"},
        ];


        let includeAmazonVendor = false;
        let includeQuelBonPlan = false;
        for (let i in this._marketplaceToImportFrom) {
            if (this._marketplaceToImportFrom[i].includes("AMAZONVENDOR")) {
                includeAmazonVendor = true;
            }
            if (this._marketplaceToImportFrom[i].includes("QUELBONPLAN")) {
                includeQuelBonPlan = true;
            }
        }
        if (includeAmazonVendor) {
            mandatoryOrderStatesToMap.push({smStatus: 20, enLabel: "Delivered - AMAZON VENDOR", frLabel: "Délivré - AMAZON VENDOR"})
        }
        if (includeQuelBonPlan) {
            mandatoryOrderStatesToMap.push({smStatus: 21, enLabel: "Ready to ship - QuelBonPlan", frLabel: "Prêt pour expédition - QuelBonPlan"})
        }

        $("select[name^='SM_PS_ORDER_MAP']").each(function () {
            let index = -1;
            for (let i in mandatoryOrderStatesToMap) {
                if ($(this).val() == mandatoryOrderStatesToMap[i].smStatus) {
                    index = i;
                }
            }
            if (index !== -1) {
                mandatoryOrderStatesToMap.splice(index, 1);
            }
        })
        if (mandatoryOrderStatesToMap.length > 0) {
            isOk = false;
            let errorMessage = this._l("The following order states need to be matched")+": ";
            for (let i in mandatoryOrderStatesToMap) {
                errorMessage += '<code>' + mandatoryOrderStatesToMap[i][this._lang+"Label"] + '</code>';
            }
            this._validatorErrors.push(errorMessage)
        }
        return isOk;
    }

    init () {
        // prevent submitting form when hitting enter
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        $(".btn-navigate-form-step").click({_this: this}, function (e) {
            let _this = e.data._this;
            let stepNumberToNavigate = parseInt($(this).attr("step_number"));
            let currentStepNumber = stepNumberToNavigate - 1;

            $('.error-message').remove();

            if (currentStepNumber === 0) {
                currentStepNumber = 1;
            }

            if (_this.validateFormStep(currentStepNumber)) {
                _this.navigateToFormStep(stepNumberToNavigate);
            } else {
                for (let i in _this._validatorErrors) {
                    $("#step-" + currentStepNumber).append("<p class='error-message'><small>"+_this._validatorErrors[i]+"</small></p>")
                }
            }
        });

        $('.submit-btn').click({_this: this}, function (e) {
            $('.error-message').remove();
            let _this = e.data._this
            if (!_this.validateFormStep(_this._ORDER_STATES_MAPPING_STEP_NUMBER)) {
                e.preventDefault();
                for (let i in _this._validatorErrors) {
                    $("#step-" + _this._ORDER_STATES_MAPPING_STEP_NUMBER).append("<p class='error-message'><small>"+_this._validatorErrors[i]+"</small></p>")
                }
            }
        })

        return this;
    }
}

$(function () {
    let _smWizard = new SmWizard();
    _smWizard.init().start().navigate().catchClicksAndEvents().showDefaultValues();
})