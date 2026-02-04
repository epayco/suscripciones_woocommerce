<?php

namespace EpaycoSubscription\Woocommerce\Gateways;

use EpaycoSubscription\Woocommerce\Interfaces\EpaycoSubscriptionGatewayInterface;
use EpaycoSubscription\Woocommerce\WoocommerceEpaycoSubscription;
use Exception;

use WC_Payment_Gateway;

abstract class AbstractGateway extends WC_Payment_Gateway implements EpaycoSubscriptionGatewayInterface
{
    public const ID = '';

    public const CHECKOUT_NAME = '';

    public const WEBHOOK_API_NAME = '';

    public const LOG_SOURCE = '';

    public WoocommerceEpaycoSubscription $epaycosuscription;

    /**
     * Abstract Gateway constructor
     * @throws Exception
     */
    public function __construct()
    {
        global $epaycosuscription;

        $this->epaycosuscription = $epaycosuscription;

        $this->has_fields = true;
        $this->supports   = [ 'products', 'refunds' ];
        $this->init_settings();
    }

    /**
     * Init form fields for checkout configuration
     *
     * @return void
     */
    public function init_form_fields(): void
    {
        $this->form_fields = [];
    }

    /**
     * Added gateway scripts
     *
     * @param string $gatewaySection
     *
     * @return void
     */
    public function payment_scripts(string $gatewaySection): void
    {
        if ($this->canAdminLoadScriptsAndStyles($gatewaySection)) {
            $this->registerAdminScripts();
        }


        if ($this->canCheckoutLoadScriptsAndStyles()) {
            $this->registerCheckoutScripts();
            $this->registerAdminScripts();
        }
    }

    /**
     * Check if admin scripts and styles can be loaded
     *
     * @param string $gatewaySection
     *
     * @return bool
     */
    public function canAdminLoadScriptsAndStyles(string $gatewaySection): bool
    {
        return $this->epaycosuscription->hooks->admin->isAdmin() && ( $this->epaycosuscription->helpers->url->validatePage('wc-settings') &&
                $this->epaycosuscription->helpers->url->validateSection($gatewaySection)
            );
    }

       /**
     * Register admin scripts
     *
     * @return void
     */
    public function registerAdminScripts()
    {
        $this->epaycosuscription->hooks->scripts->registerAdminScript(
            'wc_epaycosuscription_admin_components',
            $this->epaycosuscription->helpers->url->getJsAsset('admin/ep-admin-configs')
        );

        $this->epaycosuscription->hooks->scripts->registerAdminStyle(
            'wc_epaycosuscription_admin_components',
            $this->epaycosuscription->helpers->url->getCssAsset('admin/ep-admin-configs')
        );
    }

    /**
     * Check if admin scripts and styles can be loaded
     *
     * @return bool
     */
    public function canCheckoutLoadScriptsAndStyles(): bool
    {
        return $this->epaycosuscription->hooks->gateway->isEnabled($this) &&
            ! $this->epaycosuscription->helpers->url->validateQueryVar('order-received');
    }

    /**
     * Register checkout scripts
     *
     * @return void
     */
    public function registerCheckoutScripts(): void
    {
        /*
        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'wc_epaycosubscription_checkout_components',
            $this->epaycosuscription->helpers->url->getJsAsset('checkouts/ep-plugins-components'),
            [
                'ep_json_url' => EPS_PLUGIN_URL,
                'lang' => substr(get_locale(), 0, 2)
            ]
        );*/
    }

    /**
     * Render gateway checkout template
     *
     * @return void
     */
    public function payment_fields(): void
    {
    }

    /**
     * Validate gateway checkout form fields
     *
     * @return bool
     */
    public function validate_fields(): bool
    {
        return true;
    }

    /**
     * Process payment and create woocommerce order
     *
     * @param $order_id
     *
     * @return array
     * @throws Exception
     */
    public function process_payment($order_id): array
    {
        return [];
    }

    /**
     * Receive gateway webhook notifications
     *
     * @return void
     */
    public function webhook(): void
    {

    }

    /**
     * Verify if the gateway is available
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        return true;
    }

    protected function errorMessages($dataError){
        $error = "Ocurrió un error, por favor contactar con soporte.";
        $message = $error;
        $errores_listados = [];

        // If it is an array, process the data
        if (is_array($dataError)) {
            // Try to get the main message
            if (isset($dataError['message']) && !empty($dataError['message'])) {
                $message = $dataError['message'];
            }

            // Process nested errors in array format
            if (isset($dataError['data']['errors']) && is_array($dataError['data']['errors'])) {
                foreach ($dataError['data']['errors'] as $campo => $mensajes) {
                    if (is_array($mensajes)) {
                        foreach ($mensajes as $msg) {
                            $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                        }
                    } else {
                        $errores_listados[] = ucfirst($campo) . ': ' . $mensajes;
                    }
                }
            }

            // Process nested errors in object format
            if (isset($dataError['data']->errors) && is_array($dataError['data']->errors)) {
                foreach ($dataError['data']->errors as $campo => $mensajes) {
                    if (is_array($mensajes)) {
                        foreach ($mensajes as $msg) {
                            $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                        }
                    } else {
                        $errores_listados[] = ucfirst($campo) . ': ' . $mensajes;
                    }
                }
            }

            // If data.errors is a direct string, use it as the message
            if (isset($dataError['data']['errors']) && is_string($dataError['data']['errors'])) {
                $message = $dataError['data']['errors'];
            }

            // If there is no main message but there are listed errors, use the first one
            if ($message === $error && !empty($errores_listados)) {
                $message = $errores_listados[0];
                array_shift($errores_listados);
            }
        } elseif (is_string($dataError)) {
            // If it is a direct string, use it as the message
            $message = $dataError;
        }

        $errorMessage = $message;
        if (!empty($errores_listados)) {
            $errorMessage .= ' | ' . implode(' | ', $errores_listados);
        }

        return $errorMessage;
    }

}