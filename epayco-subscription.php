<?php
/**
 * @since             1.0.0
 * @package           epayco-subscription
 *
 * @wordpress-plugin
 * Plugin Name:       ePayco WooCommerce  Suscripction
 * Description:       Plugin ePayco WooCommerce.
 * Version:           5.0.x
 * Author:            ePayco
 * Author URI:
 *Lice
 * Domain Path:       /languages
 */

if (!defined( 'ABSPATH' )) exit;

if(!defined('EPAYCO_SUBSCRIPTION_SE_VERSION')){
    define('EPAYCO_SUBSCRIPTION_SE_VERSION', '3.0.1');
}


add_action('plugins_loaded','epayco_subscription_init',0);

function epayco_subscription_init(){
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }
    require_once(dirname(__FILE__) . '/epayco-settings.php');
    $plugin = new Epayco_Subscription_Config(__FILE__, EPAYCO_SUBSCRIPTION_SE_VERSION, 'epayco-subscription');
    $plugin->run_epayco();
    if ( get_option( 'subscription_epayco_se_redirect', false ) ) {
        delete_option( 'subscription_epayco_se_redirect' );
        wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=epayco-subscription' ) );
        //page=wc-settings&tab=checkout&section=epayco-subscription
    }
}

function activate_subscription_epayco_se(){
    global $wpdb;

    $table_subscription_epayco = $wpdb->prefix . 'epayco_subscription';
    $table_plan_epayco = $wpdb->prefix . 'epayco_plans';
    $table_setings_epayco = $wpdb->prefix . 'epayco_setings';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_subscription_epayco '" ) !== $table_subscription_epayco ) {

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

        dbDelta( $sql );
        dbDelta( $sql2 );
        dbDelta( $sql3 );
    }

    add_option( 'subscription_epayco_se_redirect', true );
}

register_activation_hook( __FILE__, 'activate_subscription_epayco_se' );