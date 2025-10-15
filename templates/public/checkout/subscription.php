<?php

/**
 * @var string $style
 * @var string $card_style
 * @var string $cardsjscss
 * @var string $logo_comercio
 * @var string $amount
 * @var string $epayco
 * @var string $shop_name
 * @var string $product_name_
 * @var string $currency
 * @var string $email_billing
 * @var string $name_billing
 * @var string $redirect_url
 * @var string $str_countryCode
 * @var string $stylemin
 * @var string $apiKey
 * @var string $privateKey
 * @var string $lang
 * @var string $card_unmin
 * @var string $epaycojs
 * @var string $indexjs
 * @var string $appjs
 * @var string $cardsjs
 * @var string $epaycocheckout
 * @var string $css_checkout
 * @var string $js_suscription
 * @see \EpaycoSubscription\Woocommerce\Gateways\EpaycoSuscription
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>


<head>
    <?php
    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link rel="stylesheet" type="text/css" href="' . esc_html($css_checkout) . '">';
    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link rel="stylesheet" id="cardjsmincss" type="text/css" href="' . esc_html($card_style) . '">';
    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link rel="stylesheet" type="text/css" href="' . esc_html($cardsjscss) . '">';
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>

<body class="lang-es">
    <section class="modal-container">
        <section class="content animated zoomIn content-form">
            <div id="content-errors"></div>
            <form id="form-action" method="post" novalidate="" action="<?php echo esc_html($redirect_url); ?>">
            </form>
        </section>
    </section>
    <div class="cancelT-modal dn display-none" id="cancelT_modal">
        <div class="ventana dn">
            <div class="icono">
                <svg class="svg-inline--fa fa-exclamation-circle fa-w-16" aria-hidden="true" data-prefix="fa" data-icon="exclamation-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path>
                </svg>
            </div>
            <p>¿Está seguro de Cancelar esta Transacción?</p>
            <div class="acciones">
                <button id="regresa-t">Regresar</button>
                <button id="cancel-transaction">Cancelar Transacción</button>
            </div>
        </div>
    </div>
    <div class="modal-expiration-time dn display-none" id="mdlInactivityTime">
        <div class="ventana dn" id="mdlInactivityTimeBody">
            <div class="mdl-expiration-time">
                <p class="mdl-expiration-time-title">Cuidado</p>
                <p class="mdl-expiration-time-content padding-10">
                    Su sesión va a expirar en:
                </p>
                <div class="text-center">
                    <span class="spinner"></span>
                    <h1 id="counterInactivity">45</h1>
                    <p class="mdl-expiration-time-content-time">Segundos</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-block">Continuar</button>
        </div>
    </div>
    <div class="modal-expiration-time dn display-none" id="mdlTimeExpired">
        <div class="ventana dn" id="mdlTimeExpiredBody">
            <div class="mdl-expiration-time">
                <div class="text-center">
                    <?php echo wp_get_attachment_image(3, 'full', false, [
                        'class' => 'img-65x65',
                        'alt' => 'icono-warning',
                        'style' => 'display: block; margin: auto; text-align: center;'
                    ]); ?>
                </div>
            </div>
            <p class="mdl-expiration-time-title">Su sesión ha expirado por inactividad</p>
            <p class="mdl-expiration-time-content text-center">
                De clic en cerrar para regresar e iniciar una nueva transacción.
            </p>
            <button type="button" class="btn btn-primary btn-block" id="btnMdlTimeExpired">Cerrar</button>
        </div>
    </div>



    <!-- Checkout Desktop -->
    <div id="p_c" hidden="true"><?php echo esc_html($apiKey); ?></div>
    <div id="p_p" hidden="true"><?php echo esc_html($privateKey); ?></div>
    <div id="lang_epayco" hidden="true"><?php echo esc_html($lang); ?></div>
    <div class="loader-container">
        <div class="loading"></div>
    </div>
    <p class="epayco-title epayco-title-center" id="epayco_title">
        <span class="animated-points">Cargando métodos de pago</span>
        <br>
        <small class="epayco-subtitle"> Si no se cargan automáticamente, de clic en el botón "Pagar con ePayco"</small>
    </p>
    <center>
        <button data-modal-target="#centered" id="button_epayco" class="button-epayco">
        </button>
    </center>
    <div class="middle-xs bg_onpage porcentbody m-0">
        <div class="centered" id="centered" style="position: relative;">
            <div class="loadoverlay" id="loadoverlay">
                <!--<div class="loader loadimg"></div>
                <i class="fa fa-lock fa-lg loadshield2" style="color:gray;position:fixed;" aria-hidden="true"></i>
                <span class="loadtext">Procesando Pago</span> -->
                <div class="loaderContainer">
                    <div class="loader">
                    </div>
                    <div class="loader-positioned">
                        <img src="https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/loader.png" alt="Loading" />
                    </div>
                    <div class="textLoader">Procesando Pago</div>
                </div>

                <div class="footer-modal hidden-print" id="footer-animated">
                    <p id="pagar_logo_es">
                        <span class="security-icon-green" style="font-size: 1.2em; vertical-align: middle;">&#128274;</span> Pago seguro por
                        <?php
                        // Usar plugins_url para generar URL pública correcta
                        $logo_epayco = plugins_url('assets/images/logo-epayco.png', dirname(dirname(dirname(__FILE__))));
                        ?>
                        <img src="<?php echo esc_url($logo_epayco); ?>" class="logo-epayco-footer" alt="icono tarjeta" />
                    </p>
                    <p id="pagar_logo_en">
                        <span style="color: #2ECC71; font-size: 1.2em; vertical-align: middle;">&#128274;</span> Secure payment by
                        <img src="<?php echo esc_url($logo_epayco); ?>" class="logo-epayco-footer" alt="icono tarjeta" />
                    </p>
                </div>
            </div>
            <div class="onpage relative" name="onpage-suscription" id="web-checkout-content" style="display: none;">
                <div class="form-header">
                    <div class="div-logo">
                        <div class="logo-container">
                            <div class="logo-img">
                                <img src="<?php echo esc_html($logo_comercio); ?>" alt="Logo" class="logo">
                            </div>
                        </div>
                        <div class="div-text">
                            <div class="text-title">
                                <?php echo esc_html($product_name_); ?>
                                <button type="button" class="toggleCvv-1 info-button">
                                    <span id="cvvIcon-1">
                                        <?php
                                        // Usar plugins_url para generar URL pública correcta
                                        $icon_info_url = plugins_url('assets/images/icon-info.png', dirname(dirname(dirname(__FILE__))));
                                        ?>
                                        <img id="openModal3" src="<?php echo esc_url($icon_info_url); ?>" />
                                    </span>
                                </button>
                            </div>
                            <div class="text-subtitle">
                              
                                <?php echo esc_html($shop_name); ?>
                            </div>

                        </div>
                        <div class="div-exit-lang">
                            <div class="color-exit hidden-print closeIcon" id="closeModal">
                                <div data-close-button class="icon-cancel icon-cancel-button">&times;</div>
                            </div>

                            <!-- Botón para abrir el modal de idioma -->
                            <button id="openModal" class="language-button">
                                <span id="languageText">ES</span>
                                <span class="language-button-dropdown">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" class="language-button-svg">
                                        <path d="M11.375 5.25L7 9.625L2.625 5.25" stroke="white" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </button>

                        </div>
                    </div>
                </div>

                <div class="body-modal fix-top-safari">
                    <div class="form-row-form">
                        <div class="wc scroll-content">
                            <div class="separate">
                                <h2 class="title-body title-body-card-info" id="info_es">
                                    <span>
                                        <?php
                                        // Usar plugins_url para generar URL pública correcta
                                        $icon_card = plugins_url('assets/images/CreditCard.png', dirname(dirname(dirname(__FILE__))));
                                        ?>
                                        <img src="<?php echo esc_url($icon_card); ?>" class="card-icon-title" alt="icono tarjeta" />
                                    </span>
                                    Tarjeta Crédito y Débito
                                </h2>
                                <h2 class="title-body title-body-card-info" id="info_en" style="display: none;">
                                    <span>
                                        <img src="<?php echo esc_url($icon_card); ?>" class="card-icon-title" alt="card icon" />
                                    </span>
                                    Credit and Debit Card
                                </h2>
                            </div>
                            <div class="menu-select">
                                <form id="token-credit" action=" <?php echo esc_html($redirect_url) ?>  " method="post">
                                    <div class="card-js" data-icon-colour="#158CBA">
                                        <div class="form-control">
                                            <input class="name" id="the-card-name-element" data-epayco="card[name]" name="name" required value="<?php echo esc_html($name_billing) ?>">
                                            <label for="nameInput" id="nameLabel_es">Nombre y Apellidos</label>
                                            <label for="nameInput" id="nameLabel_en" style="display: none;">Full Name</label>

                                        </div>
                                    </div>
                                    <div class="card-js" data-icon-colour="#158CBA">
                                        <div class="input-form input-form-relative">
                                            <div class="form-control">
                                                <input class="card-number my-custom-class" data-epayco="card[number]" required id="the-card-number-element" name="card-number2" placeholder="**** **** **** ****" type="tel" maxlength="19" x-autocompletetype="cc-number" autocompletetype="cc-number" autocorrect="off" spellcheck="off" autocapitalize="off">
                                                <label for="cardNumberInput" id="cardLabel_es">Número de tarjeta</label>
                                                <label for="cardNumberInput" id="cardLabel_en" style="display: none;">Card Number</label>
                                                <?php echo wp_get_attachment_image(1, 'full', false, ['class' => 'img-card img-card-franchise', 'id' => 'logo_franchise_2']); ?>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-form" hidden>
                                        - <span class="icon-credit-card color icon-input"><i class="fas fa-envelope"></i></span>
                                        <input type="tel" class="binding-input inspectletIgnore" name="card_email" autocomplete="off" hidden="true" data-epayco="card[email]" value="<?php echo esc_html($email_billing) ?>">
                                    </div>
                                    <div class="form-control">
                                        <div class="expiration-cvv-container">
                                            <div class="form-expiration form-expiration-flex">
                                                <input type="text" id="expInput" name="expInput" placeholder="MM/AA" maxlength="7" autocomplete="off" required>
                                                <label for="expInput" id="expLabel_es">Fecha expiración</label>
                                                <label for="expInput" id="expLabel_en" style="display: none;">Expiration Date</label>
                                                <input type="hidden" id="month-value" name="month" data-epayco="card[exp_month]">
                                                <input type="hidden" id="year-value" name="year" data-epayco="card[exp_year]">
                                            </div>
                                            <div class="input-form normalinput cvv_style cvv-container-flex" id="cvc_">
                                                <input type="password" placeholder="***" class="nomargin binding-input cvv-input-style" name="cvc" id="card_cvc" autocomplete="off" maxlength="4" data-epayco="card[cvc]">

                                                <button type="button" class="toggleCvv-1 toggle-cvv-button">
                                                    <span class="cvvIcon-info">
                                                        <!-- Icono ojo abierto (mostrar CVV) -->
                                                        <svg id="cvv-eye-open" xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none" class="cvv-eye-icon">
                                                            <path d="M13.4537 13.454C13.9637 12.944 14.2501 12.2524 14.2501 11.5313C14.2501 10.8101 13.9637 10.1185 13.4537 9.60855C12.9438 9.09861 12.2522 8.81213 11.531 8.81213C10.8098 8.81213 10.1182 9.09861 9.60829 9.60855C9.09835 10.1185 8.81187 10.8101 8.81187 11.5313C8.81187 12.2524 9.09835 12.944 9.60829 13.454C10.1182 13.9639 10.8098 14.2504 11.531 14.2504C12.2522 14.2504 12.9438 13.9639 13.4537 13.454Z" stroke="#BCBCBC" stroke-width="1.81275" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M2.88202 11.5313C4.03674 7.85411 7.47282 5.18665 11.5307 5.18665C15.5894 5.18665 19.0246 7.85411 20.1793 11.5313C19.0246 15.2085 15.5894 17.8759 11.5307 17.8759C7.47282 17.8759 4.03674 15.2085 2.88202 11.5313Z" stroke="#BCBCBC" stroke-width="1.81275" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <!-- Icono ojo cerrado (ocultar CVV) -->
                                                        <svg id="cvv-eye-closed" xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none" class="cvv-eye-hidden">
                                                            <path d="M13.4537 13.454C13.9637 12.944 14.2501 12.2524 14.2501 11.5313C14.2501 10.8101 13.9637 10.1185 13.4537 9.60855C12.9438 9.09861 12.2522 8.81213 11.531 8.81213C10.8098 8.81213 10.1182 9.09861 9.60829 9.60855C9.09835 10.1185 8.81187 10.8101 8.81187 11.5313C8.81187 12.2524 9.09835 12.944 9.60829 13.454C10.1182 13.9639 10.8098 14.2504 11.531 14.2504C12.2522 14.2504 12.9438 13.9639 13.4537 13.454Z" stroke="#BCBCBC" stroke-width="1.81275" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path d="M2.88202 11.5313C4.03674 7.85411 7.47282 5.18665 11.5307 5.18665C15.5894 5.18665 19.0246 7.85411 20.1793 11.5313C19.0246 15.2085 15.5894 17.8759 11.5307 17.8759C7.47282 17.8759 4.03674 15.2085 2.88202 11.5313Z" stroke="#BCBCBC" stroke-width="1.81275" stroke-linecap="round" stroke-linejoin="round" />
                                                            <!-- Puedes agregar una línea diagonal para el "ojo cerrado" si lo deseas -->
                                                            <line x1="5" y1="5" x2="18" y2="18" stroke="#BCBCBC" stroke-width="2" />
                                                        </svg>

                                                    </span>
                                                </button>
                                                <label for="cvvInput" class="cvv-label" id="cvvLabel_es">CVV</label>
                                                <label for="cvvInput" class="cvv-label" id="cvvLabel_en" style="display: none;">CVV</label>
                                            </div>
                                        </div>
                                    </div> <!-- CARD MINI -->

                                    <div class="card mini" id="cardContainer">
                                        <!-- Frente -->
                                        <div class="card-face card-front" id="cardFront">
                                            <div class="chipContainer">
                                                <div class="chip">
                                                    <div class="chip2"></div>
                                                </div>
                                            </div>
                                            <div class="brand brand-logo-position" id="logoFranquicia">
                                                <img src="" class="card-logo-front card-logo-front-style">
                                            </div>
                                            <div id="cardNumber" class="card-number card-number-style">XXXX XXXX XXXX XXXX</div>
                                            <div class="card-footer">
                                                <div>
                                                    <div class="card-name-label card-name-label-style" id="cardNameLabel_es">NOMBRE Y APELLIDOS</div>
                                                    <div class="card-name-label card-name-label-style" id="cardNameLabel_en" style="display: none;">FULL NAME</div>
                                                    <div id="cardName" class="card-name card-name-style"><?php echo esc_html($name_billing) ?></div>
                                                </div>
                                                <div class="card-exp-container">
                                                    <div class="card-exp-label card-exp-label-style" id="cardExpLabel_es">FECHA EXPIRACIÓN</div>
                                                    <div class="card-exp-label card-exp-label-style" id="cardExpLabel_en" style="display: none;">EXPIRY DATE</div>
                                                    <div id="cardExp" class="card-exp card-exp-style">MM/AA</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dorso -->

                                        <div class="card-face card-back" id="cardBack">
                                            <div class="black-strip"></div>
                                            <div class="black-line"></div>
                                            <div class="cvv-label-back">CVV</div>
                                            <div class="cvv-box" id="cvvBox">***</div>
                                            <?php
                                            $logo_card = plugins_url('assets/images/card-logo-sello.svg', dirname(dirname(dirname(__FILE__))));
                                            ?>
                                            <img src="<?php echo esc_url($logo_card); ?>" id="card-logo-back" />
                                        </div>

                                        <div id="brandLogoBack"></div>


                                    </div>
                                    <div class="form-footer">
                                        <div class="form-row-footer">
                                            <div class="form-row-footer-2">
                                                <div>
                                                    <div class="form-row-footer-3">
                                                        <div class="form-row-footer-4">
                                                            <span class="spam-total">$<?php echo esc_html($amount); ?></span>
                                                            <span class="spam-currency"> <?php echo esc_html($currency) ?></span>
                                                        </div>
                                                        <div id="openModal2" class="ver-detalle-wrap">
                                                            <a href="#" class="ver-detalle-link" id="verDetalle_es">Ver detalle</a>
                                                            <a href="#" class="ver-detalle-link" id="verDetalle_en" style="display: none;">View details</a>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="form-row-footer-5">
                                                    <!-- class="button-pay" -->
                                                    <button class="button-pay call_action bgcolor white_font pointer load hidden-print" id="send-form">
                                                        <h2 class="button-pay-text" id="pagar_es">Pagar</h2>
                                                        <h2 class="button-pay-text" id="pagar_en" style="display: none;">Pay</h2>
                                                    </button>
                                                    <div class="container-payment">
                                                        <span class="text-payment" id="securedBy_es">Protegido por</span>
                                                        <span class="text-payment" id="securedBy_en" style="display: none;">Secured by</span>
                                                        <?php
                                                        // Usar plugins_url para generar URL pública correcta
                                                        $logo_epayco2 = plugins_url('assets/images/logo-epayco.png', dirname(dirname(dirname(__FILE__))));
                                                        ?>
                                                        <img src="<?php echo esc_url($logo_epayco2); ?>" class="epayco-logo-style" alt="icono tarjeta" />
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <div class="clearfix"></div>
                                    </div>


                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal 1: Cambiar idioma -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Cambiar idioma</h2>
                    <span class="close">&times;</span>
                </div>
                <a id="enButton" class="languaje pointer" data-en-button data-language="en">Inglés</a>
                <a id="esButton" class="languaje pointer" data-es-button data-language="es">Español</a>
            </div>
        </div>

        <!-- Modal 2: Confirmar valor pedido-->
        <div id="myModal2" class="modal">
            <div class="modal-content-2">
                <div class="modal-header">
                    <h2>Detalle del total</h2>
                    <span class="close">&times;</span>
                </div>
                <p class="languaje-2 modal-total-display">
                    <span class="modal-total-label">Total a pagar</span>
                    <span class="modal-total-amount">$<?php echo esc_html($amount); ?> <?php echo esc_html($currency); ?></span>
                </p>
            </div>
        </div>

        <!-- Modal 3: Mensaje de éxito -->
        <div id="myModal3" class="modal">
            <div id="modal-content-3">
                <div class="modal-header">
                    <h2>Información</h2>
                    <span class="close">&times;</span>
                </div>
                <div class="contact-info">
                    <div class="info-item">
                        <span class="icon">📞</span>
                        <div>
                            <strong>Llámanos</strong><br>
                            <span class="contact-info-text">+57 3242127691</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">✉️</span>
                        <div>
                            <strong>Escríbenos</strong><br>
                            <span class="contact-info-text">comercial@somosgom.com</span>
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="icon">🌐</span>
                        <div>
                            <strong>Visítanos</strong><br>
                            <span class="contact-info-text">somosgom.com</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>
    <div id="overlay"></div>
    </div>

    </div>

</body>


<!-- <?php
        // Ensure WordPress functions are available
        if (function_exists('wp_enqueue_script') && function_exists('esc_url')) {
            // Enqueue the script properly with a version parameter
            wp_enqueue_script('epayco-index-js', esc_url($indexjs), array(), '1.0.0', true);
        }
        ?> -->
<?php
// Ensure WordPress functions are available
if (function_exists('wp_enqueue_script') && function_exists('esc_url')) {
    // Enqueue the script properly with a version parameter
    wp_enqueue_script('epayco-checkout-js', esc_url($epaycocheckout), array(), '1.0.0', true);
}
?>

<?php
// Ensure WordPress functions are available
if (function_exists('wp_enqueue_script') && function_exists('esc_url')) {
    // Enqueue the script properly with a version parameter
    wp_enqueue_script('epayco-checkout-js', esc_url($js_suscription), array(), '1.0.0', true);
}
?>