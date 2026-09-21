<?php

if (!defined('ABSPATH')) {
    exit;
}

return array(

    
    'enabled' => array(
        'title' => __('Habilitar/Deshabilitar', 'suscripciones_woocommerce'),
        'type' => 'checkbox',
        'label' => __('Habilitar ePayco Checkout Suscripción', 'suscripciones_woocommerce'),
        'default' => 'yes'
    ),
    'epayco_title' => array(
        'title' => __('Titulo', 'suscripciones_woocommerce'),
        'type' => 'text',
      'description' => __('Corresponde al título que los usuarios visualizan en el checkout', 'suscripciones_woocommerce'),
       'default' => __('Titulo', 'suscripciones_woocommerce'),
        'desc_tip' => true,
    ),
    'shop_name' => array(
        'title' => __('Nombre del comercio', 'suscripciones_woocommerce'),
        'type' => 'text',
        'label' => __('Corresponde al nombre de la tienda que los usuarios visualizan en el checkout', 'suscripciones_woocommerce'),
        'default' => __('Nombre de la tienda', 'suscripciones_woocommerce'),
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __('Description', 'suscripciones_woocommerce'),
        'type' => 'textarea',
        'label' => __('Corresponde a la descripción de la tienda que los usuarios visualizan en el checkout', 'suscripciones_woocommerce'),
        'desc_tip' => true,
    ),
    'environment' => array(
        'title' => __('Modo', 'suscripciones_woocommerce'),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'description' => __('mode prueba/producción', 'suscripciones_woocommerce'),
        'desc_tip' => true,
        'default' => true,
        'options' => array(
            false => __('Production', 'suscripciones_woocommerce'),
            true => __('Test', 'suscripciones_woocommerce'),
        ),
    ),
    'custIdCliente' => array(
        'title' => __('P_CUST_ID_CLIENTE', 'suscripciones_woocommerce'),
        'type' => 'text',
        'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'suscripciones_woocommerce'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'pKey' => array(
        'title' => __('P_KEY', 'suscripciones_woocommerce'),
        'type' => 'text',
        'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'suscripciones_woocommerce'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'apiKey' => array(
        'title' => __('PUBLIC_KEY', 'suscripciones_woocommerce'),
        'type' => 'text',
        'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'suscripciones_woocommerce'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'privateKey' => array(
        'title' => __('PRIVATE_KEY', 'suscripciones_woocommerce'),
        'type' => 'password',
        'description' => __('La encuentra en el panel de ePayco, integraciones, Llaves API', 'suscripciones_woocommerce'),
        'default' => '',
        'desc_tip' => true,
        'placeholder' => ''
    ),
    'epayco_endorder_state' => array(
        'title' => __('Estado Final del Pedido', 'suscripciones_woocommerce'),
        'type' => 'select',
        'css' => 'line-height: inherit',
        'description' => __('Seleccione el estado del pedido que se aplicaría a la hora de aceptar y confirmar el pago de la orden', 'suscripciones_woocommerce'),
        'options' => array(
            'epayco-processing' => "ePayco Procesando Pago",
            "epayco-completed" => "ePayco Pago Completado",
            'processing' => "Procesando",
            "completed" => "Completado"
        ),
    ),
);