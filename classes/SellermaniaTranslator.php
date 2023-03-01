<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class SellermaniaTranslator
{
    const TRANSLATIONS = [
        // available marketplaces config
        "Not connected" => [
            "fr" => "Non connectée"
        ],
        "Connected" => [
            "fr" => "Connectée"
        ],
        "Marketplace included in your subscription and connected to your Sellermania account" => [
            "fr" => "Marketplace incluse dans votre abonnement et connectée à votre compte Sellermania"
        ],
        "Marketplace included in your subscription but not connected to your Sellermania account" => [
            "fr" => "Marketplace incluse dans votre abonnement mais non connectée à votre compte Sellermania"
        ],
        "Do not import the orders" => [
            "fr" => "Ne pas importer les commandes"
        ],
        "Import the orders with auto-confirmation (recommended)" => [
            "fr" => "Importer les commandes avec auto-confirmation (recommandé)"
        ],
        "Import the orders without auto-confirmation" => [
            "fr" => "importer les commandes sans auto-confirmation"
        ],

        // Shipping carriers config
        "Prestashop carriers" => [
            "fr" => "Transporteurs Prestashop",
        ],
        "Marketplace carriers" => [
            "fr" => "Transporteurs marketplace",
        ],
        "Shipping service" => [
            "fr" => "Service de livraison",
        ],

        // validator
        "The following order states need to be matched:" => [
            "fr" => "Vous devez faire la correspondance pour ces états de commandes:",
        ],
        "A default matching was suggested to fix the error, you can keep it by re-saving your configuration." => [
            "fr" => "Une correspondance a été faite comme suggestion pour vous corriger le problème. Vous pouvez la garder en sauvegardant de nouveau votre configuration",
        ],
        "You are importing orders from" => [
            "fr" => "Vous avez choisi d'importer des commandes de",
        ],
        "but you haven't configured one shipping carrier at least for it" => [
            "fr" => "mais vous n'avez configuré au moins un transporteur pour cette marketplace",
        ],
        "You need to define a positive number of days" => [
            "fr" => "Vous devez fournir un nombre de jours positif"
        ],
        "You need to choose the way you want to export your catalog" => [
            "fr" => "Vous devez choisir la méthode d'export de votre catalogue"
        ],
        "You need to provide a positive number for last X days" => [
            "fr" => "Vous devez indiquer un nombre positif pour les X derniers jours"
        ],
        "You need to provide a positive number of orders to import" => [
            "fr" => "Vous devez indiquer un nombre positif pour les commandes à importer"
        ],
        "A problem was detected with your API connection. Please check your credentials" => [
            "fr" => "Un problème de connexion API a été détecté. Veuillez vérifier vos informations de connexion.",
        ],
        "For more details" => [
            "fr" => "Pour plus de détails"
        ],
        "A configured shipping carrier for" => [
            "fr" => "Un transporteur configuré pour"
        ],
        "should have a shipping service" => [
            "fr" => "doit avoir un service de livraison"
        ]
    ];

    private $lang;

    public function __construct ($lang = null)
    {
        if (!$lang) {
            $this->lang = Context::getContext()->language->iso_code;
        } else {
            $this->lang = $lang;
        }
    }

    public function l($k)
    {
        if (array_key_exists($k, self::TRANSLATIONS)) {
            if (array_key_exists($this->lang, self::TRANSLATIONS[$k])) {
                return self::TRANSLATIONS[$k][$this->lang];
            }
        }
        /*if (isset(self::TRANSLATIONS[$k][$this->lang])) {
            return self::TRANSLATIONS[$k][$this->lang];
        }*/

        return $k;
    }
}