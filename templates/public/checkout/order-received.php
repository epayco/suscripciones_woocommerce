<?php

/**
 * Part of Woo Epayco Module
 * Author - Epayco
 * Developer
 * Copyright - Copyright(c) Epayco [https://www.epayco.com]
 * License - https://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 *
 * @package Epayco
 */

/**
 * Payment gateway configuration parameters
 *
 * @var string $referencePayco The reference ID of the order to be queried in the payment gateway
 * @var bool   $sendEmail      Flag to enable/disable sending payment receipt to customer's email
 * @var string $lang           Language code for the payment component interface (es: Spanish, en: English, pt: Portuguese)
 */

if (!defined('ABSPATH')) {
  exit;
}
?>
<div id="epayco-cms-detail-purchase"></div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.DetailPurchase) {
      DetailPurchase.config({

        referencePayco: <?php echo json_encode($referencePayco); ?>,
        sendEmail: <?php echo json_encode($sendEmail); ?>,
        lang: <?php echo json_encode($lang); ?>
      });
    } else {
      console.error("DetailPurchase no está cargado todavía");
    }
  });
</script>