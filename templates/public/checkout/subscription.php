<?php

/**
 * @var string $epayco
 * @see \EpaycoSubscription\Woocommerce\Gateways\EpaycoSuscription
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class='mp-checkout-container'>
    <div class="mp-checkout-epayco-container">
        <div class="mp-checkout-epayco-content">
            <?php echo esc_html($epayco); ?>
            <!-- NOT DELETE LOADING-->
            <div id="mp-box-loading"></div>
        </div>

    </div>
</div>

