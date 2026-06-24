<?php

/**
 * @var string $style
 * @var string $general
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
 *  @var string $epaycocheckout
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
    echo '<link rel="stylesheet" type="text/css" id="movil_header" href="' . esc_html($style) . '">';

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link rel="stylesheet" type="text/css" href="' . esc_html($general) . '">';

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link rel="stylesheet" id="cardjsmincss" type="text/css" href="' . esc_html($card_style) . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet

    echo '<link rel="stylesheet" type="text/css" href="' . esc_html($cardsjscss) . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    ?>
</head>


<body>
    <div class="" id="movil_mainContainer" style="top:0px">
        <section class="modal-container">
            <style>
                .form-container .icon {
                    color: #3582b7 !important;
                    width: 20px !important;
                }

                .button-container .pay-type {
                    border: 1px solid #3582b7;
                }

                .button-container .pay-type .icon {
                    color: #3582b7;
                }

                .cont-btn {
                    padding: 8px;
                    background-color: #f3f3f3;
                    border-radius: 0 0 10px 10px;
                    text-align: center;
                }
            </style>

            <?php
            // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template local variables defined within this file and passed from controller
            if (strtoupper($lang) == 'ES') {
  
                $loader = 'https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/loader.png';
                $button = 'https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/botonPagarEpayco.png';
                $title = 'Cargando métodos de pago';
                $subtitle = 'Si no se cargan automáticamente, haz click en el botón "Pagar con ePayco"';
                $processing = 'Procesando Pago...';
                $name = 'Nombre';
                $cardNumber = 'Tarjeta';
                $expiry = 'Vence';
                $cvc = 'CVV';
                $infoCard = 'Información de la tarjeta';
                $exit = 'Salir';
                $save = 'Guardar';
                $cancel = '¿Estás seguro que deseas cancelar esta transacción?';
                $cancelTransaction = 'Cancelar Transacción';
                $expiredSession = 'Tu sesión ha expirado por inactividad';
                $cancelButton = 'Cancelar';
                $securePayment = 'Pago seguro por';
                $warning = 'Advertencia';
                $sessionExpires = 'Tu sesión vencerá en:';
                $seconds = 'Segundos';
                $continue = 'Continuar';
                $close = 'Cerrar';
                $return = 'Volver';
                $logout = 'Cerrar sesión';
                $clickClose = 'Haz click aquí para cerrar';
                $payButton = 'Pagar';
                $cardInfo = 'Ingrese su nombre ';
                $nameCardPlaceholder = 'Nombre completo';
                $cardInfoTitle = 'Información de la tarjeta';
                $pay = 'Pagar';
            } else {
                $loader = 'https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/loader.png';
                $button = 'https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/payBottonEpayco.png';
                $title = 'Loading payment methods';
                $subtitle = 'If they do not load automatically, click on the "Pay with ePayco" button';
                $processing = 'Processing Payment...';
                $name = 'Name';
                $cardNumber = 'Card';
                $expiry = 'Expiry';
                $cvc = 'CVV';
                $infoCard = 'Credit card information';
                $exit = 'Exit';
                $save = 'Save';
                $cancel = 'Are you sure you want to cancel this transaction?';
                $cancelTransaction = 'Cancel Transaction';
                $expiredSession = 'Your session has expired due to inactivity';
                $cancelButton = 'Cancel';
                $securePayment = 'Secure payment by';
                $warning = 'Warning';
                $sessionExpires = 'Your session will expire in:';
                $seconds = 'Seconds';
                $continue = 'Continue';
                $close = 'Close';
                $return = 'Return';
                $logout = 'Log out';
                $clickClose = 'Click close to return and start a new transaction.';
                $payButton = 'Pay';
                $cardInfoTitle = 'Credit card information';
                $cardInfo = 'Enter the data of your card';
                $nameCardPlaceholder = 'Full name';
                $pay = 'Pay';
            }
            // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            ?>
            <div class="loading-home op" style="display: none" id="loading_home">
                <!--<div class="circulo ">
                    <div class="lock">
                        <svg class="svg-inline--fa fa-lock fa-w-14" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                            <path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path>
                        </svg>
                    </div>
                    Procesando Pago...
                </div>-->
                <div class="loaderContainer">
                    <div class="loader">
                    </div>
                    <div style="position:absolute; top:38px">
                        <?php // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent 
                        ?>
                        <img src="<?php echo esc_html($loader); ?>" alt="Loading" />
                    </div>
                    <div class="textLoader"><?php echo esc_html($processing); ?></div>
                </div>
            </div>
            <section class="modal" hidden id="movil_modal" style="padding-top: 0rem !important;">
                <header class="animated fadeInDown" style="background-color: #3582b7 !important">
                    <div class="title-container  ">
                        <div class="logo-commerce">
                            <div class="logo-container">
                                <!--<?php echo wp_get_attachment_image(0, 'full'); ?>-->
                                <img
                                    loading="lazy"
                                    decoding="async"
                                    width="90"
                                    height="90"
                                    src="<?php echo esc_html($logo_comercio); ?>"
                                    class="attachment-full size-full"
                                    alt="" />
                            </div>

                        </div>
                        <div class="col title">
                            <div class="comercio-name " style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; white-space: normal; line-height: 1.2;">
                                <?php echo esc_html($product_name_); ?>
                            </div>
                            <div class="description-cont ">
                                <p><?php echo esc_html($shop_name); ?></p>
                                <strong class="monto">
                                    $<?php echo esc_html($amount); ?>
                                    <input type="hidden" value="<?php echo esc_html($amount); ?>" id="currentAmount">
                                    <span class="moneda"><?php echo esc_html($currency); ?></span>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="langaugeCancelt">
                        <div class="cancelPayment" id="cancel-t">
                            <svg class="svg-inline--fa fa-times fa-w-12" aria-hidden="true" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M323.1 441l53.9-53.9c9.4-9.4 9.4-24.5 0-33.9L279.8 256l97.2-97.2c9.4-9.4 9.4-24.5 0-33.9L323.1 71c-9.4-9.4-24.5-9.4-33.9 0L192 168.2 94.8 71c-9.4-9.4-24.5-9.4-33.9 0L7 124.9c-9.4 9.4-9.4 24.5 0 33.9l97.2 97.2L7 353.2c-9.4 9.4-9.4 24.5 0 33.9L60.9 441c9.4 9.4 24.5 9.4 33.9 0l97.2-97.2 97.2 97.2c9.3 9.3 24.5 9.3 33.9 0z"></path>
                            </svg><!-- <i class="fa fa-times"></i> -->
                        </div>
                        <!-- <div class="language-switch">
                            <a class="dn set-lang pointer l-es" data-lang="es" id="data_lang_es">ES</a>
                            <a class=" set-lang pointer l-en" data-lang="en" id="data_lang_en">EN</a>
                        </div> -->
                    </div>
                    <div id="email-container" class="email-container active">
                        <?php echo esc_html($email_billing); ?>
                        <div class="container-acvive-email " style="display: none;">
                            <div class="back-button">
                                <svg class="svg-inline--fa fa-angle-left fa-w-8" aria-hidden="true" data-prefix="fa" data-icon="angle-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                                    <path fill="currentColor" d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path>
                                </svg><!-- <button class="fa fa-angle-left" ></button> -->
                            </div>
                            <div class="volverSalir">
                                <p class="email ">&nbsp;&nbsp;jhon.doe@epayco.com</p>
                                <button class="log-out " onclick="goBack();">
                                    <span class="logout-text"><?php echo esc_html($logout); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </header>
                <section class="content animated zoomIn " style="
                    background-color: white; padding-top: 0rem;">
                    <div id="content-errors"></div>
                    <form id="form-action" method="post" novalidate="" action="<?php echo esc_html($redirect_url); ?>">
                        <div class="step step-tdc main-steps active" data-group="tdc" active="" style="margin: 0px;">
                            <div class="step-container">
                                <div class="step-form">
                                    <!-- Name -->
                                    <div class="form-container extra-label">
                                        <!--<i class="fas fa-user icon" style="font-size: 1.5em; color: #000;"></i>-->
                                        <svg class="svg-inline--fa fa-user fa-w-16 icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path fill="#2a7ab7" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z" />
                                        </svg>
                                        <div class="label-container">
                                            <label for="card" id="label_name_es" style="display:table-cell"><?php echo esc_html($name); ?></label>
                                        </div>
                                        <div class="input-container">
                                            <input type="text" name="name" placeholder="<?php echo esc_html($nameCardPlaceholder); ?>" value="<?php echo esc_html($name_billing) ?>" style="margin-left: 6px; text-align: left;">
                                        </div>
                                    </div>

                                    <div class="form-container">
                                        <svg class="svg-inline--fa fa-credit-card fa-w-18 icon" aria-hidden="true" data-prefix="fa" data-icon="credit-card" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M0 432c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V256H0v176zm192-68c0-6.6 5.4-12 12-12h136c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H204c-6.6 0-12-5.4-12-12v-40zm-128 0c0-6.6 5.4-12 12-12h72c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM576 80v48H0V80c0-26.5 21.5-48 48-48h480c26.5 0 48 21.5 48 48z"></path>
                                        </svg><!-- <i class="icon fa fa-credit-card"></i> -->
                                        <div class="label-container ">
                                            <label for="card" id="label_card_es" style="display:table-cell"><?php echo esc_html($cardNumber); ?></label>
                                        </div>
                                        <div class="input-container">
                                            <div class="card-jss" data-icon-colour="#158CBA">
                                                <div class="card-number2-wrapper">
                                                    <input class="card-number2" id="the-card-number-element"
                                                        data-epayco="card[number]" required="" name="card-number2"
                                                        placeholder="**** **** **** ****" type="tel" maxlength="19"
                                                        x-autocompletetype="cc-number"
                                                        autocompletetype="cc-number"
                                                        autocorrect="off" spellcheck="off"
                                                        autocapitalize="off" style=" margin-left: 3px; text-align: left; padding-right: 20px !important;">
                                                </div>
                                            </div>
                                            <?php echo wp_get_attachment_image(1, 'full', false, ['class' => 'img-card', 'id' => 'logo_franchise']); ?>
                                            <input type="hidden" name="valid_franchise" value="false">
                                        </div>
                                    </div>
                                    <!-- End Name -->
                                    <div class="dosCampos">
                                        <!-- Expiry Date -->
                                        <div class="form-container dateYex fecha-exp">
                                            <svg class="svg-inline--fa fa-calendar fa-w-14 icon" aria-hidden="true" data-prefix="fa" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                                                <path fill="currentColor" d="M12 192h424c6.6 0 12 5.4 12 12v260c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V204c0-6.6 5.4-12 12-12zm436-44v-36c0-26.5-21.5-48-48-48h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v36c0 6.6 5.4 12 12 12h424c6.6 0 12-5.4 12-12z"></path>
                                            </svg><!-- <i class="icon fa fa-calendar "></i> -->
                                            <div class="label-cvv-container RO">
                                                <label for="month-value" id="label_expiry_es" style="display:table-cell"><?php echo esc_html($expiry); ?></label>
                                            </div>
                                            <div class="input-expiry-container" style="display: flex !important; gap: 13px !important; flex-wrap: nowrap !important; align-items: center !important; width:50% !important;">
                                                <input type="number" name="month" id="month-value" placeholder="MM" maxlength="2" autocomplete="off" data-epayco="card[exp_month]" required style="width: 50% !important; flex: 0 0 calc(50% - 9.5px) !important; text-align: left;     margin-left: 13px;">
                                                <span style="display: flex !important; align-items: center !important; flex: 0 0 auto !important; color: gray; margin-left: -6px;">/</span>
                                                <input type="number" name="year" id="year-value" placeholder="YYYY" maxlength="4" autocomplete="off" data-epayco="card[exp_year]" required style="width: 50% !important; flex: 0 0 calc(50% - -4.5px) !important; text-align: left;">
                                            </div>
                                        </div>
                                        <!-- End Expiry Date -->
                                        <!-- Card Secret Code -->
                                        <div class="form-container dateYex  text-center line code-sec">
                                            <div class="label-cvv-container " style="display: block;">
                                                <svg class="svg-inline--fa fa-lock fa-w-14 icon" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                                                    <path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path>
                                                </svg><!-- <i class="fa fa-lock icon"></i> -->
                                                <label for="card_cvc"><?php echo esc_html($cvc); ?></label>
                                            </div>
                                            <div class="input-cvv-container">
                                                <input type="password" placeholder="***" maxlength="4" name="cvc" id="card_cvc" autocomplete="off" data-epayco="card[cvc]" style="border-color: transparent !important;box-shadow: none !important; text-align: left;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </section>
        </section>
        <footer class="footer-buttons" hidden id="movil_footer">
            <div class="button-actions" style="display: none;">
                <button class="action-oneclick cancel-oneclick" id="cancel-d" style="background-color: #D8D8D8"><?php echo esc_html($cancelButton); ?></button>
                <button class="action-oneclick save-oneclik"><?php echo esc_html($save); ?></button>
            </div>
            <button id="continue-tdc" class="continue-container text-center btnpay" style="background-color: #3582b7;" type="submit" form="form-action">
                <?php echo esc_html($payButton); ?>
                <!--<svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg="">
                    <path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path>
                </svg>--><!-- <i class="fas fa-angle-right"></i> -->
            </button>
            <div class="brand-footer">
                <p style="color:#1C0E49">
                    <svg class="svg-inline--fa fa-lock fa-w-14 secure" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                        <path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path>
                    </svg><!-- <i class="fa fa-lock secure"></i> --> <?php echo esc_html($securePayment); ?>
                </p>
                <?php echo wp_get_attachment_image(2, 'full', false, ['id' => 'logo_epayco', 'alt' => 'ePayco Logo', 'height' => '15px']); ?>

            </div>
        </footer>

        <div class="cancelT-modal dn" id="cancelT_modal" style="display:none">
            <div class="ventana dn">
                <div class="icono">

                </div>
                <p><?php echo esc_html($cancel); ?></p>
                <div class="acciones">
                    <button id="regresa-t"><?php echo esc_html($return); ?></button>
                    <button id="cancel-transaction"><?php echo esc_html($cancelTransaction); ?></button>
                </div>
            </div>
        </div>
        <div class="modal-expiration-time dn" id="mdlInactivityTime" style="display:none">
            <div class="ventana dn" id="mdlInactivityTimeBody">
                <div class="mdl-expiration-time">
                    <p class="mdl-expiration-time-title"><?php echo esc_html($warning); ?></p>
                    <p class="mdl-expiration-time-content padding-10">
                        <?php echo esc_html($sessionExpires); ?>
                    </p>
                    <div class="text-center">
                        <span class="spinner"></span>
                        <h1 id="counterInactivity">45</h1>
                        <p class="mdl-expiration-time-content-time"><?php echo esc_html($seconds); ?></p>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-block"><?php echo esc_html($continue); ?></button>
            </div>
        </div>
        <div class="modal-expiration-time  dn" id="mdlTimeExpired" style="display:none">
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
                <p class="mdl-expiration-time-title"><?php echo esc_html($expiredSession); ?></p>
                <p class="mdl-expiration-time-content text-center">
                    <?php echo esc_html($clickClose); ?>
                </p>
                <button type="button" class="btn btn-primary btn-block" id="btnMdlTimeExpired"><?php echo esc_html($close); ?></button>
            </div>
        </div>
    </div>

    <!-- Checkout Desktop -->
    <div id="p_c" hidden="true"><?php echo esc_html($apiKey); ?></div>
    <div id="p_p" hidden="true"><?php echo esc_html($privateKey); ?></div>
    <div id="lang_epayco" hidden="true"><?php echo esc_html($lang); ?></div>
    <div class="loader-container">
        <div class="loading"></div>
    </div>

    <p style="text-align: center;" class="epayco-title" id="epayco_title">
        <span class="animated-points"><?php echo esc_html($title); ?></span>
        <br>
        <small class="epayco-subtitle"> <?php echo esc_html($subtitle); ?></small>
    </p>
    <center>


        <button data-modal-target="#centered" id="button_epayco" style="
                  background-image: url(<?php echo esc_url($button); ?>);
                  background-repeat:no-repeat;
                  background-size: contain;
                  height:39px;
                  width:300px;
                  background-position:center;
                  border-color: #28303d00;
                  border-radius: 6px;
                  background-color: #28303d00;
                  cursor: pointer;
                  ">
        </button>
    </center>
    <div class="middle-xs bg_onpage porcentbody m-0" style="margin: 0">
        <div class="centered" id="centered">
            <div class="loadoverlay" id="loadoverlay">
                <!--<div class="loader loadimg"></div>
                <i class="fa fa-lock fa-lg loadshield2" style="color:gray;position:fixed;" aria-hidden="true"></i>
                <span class="loadtext">Procesando Pago</span> -->
                <div class="loaderContainer">
                    <div class="loader">
                    </div>
                    <div style="position:absolute; top:38px">
                        <?php // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent 
                        ?>
                        <img src="<?php echo esc_html($loader); ?>" alt="Loading" />
                    </div>
                    <div class="textLoader"><?php echo esc_html($processing); ?></div>
                </div>
            </div>
            <div class="onpage relative" id="web-checkout-content">
                <div class="header-modal hidden-print">
                    <div class="logo-comercio">
                        <img
                            loading="lazy"
                            decoding="async"
                            width="90" height="90"
                            src="<?php echo esc_html($logo_comercio); ?>"
                            class="img-card"
                            alt=""
                            id="image-safari"
                            style="width: 90%;">
                        <!--<?php echo wp_get_attachment_image(0, 'full', false, ['class' => 'img-card', 'id' => 'image-safari', 'style' => 'width: 90%;']); ?> -->
                    </div>
                    <div class="header-modal-text">
                        <h1 style="font-size: 17px;margin-bottom:3px;height: auto;margin: 0.2rem  1.5rem !important;color: black; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; white-space: normal; line-height: 1.2;"><?php echo esc_html($product_name_); ?></h1>
                        <h2 style="font-size: 12px;margin-bottom:3px;color: #848484;margin: 0.2rem 1.5rem !important;  font-family: Poppins"><?php echo esc_html($shop_name) ?></h2>
                        <h1 style="font-size: 17px;margin-bottom:3px;height: 20px;margin: 0.2rem  1.5rem !important;color: #3582b7;font-weight: 900;">$<?php echo esc_html($amount); ?> <?php echo esc_html($currency) ?></h1>
                    </div>
                    <div class="color-exit hidden-print closeIcon" id="closeModal">
                        <div data-close-button class="icon-cancel">&times;</div>
                    </div>
                </div>
                <div class="body-modal fix-top-safari">
                    <div class="bar-option hidden-print">
                        <div class="dropdown select-pais pointer" id="sample">
                            <dd>
                                <ul id="foo"></ul>
                            </dd>
                            <p style="position: absolute !important;">
                                <a class="dropdown-toggle blockd" style="background: none; border: none;" type="button" data-toggle="dropdown">
                                    <div class="flag flag-icon-background flag-icon-co" data-toggle="dropdown" id="flag"></div>

                                    <div class="estilosContryName" id="countryName">Colombia</div>
                                    <i class="fa fa-caret-down caret-languaje" id="icon-flecha" aria-hidden="true"></i>

                                </a>
                            </p>
                            <ul class="dropdown-menu" id="dropdown-countries"></ul>
                        </div>
                        <p style="display: flex; margin: 0px; display: none;"><span id="result" hidden><?php echo esc_html($str_countryCode) ?></span><a id="esButton" class="languaje pointer" data-es-button data-language="es">ES</a><a id="enButton" class="languaje pointer" data-en-button data-language="en">EN</a></p>
                    </div>
                    <div class="wc scroll-content">
                        <div class="separate">
                            <h2 class="title-body" style="text-align: left;width: calc(100% - 1.9em);
                                margin: 0 auto 1em; font-size: 16px; font-weight: 500; color: #3a3a3a;font-family: 'Poppins' " id="info_es"><?php echo esc_html($infoCard); ?>
                            </h2>
                            <h2 class="title-body" style="text-align: left;width: calc(100% - 1.5em);
                                margin: 0 auto 1em; font-size: 16px; font-weight: 500; color: #3a3a3a;font-family: 'Poppins'" id="info_en"><?php echo esc_html($infoCard); ?>
                            </h2>
                        </div>
                        <div class="menu-select">
                            <form id="token-credit" action=" <?php echo esc_html($redirect_url) ?>  " method="post">
                                <div class="card-js" data-icon-colour="#158CBA">
                                    <div class="input-form">
                                        <span class="icon-user color icon-input"><i class="fas fa-user" style="margin-left: -2px;"></i></span>
                                        <input class="name" id="the-card-name-element" data-epayco="card[name]" placeholder="<?php echo esc_html($nameCardPlaceholder); ?>" name="name" required value="<?php echo esc_html($name_billing) ?>">
                                    </div>
                                </div>
                                <div class="card-js" data-icon-colour="#158CBA">
                                    <div class="input-form" style="position: relative;">
                                        <span class="icon-credit-card color icon-input"><i class="far fa-credit-card" style="margin-left: -5px;"></i></span>
                                        <input class="card-number my-custom-class" data-epayco="card[number]" required id="the-card-number-element" name="card-number2" placeholder="**** **** **** ****" type="tel" maxlength="19" x-autocompletetype="cc-number" autocompletetype="cc-number" autocorrect="off" spellcheck="off" autocapitalize="off" style="padding-right: 40px;">

                                        <?php echo wp_get_attachment_image(1, 'full', false, ['class' => 'img-card', 'id' => 'logo_franchise', 'style' => 'display: block;position: absolute;right: 12px;top: 41.5%;transform: translateY(-50%);width: 40px;']); ?>

                                    </div>
                                </div>

                                <div class="input-form" hidden>
                                    - <span class="icon-credit-card color icon-input"><i class="fas fa-envelope"></i></span>
                                    <input type="tel" class="binding-input inspectletIgnore" name="card_email" autocomplete="off" hidden="true" data-epayco="card[email]" value="<?php echo esc_html($email_billing) ?>">
                                </div>
                                <div class="select-option bordergray vencimiento" style="float:left" id="expiration">
                                    <div class="input-form full-width noborder monthcredit nomargin">
                                        <span class="icon-date_range color icon-select"><i class="far fa-calendar-alt"></i></span>
                                        <input type="number" class="binding-input inspectletIgnore" id="month-value" name="month" placeholder="MM" maxlength="2" autocomplete="off" data-epayco="card[exp_month]" required>
                                    </div>
                                    <div class="" style="float:left; width:5%; margin:0; text-align:center; line-height: 40px; height: 37px; background-color: white; color:#a3a3a3;">/</div>
                                    <div class="input-form full-width normalinput noborder yearcredit nomargin">
                                        <input type="number" name="year" id="year-value" placeholder="  YYYY" maxlength="4" autocomplete="off" data-epayco="card[exp_year]" required>
                                    </div>
                                </div>
                                <div class="input-form normalinput cvv_style" id="cvc_">
                                    <input type="password" placeholder="CVC" class="nomargin binding-input" name="cvc" id="card_cvc" autocomplete="off" maxlength="4" data-epayco="card[cvc]">
                                    <i class="fa color fa-question-circle pointer" aria-hidden="true" style="right: 10px;padding: 0;top: -5px;font-size: 24px !important;"></i>
                                </div>
                                <br>
                                <div class="clearfix"></div>
                                <button class="call_action bgcolor white_font pointer load hidden-print" id="send-form">
                                    <h2 style="color: white; font-family: 'Poppins'" id="pagar_es"><?php echo esc_html($pay); ?></h2>
                                    <h2 style="color: white; font-family: 'Poppins'" id="pagar_en"><?php echo esc_html($pay); ?></h2>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-modal hidden-print" id="footer-animated">
                <p id="pagar_logo_es">
                    <i class="fa fa-lock fa-lg" style="color: #2ECC71" aria-hidden="true"></i><?php echo esc_html($securePayment); ?>
                    <?php echo wp_get_attachment_image(4, 'full', false, ['height' => '20', 'style' => 'display: inline;']); ?>
                </p>
                <p id="pagar_logo_en">
                    <i class="fa fa-lock fa-lg" style="color: #2ECC71" aria-hidden="true"></i><?php echo esc_html($securePayment); ?>
                    <?php echo wp_get_attachment_image(4, 'full', false, ['height' => '20', 'style' => 'display: inline;']); ?>
                </p>
            </div>
        </div>
        <div id="overlay"></div>
    </div>
    <div id="style_min" hidden><?php echo esc_html($stylemin) ?></div>
    </div>

    <script id="movil" hidden>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JavaScript generated internally by the plugin.
        echo $appjs;
        ?>
    </script>
</body>


<?php
// Ensure WordPress functions are available
if (function_exists('wp_enqueue_script') && function_exists('esc_url')) {
    // Enqueue the script properly with a version parameter
    wp_enqueue_script('epayco-index-js', esc_url($indexjs), array(), '1.0.0', true);
}
?>
<?php
// Ensure WordPress functions are available
if (function_exists('wp_enqueue_script') && function_exists('esc_url')) {
    // Enqueue the script properly with a version parameter
    wp_enqueue_script('epayco-checkout-js', esc_url($epaycocheckout), array(), '1.0.0', true);
}
?>