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
    public const WEBHOOK_API_NAME_VALIDATION = 'WC_WooEpaycoSuscription_Validation';


    /**
     * @const
     */
    public const LOG_SOURCE = 'EpaycoSuscription_Gateway';

    public $cron_data;

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
        $this->epaycosuscription->hooks->endpoints->registerApiEndpoint(self::WEBHOOK_API_NAME_VALIDATION, [$this, 'validate_ePayco_request']);
        $this->epaycosuscription->hooks->gateway->getAdminCredentiaslFields($this, 'ePayco_credentials_validation');
    
        $lang = get_locale();
        $lang = explode('_', $lang);
        $lang = $lang[0];
        $this->cron_data = $this->get_option('cron_data');

        add_action('woocommerce_subscription_status_cancelled', [$this, 'on_wc_subscription_cancelled']);

        add_action('woocommerce_epayco_suscripcion_cleanup_draft_orders', [$this, 'delete_epayco_expired_draft_orders']);
        add_action('woocommerc_epayco_suscripcion_cron_hook', [$this, 'woocommerc_epayco_suscripcion_cron_job_funcion']);
        add_action('admin_init', [$this, 'install']);
        $this->epaycoSdk = new EpaycoSdk\Epayco(
            [
                "apiKey" => $this->get_option('apiKey'),
                "privateKey" => $this->get_option('privateKey'),
                "lenguage" => strtoupper($lang),
                "test" => (bool)$this->get_option('environment')
            ]
        );
    }

    public function install()
    {
        $this->maybe_create_cronjobs();
    }
    protected function maybe_create_cronjobs()
    {
        $cron_data = $this->cron_data == "yes" ? true : false;
        if ($cron_data) {
            if (function_exists('as_next_scheduled_action') && false === as_next_scheduled_action('woocommerce_epayco_suscripcion_cleanup_draft_orders')) {
                as_schedule_recurring_action(time() + 3600, 3600, 'woocommerce_epayco_suscripcion_cleanup_draft_orders');
            }
        }
    }


    public function woocommerc_epayco_suscripcion_cron_job_funcion()
    {

        if (isset($this->cron_data)) {
            $cron_data = $this->cron_data == "yes" ? true : false;
            if ($cron_data) {
                $this->updateStatusSubscription();
            }
        }
    }

    public function delete_epayco_expired_draft_orders()
    {

        $this->updateStatusSubscription();
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
                'label' => __('Habilitar ePayco suscripción', 'epayco-subscriptions-for-woocommerce'),
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
            /*'shop_icon' => array(
                'title' => __('Icono del comercio', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'text',
                'description' => __('Corresponde al icono de la tienda que los usuarios visualizan en el checkout', 'epayco-subscriptions-for-woocommerce'),
                'default' => __('', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
            ),*/
            'description' => array(
                'title' => __('Descripción', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'textarea',
                'description' => __('Corresponde al descripción de la tienda que los usuarios visualizan en el checkout', 'epayco-subscriptions-for-woocommerce'),
                'default' => __('Subscription ePayco', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
            ),
            'environment' => array(
                'title' => __('Modo de pruebas', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'select',
                'class' => 'wc-enhanced-select',
                'description' => __('mode prueba/producción', 'epayco-subscriptions-for-woocommerce'),
                'desc_tip' => true,
                'default' => true,
                'options' => array(
                    false => __('Produción', 'epayco-subscriptions-for-woocommerce'),
                    true => __('Pruebas', 'epayco-subscriptions-for-woocommerce'),
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
                'type' => 'text',
                'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'epayco-subscriptions-for-woocommerce'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => ''
            ),
            'epayco_endorder_state' => array(
                'title' => __('Estado final del pedido', 'epayco-subscriptions-for-woocommerce'),
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
            'cron_data'     => array(
                'title'       => __('Rastreo de orden ', 'epayco-subscriptions-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Habilitar el rastreo de orden ', 'epayco-subscriptions-for-woocommerce'),
                'description' => __('Mantendremos tus suscripciones actualizadas cada hora. Recomendamos activar esta opción solo en caso de fallos en la actualización automática del estado de las suscripciones.', 'epayco-subscriptions-for-woocommerce'),
                'default'     => 'no',
            ),
        );
    }


    /**
     * Output the gateway settings screen.
     */

    public function admin_options()
    {
        $validation_url = get_site_url() . "/";
        $validation_url = add_query_arg('wc-api', self::WEBHOOK_API_NAME_VALIDATION, $validation_url);
        //$validation_url = add_query_arg('wc-api', get_class($this) . "Validation", $validation_url);
        ?>
        <div id="path_validate" hidden>
            <?php esc_html_e($validation_url, 'text_domain'); ?>
        </div>
        <img src="<?php echo EPAYCO_PLUGIN_SUSCRIPCIONES_URL . '/assets/images/iconoepayco2025.png' ?>">
        <div style="color: #31708f; background-color: #d9edf7; border-color: #bce8f1; padding: 10px; border-radius: 5px;">
            <h2><?php esc_html_e('ePayco Suscripciones', 'epayco-subscriptions-for-woocommerce'); ?></h2>
            Con este módulo, podrás aceptar pagos de suscripciones de forma segura a través de la plataforma ePayco.
            <br>Cuando un cliente selecciona ePayco como método de pago, el estado del pedido cambiará a <strong>“ePayco Esperando Pago”</strong>.
            <br>Una vez que el pago sea aceptado o rechazado, ePayco notificará automáticamente a tu tienda y el estado del pedido se <br>
            actualizará en consecuencia.
            <br><br>
        </div>
        <table class="form-table">
            <tbody>
                <?php
                $this->generate_settings_html();
                ?>
                <!--<tr valign="top" >
                <th scope="row" class="titledesc">
                    <label for="woocommerce_epayco_enabled"><?php esc_html_e('Validar llaves', 'epayco-subscriptions-for-woocommerce'); ?></label>
                    <span hidden id="public_key">0</span>
                    <span hidden id="private_key">0</span>
                <td class="forminp">
                    <form method="post" action="#">
                        <label for="woocommerce_epayco_enabled">
                        </label>
                        <input type="button" class="button-primary woocommerce-save-button validar" value="Validar">
                        <p class="description">
                            <?php esc_html_e('Validación de llaves PUBLIC_KEY y PRIVATE_KEY', 'epayco-subscriptions-for-woocommerce'); ?>
                        </p>
                    </form>
                    <br>
                    <div id="myModal" class="modal" >
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <center>
                                <img src="<?php echo EPAYCO_PLUGIN_SUSCRIPCIONES_URL . 'assets/images/logo_warning.png' ?>">
                            </center>
                            <p><strong><?php esc_html_e('Llaves de comercio inválidas', 'epayco-subscriptions-for-woocommerce'); ?></strong> </p>
                            <p><?php esc_html_e('Las llaves Public Key, Private Key insertadas', 'epayco-subscriptions-for-woocommerce'); ?><br><?php esc_html_e('del comercio son inválidas.', 'epayco-subscriptions-for-woocommerce'); ?><br><?php esc_html_e('Consúltelas en el apartado de integraciones', 'epayco-subscriptions-for-woocommerce'); ?> <br><?php esc_html_e('Llaves API en su Dashboard ePayco.', 'epayco-subscriptions-for-woocommerce'); ?>,</p>
                        </div>
                        <span class="loader"></span>
                    </div>

                </td>
                </th>
            </tr>-->
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
     * @param $validationData
     */
    public function ePayco_credentials_validation($validationData)
    {
        $username = sanitize_text_field($validationData['epayco_publickey']);
        $password = sanitize_text_field($validationData['epayco_privatey']);
        $response = wp_remote_post('https://apify.epayco.co/login', array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            ),
        ));


        $data = json_decode(wp_remote_retrieve_body($response));
        if ($data->token) {
            $response = wp_remote_get("https://secure.payco.co/restpagos/validarllaves?public_key=".trim($username));

            if ( is_wp_error( $response ) ) {
                error_log( 'ePayco validation: ' . $response->get_error_message() );
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("checkout_error".$response->get_error_message());
                }
                return wp_send_json("{success:false}");
            }

            $body = wp_remote_retrieve_body( $response );
            return wp_send_json($body);
        }else{
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("checkout_error".json_encode($data));
            }
            return wp_send_json("{success:false}");
        }
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
        $order_data = $order->get_data();
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
        //$logo_comercio=$this->get_option('shop_icon');
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
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
        
        if (!isset($_REQUEST['_wpnonce']) || !\wp_verify_nonce(\sanitize_text_field(\wp_unslash($_REQUEST['_wpnonce'])), 'epayco_subscription_action')) {
            if (!function_exists('wp_die') || !function_exists('__')) {
                require_once ABSPATH . 'wp-includes/pluggable.php';
            }
            // \wp_die(esc_html__('Nonce verification failed', 'epayco-subscriptions-for-woocommerce'));
        }
        //obtiene los datos de la orden y la suscripcion desde los parametros recibidos
        $params = $_REQUEST;
        $order_id = isset($_REQUEST["order_id"]) ? sanitize_text_field(wp_unslash($_REQUEST["order_id"])) : '';
        $order = new \WC_Order($order_id);
        $table_name = $wpdb->prefix . 'epayco_plans';
        $table_name_setings = $wpdb->prefix . 'epayco_setings';
        $order_id = $params['order_id'];
        $order = new \WC_Order($order_id);

        $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
        // subscription_id = $subscriptions[0]->get_id() ?? 0;
        // $subscription = wcs_get_subscription($subscription_id);

        $token = $params['epaycoToken'];
        if (is_null($token) || $token == "null" ) {
            $error =  __('Token no generado, por favor intente de nuevo.', 'epayco-subscriptions-for-woocommerce');
            wc_add_notice($error, 'error');
            wp_redirect(wc_get_checkout_url());
            exit;
        }
        $customerName =  $params['name'];
        $customerData = $this->paramsBilling($subscriptions, $order, $customerName);
        $customerData['token_card'] = $token;
        $this->custIdCliente =  $this->get_option('custIdCliente');
        $cache_key = "epayco_customer_{$this->custIdCliente}_{$customerData['email']}";
        $customerGetData = wp_cache_get($cache_key, 'epayco');

        $customer_id = $this->createOrUpdateCustomer($customerGetData, $customerData,$token);
         if(is_null($customer_id)){
             $customer = $this->customerCreate($customerData);
             if ($customer->data->status == 'error' || !$customer->status) {
                 if (class_exists('WC_Logger')) {
                    
                     $logger->info("customerCreate: " . json_encode($customer));
                 }
                 $customerJson = json_decode(json_encode($customer), true);
                 $dataError = $customerJson;
                 $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                 wc_add_notice($error, 'error');
                 wp_redirect(wc_get_checkout_url());
                 exit;
             }else{
                 $inserCustomer = $wpdb->insert(
                    $table_name_setings,
                    [
                        'id_payco' => $this->custIdCliente,
                        'customer_id' => $customer->data->customerId,
                        'token_id' => $customerData['token_card'],
                        'email' => $customerData['email']
                    ]
                );
                if (!$inserCustomer) {
                    $response_status = [
                        'status' => false,
                        'message' => __('No se inserto el registro del cliente en la base de datos.', 'epayco-subscriptions-for-woocommerce')
                    ];
                }
                if (class_exists('WC_Logger')) {
                        $logger->info("Error : 'No se inserto el registro del cliente en la base de datos.'");
                    }
                 $customerData['customer_id'] = $customer->data->customerId;
             }
         }else{
             $customerData['customer_id'] = $customer_id;
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
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("Error : " . $exception->getMessage());
                }
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

    public function validate_ePayco_request() :void {
        @ob_clean();
        if (! empty($_REQUEST)) {
            header('HTTP/1.1 200 OK');
            do_action("ePayco_init_validation", $_REQUEST);
        } else {
            wp_die('Do not access this page directly (ePayco)');
        }
    }

    public function customerCreate(array $data)
    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
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
            if (class_exists('WC_Logger')) {
                $logger->info(json_encode($customer));
            }
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger->info("customerCreate".$exception->getMessage());
            }
            echo esc_html('create client: ' . $exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger->info("Error : " . $exception->getMessage());
            }
            die();
        }

        return $customer;
    }

    public function customerAddToken($customer_id, $token_card)
    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
        $customer = false;
        try {
            $customer = $this->epaycoSdk->customer->addNewToken(
                [
                    "token_card" => $token_card,
                    "customer_id" => $customer_id
                ]
            );
            if (class_exists('WC_Logger')) {
                $logger->info(json_encode($customer));
            }
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger->info("customerAddToken".$exception->getMessage());
            }
            echo esc_html('add token: ' . $exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger->info("Error : " . $exception->getMessage());
            }
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
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("getPlans".$exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("Error : " . $exception->getMessage());
                }
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
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("getPlansList".$exception->getMessage());
            }
            echo esc_html($exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error : " . $exception->getMessage());
            }
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
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("getPlanById".$exception->getMessage());
            }
            echo esc_html($exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error : " . $exception->getMessage());
            }
            die();
        }
    }


    public function validatePlan($create, $order_id, array $plans, $subscriptions, $customer, $confirm_url, $order, $confirm = null, $update = null, $getPlans = null)
    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
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
                $newPlanJson = json_decode($newPLan, true);
                if (class_exists('WC_Logger')) {
                    $logger->info("Error : " . json_encode($newPLan));
                }
                $dataError = $newPlanJson;
   
                if (is_array($dataError)) {
                    $message = $dataError['message'];
                    $errores_listados = [];
                    if (isset($dataError['data']['errors']) && is_array($dataError['data']['errors'])) {
                        foreach ($dataError['data']['errors'] as $campo => $mensajes) {
                            foreach ($mensajes as $msg) {
                                $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                            }
                        }
                    }
                    if (isset($dataError['data']->errors) && is_array($dataError['data']->errors)) {
                        foreach ($dataError['data']->errors as $campo => $mensajes) {
                            foreach ($mensajes as $msg) {
                                $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                            }
                        }
                    }
                    
                }else{
                    if (isset($newPLan->data->errors) && is_array($newPLan->data->errors)) {
                        foreach ($newPLan->data->errors as $campo => $mensajes) {
                            foreach ($mensajes as $msg) {
                                $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                            }
                        }
                    }
                    $message = isset($newPLan->message) ? $newPLan->message : (isset($newPLan["message"]) ? $newPLan["message"] : __('El identificador del plan ya está en uso para este comercio. Por favor, elija un nombre diferente para el plan.', 'epayco-subscriptions-for-woocommerce'));
                }


                $errorMessage = $message;
                if (!empty($errores_listados)) {
                    $errorMessage .=  implode(' | ', $errores_listados);
                }
            
                

                $response_status = [
                    'status' => false,
                    /* translators: %s será reemplazado con el mensaje de error del nuevo plan */
                    'message' => $errorMessage,
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

        if ($plan_id_cart == $plan_id_epayco) {

            try {
                if (intval($plan_amount_cart) == $plan_amount_epayco) {
                    return $this->process_payment_epayco($plans, $customer, $confirm_url, $subscriptions, $order);
                } else {
                    return $this->validateNewPlanData($subscriptions, $order_id, $plan_amount_cart, $plan_currency_epayco, $plans, $customer, $confirm_url, $order);
                }
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info($exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                die();
                return false;
            }
        } else {
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("el id del plan creado no concuerda!");
            }
            echo 'el id del plan creado no concuerda!';
            die();
        }
    }

    public function validateNewPlanData($subscriptions, $order_id, $value, $currency, $plans, $customer, $confirm_url, $order)
    {
        global $wpdb;


        $subsCreated = $this->planUpdate($plans);
        if ($subsCreated->success) {

            return $this->process_payment_epayco($plans, $customer, $confirm_url, $subscriptions, $order);
        }
        die();

        $table_name = $wpdb->prefix . 'epayco_plans';
        $wc_order_product_lookup = $wpdb->prefix . "wc_order_product_lookup";

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
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
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


            if (class_exists('WC_Logger')) {
                $logger->info(json_encode($plan_));
            }
                return $plan_;
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger->info("plansCreate".$exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger->info("Error : " . $exception->getMessage());
                }
                die();
            }
        }
    }


    public function subscriptionCreate(array $plans, array $customer, $confirm_url)

    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
        foreach ($plans as $plan) {
            try {
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCreate : " . json_encode($customer));
                }
                
                 $logger->info("customer_id : " . json_encode($customer));
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
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCreate : " . json_encode($suscriptioncreted));
                }
                return $suscriptioncreted;
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCreate".$exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger->info("Error : " . $exception->getMessage());
                }
                die();
            }
        }
    }


    public function subscriptionCharge(array $plans, array $customer, $confirm_url, $epayco_subscription_id)
    {
        $subs = [];
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }

        foreach ($plans as $plan) {
            try {
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCharge : " . json_encode($customer));
                }
                $subs[] = $this->epaycoSdk->subscriptions->charge(
                    [
                        "id_plan" => $plan['id_plan'],
                        "customer" => $customer['customer_id'],
                        "token_card" => $customer['token_card'],
                        "doc_type" => $customer['type_document'],
                        "doc_number" => $customer['doc_number'],
                        "ip" => $this->getIP(),
                        "url_confirmation" => $confirm_url,
                        "method_confirmation" => "POST",
                        "idSubscription" => $epayco_subscription_id
                    ]
                );
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCharge".$exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger->info("Error : " . $exception->getMessage());
                }
                die();
            }
        }
        if (class_exists('WC_Logger')) {
            $logger->info("subscriptionCharge : " . json_encode($subs));
        }
        return $subs;
    }


    public function planUpdate(array $plans)
    {
        foreach ($plans as $plan) {
            try {
                $plan_ = $this->epaycoSdk->plan->update(
                    (string)strtolower($plan['id_plan']),
                    [
                        "name" => (string)$plan['name'],
                        "description" => (string)$plan['description'],
                        "amount" => $plan['amount'],
                        "currency" => $plan['currency'],
                        "interval" => $plan['interval'],
                        "interval_count" => $plan['interval_count'],
                        "trial_days" => $plan['trial_days'],
                        "iva" => $plan['iva'],
                    ]
                );



                return $plan_;
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("planUpdate".$exception->getMessage());
                }
                echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("Error : " . $exception->getMessage());
                }
                die();
            }
        }
    }



    public function cancelSubscription($subscription_id)
    {
        try {
            $result = $this->epaycoSdk->subscriptions->cancel($subscription_id);
            error_log("ePayco cancel result: " . print_r($result, true));
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            }
            error_log("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            }
            throw $exception;
        }
    }


    private function getWooCommerceSubscriptionFromOrderId($orderId)
    {
        $subscriptions = wcs_get_subscriptions_for_order($orderId);

        return $subscriptions;
    }


    public function setPaymentsIdData(\WC_Order $order, $value): void
    {
        try {
            $logger = new \WC_Logger();
            if ($order instanceof \WC_Order) {
                $order->add_meta_data(self::ID, $value);
                $order->save();
            }
        } catch (\Exception $ex) {
            $error_message = "Unable to update batch of orders on action got error: {$ex->getMessage()}";
            $logger->add(self::ID, $error_message);
        }
    }


    public function setPaymentsIdDataForSubscription($subscription, $value): void
    {
        try {
            $logger = new \WC_Logger();

            if (is_array($subscription)) {
                foreach ($subscription as $sub) {
                    if ($sub instanceof \WC_Subscription) {
                        $sub->delete_meta_data(self::ID);
                        $sub->update_meta_data(self::ID, $value);
                        $sub->save();


                        $sub = wcs_get_subscription($sub->get_id());
                    } else {
                        $logger->add($this->id, "Elemento no es una instancia de WC_Subscription.");
                    }
                }
            } else {
                $logger->add($this->id, "Objeto no es una instancia de WC_Subscription.");
            }
        } catch (\Exception $ex) {
            $error_message = "Error al actualizar la suscripción: {$ex->getMessage()}";
            $logger->add($this->id, $error_message);
        }
    }






    public function paramsBilling($subscriptions, $order, $customerName)
    {
        $data = [];
        $subscription = end($subscriptions);
        if ($subscription) {

            $doc_number = get_post_meta($subscription->get_id(), '_epayco_billing_dni', true) != null ? get_post_meta($subscription->get_id(), '_epayco_billing_dni', true)  : ($order->get_meta('_epayco_billing_dni') !== "" ? $order->get_meta('_epayco_billing_dni') :  $order->get_meta('_billing_custom_field'));
            $type_document = get_post_meta($subscription->get_id(), '_epayco_billing_type_document', true) != null ? get_post_meta($subscription->get_id(), '_epayco_billing_type_document', true) : ($order->get_meta('_epayco_billing_type_document') !== "" ? $order->get_meta('_epayco_billing_type_document')  : "CC");

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
            $total = $subscription->get_base_data()['total'];
            $tax = $subscription->get_base_data()['total_tax'] ?? 0;
            $subtotal = $total - $tax;
            if ($subtotal > 0 && $tax > 0) {
                $tax_percentage = ($tax / $subtotal) * 100;
                $tax_percentage = intval($tax_percentage); // Redondear a 2 decimales
            } else {
                $tax_percentage = 0;
            }
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
            $plan_name = strtolower(str_replace("__", "_", $plan_id));
            $normalized = preg_replace('/[-_]+/', '_', $plan_code);
            $normalized = strtolower($normalized);
            $normalized = preg_replace('/[^a-z0-9_]/', '', $normalized);
            $description = str_replace('_', ' ', $plan_name);
            $plans[] = array_merge(
                [
                    "id_plan" => $normalized,
                    "name" => "Plan $description",
                    "description" => "Plan $description",
                    "currency" => $order_currency,
                    "amount" => $total,
                    "iva" => $tax_percentage,
                    "ico" => 0
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
                    if ($current_state != "epayco_on_hold" || $current_state == "pending") {
                        $this->restore_order_stock($order->id, '+');
                    }
                } else {
                    $message = 'Pago pendiente de aprobación';
                    $orderStatus = "epayco-on-hold";
                    if ($current_state != "epayco-on-hold" || $current_state == "pending") {
                        // $this->restore_order_stock($order->id, '+');
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
                    // $this->restore_order_stock($order->get_id(), "-");


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

            if ($count === $quantitySubscriptions && count($messageStatus['message']) >= $count) {
                $messageStatus['status'] = false;
            }

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
                    if (class_exists('WC_Logger')) {
                        $logger = wc_get_logger();
                        $logger->info("sub error: ".$exception->getMessage());
                    }
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
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("sub error: ".$exception->getMessage());
                }
                die();
            }
        }
        return $result;
    }

    public function process_payment_epayco(array $plans, array $customerData, $confirm_url, $subscriptions, $order)
    {
        $subsCreated = $this->subscriptionCreate($plans, $customerData, $confirm_url);

        if ($subsCreated->status) {
            if (isset($subsCreated->id)) {
                $epayco_subscription_id = $subsCreated->id;
                if (wcs_order_contains_subscription($order->get_id())) {
                    $subscriptions_wc = wcs_get_subscriptions_for_order($order, ['order_type' => 'parent']);
                    foreach ($subscriptions_wc as $subscription) {
                        update_post_meta($subscription->get_id(), '_epayco_subscription_id', $epayco_subscription_id);
                    }
                }
            }
            $subs = $this->subscriptionCharge($plans, $customerData, $confirm_url, $epayco_subscription_id);
            foreach ($subs as $sub) {
                $customerId = isset($subsCreated->customer->_id) ? $subsCreated->customer->_id : null;
                $suscriptionId = isset($subsCreated->id) ? $subsCreated->id : null;
                $this->setPaymentsIdDataForSubscription($subscriptions, $suscriptionId);
                $planId = isset($subsCreated->data->idClient) ? $subsCreated->data->idClient : null;
                $validation = !is_null($sub->status) ? $sub->status : $sub->success;
                if ($validation) {
                    $messageStatus = $this->handleStatusSubscriptions($subs, $subscriptions, $customerData, $order, $customerId, $suscriptionId, $planId);

                    $response_status = [
                        'ref_payco' => $messageStatus['ref_payco'][0],
                        'status' => $messageStatus['status'],
                        'message' => $messageStatus['message'][0],
                        'url' => $order->get_checkout_order_received_url()
                    ];
                } else {
                    $subJson = json_decode(json_encode($sub), true);
                    if (class_exists('WC_Logger')) {
                        $logger = wc_get_logger();
                        $logger->info("process_payment_epayco_error_sub:".json_encode($sub));
                    }
                    $dataError = $subJson;
                    $message = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('Ocurrió un error, por favor contactar con soporte.', 'epayco-subscriptions-for-woocommerce'));
                    $errores_listados = [];
                    if (isset($dataError['data']['errors'])) {
                        if (is_array($dataError['data']['errors'])) {
                            foreach ($dataError['data']['errors'] as $campo => $mensajes) {
                                foreach ($mensajes as $msg) {
                                    $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                                }
                            }
                        } else {
                            $errores_listados[] = $dataError['data']['errors'];
                        }
                    }
                    if (isset($dataError['data']->errors) && is_array($dataError['data']->errors)) {
                        foreach ($dataError['data']->errors as $campo => $mensajes) {
                            foreach ($mensajes as $msg) {
                                $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                            }
                        }
                    }
                    $errorMessage = $message;
                    if (!empty($errores_listados)) {
                        $errorMessage .=  implode(' | ', $errores_listados);
                    }

                    $response_status = [
                        'ref_payco' => null,
                        'status' => false,
                        'message' => $errorMessage,
                        'url' => $order->get_checkout_order_received_url()
                    ];
                }
            }
        } else {
            $subsCreatedJson = json_decode(json_encode($subsCreated), true);
            $dataError = $subsCreatedJson;
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("process_payment_epayco_error:".json_encode($subsCreated));
            }
            $message = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('Ocurrió un error, por favor contactar con soporte.', 'epayco-subscriptions-for-woocommerce'));
            $errores_listados = [];
            if (isset($dataError['data']['errors'])) {
                if (is_array($dataError['data']['errors'])) {
                    foreach ($dataError['data']['errors'] as $campo => $mensajes) {
                        foreach ($mensajes as $msg) {
                            $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                        }
                    }
                } else {
                    $errores_listados[] = $dataError['data']['errors'];
                }
            }
            if (isset($dataError['data']->errors) && is_array($dataError['data']->errors)) {
                foreach ($dataError['data']->errors as $campo => $mensajes) {
                    foreach ($mensajes as $msg) {
                        $errores_listados[] = ucfirst($campo) . ': ' . $msg;
                    }
                }
            }
            $errorMessage = $message." ";
            if (!empty($errores_listados)) {
                $errorMessage .=  implode(' | ', $errores_listados);
            }          
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
                            // $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago exitoso';
                        $orderStatus = $this->get_option('epayco_endorder_state');
                        if (!($current_state == "epayco-on-hold")) {
                            // $this->restore_order_stock($order->get_id(), "+");
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



    public function updateStatusSubscription()
    {
        $subs = $this->epaycoSdk->subscriptions->getList();
        $logger = new \WC_Logger();
        global $wpdb;
        $table_name = $wpdb->prefix . 'wc_orders';
        if (!empty($subs->data)) {
            foreach ($subs->data as $epayco_subscription) {
                $epayco_id = $epayco_subscription->_id ?? null;
                $epayco_status = strtolower($epayco_subscription->status ?? '');

                if (empty($epayco_id)) {
                    // $logger->add(self::LOG_SOURCE, "Suscripción sin ID encontrada, omitiendo...");
                    continue;
                }

                // $logger->add(self::LOG_SOURCE, "Consultando suscripción SDK ePayco : ID={$epayco_id}, status_plan={$epayco_status}");

                $args = [
                    'post_type'      => 'shop_subscription',
                    'post_status'    => 'any',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'meta_query'     => [
                        [
                            'key'     => '_epayco_subscription_id',
                            'value'   => strval(trim($epayco_id)),
                            'compare' => '=',
                        ],
                    ],
                ];

                foreach ($args['meta_query'] as $meta_query) {
                    // $logger->add(
                    //     self::LOG_SOURCE,
                    //     "Buscando suscripciones con meta key={$meta_query['key']} y value={$meta_query['value']}"
                    // );
                }

                global $wpdb;
                $meta = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
                        '_epayco_subscription_id',
                        strval(trim($epayco_id))
                    )
                );
                // $logger->add(self::LOG_SOURCE, "Consulta directa encontró: " . count($meta));

                if (!empty($meta)) {
                    foreach ($meta as $row) {
                        $wc_subscription_id = $row->post_id;
                        $wc_subscription = wcs_get_subscription($wc_subscription_id);
                        if ($wc_subscription) {
                            $current_status = $wc_subscription->get_status();
                            $desired_status = null;
                            if (in_array($epayco_status, ['inactive', 'cancelled', 'canceled'])) {
                                $desired_status = 'cancelled';
                            } elseif ($epayco_status === 'pending') {
                                $desired_status = 'on-hold';
                            } elseif ($epayco_status === 'active') {
                                $desired_status = 'active';
                            }

                            if ($desired_status) {
                                if ($current_status !== $desired_status) {
                                    if($current_status === 'pending-cancel' && $desired_status === 'active'){
                                        try {
                                            $subscription_id = $wc_subscription->get_id();
                                            $sql = $wpdb->prepare(
                                                "UPDATE {$table_name} SET status = %s WHERE id = %d",
                                                'wc-active',
                                                $subscription_id
                                            );
                                            
                                            $result = $wpdb->query($sql);
                            
                                            if ($result === false) {
                                                $logger->add(self::LOG_SOURCE, "❌ Error ejecutando la consulta para la suscripción #{$subscription_id}");
                                            } elseif ($result === 0) {
                                                $logger->add(self::LOG_SOURCE, "ℹ️ Consulta ejecutada, pero ninguna fila fue actualizada para la suscripción #{$subscription_id}");
                                            } else {
                                                $logger->add(self::LOG_SOURCE, "✅ Suscripción #{$subscription_id} actualizada correctamente (filas afectadas: {$result})");
                                            }
                            
                                        } catch (Exception $e) {
                                            $logger->add(self::LOG_SOURCE, "❗ Excepción al ejecutar la consulta para la suscripción #{$subscription_id}: " . $e->getMessage());
                                        }
                                    }else{
                                        $wc_subscription->update_status($desired_status);
                                    }
                                    
                                } 
                            } 
                        }
                    }
                } 
            }
        } 
    }


    public function on_wc_subscription_cancelled($subscription)
    {
        $epayco_subscription_id = get_post_meta($subscription->get_id(), '_epayco_subscription_id', true);

        if ($epayco_subscription_id) {
            $this->cancelSubscription($epayco_subscription_id);
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
    
    
    public function createOrUpdateCustomer($customerGetData, $customerData, $token){
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $table_name_setings = $wpdb->prefix . 'epayco_setings';
        $logger = wc_get_logger();
        
        
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
                
             $logger->info("consulta: " .  $customerGetData );

            if (!empty($customerGetData)) {
                wp_cache_set($cache_key, $customerGetData, 'epayco', 3600); // Cache por 1 hora
            }
        }

        if (count($customerGetData) == 0) {

            $customer = $this->customerCreate($customerData);
            if ($customer->data->status == 'error' || !$customer->status) {
                if (class_exists('WC_Logger')) {
                    
                    $logger->info("customerCreate: " . json_encode($customer));
                }
                $customerJson = json_decode(json_encode($customer), true);
                $dataError = $customerJson;
                $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                wc_add_notice($error, 'error');
                wp_redirect(wc_get_checkout_url());
                exit;
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $inserCustomer = $wpdb->insert(
                $table_name_setings,
                [
                    'id_payco' => $this->custIdCliente,
                    'customer_id' => $customer->data->customerId,
                    'token_id' => $customerData['token_card'],
                    'email' => $customerData['email']
                ]
            );


            if (!$inserCustomer) {
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("inserCustomer: " . json_encode($customer));
                }
                $customerJson = json_decode(json_encode($customer), true);
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("Error : " . json_encode($customer));
                }
                $dataError = $customerJson;
                $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('No se inserto el registro del cliente en la base de datos.', 'epayco-subscriptions-for-woocommerce'));
                wc_add_notice($error, 'error');
                wp_redirect(wc_get_checkout_url());
                exit;
            }
            return $customer->data->customerId;
        } else {
            $count_customers = 0;
            for ($i = 0; $i < count($customerGetData); $i++) {
                $email = $customerGetData[$i]->email ?? $customerGetData[0]['email'];
                if ($email == $customerData['email']) {
                    $count_customers += 1;
                }
            }


            if ($count_customers == 0) {
                $customer = $this->customerCreate($customerData);
                if ($customer->data->status == 'error') {
                    if (class_exists('WC_Logger')) {
                        $logger->info("customerCreate: " . json_encode($customer));
                    }
                    $customerJson = json_decode(json_encode($customer), true);
                    if (class_exists('WC_Logger')) {
                        $logger->info("Error : " . json_encode($customer));
                    }
                    $dataError = $customerJson;
                    $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                    wc_add_notice($error, 'error');
                    wp_redirect(wc_get_checkout_url());
                    exit;
                }
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $inserCustomer = $wpdb->insert(
                    $table_name_setings,
                    [
                        'id_payco' => $this->custIdCliente,
                        'customer_id' => $customer->data->customerId,
                        'token_id' => $customerData['token_card'],
                        'email' => $customerData['email']
                    ]
                );
                if (!$inserCustomer) {
                    $response_status = [
                        'status' => false,
                        'message' => __('No se inserto el registro del cliente en la base de datos.', 'epayco-subscriptions-for-woocommerce')
                    ];
                }
                if (class_exists('WC_Logger')) {
                        $logger->info("Error : 'No se inserto el registro del cliente en la base de datos.'");
                    }
                return $customer->data->customerId;
            } else {
                for ($i = 0; $i < count($customerGetData); $i++) {
                    $email = $customerGetData[$i]->email ?? $customerGetData[0]['email'];
                    $token_id = $customerGetData[$i]->token_id ?? $customerGetData[0]['token_id'];
                    $customer_id = $customerGetData[$i]->customer_id ?? $customerGetData[0]['customer_id'];

                    if ($email == $customerData['email'] && $token_id != $token) {
                        if (is_null($customer_id)) {
                            $customer = $this->customerCreate($customerData);
                           
                            if ($customer->data->status == 'error') {
                                if (class_exists('WC_Logger')) {
                                    $logger->info("customerCreate: " . json_encode($customer));
                                }
                                
                                $customerJson = json_decode(json_encode($customer), true);
                                $dataError = $customerJson;
                                $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                                wc_add_notice($error, 'error');
                                wp_redirect(wc_get_checkout_url());
                                exit;
                            }
                            $customer_id = $customer->data->customerId;
                        } else {
                            $isAddedToken = $this->customerAddToken($customer_id, $token);
                            if (!$isAddedToken->status) {
                                if (class_exists('WC_Logger')) {
                                    $logger = wc_get_logger();
                                    $logger->info("isAddedToken: " . json_encode($isAddedToken));
                                }
                                $customerJson = json_decode(json_encode($isAddedToken), true);
                                $dataError = $customerJson;
                                $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('Error: El token que desea asociar ya se encuentra asociado a otro customer', 'epayco-subscriptions-for-woocommerce'));
                                wc_add_notice($error, 'error');
                                wp_redirect(wc_get_checkout_url());
                                exit;
                            }
                        }

                        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                        $inserCustomer = $wpdb->update(
                            $table_name_setings,
                            [
                                'id_payco' => $this->custIdCliente,
                                'customer_id' => $customer_id,
                                'token_id' => $token
                            ],
                            [
                                'email' => $email
                            ]
                        );
                        if (!$inserCustomer) {
                            if (class_exists('WC_Logger')) {
                                $logger = wc_get_logger();
                                $logger->info("customerCreate".json_encode($customer));
                            }
                            $customerJson = json_decode(json_encode($customer), true);
                            if (class_exists('WC_Logger')) {
                                $logger = wc_get_logger();
                                $logger->info("Error : " . json_encode($customer));
                            }
                            $dataError = $customerJson;
                            $error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('No se inserto el registro del cliente en la base de datos.', 'epayco-subscriptions-for-woocommerce'));
                            wc_add_notice($error, 'error');
                            wp_redirect(wc_get_checkout_url());
                            exit;
                        }
                        $customerData['token_card'] = $token_id;
                    }
                    return $customer_id;
                }
            }
        }
    }


    public function updateCustomerInfo($customer,$customerData) {
      global $wpdb;

        $table_name = $wpdb->prefix . 'settings_epayco';

        $id_payco    = $this->custIdCliente;
        $customer_id = $customer->data->customerId;
        $token_id    = $customerData['token_card'];
        $email       = $customerData['email'];

        // Preparar SQL con ON DUPLICATE KEY
        $sql = $wpdb->prepare("
            INSERT INTO $table_name (id_payco, customer_id, token_id, email)
            VALUES (%s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
                id_payco = VALUES(id_payco),
                customer_id = VALUES(customer_id),
                token_id = VALUES(token_id)
        ", $id_payco, $customer_id, $token_id, $email);

        $wpdb->query($sql);  
    }
}
