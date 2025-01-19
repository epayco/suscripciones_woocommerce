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
        $this->epaycosuscription->hooks->gateway->registerCustomBillingFieldOptions();
        $this->epaycosuscription->hooks->gateway->registerGatewayReceiptPage($this->id, [$this, 'receiptPage']);
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
                'title' => __('Habilitar/Deshabilitar', 'epayco_woocommerce_sub'),
                'type' => 'checkbox',
                'label' => __('Habilitar ePayco Checkout Suscription', 'epayco-subscription'),
                'default' => 'yes'
            ),
            'epayco_title' => array(
                'title' => __('Titulo', 'epayco-subscription'),
                'type' => 'text',
                'description' => __('Corresponde al titulo que los usuarios visualizan el chekout'),
                'default' => __('Subscription ePayco'),
                'desc_tip' => true,
            ),
            'shop_name' => array(
                'title' => __('Nombre del comercio'),
                'type' => 'text',
                'description' => __('Corresponde al nombre de la tienda que los usuarios visualizan en el checkout'),
                'default' => __('Subscription ePayco'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description'),
                'type' => 'textarea',
                'description' => __('Corresponde al descripción de la tienda que los usuarios visualizan en el checkout'),
                'default' => __('Subscription ePayco'),
                'desc_tip' => true,
            ),
            'environment' => array(
                'title' => __('Modo'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('mode prueba/producción'),
                'desc_tip' => true,
                'default' => true,
                'options' => array(
                    false => __('Production'),
                    true => __('Test'),
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
            ),
        );
    }


    /**
     * Output the gateway settings screen.
     */
    public function admin_options()
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
            'wc_epaycosubscription_jquery',
            'https://code.jquery.com/jquery-1.11.3.min.js'
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
     * @throws Exception
     */
    public function process_payment($order_id): array
    {
        $order = wc_get_order($order_id);
        try {
            $urlReceived =  $order->get_checkout_payment_url( true );
            return [
                'result'   => 'success',
                'redirect' => $urlReceived,
            ];

        }catch (Exception $e) {
            return [
                'result'   => 'false',
                'message' =>  $e->getMessage(),
                'redirect' => '',
            ];
        }
    }

    /**
     * Render Receipt  page
     *
     * @param $order_id
     */
    public function receiptPage($order_id): void
    {
        global $woocommerce;
        global $wpdb;
        $subscription = new \WC_Subscription($order_id);
        $order = wc_get_order($order_id);
        $order_data = $order->get_data(); // The Order data
        $name_billing = $subscription->get_billing_first_name() . ' ' . $subscription->get_billing_last_name();
        $email_billing = $subscription->get_billing_email();
        $redirect_url = get_site_url() . "/";
        $redirect_url = add_query_arg('wc-api', get_class($this), $redirect_url);
        $redirect_url = add_query_arg('order_id', $order_id, $redirect_url);
        $amount = $subscription->get_total();
        $mountFloat = floatval($amount);
        $currency = get_woocommerce_currency();
        $descripcionParts = array();
        foreach ($subscription->get_items() as $product) {
            $clearData = str_replace('_', ' ', $this->string_sanitize($product['name']));
            $descripcionParts[] = $clearData;
        }

        $descripcion = implode(' - ', $descripcionParts);
        if (substr_count($descripcion, ' - ') >= 1) {
            $product_name = $descripcionParts[0];
            $porciones = explode(" - ", $product_name);
            $product_name = $porciones[0] . "...";
        } else {
            $product_name = $descripcion;
        }
        if (strlen($product_name) < 20) {
            $product_name_ = $descripcion;
        } else {
            $resultado = substr($product_name, 0, 19);
            $product_name_ = $resultado . "...";
        }

        $logo_comercio = plugin_dir_url(__FILE__) . 'assets/images/comercio.png';
        echo ' <div class="loader-container">
                    <div class="loading"></div>
                </div>
                <p style="text-align: center;" class="epayco-title">
                    <span class="animated-points">Cargando métodos de pago</span>
                    <br><small class="epayco-subtitle"></small>
                </p>';
        $idioma = substr(get_locale(), 0, 2);
        if ($idioma === "en") {
            $epaycoButtonImage = 'https://multimedia.epayco.co/epayco-landing/btns/Boton-epayco-color-Ingles.png';
        }else{
            $epaycoButtonImage = 'https://multimedia.epayco.co/epayco-landing/btns/Boton-epayco-color1.png';
        }
        echo '<p>       
                 <center>
                    <a id="btn_epayco" href="#">
                       <img src="'.$epaycoButtonImage.'">
                    </a>
                 </center> 
               </p>';
          $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
              'wc_epaycosuscription_checkout',
              $this->epaycosuscription->helpers->url->getJsAsset('checkouts/suscription/ep-suscription-checkout'),
              [
                  'site_id' => 'richi',
              ]
          );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_animate',
            $this->epaycosuscription->helpers->url->getCssAsset('animate')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_card-js',
            $this->epaycosuscription->helpers->url->getCssAsset('card-js')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_cardsjs',
            $this->epaycosuscription->helpers->url->getCssAsset('cardsjs')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_general',
            $this->epaycosuscription->helpers->url->getCssAsset('general')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_style',
            $this->epaycosuscription->helpers->url->getCssAsset('style')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_subscription-epayco',
            $this->epaycosuscription->helpers->url->getCssAsset('subscription-epayco')
        );
        $this->epaycosuscription->hooks->template->getWoocommerceTemplate(
            'public/checkout/subscription.php',
            [
                'epayco'  => 'epayco subscription'
            ]
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

    public function string_sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
        return $clean;
    }
}