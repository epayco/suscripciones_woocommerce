<?php
return array(
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
    'epayco_endorder_state' => array(
        'title' => __('Estado Final del Pedido'),
        'type' => 'select',
        'css' => 'line-height: inherit',
        'description' => __('Seleccione el estado del pedido que se aplicar谩 a la hora de aceptar y confirmar el pago de la orden'),
        'options' => array(
            'epayco-processing' => "ePayco Procesando Pago",
            "epayco-completed" => "ePayco Pago Completado",
            'processing' => "Procesando",
            "completed" => "Completado"
        ),
    ),
);

