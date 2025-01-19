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
        $logo_comercio = plugins_url('assets/images/comercio.png', EPS_PLUGIN_FILE);
        $style = plugins_url('assets/css/style.css', EPS_PLUGIN_FILE);
        $general = plugins_url('assets/css/general.css', EPS_PLUGIN_FILE);
        $card_style = plugins_url('assets/css/card-js.min.css', EPS_PLUGIN_FILE);
        $stylemin = plugins_url('assets/css/style.min.css', EPS_PLUGIN_FILE);
        $cardsjscss = plugins_url('assets/css/cardsjs.css', EPS_PLUGIN_FILE);
        $card_unmin = plugins_url('assets/js/card-js-unmin.js', EPS_PLUGIN_FILE);
        $indexjs = plugins_url('assets/js/index.js', EPS_PLUGIN_FILE);
        $appjs = plugins_url('assets/js/app.min.js', EPS_PLUGIN_FILE);
        $cardsjs = plugins_url('assets/js/cardsjs.js', EPS_PLUGIN_FILE);
        $epaycojs = plugins_url('assets/js/epayco.js', EPS_PLUGIN_FILE);
        //$epaycojs ="https://checkout.epayco.co/epayco.min.js";

        $lang = get_locale();
        $lang = explode('_', $lang);
        $lang = $lang[0];

        if (ini_get('allow_url_fopen')) {
            $str_arr_ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']));
        } else {
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']);
            $contents = curl_exec($c);
            curl_close($c);
            $str_arr_ipdat = @json_decode($contents);
        }

        if (!empty($str_arr_ipdat) and $str_arr_ipdat->geoplugin_status != 404) {
            $str_countryCode = $str_arr_ipdat->geoplugin_countryCode;
        } else {
            $str_countryCode = "CO";
        }

        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
              'wc_epaycosuscription_checkout',
              $this->epaycosuscription->helpers->url->getJsAsset('checkouts/suscription/ep-suscription-checkout'),
              [
                  'site_id' => 'epayco',
              ]
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_style',
            $this->epaycosuscription->helpers->url->getCssAsset('style')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_general',
            $this->epaycosuscription->helpers->url->getCssAsset('general')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_card-js',
            $this->epaycosuscription->helpers->url->getCssAsset('card-js')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_cloudflare',
            "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_cardsjs',
            $this->epaycosuscription->helpers->url->getCssAsset('cardsjs')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_fontawesome',
            "https://use.fontawesome.com/releases/v5.2.0/css/all.css"
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_cloudflare',
            "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.4.2/css/bootstrap-slider.min.css"
        );
        /*
                $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
                    'jquery',
                    "https://code.jquery.com/jquery-1.11.3.min.js"
                );

                $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
                    'app',
                    $this->epaycosuscription->helpers->url->getJsAsset('app')
                );
                $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
                    'cardsjs',
                    $this->epaycosuscription->helpers->url->getJsAsset('cardsjs')
                );
                $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
                    'card-js-unmin',
                    $this->epaycosuscription->helpers->url->getJsAsset('card-js-unmin')
                );

                $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
                    'index',
                    $this->epaycosuscription->helpers->url->getJsAsset('index')
                );*/

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_animate',
            $this->epaycosuscription->helpers->url->getCssAsset('animate')
        );

        $this->epaycosuscription->hooks->template->getWoocommerceTemplate(
            'public/checkout/subscription.php',
            [
                'logo_comercio' => $logo_comercio,
                'amount' => $amount,
                'epayco'  => 'epayco subscription',
                'shop_name' => 'shop_name',
                'product_name_' => $product_name_,
                'currency' => $currency,
                'email_billing' => $email_billing,
                'redirect_url' => $redirect_url,
                'name_billing' => $name_billing,
                'str_countryCode' => $str_countryCode,
                'style' => $style,
                'general' => $general,
                'card_style' => $card_style,
                'cardsjscss' => $cardsjscss,
                'indexjs' => $indexjs,
                'stylemin' => $stylemin,
                'card_unmin' => $card_unmin,
                'appjs' => $appjs,
                'cardsjs' => $cardsjs,
                'epaycojs' => $epaycojs
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