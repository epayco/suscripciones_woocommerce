<?php
/**
 * @since             1.0.0
 * @package           epayco-subscription
 *
 * @wordpress-plugin
 * Plugin Name:       ePayco WooCommerce Suscripction
 * Description:       Plugin ePayco WooCommerce.
 * Version:           5.1.0
 * Author:            ePayco
 * Author URI:
 * Licence
 * Domain Path:       /languages
 */

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;

if (!defined('ABSPATH')) exit;

if (!defined('EPAYCO_SUBSCRIPTION_SE_VERSION')) {
    define('EPAYCO_SUBSCRIPTION_SE_VERSION', '3.0.1');
}


add_action('plugins_loaded', 'epayco_subscription_init', 0);
add_action('plugins_loaded', 'register_epayco_suscription_order_status');
add_filter('wc_order_statuses', 'add_epayco_suscription_to_order_statuses');
add_action('admin_head', 'styling_admin_suscription_order_list');
add_action('woocommerce_checkout_update_order_meta', 'some_custom_checkout_field_update_order_meta');

add_action('before_woocommerce_init', function () {
    if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__);
    }

    if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__);
    }
});

function epayco_subscription_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    registerBlocks();
    require_once(dirname(__FILE__) . '/epayco-settings.php');
    $plugin = new Epayco_Subscription_Config(__FILE__, EPAYCO_SUBSCRIPTION_SE_VERSION, 'epayco-subscription');
    $plugin->run_epayco();
    if (get_option('subscription_epayco_se_redirect', false)) {
        delete_option('subscription_epayco_se_redirect');
        wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section=epayco-subscription'));
    }
}

/**
     * Register woocommerce blocks support
     *
     * @return void
     */
    function registerBlocks(): void
    {
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            require_once 'lib/blocks/epayco-block.php';
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function (PaymentMethodRegistry $payment_method_registry) {
                    $payment_method_registry->register(new CustomBlock());
                }
            );
        }
    }

function register_epayco_suscription_order_status()
{
    register_post_status('wc-epayco-failed', array(
        'label' => 'ePayco Pago Fallido',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Fallido <span class="count">(%s)</span>', 'ePayco Pago Fallido <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco_failed', array(
        'label' => 'ePayco Pago Fallido Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Fallido Prueba <span class="count">(%s)</span>', 'ePayco Pago Fallido Prueba <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco-cancelled', array(
        'label' => 'ePayco Pago Cancelado',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Cancelado <span class="count">(%s)</span>', 'ePayco Pago Cancelado <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco_cancelled', array(
        'label' => 'ePayco Pago Cancelado Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Cancelado Prueba <span class="count">(%s)</span>', 'ePayco Pago Cancelado Prueba <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco-on-hold', array(
        'label' => 'ePayco Pago Pendiente',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Pendiente <span class="count">(%s)</span>', 'ePayco Pago Pendiente <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco_on_hold', array(
        'label' => 'ePayco Pago Pendiente Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Pendiente Prueba <span class="count">(%s)</span>', 'ePayco Pago Pendiente Prueba <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco-processing', array(
        'label' => 'ePayco Procesando Pago',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Procesando Pago <span class="count">(%s)</span>', 'ePayco Procesando Pago <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco_processing', array(
        'label' => 'ePayco Procesando Pago Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Procesando Pago Prueba<span class="count">(%s)</span>', 'ePayco Procesando Pago Prueba<span class="count">(%s)</span>')
    ));

    register_post_status('wc-processing', array(
        'label' => 'Procesando',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('Procesando<span class="count">(%s)</span>', 'Procesando<span class="count">(%s)</span>')
    ));

    register_post_status('wc-processing_test', array(
        'label' => 'Procesando Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('Procesando Prueba<span class="count">(%s)</span>', 'Procesando Prueba<span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco-completed', array(
        'label' => 'ePayco Pago Completado',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Completado <span class="count">(%s)</span>', 'ePayco Pago Completado <span class="count">(%s)</span>')
    ));

    register_post_status('wc-epayco_completed', array(
        'label' => 'ePayco Pago Completado Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('ePayco Pago Completado Prueba <span class="count">(%s)</span>', 'ePayco Pago Completado Prueba <span class="count">(%s)</span>')
    ));

    register_post_status('wc-completed', array(
        'label' => 'Completado',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('Completado<span class="count">(%s)</span>', 'Completado<span class="count">(%s)</span>')
    ));

    register_post_status('wc-completed_test', array(
        'label' => 'Completado Prueba',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('Completado Prueba<span class="count">(%s)</span>', 'Completado Prueba<span class="count">(%s)</span>')
    ));
}

function add_epayco_suscription_to_order_statuses($order_statuses)
{
    $new_order_statuses = array();
    $epayco_order = get_option('epayco_order_status');
    $testMode = $epayco_order == true ? "true" : "false";
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-cancelled' === $key) {
            if ($testMode == "true") {
                $new_order_statuses['wc-epayco_cancelled'] = 'ePayco Pago Cancelado Prueba';
            } else {
                $new_order_statuses['wc-epayco-cancelled'] = 'ePayco Pago Cancelado';
            }
        }

        if ('wc-failed' === $key) {
            if ($testMode == "true") {
                $new_order_statuses['wc-epayco_failed'] = 'ePayco Pago Fallido Prueba';
            } else {
                $new_order_statuses['wc-epayco-failed'] = 'ePayco Pago Fallido';
            }
        }

        if ('wc-on-hold' === $key) {
            if ($testMode == "true") {
                $new_order_statuses['wc-epayco_on_hold'] = 'ePayco Pago Pendiente Prueba';
            } else {
                $new_order_statuses['wc-epayco-on-hold'] = 'ePayco Pago Pendiente';
            }
        }

        if ('wc-processing' === $key) {
            if ($testMode == "true") {
                $new_order_statuses['wc-epayco_processing'] = 'ePayco Pago Procesando Prueba';
            } else {
                $new_order_statuses['wc-epayco-processing'] = 'ePayco Pago Procesando';
            }
        } else {
            if ($testMode == "true") {
                $new_order_statuses['wc-processing_test'] = 'Procesando Prueba';
            } else {
                $new_order_statuses['wc-processing'] = 'Procesando';
            }
        }

        if ('wc-completed' === $key) {
            if ($testMode == "true") {
                $new_order_statuses['wc-epayco_completed'] = 'ePayco Pago Completado Prueba';
            } else {
                $new_order_statuses['wc-epayco-completed'] = 'ePayco Pago Completado';
            }
        } else {
            if ($testMode == "true") {
                $new_order_statuses['wc-completed_test'] = 'Completado Prueba';
            } else {
                $new_order_statuses['wc-completed'] = 'Completado';
            }
        }
    }
    return $new_order_statuses;
}

function styling_admin_suscription_order_list()
{
    global $pagenow, $post;
    if ($pagenow != 'edit.php') return; // Exit
    if (get_post_type($post->ID) != 'shop_order') return; // Exit
    // HERE we set your custom status
    $epayco_order = get_option('epayco_order_status');
    $testMode = $epayco_order == true ? "true" : "false";
    if ($testMode == "true") {
        $order_status_failed = 'epayco_failed';
        $order_status_on_hold = 'epayco_on_hold';
        $order_status_processing = 'epayco_processing';
        $order_status_processing_ = 'processing_test';
        $order_status_completed = 'epayco_completed';
        $order_status_cancelled = 'epayco_cancelled';
        $order_status_completed_ = 'completed_test';

    } else {
        $order_status_failed = 'epayco-failed';
        $order_status_on_hold = 'epayco-on-hold';
        $order_status_processing = 'epayco-processing';
        $order_status_processing_ = 'processing';
        $order_status_completed = 'epayco-completed';
        $order_status_cancelled = 'epayco-cancelled';
        $order_status_completed_ = 'completed';
    }
    ?>

    <style>
        .order-status.status-<?php echo sanitize_title( $order_status_failed); ?> {
            background: #eba3a3;
            color: #761919;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_on_hold); ?> {
            background: #f8dda7;
            color: #94660c;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_processing ); ?> {
            background: #c8d7e1;
            color: #2e4453;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_processing_ ); ?> {
            background: #c8d7e1;
            color: #2e4453;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_completed ); ?> {
            background: #d7f8a7;
            color: #0c942b;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_completed_ ); ?> {
            background: #d7f8a7;
            color: #0c942b;
        }

        .order-status.status-<?php echo sanitize_title( $order_status_cancelled); ?> {
            background: #eba3a3;
            color: #761919;
        }
    </style>

    <?php
}

function activate_subscription_epayco()
{
    global $wpdb;

    $table_subscription_epayco = $wpdb->prefix . 'epayco_subscription';
    $table_plan_epayco = $wpdb->prefix . 'epayco_plans';
    $table_setings_epayco = $wpdb->prefix . 'epayco_setings';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_subscription_epayco '") !== $table_subscription_epayco) {

        $sql = "CREATE TABLE $table_subscription_epayco (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		order_id INT(10) NOT NULL,
		ref_payco VARCHAR(60) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
        $sql2 = "CREATE TABLE $table_plan_epayco (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id INT(10) NOT NULL,
            plan_id VARCHAR(255) NOT NULL,
            amount decimal(19,4) NOT NULL,
            product_id bigint(20) NOT NULL,
            currency VARCHAR(60) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql3 = "CREATE TABLE IF NOT  EXISTS $table_setings_epayco (
            id INT NOT NULL AUTO_INCREMENT,
            id_payco TEXT NULL,
            customer_id TEXT NULL,
            token_id TEXT NULL,
            email TEXT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);
        dbDelta($sql2);
        dbDelta($sql3);
    }

    add_option('subscription_epayco_se_redirect', true);
}

function some_custom_checkout_field_update_order_meta($order_id)
{


    if (!empty($_POST['recipient_address'])) {
        add_post_meta($order_id, 'recipient_address', sanitize_text_field($_POST['recipient_address']));
    }
    if (!empty($_POST['recipient_phone_number'])) {
        update_post_meta($order_id, 'recipient phone number', sanitize_text_field($_POST['recipient_phone_number']));
    }

}

register_activation_hook(__FILE__, 'activate_subscription_epayco');