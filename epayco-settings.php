<?php

class Epayco_Subscription_Config
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var WC_Logger
     */
    public $logger;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version, $name)
    {
        $this->file = $file;
        $this->version = $version;
        $this->name = $name;
        // Path.
        $this->plugin_path = trailingslashit(plugin_dir_path($this->file));
        $this->plugin_url = trailingslashit(plugin_dir_url($this->file));
        $this->includes_path = $this->plugin_path;
        $this->lib_path = $this->plugin_path . trailingslashit('lib');
        $this->logger = new WC_Logger();
    }

    public function run_epayco()
    {
        try {
            if ($this->_bootstrapped) {
                throw new Exception(__('Subscription ePayco can only be called once', 'epayco-subscriptions-for-woocommerce'));
            }
            $this->_run();
            $this->_bootstrapped = true;
        } catch (Exception $e) {
            if (is_admin() && !defined('DOING_AJAX')) {
                subscription_epayco_se_notices('Subscription ePayco: ' . $e->getMessage());
            }
        }
    }

   
 protected function _run()
{
    require_once($this->includes_path . 'epayco-subscription-epayco.php');
    require_once($this->includes_path . 'epayco-class-subscription.php');

    if (!class_exists('Epayco\Epayco')) {
        require_once($this->lib_path . 'vendor/autoload.php');
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
    add_filter('woocommerce_payment_gateways', array($this, 'woocommerce_suscription_epayco_add_gateway'));
    add_filter('woocommerce_checkout_fields', array($this, 'custom_woocommerce_billing_fields'));

    // Encolar los scripts y estilos
    add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
}


    

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="admin.php?page=wc-settings&tab=checkout&section=epayco-subscription">' . esc_html__('Configuraciones', 'epayco-subscriptions-for-woocommerce') . '</a>';

        $plugin_links[] = '<a href="https://docs.epayco.co/integrations/plugins/" target="_blank">' . esc_html__('Documentación', 'epayco-subscriptions-for-woocommerce') . '</a>';

        return array_merge($plugin_links, $links);
    }

    public function woocommerce_suscription_epayco_add_gateway($methods)
    {

        if (class_exists('WC_Subscriptions'))
            $methods[] = 'WC_Payment_Epayco_Subscription';
        return $methods;
    }

    public function custom_woocommerce_billing_fields($fields)
    {
        $fields['billing']['billing_type_document'] = array(
            'label' => __('Tipo de documento', 'epayco-subscriptions-for-woocommerce'),
            'placeholder' => '',
            'required' => true,
            'clear' => false,
            'type' => 'select',
            'class' => array('custom-field-class'),
            'default' => 'CC',
            'options' => array(
                'CC'  => __('Cédula de ciudadanía', 'epayco-subscriptions-for-woocommerce'), // Línea 113  
                'CE'  => __('Cédula de extranjería', 'epayco-subscriptions-for-woocommerce'),
                'PPN' => __('Pasaporte', 'epayco-subscriptions-for-woocommerce'),
                'SSN' => __('Número de seguridad social', 'epayco-subscriptions-for-woocommerce'),
                'LIC' => __('Licencia de conducción', 'epayco-subscriptions-for-woocommerce'),
                'NIT' => __('(NIT) Número de identificación tributaria', 'epayco-subscriptions-for-woocommerce'),
                'TI'  => __('Tarjeta de identidad', 'epayco-subscriptions-for-woocommerce'),
                'DNI' => __('Documento nacional de identificación', 'epayco-subscriptions-for-woocommerce') // Línea 120  
            )
        );

        $fields['billing']['billing_dni'] = array(
            'label' => __('Número de documento', 'epayco-subscriptions-for-woocommerce'),
            'placeholder' => _x('Número de documento...', 'placeholder', 'epayco-subscriptions-for-woocommerce'),
            'required' => true,
            'clear' => false,
            'type' => 'number',
            'class'       => array('custom-field-class'),
        );


        $fields['shipping']['shipping_type_document'] = array(
            'label' => __('Tipo de documento', 'epayco-subscriptions-for-woocommerce'),
            'placeholder' => '',
            'required' => true,
            'clear' => false,
            'type' => 'select',
            'class'       => array('custom-field-class'),
            'default' => 'CC',
            'options' => array(
                'CC'  => __('Cédula de ciudadanía', 'epayco-subscriptions-for-woocommerce'), // Línea 113  
                'CE'  => __('Cédula de extranjería', 'epayco-subscriptions-for-woocommerce'),
                'PPN' => __('Pasaporte', 'epayco-subscriptions-for-woocommerce'),
                'SSN' => __('Número de seguridad social', 'epayco-subscriptions-for-woocommerce'),
                'LIC' => __('Licencia de conducción', 'epayco-subscriptions-for-woocommerce'),
                'NIT' => __('(NIT) Número de identificación tributaria', 'epayco-subscriptions-for-woocommerce'),
                'TI'  => __('Tarjeta de identidad', 'epayco-subscriptions-for-woocommerce'),
                'DNI' => __('Documento nacional de identificación', 'epayco-subscriptions-for-woocommerce') // Línea 120  
            )
        );

        $fields['shipping']['shipping_dni'] = array(
            'label' => __('Número de documento', 'epayco-subscriptions-for-woocommerce'),
            'placeholder' => _x('Número de documento...', 'placeholder', 'epayco-subscriptions-for-woocommerce'),
            'required' => true,
            'clear' => false,
            'type' => 'number',
            'class'       => array('custom-field-class'),
        );

        return $fields;
    }

    public function enqueue_scripts()
    {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();

        if ($gateways['epayco-subscription']->enabled === 'yes' && is_checkout()) {
            wp_enqueue_script('crypto-epayco', $this->plugin_url . 'assets/js/crypto-js.js', array(), $this->version, true);
            wp_enqueue_style('frontend-subscription-epayco', $this->plugin_url . 'assets/css/card-js.min.css', array(), $this->version, null);
        }
    }


    public function log($message = '')
    {
        if (is_array($message) || is_object($message)) {
            $message = wp_json_encode($message, JSON_PRETTY_PRINT);
        }
        
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
            $logger->info($message, array('source' => 'epayco-subscription'));
        }
    }
    
}