<?php

namespace EpaycoSubscription\Woocommerce\Gateways;

use Exception;
use Epayco as EpaycoSdk;

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

    protected EpaycoSdk\Epayco $epaycoSdk;

    /**
     * BasicGateway constructor
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->id        = self::ID;
        $this->title     = $this->epaycosuscription->storeConfig->getGatewayTitle($this, 'epayco');
        $this->init_form_fields();
        $this->payment_scripts($this->id);
        $this->supports = [
            'subscriptions',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_cancellation',
            'multiple_subscriptions'
        ];
        $this->description        = 'Pagos de suscripciónes con epayco';
        $this->method_title       = 'Suscripciónes ePayco';
        $this->method_description = 'Crea productos de suscripciónes para tus clientes';
        $this->epaycosuscription->hooks->gateway->registerUpdateOptions($this);
        $this->epaycosuscription->hooks->gateway->registerGatewayTitle($this);
        //  $this->epaycosuscription->hooks->gateway->registerThankyouPage($this->id, [$this, 'saveOrderPaymentsId']);
        $this->epaycosuscription->hooks->gateway->registerAvailablePaymentGateway();
        $this->epaycosuscription->hooks->gateway->registerCustomBillingFieldOptions();
        $this->epaycosuscription->hooks->gateway->registerGatewayReceiptPage($this->id, [$this, 'receiptPage']);
        $this->epaycosuscription->hooks->checkout->registerReceipt($this->id, [$this, 'renderOrderForm']);
        $this->epaycosuscription->hooks->endpoints->registerApiEndpoint(self::WEBHOOK_API_NAME, [$this, 'webhook']);
        $lang = get_locale();
        $lang = explode('_', $lang);
        $lang = $lang[0];
        $this->epaycoSdk = new EpaycoSdk\Epayco(
            [
                "apiKey" => $this->get_option('apiKey'),
                "privateKey" => $this->get_option('privateKey'),
                "lenguage" => strtoupper($lang),
                "test" => (bool)$this->get_option('environment')
            ]
        );
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
                'title' => __('Habilitar/Deshabilitar', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Habilitar ePayco Checkout Suscription', 'epayco-subscriptions-for-woocommerce'),
                'default' => 'yes'
            ),
            'epayco_title' => array(
                'title' => __('Titulo', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('Corresponde al titulo que los usuarios visualizan el chekout', 'epayco-subscriptions-for-woocommerce'),
                'default' => __('Subscription ePayco', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
            ),
            'shop_name' => array(
                'title' => __('Nombre del comercio', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('Corresponde al nombre de la tienda que los usuarios visualizan en el checkout', 'epayco-subscriptions-for-woocommerce'),
                'default' => __('Subscription ePayco', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'textarea',
                'description' => __('Corresponde al descripción de la tienda que los usuarios visualizan en el checkout', 'epayco-subscriptions-for-woocommerce'),
                'default' => __('Subscription ePayco', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
            ),
            'environment' => array(
                'title' => __('Modo', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('mode prueba/producción', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
                'default' => true,
                'options' => array(
                    false => __('Production', 'epayco-subscriptions-for-woocommerce'),
                    true => __('Test', 'epayco-subscriptions-for-woocommerce'),
                ),
            ),
            'custIdCliente' => array(
                'title' => __('P_CUST_ID_CLIENTE', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'epayco-subscriptions-for-woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'pKey' => array(
                'title' => __('P_KEY', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'epayco-subscriptions-for-woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'apiKey' => array(
                'title' => __('PUBLIC_KEY', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'epayco-subscriptions-for-woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'privateKey' => array(
                'title' => __('PRIVATE_KEY', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'password',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'epayco-subscriptions-for-woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'epayco_endorder_state' => array(
                'title' => __('Estado Final del Pedido', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'select',
                'css' => 'line-height: inherit',
                'description' => __('Seleccione el estado del pedido que se aplicaría a la hora de aceptar y confirmar el pago de la orden', 'epayco-subscriptions-for-woocommerce'),
                'options' => array(
                    'epayco-processing' => "ePayco Procesando Pago",
                    "epayco-completed" => "ePayco Pago Completado",
                    'processing' => "Procesando",
                    "completed" => "Completado"
                ),
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
    public function payment_fields(): void {}
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
            $urlReceived =  $order->get_checkout_payment_url(true);
            return [
                'result'   => 'success',
                'redirect' => $urlReceived,
            ];
        } catch (Exception $e) {
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
        $redirect_url = add_query_arg('wc-api', self::WEBHOOK_API_NAME, $redirect_url);
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
        $general = plugins_url('assets/css/general.min.css', EPS_PLUGIN_FILE);
        $card_style = plugins_url('assets/css/card-js.min.css', EPS_PLUGIN_FILE);
        $stylemin = plugins_url('assets/css/style.min.css', EPS_PLUGIN_FILE);
        $cardsjscss = plugins_url('assets/css/cardsjs.min.css', EPS_PLUGIN_FILE);
        $card_unmin = plugins_url('assets/js/card-js-unmin.js', EPS_PLUGIN_FILE);
        $indexjs = plugins_url('assets/js/index.js', EPS_PLUGIN_FILE);
        $appjs = plugins_url('assets/js/app.js', EPS_PLUGIN_FILE);
        $cardsjs = plugins_url('assets/js/cardsjs.js', EPS_PLUGIN_FILE);
        $epaycojs = plugins_url('assets/js/epayco.js', EPS_PLUGIN_FILE);
        //$epaycojs ="https://checkout.epayco.co/epayco.min.js";
        $epaycocheckout =  plugins_url('assets/js/epaycocheckout.js', EPS_PLUGIN_FILE);

        $lang = get_locale();
        $lang = explode('_', $lang);
        $lang = $lang[0];

        if (ini_get('allow_url_fopen')) {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $remote_addr = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
                $str_arr_ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $remote_addr));
            } else {
                $str_arr_ipdat = null;
            }
        } else {
            if (\function_exists('wp_remote_get')) {
                $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
                $response = \wp_remote_get("http://www.geoplugin.net/json.gp?ip=" . $remote_addr);
            } else {
                throw new Exception('WordPress environment not loaded. wp_remote_get function is unavailable.');
            }
            if (is_wp_error($response)) {
                $str_arr_ipdat = null;
            } else {
                $contents = wp_remote_retrieve_body($response);
                $str_arr_ipdat = @json_decode($contents);
            }
        }

        if (!empty($str_arr_ipdat) and $str_arr_ipdat->geoplugin_status != 404) {
            $str_countryCode = $str_arr_ipdat->geoplugin_countryCode;
        } else {
            $str_countryCode = "CO";
        }
/*
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
            'epaycocheckout',
            $this->epaycosuscription->helpers->url->getJsAsset('epaycocheckout')
        );
        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'card-js-unmin',
            $this->epaycosuscription->helpers->url->getJsAsset('card-js-unmin')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutScript(
            'index',
            $this->epaycosuscription->helpers->url->getJsAsset('index')
        );

        $this->epaycosuscription->hooks->scripts->registerCheckoutStyle(
            'wc_epaycosubscription_animate',
            $this->epaycosuscription->helpers->url->getCssAsset('animate')
        );
*/
        $this->epaycosuscription->hooks->template->getWoocommerceTemplate(
            'public/checkout/subscription.php',
            [
                'logo_comercio' => $logo_comercio,
                'amount' => $amount,
                'epayco'  => 'epayco subscription',
                'shop_name' => $this->get_option('shop_name'),
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
                'epaycojs' => $epaycojs,
                'apiKey' => $this->get_option('apiKey'),
                'privateKey' => $this->get_option('privateKey'),
                'lang' => $lang,
                'epaycocheckout' => $epaycocheckout,

            ]
        );
    }

    public function webhook(): void
    {

        global $woocommerce;
        global $wpdb;
        if (!isset($_REQUEST['_wpnonce']) || !\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_REQUEST['_wpnonce'])), 'epayco_subscription_action')) {
            if (!function_exists('wp_die') || !function_exists('__')) {
                require_once ABSPATH . 'wp-includes/pluggable.php';
            }
            // \wp_die(esc_html__('Nonce verification failed', 'epayco-subscriptions-for-woocommerce'));
        }
        $params = $_REQUEST;
        $order_id = isset($_REQUEST["order_id"]) ? sanitize_text_field(wp_unslash($_REQUEST["order_id"])) : '';
        $order = new \WC_Order($order_id);
        $table_name = $wpdb->prefix . 'epayco_plans';
        $table_name_setings = $wpdb->prefix . 'epayco_setings';
        $order_id = $params['order_id'];
        $order = new \WC_Order($order_id);
        $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
        $token = $params['epaycoToken'];
        $customerName =  $params['name'];
        $customerCard = $params['card-number2'];
        $customerData = $this->paramsBilling($subscriptions, $order, $customerCard, $customerName);
        $customerData['token_card'] = $token;
        $this->custIdCliente =  $this->get_option('custIdCliente');
        $sql_ = 'SELECT * FROM ' . $table_name_setings . ' WHERE id_payco = ' . $this->custIdCliente . ' AND email = ' . $customerData['email'];
        $cache_key = "epayco_customer_{$this->custIdCliente}_{$customerData['email']}";
        $customerGetData = wp_cache_get($cache_key, 'epayco');

        if ($customerGetData === false) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $customerGetData = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT * FROM $table_name_setings WHERE id_payco = %d AND email = %s",
                    $this->custIdCliente,
                    $customerData['email']
                ),
                ARRAY_A
            );

            if (!empty($customerGetData)) {
                wp_cache_set($cache_key, $customerGetData, 'epayco', 3600); // Cache por 1 hora
            }
        }

        if (count($customerGetData) == 0) {
            $customer = $this->customerCreate($customerData);
            if ($customer->data->status == 'error' || !$customer->status) {
                $response_status = [
                    'status' => false,
                    /* translators: %s será reemplazado con el mensaje de error del cliente */
                    'message' => sprintf(esc_html__('Error: %s', 'epayco-subscriptions-for-woocommerce'), esc_html($customer->message))

                ];
            }
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $inserCustomer = $wpdb->insert(
                $table_name_setings,
                [
                    'id_payco' => $this->custIdCliente,
                    'customer_id' => $customer->data->customerId,
                    'token_id' => $customerData['token_card'],
                    'email' => $customerData['email'],
                    'name' => $customerData['name']
                ]
            );
            if (!$inserCustomer) {
                $response_status = [
                    'status' => false,
                    'message' => __('internar error, tray again', 'epayco-subscriptions-for-woocommerce')
                ];
            }
            $customerData['customer_id'] = $customer->data->customerId;
        } else {
            $count_customers = 0;
            for ($i = 0; $i < count($customerGetData); $i++) {
                $email = $customerGetData[$i]->email??$customerGetData[0]['email'];
                if ($email == $customerData['email']) {
                    $count_customers += 1;
                }
            }
            if ($count_customers == 0) {
                $customer = $this->customerCreate($customerData);
                if ($customer->data->status == 'error') {
                    $response_status = [
                        'status' => false,

                        /* translators: %s será reemplazado con el mensaje de error del nuevo plan */
                        'message' => sprintf(__('Error: %s', 'epayco-subscriptions-for-woocommerce'), $newPLan->message)


                    ];
                }
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $inserCustomer = $wpdb->insert(
                    $table_name_setings,
                    [
                        'id_payco' => $this->custIdCliente,
                        'customer_id' => $customer->data->customerId,
                        'token_id' => $customerData['token_card'],
                        'email' => $customerData['email'],
                        'name' => $customerData['name']
                    ]
                );
                if (!$inserCustomer) {
                    $response_status = [
                        'status' => false,
                        'message' => __('internar error, tray again', 'epayco-subscriptions-for-woocommerce')
                    ];
                }
                $customerData['customer_id'] = $customer->data->customerId;
            } else {
                for ($i = 0; $i < count($customerGetData); $i++) {
                    $email = $customerGetData[$i]->email??$customerGetData[0]['email'];
                    $token_id = $customerGetData[$i]->token_id??$customerGetData[0]['token_id'];
                    $customer_id = $customerGetData[$i]->customer_id??$customerGetData[0]['customer_id'];
                    if ($email == $customerData['email'] && $token_id != $token) {
                         $this->customerAddToken($token_id, $customerData['token_card']);
                    }
                    $customerData['customer_id'] = $customer_id;
                }
            }
        }
        $confirm_url = $this->getUrlNotify($order_id);
        $plans = $this->getPlansBySubscription($subscriptions);
        $getPlans = $this->getPlans($plans);
        //$getPlansList = $this->getPlansList();

        if (!$getPlans) {
            $validatePlan_ = $this->validatePlan(true, $order_id, $plans, $subscriptions, $customerData, $confirm_url, $order, false, false, null);
        } else {
            $validatePlan_ = $this->validatePlan(false, $order_id, $plans, $subscriptions, $customerData, $confirm_url, $order, true, false, $getPlans);
        }

        if ($validatePlan_) {
            try {
                $response_status = $validatePlan_;
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }
        if (!$response_status['status']) {
            wc_add_notice($response_status['message'], 'error');
            $order = new \WC_Order($order_id);
            if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
                $redirect = array(
                    'result' => 'success',
                    'redirect' => add_query_arg('order-pay', $order->get_id(), add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
                );
            } else {
                $redirect = array(
                    'result' => 'success',
                    'redirect' => add_query_arg('order', $order->get_id(), add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
                );
            }
            wp_redirect($redirect["redirect"]);
        } else {
            WC()->cart->empty_cart();
            $arguments = array();
            $arguments['ref_payco'] = $response_status['ref_payco'];
            $redirect_url = $response_status['url'];
            $redirect_url = add_query_arg($arguments, $redirect_url);
            wp_redirect($redirect_url);
        }
    }

    public function customerCreate(array $data)
    {
        $customer = false;
        try {
            $customer = $this->epaycoSdk->customer->create(
                [
                    "token_card" => $data['token_card'],
                    "name" => $data['name'],
                    "email" => $data['email'],
                    "phone" => $data['phone'],
                    "cell_phone" => $data['phone'],
                    "country" => $data['country'],
                    "city" => $data['city'],
                    "address" => $data['address'],
                    "default" => true
                ]
            );
        } catch (Exception $exception) {
            echo esc_html('create client: ' . $exception->getMessage());
            die();
        }

        return $customer;
    }

    public function customerAddToken($customer_id, $token_card)
    {
        $customer = false;
        try {
            $customer = $this->epaycoSdk->customer->addNewToken(
                [
                    "token_card" => $token_card,
                    "customer_id" => $customer_id
                ]
            );
        } catch (Exception $exception) {
            echo esc_html('add token: ' . $exception->getMessage());
            die();
        }

        return $customer;
    }

    public function getPlans(array $plans)
    {
        foreach ($plans as $key => $plan) {
            try {
                $plan = $this->epaycoSdk->plan->get(strtolower($plans[$key]['id_plan']));
                if ($plan->status) {
                    unset($plans[$key]);
                    return $plan;
                } else {
                    return false;
                }
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }
    }

    public function getPlansList()
    {
        try {
            $plan = $this->epaycoSdk->plan->getList();
            if ($plan->status) {
                return $plan;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo esc_html($exception->getMessage());
            die();
        }
    }

    public function getPlanById($plan_id)
    {
        try {
            $plan = $this->epaycoSdk->plan->get(strtolower($plan_id));
            if ($plan->status) {
                return $plan;
            } else {
                return false;
            }
        } catch (Exception $exception) {
            echo esc_html($exception->getMessage());
            die();
        }
    }


    public function validatePlan($create, $order_id, array $plans, $subscriptions, $customer, $confirm_url, $order, $confirm = null, $update = null, $getPlans = null)
    {

        if ($create) {
            $newPLan = $this->plansCreate($plans);
            if ($newPLan->status) {
                $getPlans_ = $this->getPlans($plans);
                if ($getPlans_) {
                    $eXistPLan = $this->validatePlanData($plans, $getPlans_, $order_id, $subscriptions, $customer, $confirm_url, $order);
                } else {
                    $this->validatePlan(true, $order_id, $plans, $subscriptions, $customer, $confirm_url, $order, false, false, null);
                }
            } else {
                $response_status = [
                    'status' => false,

                    /* translators: %s será reemplazado con el mensaje de error del nuevo plan */
                    'message' => sprintf(__('Error: %s', 'epayco-subscriptions-for-woocommerce'), $newPLan->message)

                ];
                return $response_status;
            }
        } else {
            if ($confirm) {
                $eXistPLan = $this->validatePlanData($plans, $getPlans, $order_id, $subscriptions, $customer, $confirm_url, $order);
            }
        }
        return $eXistPLan;
    }

    public function validatePlanData($plans, $getPlans, $order_id, $subscriptions, $customer, $confirm_url, $order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $wc_order_product_lookup = $wpdb->prefix . "wc_order_product_lookup";
        foreach ($plans as $plan) {
            $plan_amount_cart = $plan['amount'];
            $plan_id_cart = $plan['id_plan'];
            $plan_currency_cart = $plan['currency'];
        }
        $plan_amount_epayco = $getPlans->plan->amount;
        $plan_id_epayco = $getPlans->plan->id_plan;
        $plan_currency_epayco = $getPlans->plan->currency;
        //validar que el id del plan del carrito concuerda con el plan creado
        if ($plan_id_cart == $plan_id_epayco) {
            //validar que el valor del carrito de compras concuerda con el del plan creado
            try {
                if (intval($plan_amount_cart) == $plan_amount_epayco) {
                    return $this->process_payment_epayco($plans, $customer, $confirm_url, $subscriptions, $order);
                } else {
                    return $this->validateNewPlanData($subscriptions, $order_id, true, false, $plans, $customer, $confirm_url, $order);
                }
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
                return false;
            }
        } else {
            echo 'el id del plan creado no concuerda!';
            die();
        }
    }

    public function validateNewPlanData($subscriptions, $order_id, $value, $currency, $plans, $customer, $confirm_url, $order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $wc_order_product_lookup = $wpdb->prefix . "wc_order_product_lookup";
        /*valida la actualizacion del precio del plan*/
        foreach ($subscriptions as $key => $subscription) {
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $product_id_ = $product_plan['id'];
            $porciones = explode("-", $product_id_);
            $product_id = $porciones[0];
        }
        $sql = 'SELECT * FROM ' . $wc_order_product_lookup . ' WHERE order_id =' . intval($order_id);
        $cache_key = "order_products_{$order_id}";
        $cached_results = wp_cache_get($cache_key, 'epayco');

        if ($cached_results === false) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT * FROM {$wc_order_product_lookup} WHERE order_id = %d",
                    $order_id
                ),
                ARRAY_A
            );

            wp_cache_set($cache_key, $results, 'epayco', 3600); // Caché por 1 hora
        } else {
            $results = $cached_results;
        }
        $product_id = $results[0]->product_id ? $results[0]->product_id : $product_id;
        $query = 'SELECT * FROM ' . $table_name . ' WHERE order_id =' . intval($order_id);
        $cache_key = "order_data_{$order_id}";
        $cached_order_data = wp_cache_get($cache_key, 'epayco');

        if ($cached_order_data === false) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $orderData = $wpdb->get_row(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT * FROM {$table_name} WHERE order_id = %d",
                    $order_id
                ),
                ARRAY_A
            );

            wp_cache_set($cache_key, $orderData, 'epayco', 3600); // Caché por 1 hora
        } else {
            $orderData = $cached_order_data;
        }

        if (count($orderData) == 0) {
            if ($value) {
                $savePlanId_ = $this->savePlanId($order_id, $plans, $subscriptions, null, $product_id);
                if ($savePlanId_) {
                    $cache_key = "order_data_{$order_id}";
                    $cached_order_data = wp_cache_get($cache_key, 'epayco');

                    if ($cached_order_data === false) {
                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                        $orderData = $wpdb->get_row(
                            $wpdb->prepare(
                                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                                "SELECT * FROM {$table_name} WHERE order_id = %d",
                                $order_id
                            ),
                            ARRAY_A
                        );

                        wp_cache_set($cache_key, $orderData, 'epayco', 3600); // Caché por 1 hora
                    } else {
                        $orderData = $cached_order_data;
                    }

                    if (count($orderData) == 0) {
                        return false;
                    } else {
                        foreach ($plans as $plan) {
                            $plan_currency_cart = $plan['currency'];
                            $plan_interval_cart = $plan['interval'];
                            $plan_interval_count_cart = $plan['interval_count'];
                            $plan_trial_days_cart = $plan['trial_days'];
                            $plan_name = $plan['name'];
                            $plan_description = $plan['description'];
                        }

                        $newPlanToCreated[0] = [
                            "id_plan" => (string)$orderData[0]->plan_id,
                            "name" => $plan_name,
                            "description" => $plan_description,
                            "currency" => $plan_currency_cart,
                            "trial_days" => intval($plan_trial_days_cart),
                            "amount" => $orderData[0]->amount,
                            "interval" => $plan_interval_cart,
                            "interval_count" => $plan_interval_count_cart,
                        ];

                        //crear nuevo plan con precio actualizado
                        $newPLan = $this->plansCreate($newPlanToCreated);
                        if ($newPLan->status) {
                            $getPlans_ = $this->getPlans($newPlanToCreated);
                            if ($getPlans_) {
                                return $this->process_payment_epayco($newPlanToCreated, $customer, $confirm_url, $subscriptions, $order);
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {

            $plan_id_s = $orderData[0]->plan_id;
            $getPlanById_ = $this->getPlanById($plan_id_s);
            if ($getPlanById_->status) {
                $newPlanToCreated_[0] = [
                    "id_plan" => $getPlanById_->plan->id_plan,
                    "name" => $getPlanById_->plan->name,
                    "description" => $getPlanById_->plan->description,
                    "amount" => $getPlanById_->plan->amount,
                    "currency" => $getPlanById_->plan->currency,
                    "interval_count" => $getPlanById_->plan->interval_count,
                    "interval" => $getPlanById_->plan->interval,
                    "trial_days" => $getPlanById_->plan->id_plan,
                ];

                return $this->process_payment_epayco($newPlanToCreated_, $customer, $confirm_url, $subscriptions, $order);
            }
        }
    }


    public function plansCreate(array $plans)
    {

        foreach ($plans as $plan) {
            try {
                $plan_ = $this->epaycoSdk->plan->create(
                    [
                        "id_plan" => (string)strtolower($plan['id_plan']),
                        "name" => (string)$plan['name'],
                        "description" => (string)$plan['description'],
                        "amount" => $plan['amount'],
                        "currency" => $plan['currency'],
                        "interval" => $plan['interval'],
                        "interval_count" => $plan['interval_count'],
                        "trial_days" => $plan['trial_days']
                    ]
                );



                return $plan_;
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }
    }

    public function subscriptionCreate(array $plans, array $customer, $confirm_url)

    {
        foreach ($plans as $plan) {

            try {
                $suscriptioncreted = $this->epaycoSdk->subscriptions->create(
                    [
                        "id_plan" => $plan['id_plan'],
                        "customer" => $customer['customer_id'],
                        "token_card" => $customer['token_card'],
                        "doc_type" => $customer['type_document'],
                        "doc_number" => $customer['doc_number'],
                        "url_confirmation" => $confirm_url,
                        "method_confirmation" => "POST"
                    ]
                );

                return $suscriptioncreted;
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }
    }

    public function subscriptionCharge(array $plans, array $customer, $confirm_url)
    {
        $subs = [];

        foreach ($plans as $plan) {
            try {
                $subs[] = $this->epaycoSdk->subscriptions->charge(
                    [
                        "id_plan" => $plan['id_plan'],
                        "customer" => $customer['customer_id'],
                        "token_card" => $customer['token_card'],
                        "doc_type" => $customer['type_document'],
                        "doc_number" => $customer['doc_number'],
                        "ip" => $this->getIP(),
                        "url_confirmation" => $confirm_url,
                        "method_confirmation" => "POST"
                    ]
                );
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }

        return $subs;
    }

    public function cancelSubscription($subscription_id)
    {
        try {
            $this->epaycoSdk->subscriptions->cancel($subscription_id);
        } catch (Exception $exception) {
            echo esc_html($exception->getMessage());
            die();
        }
    }

    private function getWooCommerceSubscriptionFromOrderId($orderId)
    {
        $subscriptions = wcs_get_subscriptions_for_order($orderId);

        return $subscriptions;
    }


    public function paramsBilling($subscriptions, $order, $customerCard, $customerName)
    {
        $data = [];
        $subscription = end($subscriptions);
        if ($subscription) {


            // Recuperar campos adicionales en el archivo EpaycoSuscription.php
            $doc_number = get_post_meta($subscription->get_id(), '_epayco_billing_dni', true) != null ? get_post_meta($subscription->get_id(), '_epayco_billing_dni', true) : $order->get_meta('_epayco_billing_dni');
            $type_document = get_post_meta($subscription->get_id(), '_epayco_billing_type_document', true) != null ? get_post_meta($subscription->get_id(), '_epayco_billing_type_document', true) : $order->get_meta('_epayco_billing_type_document');



            $data['name'] = $customerName;
            $data['email'] = $subscription->get_billing_email();
            $data['phone'] = $subscription->get_billing_phone();
            $data['country'] = $subscription->get_shipping_country() ? $subscription->get_shipping_country() : $subscription->get_billing_country();
            $data['city'] = $subscription->get_shipping_city() ? $subscription->get_shipping_city() : $subscription->get_billing_city();
            $data['address'] = $subscription->get_shipping_address_1() ? $subscription->get_shipping_address_1() . " " . $subscription->get_shipping_address_2() : $subscription->get_billing_address_1() . " " . $subscription->get_billing_address_2();
            $data['doc_number'] = $doc_number;
            $data['type_document'] = $type_document;

            return $data;
        } else {
            $redirect = array(
                'result' => 'success',
                'redirect' => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
            wc_add_notice('EL producto que intenta pagar no es permitido', 'error');
            wp_redirect($redirect["redirect"]);
            die();
        }
    }

    public function getPlansBySubscription(array $subscriptions)
    {
        $plans = [];
        foreach ($subscriptions as $key => $subscription) {
            $total_discount = $subscription->get_total_discount();
            $order_currency = $subscription->get_currency();
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $quantity = $product_plan['quantity'];
            $product_name = $product_plan['name'];
            $product_id = $product_plan['id'];
            $trial_days = $this->getTrialDays($subscription);
            $plan_code = "$product_name-$product_id";
            $plan_code = $trial_days > 0 ? "$product_name-$product_id-$trial_days" : $plan_code;
            $plan_code = get_option('woocommerce_currency') !== $order_currency ? "$plan_code-$order_currency" : $plan_code;
            $plan_code = $quantity > 1 ? "$plan_code-$quantity" : $plan_code;
            $plan_code = $total_discount > 0 ? "$plan_code-$total_discount" : $plan_code;
            $plan_code = rtrim($plan_code, "-");
            $plan_id = str_replace(array("-", "--"), array("_", ""), $plan_code);
            $plan_name = trim(str_replace("-", " ", $product_name));
            $plans[] = array_merge(
                [
                    //"id_plan" => strtolower(str_replace("__", "_", $plan_id)),
                    "id_plan" =>  "sede_bosa_la_esperanza_plan_dion_plus_0474_30",
                    "name" => "Plan $plan_name",
                    "description" => "Plan $plan_name",
                    "currency" => $order_currency,
                ],
                [
                    "trial_days" => $trial_days
                ],
                $this->intervalAmount($subscription)
            );
        }
        return $plans;
    }

    public function updatePlansBySubscription(array $subscriptions)
    {
        $ran = wp_rand(1, 999);
        $plans = [];
        foreach ($subscriptions as $key => $subscription) {
            $total_discount = $subscription->get_total_discount();
            $order_currency = $subscription->get_currency();
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $quantity = $product_plan['quantity'];
            $product_name = $product_plan['name'];
            $product_id = $product_plan['id'];
            $trial_days = $this->getTrialDays($subscription);
            $plan_code = "$product_name-$product_id";
            $plan_code = $trial_days > 0 ? "$product_name-$product_id-$trial_days" : $plan_code;
            $plan_code = get_option('woocommerce_currency') !== $order_currency ? "$plan_code-$order_currency" : $plan_code;
            $plan_code = $quantity > 1 ? "$plan_code-$quantity" : $plan_code;
            $plan_code = $total_discount > 0 ? "$plan_code-$total_discount" : $plan_code;
            $plan_code = rtrim($plan_code, "-");
            $plans[] = array_merge(
                [
                    "id_plan" => $plan_code . '-' . $ran,
                    "name" => "Plan $plan_code-$ran",
                    "description" => "Plan $plan_code-$ran",
                    "currency" => $order_currency,
                ],
                [
                    "trial_days" => $trial_days
                ],
                $this->intervalAmount($subscription)
            );
        }
        return $plans;
    }

    public function getPlan($products)
    {
        $product_plan = [];

        $product_plan['name'] = '';
        $product_plan['id'] = 0;
        $product_plan['quantity'] = 0;

        foreach ($products as $product) {
            $product_plan['name'] .= "{$product['name']}-";
            $product_plan['id'] .= "{$product['product_id']}-";
            $product_plan['quantity'] .= $product['quantity'];
        }

        $product_plan['name'] = $this->cleanCharacters($product_plan['name']);

        return $product_plan;
    }

    public function intervalAmount(\WC_Subscription $subscription)
    {
        return [
            "interval" => $subscription->get_billing_period(),
            "amount" => $subscription->get_total(),
            "interval_count" => $subscription->get_billing_interval()
        ];
    }

    public function getTrialDays(\WC_Subscription $subscription)
    {
        $trial_days = "0";
        $trial_start = $subscription->get_date('start');
        $trial_end = $subscription->get_date('trial_end');

        if ($trial_end > 0)
            $trial_days = (string)(strtotime($trial_end) - strtotime($trial_start)) / (60 * 60 * 24);

        return $trial_days;
    }

    public function cleanCharacters($string)
    {
        $string = str_replace(' ', '-', $string);
        $patern = '/[^A-Za-z0-9\-]/';
        return preg_replace($patern, '', $string);
    }

    public function getUrlNotify($order_id)
    {
        $confirm_url = get_site_url() . "/";
        $confirm_url = add_query_arg('wc-api', self::WEBHOOK_API_NAME, $confirm_url);
        $confirm_url = add_query_arg('order_id', $order_id, $confirm_url);
        $confirm_url = $confirm_url . '&confirmation=1';
        return $confirm_url;
    }

    public function handleStatusSubscriptions(array $subscriptionsStatus, array $subscriptions, array $customer, $order, $customerId, $suscriptionId, $planId)
    {

        global $wpdb;
        $table_subscription_epayco = $wpdb->prefix . 'epayco_subscription';

        $count = 0;
        $messageStatus = [];
        $messageStatus['status'] = true;
        $messageStatus['message'] = [];
        $messageStatus['ref_payco'] = [];
        $quantitySubscriptions = count($subscriptionsStatus);
        $current_state = $order->get_status();
        foreach ($subscriptions as $subscription) {

            $sub = $subscriptionsStatus[$count];
            $data = count(get_object_vars($sub->data));
            if ($data < 10) {
                $isTestTransaction = (bool)$this->get_option('environment') == true ? "yes" : "no";
                update_option('epayco_order_status', $isTestTransaction);
                $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";

                if ($isTestMode == "true") {
                    $message = 'Pago pendiente de aprobación Prueba';
                    $orderStatus = "epayco_on_hold";
                    if (
                        $current_state != "epayco_on_hold" ||
                        $current_state == "pending"
                    ) {
                        $this->restore_order_stock($order->id, '+');
                    }
                } else {
                    $message = 'Pago pendiente de aprobación';
                    $orderStatus = "epayco-on-hold";
                    if (
                        $current_state != "epayco-on-hold" ||
                        $current_state == "pending"
                    ) {
                        $this->restore_order_stock($order->id, '+');
                    }
                }
                $order->update_status($orderStatus);
                $order->add_order_note($message);
                $subscription->update_status('on-hold');
            } else {

                $isTestTransaction = $sub->data->enpruebas == 1 ? "yes" : "no";
                update_option('epayco_order_status', $isTestTransaction);
                $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
                if (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 2 || $sub->data->cod_respuesta === 4) {
                    $messageStatus['message'] = array_merge($messageStatus['message'], ["estado: {$sub->data->respuesta}"]);
                    if ($isTestMode == "true") {
                        $message = 'Pago rechazado Prueba: ' . $sub->data->ref_payco;
                        if (
                            $current_state == "epayco_failed" ||
                            $current_state == "epayco_cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco_processing" ||
                            $current_state == "epayco_completed" ||
                            $current_state == "processing_test" ||
                            $current_state == "completed_test"
                        ) {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('cancelled');
                        } else {
                            $messageClass = 'woocommerce-error';
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('cancelled');
                        }
                    } else {
                        if (
                            $current_state == "epayco-failed" ||
                            $current_state == "epayco-cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco-processing" ||
                            $current_state == "epayco-completed" ||
                            $current_state == "processing" ||
                            $current_state == "completed"
                        ) {
                            $subscription->payment_failed();
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note('Pago fallido');
                        } else {
                            $message = 'Pago rechazado' . $sub->data->ref_payco;
                            $messageClass = 'woocommerce-error';
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note('Pago fallido');
                            $subscription->payment_failed();
                        }
                    }
                }

                if (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 1) {
                    if ($isTestMode == "true") {
                        $message = 'Pago exitoso Prueba';
                        switch ($this->get_option('epayco_endorder_state')) {
                            case 'epayco-processing': {
                                    $orderStatus = 'epayco_processing';
                                }
                                break;
                            case 'epayco-completed': {
                                    $orderStatus = 'epayco_completed';
                                }
                                break;
                            case 'processing': {
                                    $orderStatus = 'processing_test';
                                }
                                break;
                            case 'completed': {
                                    $orderStatus = 'completed_test';
                                }
                                break;
                        }
                    } else {
                        $message = 'Pago exitoso';
                        $orderStatus = $this->get_option('epayco_endorder_state');
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);

                    $note = sprintf(

                        /* translators: %1$s será reemplazado con el ID de la suscripción y %2$s con la referencia de pago */
                        esc_html__('Successful subscription (subscription ID: %1$s), reference (%2$s)', 'epayco-subscriptions-for-woocommerce'),
                        esc_html($sub->subscription->_id),
                        esc_html($sub->data->ref_payco)
                    );
                    $subscription->add_order_note($note);
                    $messageStatus['ref_payco'] = array_merge($messageStatus['ref_payco'], [$sub->data->ref_payco]);
                    $subscription->payment_complete();
                    $this->restore_order_stock($order->get_id(), "+");
                } elseif (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 3) {
                    if ($isTestMode == "true") {
                        $message = 'Pago pendiente de aprobación Prueba';
                        $orderStatus = "epayco_on_hold";
                        if ($current_state != "epayco_on_hold") {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago pendiente de aprobación';
                        $orderStatus = "epayco-on-hold";
                        if ($current_state != "epayco_on_hold") {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');

                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    $wpdb->insert(
                        $table_subscription_epayco,
                        [
                            'order_id' => $subscription->get_id(),
                            'ref_payco' => $sub->data->ref_payco
                        ]
                    );
                }
            }
            $messageStatus['ref_payco'] = array_merge($messageStatus['ref_payco'], [$sub->data->ref_payco]);
            $count++;

            if ($count === $quantitySubscriptions && count($messageStatus['message']) >= $count)
                $messageStatus['status'] = false;


            update_post_meta($subscription->get_id(), 'subscription_id', $suscriptionId);
            update_post_meta($subscription->get_id(), 'id_client', $customerId);
            update_post_meta($subscription->get_id(), 'plan_id', $planId);
            update_post_meta($order->get_id(), 'subscription_id', $suscriptionId);
            update_post_meta($order->get_id(), 'id_client', $customerId);
            update_post_meta($order->get_id(), 'plan_id', $planId);
        }
        return $messageStatus;
    }

    public function savePlanId($order_id, array $plans, array $subscriptions, $update = null, $product_id = null)
    {
        $ran = wp_rand(1, 9999);

        global $wpdb;
        $table_subscription_epayco = $wpdb->prefix . 'epayco_plans';

        if ($update) {

            foreach ($plans as $plan) {
                try {
                    $plan_id_ = strtolower((string)$plan['id_plan']);
                    $plan_amount = floatval($plan['amount']);
                    $plan_currency = (string)$plan['currency'];
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                    $result = $wpdb->update(
                        $table_subscription_epayco,
                        [
                            'order_id' => intval($order_id),
                            'plan_id' => $plan_id_,
                            'amount' => $plan_amount,
                            'product_id' => $product_id,
                            'currency' => $plan_currency,
                        ],
                        [
                            'order_id' => intval($order_id),
                            'product_id' => $product_id,
                        ]
                    );
                } catch (Exception $exception) {
                    echo esc_html($exception->getMessage());
                    die();
                }
            }
        } else {

            try {
                foreach ($plans as $plan) {
                    $plan_id_ = (string)$plan['id_plan'] . "_" . $ran;
                    $plan_amount = floatval($plan['amount']);
                    $plan_currency = (string)$plan['currency'];
                }

                $dataToSave = [
                    'order_id' => intval($order_id),
                    'plan_id' => strtolower($plan_id_),
                    'amount' => $plan_amount,
                    'product_id' => $product_id,
                    'currency' => $plan_currency,
                ];

                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching1
                $result = $wpdb->insert(
                    $table_subscription_epayco,
                    $dataToSave
                );
                $result = 1;
            } catch (Exception $exception) {
                echo esc_html($exception->getMessage());
                die();
            }
        }
        return $result;
    }

    public function process_payment_epayco(array $plans, array $customerData, $confirm_url, $subscriptions, $order)
    {
        $subsCreated = $this->subscriptionCreate($plans, $customerData, $confirm_url);
        if ($subsCreated->status) {
            $subs = $this->subscriptionCharge($plans, $customerData, $confirm_url);
            foreach ($subs as $sub) {
                $customerId = isset($subsCreated->customer->_id) ? $subsCreated->customer->_id : null;
                $suscriptionId = isset($subsCreated->id) ? $subsCreated->id : null;
                $planId = isset($subsCreated->data->idClient) ? $subsCreated->data->idClient : null;
                $validation = !is_null($sub->status) ? $sub->status : $sub->success;
                if ($validation) {
                    $messageStatus = $this->handleStatusSubscriptions($subs, $subscriptions, $customerData, $order, $customerId, $suscriptionId, $planId);

                    $response_status = [
                        'ref_payco' => $messageStatus['ref_payco'][0],
                        'status' => $messageStatus['status'],
                        'message' => $messageStatus['message'],
                        'url' => $order->get_checkout_order_received_url()
                    ];
                } else {

                    $errorMessage = $sub->data->errors;
                    $response_status = [
                        'ref_payco' => null,
                        'status' => false,
                        'message' => $errorMessage,
                        'url' => $order->get_checkout_order_received_url()
                    ];
                }
            }
        } else {
            $errorMessage = $subsCreated->data->description;
            $response_status = [
                'ref_payco' => null,
                'status' => false,
                'message' => $errorMessage,
                'url' => $order->get_checkout_order_received_url()
            ];
        }
        return $response_status;
    }

    public function getIP()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = '127.0.0.1';

        return $ipaddress;
    }

    public function authSignature($x_ref_payco, $x_transaction_id, $x_amount, $x_currency_code)
    {
        $signature = hash(
            'sha256',
            trim($this->get_option('custIdCliente')) . '^'
                . trim($this->get_option('pKey')) . '^'
                . $x_ref_payco . '^'
                . $x_transaction_id . '^'
                . $x_amount . '^'
                . $x_currency_code
        );
        return $signature;
    }

    /**
     * @param $order_id
     */
    public function restore_order_stock($order_id, $operation = 'increase')
    {
        $order = wc_get_order($order_id);
        if (!get_option('woocommerce_manage_stock') == 'yes' && !sizeof($order->get_items()) > 0) {
            return;
        }

        foreach ($order->get_items() as $item) {
            // Get an instance of corresponding the WC_Product object
            $product = $item->get_product();
            $qty = $item->get_quantity(); // Get the item quantity
            wc_update_product_stock($product, $qty, $operation);
        }
    }

    public function cancelledPayment($order_id, $id_client, $subscription_id, $planId)
    {
        $order = new \WC_Order($order_id);
        $current_state = $order->get_status();
        $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
        $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
        foreach ($subscriptions as $subscription) {
            if ($isTestMode == "true") {
                $message = 'Pago rechazado Prueba';
                if (
                    $current_state == "epayco_failed" ||
                    $current_state == "epayco_cancelled" ||
                    $current_state == "failed" ||
                    $current_state == "epayco_processing" ||
                    $current_state == "epayco_completed" ||
                    $current_state == "processing_test" ||
                    $current_state == "completed_test"
                ) {
                    $order->update_status('epayco_cancelled');
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                } else {
                    $messageClass = 'woocommerce-error';
                    $order->update_status('epayco_cancelled');
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                }
            } else {
                if (
                    $current_state == "epayco-failed" ||
                    $current_state == "epayco-cancelled" ||
                    $current_state == "failed" ||
                    $current_state == "epayco-processing" ||
                    $current_state == "epayco-completed" ||
                    $current_state == "processing" ||
                    $current_state == "completed"
                ) {
                    $subscription->payment_failed();
                    $order->update_status('epayco-cancelled');
                    $order->add_order_note('Pago fallido');
                } else {
                    $message = 'Pago rechazado';
                    $messageClass = 'woocommerce-error';
                    $order->update_status('epayco-cancelled');
                    $order->add_order_note('Pago fallido');
                    $subscription->payment_failed();
                }
            }
            update_post_meta($subscription->get_id(), 'subscription_id', $subscription_id);
            update_post_meta($subscription->get_id(), 'id_client', $id_client);
            update_post_meta($subscription->get_id(), 'plan_id', $planId);
            update_post_meta($order->get_id(), 'subscription_id', $subscription_id);
            update_post_meta($order->get_id(), 'id_client', $id_client);
            update_post_meta($order->get_id(), 'plan_id', $planId);
            $response_status = [
                'ref_payco' => null,
                'status' => true,
                'message' => null,
                'url' => $order->get_checkout_order_received_url()
            ];

            return $response_status;
        }
    }

    public function subscription_epayco_confirm(array $params)
    {

        $order_id = trim(sanitize_text_field($params['order_id']));
        $order = new \WC_Order($order_id);
        $current_state = $order->get_status();
        if (isset($params['x_signature'])) {

            if (!isset($_REQUEST['_wpnonce']) || !\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_REQUEST['_wpnonce'])), 'epayco_subscription_action')) {
                wp_die(esc_html__('Nonce verification failed', 'epayco-subscriptions-for-woocommerce'));
            }

            $x_ref_payco = isset($_REQUEST['x_ref_payco']) ? trim(\sanitize_text_field(\wp_unslash($_REQUEST['x_ref_payco']))) : '';
            $x_transaction_id = isset($_REQUEST['x_transaction_id']) ? trim(\sanitize_text_field(\wp_unslash($_REQUEST['x_transaction_id']))) : '';
            if (isset($_REQUEST['x_amount'])) {
                $x_amount = trim(sanitize_text_field(wp_unslash($_REQUEST['x_amount'])));
            } else {
                $x_amount = '';
            }
            $x_currency_code = isset($_REQUEST['x_currency_code']) ? trim(sanitize_text_field(wp_unslash($_REQUEST['x_currency_code']))) : '';
            $x_signature = isset($_REQUEST['x_signature']) ? trim(sanitize_text_field(wp_unslash($_REQUEST['x_signature']))) : '';
            $x_cod_transaction_state = isset($_REQUEST['x_cod_transaction_state']) ? (int)trim(sanitize_text_field(wp_unslash($_REQUEST['x_cod_transaction_state']))) : 0;
            if ($order_id != "" && $x_ref_payco != "") {
                $authSignature = $this->authSignature($x_ref_payco, $x_transaction_id, $x_amount, $x_currency_code);
            }
        }

        $current_state = $order->get_status();
        if ($authSignature == $x_signature) {
            $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
            $x_test_request = isset($_REQUEST['x_test_request']) ? trim(sanitize_text_field(wp_unslash($_REQUEST['x_test_request']))) : '';
            $isTestTransaction = $x_test_request == "TRUE" ? "yes" : "no";
            update_option('epayco_order_status', $isTestTransaction);
            $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
            foreach ($subscriptions as $subscription) {
                if ($x_cod_transaction_state == 1) {
                    if ($isTestMode == "true") {
                        $message = 'Pago exitoso Prueba';
                        switch ($this->get_option('epayco_endorder_state')) {
                            case 'epayco-processing': {
                                    $orderStatus = 'epayco_processing';
                                }
                                break;
                            case 'epayco-completed': {
                                    $orderStatus = 'epayco_completed';
                                }
                                break;
                            case 'processing': {
                                    $orderStatus = 'processing_test';
                                }
                                break;
                            case 'completed': {
                                    $orderStatus = 'completed_test';
                                }
                                break;
                        }

                        if (!($current_state == "epayco_on_hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago exitoso';
                        $orderStatus = $this->get_option('epayco_endorder_state');
                        if (!($current_state == "epayco-on-hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $subscription->payment_complete();
                    $order->update_status($orderStatus);
                    $order->add_order_note($message);

                    $note = sprintf(
                        /* translators: %1$s será reemplazado con el ID de la suscripción y %2$s con la referencia de pago */
                        esc_html__('Successful subscription (subscription ID: %1$s), reference (%2$s)', 'epayco-subscriptions-for-woocommerce'),
                        esc_html($subscription->get_data()['id']),
                        esc_html($x_ref_payco)
                    );

                    $subscription->add_order_note($note);

                    echo "1";
                }

                if (
                    $x_cod_transaction_state == 2 ||
                    $x_cod_transaction_state == 4 ||
                    $x_cod_transaction_state == 6 ||
                    $x_cod_transaction_state == 9 ||
                    $x_cod_transaction_state == 10 ||
                    $x_cod_transaction_state == 11
                ) {
                    if ($isTestMode == "true") {
                        $message = 'Pago rechazado Prueba: ' . $x_ref_payco;
                        if (
                            $current_state == "epayco_failed" ||
                            $current_state == "epayco_cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco_processing" ||
                            $current_state == "epayco_completed" ||
                            $current_state == "processing_test" ||
                            $current_state == "completed_test"
                        ) {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                        } else {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                            if (
                                $current_state = "epayco-on-hold" ||
                                $current_state = "epayco-on-hold"
                            ) {
                                $this->restore_order_stock($order->get_id());
                            }
                        }
                    } else {
                        $message = 'Pago rechazado: ' . $x_ref_payco;
                        if (
                            $current_state == "epayco-failed" ||
                            $current_state == "epayco-cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco-processing" ||
                            $current_state == "epayco-completed" ||
                            $current_state == "processing" ||
                            $current_state == "completed"
                        ) {
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                        } else {
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                            if (
                                $current_state = "epayco-on-hold" ||
                                $current_state = "epayco-on-hold"
                            ) {
                                $this->restore_order_stock($order->get_id());
                            }
                        }
                    }
                    echo esc_html($x_cod_transaction_state);
                }

                if ($x_cod_transaction_state == 3) {
                    if ($isTestMode == "true") {
                        $message = 'Pago pendiente de aprobación Prueba';
                        $orderStatus = "epayco_on_hold";
                        if (!($current_state == "epayco_on_hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago pendiente de aprobación';
                        $orderStatus = "epayco-on-hold";
                        if (!($current_state == "epayco-on-hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                    echo "3";
                    die();
                }
            }
        } else {
            $message = 'Firma no valida';
            echo esc_html($message);
        }
    }

    /**
     * Render order form
     *
     * @param $order_id
     * @throws Exception
     */
    public function renderOrderForm($order_id): void {}

    public function string_sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", wp_strip_all_tags($string)));
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
        return $clean;
    }
}
