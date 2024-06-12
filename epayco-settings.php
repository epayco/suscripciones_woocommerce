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
                throw new Exception(__('Subscription ePayco  can only be called once'));
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
        if (!class_exists('Epayco\Epayco'))
            require_once($this->lib_path . 'vendor/autoload.php');
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        add_filter('woocommerce_payment_gateways', array($this, 'woocommerce_suscription_epayco_add_gateway'));
        add_filter('woocommerce_checkout_fields', array($this, 'custom_woocommerce_billing_fields'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="admin.php?page=wc-settings&tab=checkout&section=epayco-subscription">' . esc_html__('Configuraciones') . '</a>';
        $plugin_links[] = '<a href="https://docs.epayco.co/integrations/plugins/" target="_blank">' . esc_html__('Documentación') . '</a>';
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
            'label' => __('Tipo de documento', 'epayco-subscription'),
            'placeholder' => _x('', 'placeholder', 'epayco-subscription'),
            'required' => true,
            'clear' => false,
            'type' => 'select',
            'class' => array('my-css'),
            'default' => 'CC',
            'options' => array(
                'CC' => __('Cédula de ciudadanía'),
                'CE' => __('Cédula de extranjería'),
                'PPN' => __('Pasaporte'),
                'SSN' => __('Número de seguridad social'),
                'LIC' => __('Licencia de conducción'),
                'NIT' => __('(NIT) Número de indentificación tributaria'),
                'TI' => __('Tarjeta de identidad'),
                'DNI' => __('Documento nacional de identificación')
            )
        );

        $fields['billing']['billing_dni'] = array(
            'label' => __('Número de documento', 'epayco-subscription'),
            'placeholder' => _x('Número de documento...', 'placeholder', 'subscription-epayco'),
            'required' => true,
            'clear' => false,
            'type' => 'number',
            'class' => array('my-css')
        );


        $fields['shipping']['shipping_type_document'] = array(
            'label' => __('Tipo de documento', 'subscription-epayco'),
            'placeholder' => _x('', 'placeholder', 'epayco-subscription'),
            'required' => true,
            'clear' => false,
            'type' => 'select',
            'class' => array('my-css'),
            'default' => 'CC',
            'options' => array(
                'CC' => __('Cédula de ciudadanía'),
                'CE' => __('Cédula de extranjería'),
                'PPN' => __('Pasaporte'),
                'SSN' => __('Número de seguridad social'),
                'LIC' => __('Licencia de conducción'),
                'NIT' => __('(NIT) Número de indentificación tributaria'),
                'TI' => __('Tarjeta de identidad'),
                'DNI' => __('Documento nacional de identificación')
            )
        );

        $fields['shipping']['shipping_dni'] = array(
            'label' => __('Número de documento', 'subscription-epayco'),
            'placeholder' => _x('Número de documento...', 'placeholder', 'epayco-subscription'),
            'required' => true,
            'clear' => false,
            'type' => 'number',
            'class' => array('my-css')
        );

        return $fields;
    }

    public function enqueue_scripts()
    {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();

        if ($gateways['epayco-subscription']->enabled === 'yes' && is_checkout()) {
            wp_enqueue_script('crypto-epayco', 'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.js', array(), $this->version, true);
            wp_enqueue_style('frontend-subscription-epayco', $this->plugin_url . 'assets/css/card-js.min.css', array(), $this->version, null);
        }
    }


    public function log($message = '')
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);
        $this->logger->add('epayco-subscription', $message);
    }
}