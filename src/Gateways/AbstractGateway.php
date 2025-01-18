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

        if ($this->canCheckoutLoadScriptsAndStyles()) {
            $this->registerCheckoutScripts();
        }
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
        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'wc_epaycosubscription_checkout_components',
            $this->epaycosuscription->helpers->url->getJsAsset('checkouts/ep-plugins-components'),
            [
                'ep_json_url' => EPS_PLUGIN_URL,
                'lang' => substr(get_locale(), 0, 2)
            ]
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_checkout_components',
            $this->epaycosuscription->helpers->url->getCssAsset('checkouts/ep-plugins-components')
        );

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


}