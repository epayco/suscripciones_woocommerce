<?php

namespace EpaycoSubscription\Woocommerce\Gateways;

use Exception;

if (!defined('ABSPATH')) {
    exit;
}

class EpaycoSuscription extends AbstractGateway
{
    /**
     * @const
     */
    public const ID = 'woo-epaycosubscription';

    /**
     * @const
     */
    public const CHECKOUT_NAME = 'checkout-subscription';

    /**
     * @const
     */
    public const WEBHOOK_API_NAME = 'WC_WooEpaycoSuscription_Gateway';

    /**
     * @const
     */
    public const LOG_SOURCE = 'EpaycoSuscription_Gateway';

     /**
      * BasicGateway constructor
      * @throws Exception
      */
    public function __construct()
    {
        parent::__construct();
        $this->id        = self::ID;
        $this->title     = $this->epaycosuscription->storeConfig->getGatewayTitle($this, 'prueba epayco');
        $this->init_form_fields();
        $this->payment_scripts($this->id);
        $this->supports = [
            'subscriptions',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_cancellation',
            'multiple_subscriptions'
        ];
        $this->description        = 'pagos de suscripciones con epayco';
        $this->method_title       = 'suscripciones epayco';
        $this->method_description = 'crea productos de suscripciones para tus clientes';
        $this->epaycosuscription->hooks->gateway->registerUpdateOptions($this);
        $this->epaycosuscription->hooks->gateway->registerGatewayTitle($this);
        $this->epaycosuscription->hooks->gateway->registerThankyouPage($this->id, [$this, 'saveOrderPaymentsId']);
        $this->epaycosuscription->hooks->gateway->registerAvailablePaymentGateway();

        $this->epaycosuscription->hooks->checkout->registerReceipt($this->id, [$this, 'renderOrderForm']);
        $this->epaycosuscription->hooks->endpoints->registerApiEndpoint(self::WEBHOOK_API_NAME, [$this, 'webhook']);

    }

    /**
     * Get checkout name
     *
     * @return string
     */
    public function getCheckoutName(): string
    {
        return self::CHECKOUT_NAME;
    }

    /**
     * Init form fields for checkout configuration
     *
     * @return void
     */
    public function init_form_fields(): void
    {
        parent::init_form_fields();

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable'),
                'type' => 'checkbox',
                'label' => __('Enable Suscription ePayco'),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __('Title'),
                'type' => 'text',
                'description' => __('It corresponds to the title that the user sees during the checkout'),
                'default' => __('Subscription ePayco'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description'),
                'type' => 'textarea',
                'description' => __('It corresponds to the description that the user will see during the checkout'),
                'default' => __('Subscription ePayco'),
                'desc_tip' => true,
            ),
            'environment' => array(
                'title' => __('Environment'),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __('Enable to run tests'),
                'desc_tip' => true,
                'default' => true,
                'options'     => array(
                    false    => __( 'Production' ),
                    true => __( 'Test' ),
                ),
            ),
            'custIdCliente' => array(
                'title' => __('P_CUST_ID_CLIENTE'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'pKey' => array(
                'title' => __('P_KEY'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'apiKey' => array(
                'title' => __('PUBLIC_KEY'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'privateKey' => array(
                'title' => __('PRIVATE_KEY'),
                'type' => 'password',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            )
        );
    }


    /**
     * Output the gateway settings screen.
     */
    /*public function admin_options()
    {
        ?>
        <div style="color: #31708f; background-color: #d9edf7; border-color: #bce8f1;padding: 10px;border-radius: 5px;">
            <b>Este modulo le permite aceptar pagos seguros por la plataforma de pagos ePayco</b>
            <br>Si el cliente decide pagar por ePayco, el estado del pedido cambiara a ePayco Esperando Pago
            <br>Cuando el pago sea Aceptado o Rechazado ePayco envia una configuracion a la tienda para cambiar
            el estado del pedido.
        </div>
        <table class="form-table">
            <tbody>
            <?php
            $this->generate_settings_html();
            ?>
            </tbody>
        </table>
        <?php
    }*/

    /**
     * Added gateway scripts
     *
     * @param string $gatewaySection
     *
     * @return void
     */
    public function payment_scripts(string $gatewaySection): void
    {
        parent::payment_scripts($gatewaySection);

        if ($this->canCheckoutLoadScriptsAndStyles()) {
            $this->registerCheckoutScripts();
        }
    }

    /**
     * Register checkout scripts
     *
     * @return void
     */
    public function registerCheckoutScripts(): void
    {
        parent::registerCheckoutScripts();

        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'wc_epaycosubscription_sdk',
            'https://sdk.mercadopago.com/js/v2'
        );
        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'wc_epaycosuscription_checkout',
            $this->epaycosuscription->helpers->url->getJsAsset('checkouts/suscription/mp-suscription-checkout'),
            [
                'site_id' => '',
            ]
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
     * Get Payment Fields params
     *
     * @return array
     */
    public function getPaymentFieldsParams(): array
    {
        return [];
    }

    /**
     * Process payment and create woocommerce order
     *
     * @param $order_id
     *
     * @return array
     */
    public function process_payment($order_id): array
    {
        $order = wc_get_order( $order_id );

        // Lógica de pago (aquí iría la integración con la API del proveedor de pagos)
        // Marcar el pedido como procesado (si el pago es exitoso)
        $order->payment_complete();

        // Reducir el stock de productos
        wc_reduce_stock_levels( $order_id );

        // Vaciar el carrito de compras
        WC()->cart->empty_cart();

        // Redirigir al cliente al "gracias por su compra"
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );
    }

    /**
     * Render order form
     *
     * @param $order_id
     * @throws Exception
     */
    public function renderOrderForm($order_id): void
    {

    }
}