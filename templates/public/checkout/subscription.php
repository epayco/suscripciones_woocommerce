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
 * @var string $doc_number
 * @var string $type_document
 * @var string $lang
 * @var string $invoice
 * @var bool   $environment
 * @var string $city
 * @var string $address
 * @var string $phone_billing
 * @var string $tax
 * @var string $ico
 * @var string $confirm_url
 * @var string $detalleOrden
 * @var string $checkoutNew

 * @see \EpaycoSubscription\Woocommerce\Gateways\EpaycoSuscription
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>

<head>

    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        #content-errors ul li {
            list-style: none
        }
    </style>
</head>

<body>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
        <p class="epayco-title" id="epayco_title">
            <span class="animated-points">Cargando métodos de pago</span>
            <br>
            <small class="epayco-subtitle"> Si no se cargan automáticamente, de clic en el botón "Pagar con ePayco"</small>
        </p>
        <div id="content-errors"></div>
        <div class="input-form" hidden>
            - <span class="icon-credit-card color icon-input"><i class="fas fa-envelope"></i></span>
            <input type="tel" class="binding-input inspectletIgnore" name="card_email" autocomplete="off" hidden="true" data-epayco="card[email]" value="<?php echo esc_html($email_billing) ?>">
        </div>
        <div id="epayco-checkout-plugin"></div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.ePaycoCheckoutPlugin) {
                // Intercepta y envía los datos del checkout al webhook
                const originalXHR = window.XMLHttpRequest;
                window.XMLHttpRequest = function() {
                    const xhr = new originalXHR();
                    const originalSend = xhr.send;

                    xhr.send = function(...args) {
                        if (xhr.responseURL && xhr.responseURL.includes('WC_WooEpaycoSuscription_Gateway')) {
                            if (args[0]) {
                                try {
                                    if (args[0] instanceof FormData) {
                                        let formDataObj = {};
                                        for (let [key, value] of args[0].entries()) {
                                            formDataObj[key] = value;
                                        }
                                        fetch('<?php echo $redirect_url; ?>', {
                                            method: 'POST',
                                            body: JSON.stringify(formDataObj),
                                            headers: { 'Content-Type': 'application/json' }
                                        });
                                    }
                                    else if (typeof args[0] === 'string') {
                                        let datosParaEnviar = null;
                                        try {
                                            const decoded = atob(args[0]);
                                            try {
                                                const jsonData = JSON.parse(decoded);
                                                datosParaEnviar = jsonData;
                                            } catch (parseError) {
                                                datosParaEnviar = {decoded_data: decoded};
                                            }
                                        } catch (b64Error) {
                                            try {
                                                datosParaEnviar = JSON.parse(args[0]);
                                            } catch (jsonError) {
                                                datosParaEnviar = {raw_data: args[0]};
                                            }
                                        }
                                        if (datosParaEnviar) {
                                            fetch('<?php echo $redirect_url; ?>', {
                                                method: 'POST',
                                                body: JSON.stringify(datosParaEnviar),
                                                headers: { 'Content-Type': 'application/json' }
                                            });
                                        }
                                    }
                                } catch (e) {
                                    // Error procesando datos enviados
                                }
                            }
                        }

                        xhr.addEventListener('load', function() {
                            // Redirige si la respuesta del callback contiene return_url
                            if (xhr.responseURL && (xhr.responseURL.includes('WC_WooEpaycoSuscription_Gateway') || xhr.responseURL.includes('wc-api'))) {
                                try {
                                    const responseData = JSON.parse(xhr.responseText);
                                    if (responseData.return_url) {
                                        setTimeout(() => {
                                            window.top.location.href = responseData.return_url;
                                        }, 2000);
                                        setTimeout(() => {
                                            const closeButtons = document.querySelectorAll('[class*="close"], [class*="modal-close"], button[aria-label*="close"]');
                                            closeButtons.forEach(btn => {
                                                btn.click();
                                            });
                                        }, 1000);
                                    }
                                } catch (e) {
                                    // Error parsing response
                                }
                            }
                        });
                        return originalSend.call(this, ...args);
                    };

                    return xhr;
                };

                ePaycoCheckoutPlugin.configure({
                    publicKey: "<?php echo htmlspecialchars($apiKey); ?>",
                    test: <?php echo $environment ? 'true' : 'false'; ?>
                });

                <?php
                    $amount_float = round((float) $amount, 0);
                    $tax_float = round((float) $tax, 0);
                    $amount_formatted = number_format($amount_float, 0, '', '.');
                    $tax_formatted = number_format($tax_float, 0, '', '.');
                ?>
                ePaycoCheckoutPlugin.open({
                    invoice: <?php echo htmlspecialchars($invoice); ?>,
                    amount: "<?php echo $amount_formatted; ?>",
                    currency: "<?php echo htmlspecialchars($currency); ?>",
                    description: "<?php echo htmlspecialchars($product_name_); ?> - <?php echo $amount_formatted; ?>",
                    email: "<?php echo htmlspecialchars($email_billing); ?>",
                    base_tax: "<?php echo $amount_formatted; ?>",
                    tax: "<?php echo $tax_formatted; ?>",
                    ico: '<?php echo htmlspecialchars($ico); ?>',
                    cellphone: "<?php echo htmlspecialchars($phone_billing); ?>",
                    cellphoneType: "<?php echo htmlspecialchars($str_countryCode); ?>",
                    documentType: "<?php echo htmlspecialchars($type_document); ?>",
                    document: "<?php echo htmlspecialchars($doc_number); ?>",
                    address: "<?php echo htmlspecialchars($address); ?>",
                    city: "<?php echo htmlspecialchars($city); ?>",
                    country: "<?php echo htmlspecialchars($str_countryCode); ?>",
                    stepValue: 2,
                    lang: "<?php echo htmlspecialchars($lang); ?>",
                    actionUlr: "<?php echo $redirect_url; ?>",
                    paymentMethod: "card", // cash, pse
                });
            } else {
                console.error("ePaycoCheckoutPlugin no está cargado todavía");
            }
        });
    </script>
    <script src="ePaycoCheckoutPlugin.js"></script>


</body>