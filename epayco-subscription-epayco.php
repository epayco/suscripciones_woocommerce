<?php
    class WC_Payment_Epayco_Subscription extends WC_Payment_Gateway
    {

        public function __construct()
        {
            $this->id = 'epayco-subscription';
            $this->icon =  plugin_dir_url(__FILE__).'assets/images/logo.png';
            $this->method_title = __('ePayco Subscription');
            $this->method_description = __('Subscription ePayco recurring payments');
            $this->description  = $this->get_option( 'description' );
            $this->order_button_text = __('Pay', 'epayco-subscription');
            $this->has_fields = true;
            $this->supports = [
                'subscriptions',
                'subscription_suspension',
                'subscription_reactivation',
                'subscription_cancellation'
            ];
            $this->init_form_fields();
            $this->init_settings();
            $this->title = $this->get_option('epayco_title');
            $this->isTest = (bool)$this->get_option( 'environment' );
            update_option('epayco_order_status', $this->isTest);
            $this->currency = get_option('woocommerce_currency');
            $this->custIdCliente = $this->get_option('custIdCliente');
            $this->pKey = $this->get_option('pKey');
            $this->apiKey = $this->get_option('apiKey');
            $this->privateKey = $this->get_option('privateKey');
            $this->shop_name = $this->get_option('shop_name');
            $this->epayco_endorder_state = $this->get_option('epayco_endorder_state');
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_api_'.strtolower(get_class($this)), array($this, 'confirmation_ipn'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            add_action('ePaycosub_init', array( $this, 'ePaycoSub_successful_request'));

        }

        public function init_form_fields()
        {
            $this->form_fields = require( dirname( __FILE__ ) . '/admin/epayco-settings.php' );
        }

          public function admin_options()
    {
        ?>
        <style>
            tbody{
            }
            .epayco-table tr:not(:first-child) {
                border-top: 1px solid #ededed;
            }
            .epayco-table tr th{
                padding-left: 15px;
                text-align: -webkit-right;
            }
            .epayco-table input[type="text"]{
                padding: 8px 13px!important;
                border-radius: 3px;
                width: 100%!important;
            }
            .epayco-table .description{
                color: #afaeae;
            }
            .epayco-table select{
                padding: 8px 13px!important;
                border-radius: 3px;
                width: 100%!important;
                height: 37px!important;
            }
            .epayco-required::before{
                content: '* ';
                font-size: 16px;
                color: #F00;
                font-weight: bold;
            }

        </style>
            <div class="container-fluid">
                <div class="panel panel-default" style="">
                    <img src="<?php echo plugin_dir_url(__FILE__).'assets/images/logo.png' ?>">
                    <h3><?php echo $this->title; ?></h3>
                    <div style ="color: #31708f; background-color: #d9edf7; border-color: #bce8f1;padding: 10px;border-radius: 5px;">
                            <b>Este modulo le permite aceptar pagos seguros por la plataforma de pagos ePayco</b>
                            <br>Si el cliente decide pagar por ePayco, el estado del pedido cambiara a ePayco Esperando Pago
                            <br>Cuando el pago sea Aceptado o Rechazado ePayco envia una configuracion a la tienda para cambiar el estado del pedido.
                    </div>
                    <div class="panel-body" style="padding: 15px 0;background: #fff;margin-top: 15px;border-radius: 5px;border: 1px solid #dcdcdc;border-top: 1px solid #dcdcdc;">

                    <table class="form-table epayco-table">
                        <?php
                        $this->generate_settings_html();
                        ?>
                    </table>
                    </div>
                </div>
            </div>
        <?php
    }

        public function confirmation_ipn()
        {
            @ob_clean();
            if ( ! empty( $_REQUEST ) ) {
                header( 'HTTP/1.1 200 OK' );
                do_action( "ePaycosub_init", $_REQUEST );
            } else {
                wp_die( __("ePayco Request Failure", 'epayco-subscription') );
            }
        }
        public function receipt_page($order_id){
            global $woocommerce;
            global $wpdb;
            $subscription = new WC_Subscription($order_id);
            $order = wc_get_order( $order_id );
            $order_data = $order->get_data(); // The Order data
            $name_billing=$subscription->get_billing_first_name().' '.$subscription->get_billing_last_name();
            $email_billing=$subscription->billing_email;
            $redirect_url =get_site_url() . "/";
            $redirect_url = add_query_arg( 'wc-api', get_class( $this ), $redirect_url );
            $redirect_url = add_query_arg('order_id',$order_id,$redirect_url);
            $amount=$subscription->get_total();
            $mountFloat = floatval($amount);
            $currency = get_woocommerce_currency();
            $descripcionParts = array();
            foreach ($subscription->get_items() as $product) {
                $clearData = str_replace('_', ' ', $this->string_sanitize($product['name']));
                $descripcionParts[] = $clearData;
            }
            
            $descripcion = implode(' - ', $descripcionParts);
            if(substr_count($descripcion, ' - ')>=1){
              $product_name = $descripcionParts[0];
              $porciones = explode(" - ", $product_name);
              $product_name = $porciones[0]."...";
              }else{
                  $product_name = $descripcion;
              }
              if(strlen($product_name) < 20)
              {
                $product_name_ = $descripcion;
              }else{
                $resultado = substr($product_name, 0, 19);
                $product_name_ = $resultado."...";
              }

            $logo_comercio = plugin_dir_url(__FILE__).'assets/images/comercio.png';
            $logo_white = plugin_dir_url(__FILE__).'assets/images/logo-white.png';
            $style=plugin_dir_url(__FILE__).'assets/css/style.css';
            $stylemin=plugin_dir_url(__FILE__).'assets/css/style.min.css';
            $general=plugin_dir_url(__FILE__).'assets/css/general.css';
            $animate=plugin_dir_url(__FILE__).'assets/css/animate.min.css';
            $card_style=plugin_dir_url(__FILE__).'assets/css/card-js.min.css';
            $cardsjscss= trim(plugin_dir_url(__FILE__).'assets/css/cardsjs.css');
            $card_unmin=plugin_dir_url(__FILE__).'assets/js/card-js-unmin.js';
            $indexjs=plugin_dir_url(__FILE__).'assets/js/index.js';
            $appjs= trim(plugin_dir_url(__FILE__).'assets/js/app.min.js');
            $cardsjs= trim(plugin_dir_url(__FILE__).'assets/js/cardsjs.js');
            $epaycojs ="https://checkout.epayco.co/epayco.min.js";
            $lang =  get_locale();
            $lang = explode('_', $lang);
            $lang = $lang[0];

            if( ini_get('allow_url_fopen') ) {
                $str_arr_ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']));
            } else {
                $c = curl_init();
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR']);
                $contents = curl_exec($c);
                curl_close($c);
                $str_arr_ipdat = @json_decode($contents);
            }

            if(!empty($str_arr_ipdat) and  $str_arr_ipdat->geoplugin_status != 404) {
                $str_countryCode = $str_arr_ipdat->geoplugin_countryCode;
            }else{
                $str_countryCode = "CO";
            }

            if($mountFloat>0){
                $amout_value= '<h2 class="color" style="font-size: 16px;margin: 2rem 6.5rem !important;position: absolute;color: #3582b7;">
                      <strong>$'.$amount.'</strong>
                      <span style="font-size: 12px">'.$currency.'</span>
                    </h2>';
            }else{
                $amout_value= '<h2 class="color" style="font-size: 16px;margin: 2rem 6.5rem !important;position: absolute;color: #3582b7;">
                      <strong></strong>
                      <span style="font-size: 12px"></span>
                    </h2>';
            }

            echo '
              <!DOCTYPE html>
                <head>
                  <link rel="stylesheet" type="text/css" id="movil_header"  href="'.$style.'">
                  <link rel="stylesheet" type="text/css"  href="'.$general.'">
                  <link  rel="stylesheet" id="cardjsmincss" type="text/css" href="'.$card_style.'" />
                  
                  <link  rel="stylesheet" type="text/css" href="'.$animate.'" /> 
                  <link  rel="stylesheet" type="text/css" href="'.$cardsjscss.'" />
                  
                <title>Pasarela de pagos | ePayco</title>
                  <meta charset="utf-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
                  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
                  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
                    integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
                  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.4.2/css/bootstrap-slider.min.css"
                    rel="stylesheet">
                </head>
                <body>




                <div class=""  id="movil_mainContainer" style="top:-64px">
                 <section class="modal-container">
                   <style>
                     .form-container .icon {
                       color: #ED9733 !important;
                     }
                     .button-container .pay-type {
                       border: 1px solid #ED9733;
                     }
             
                     .button-container .pay-type .icon {
                       color: #ED9733;
                     }
             
                     .cont-btn {
                       padding: 8px;
                       background-color: #f3f3f3;
                       border-radius: 0 0 10px 10px;
                       text-align: center;
             
                     }
                   
                   </style>
                   <div class="loading-home op" style="display: none"  id="loading_home">
                     <div class="circulo ">
                       <div class="lock">
                         <svg class="svg-inline--fa fa-lock fa-w-14" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path></svg><!-- <i class="fa fa-lock"></i> -->
                       </div>
                       Procesando Pago...
                     </div>
                   </div>
             
               <section class="modal" hidden id="movil_modal">
                 <header class="animated fadeInDown" style="background-color: #ED9733 !important">
                   <div class="title-container  ">
                     <div class="logo-commerce">
                       <div class="logo-container" style="">
                         <img width="90%" src="'.$logo_comercio.'">
                       </div>
                     </div>
                     <div class="col title">
                       <div class="comercio-name ">
                         <h1>'.$this->shop_name.'</h1>
                       </div>
                       <div class="description-cont ">
                         <p>'.$product_name_.'</p>
                         <strong class="monto">
                           $'.$amount.'
                           <input type="hidden" value="'.$amount.'" id="currentAmount">
                           <span class="moneda">'.$currency.'</span>
                         </strong>
                       </div>
                     </div>
                   </div>
                   <div class="langaugeCancelt">
                     <div class="cancelPayment" id="cancel-t">
                       <svg class="svg-inline--fa fa-times fa-w-12" aria-hidden="true" data-prefix="fa" data-icon="times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M323.1 441l53.9-53.9c9.4-9.4 9.4-24.5 0-33.9L279.8 256l97.2-97.2c9.4-9.4 9.4-24.5 0-33.9L323.1 71c-9.4-9.4-24.5-9.4-33.9 0L192 168.2 94.8 71c-9.4-9.4-24.5-9.4-33.9 0L7 124.9c-9.4 9.4-9.4 24.5 0 33.9l97.2 97.2L7 353.2c-9.4 9.4-9.4 24.5 0 33.9L60.9 441c9.4 9.4 24.5 9.4 33.9 0l97.2-97.2 97.2 97.2c9.3 9.3 24.5 9.3 33.9 0z"></path></svg><!-- <i class="fa fa-times"></i> -->
                     </div>
                     <div class="language-switch">
                       <a class="dn set-lang pointer l-es" data-lang="es" id="data_lang_es">ES</a>
                       <a class=" set-lang pointer l-en" data-lang="en" id="data_lang_en">EN</a>
                     </div>
                   </div>
                   <div id="email-container" class="email-container active">
                     <h3>'.$email_billing.'</h3>
                     <div class="container-acvive-email " style="display: none;">
                       <div class="back-button">
                         <svg class="svg-inline--fa fa-angle-left fa-w-8"  aria-hidden="true" data-prefix="fa" data-icon="angle-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg><!-- <button class="fa fa-angle-left" ></button> -->
                       </div>
                       <div class="volverSalir">
                         <p class="email ">&nbsp;&nbsp;ricardo.saldarriaga@epayco.com</p>
                         <button class="log-out " onclick="goBack();">
                           <span class="logout-text">Cerrar sesión</span>
                         </button>
                       </div>
                     </div>
                   </div>
                 </header>
                 <section class="content animated zoomIn " style="
                 background-color: white;">
                   <div id="content-errors"></div>
                   <form id="form-action" method="post" novalidate=""  action="'.$redirect_url.'" >
                     
                     <div class="step step-tdc main-steps active" data-group="tdc" active="" style="margin: 0px;">
                         <div class="step-container">
                             <div class="step-form">
                                 <!-- Name -->
                                 <div class="form-container extra-label">
                                     <svg class="svg-inline--fa fa-user fa-w-16 icon" aria-hidden="true" data-prefix="fa" data-icon="user" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M256 0c88.366 0 160 71.634 160 160s-71.634 160-160 160S96 248.366 96 160 167.634 0 256 0zm183.283 333.821l-71.313-17.828c-74.923 53.89-165.738 41.864-223.94 0l-71.313 17.828C29.981 344.505 0 382.903 0 426.955V464c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48v-37.045c0-44.052-29.981-82.45-72.717-93.134z"></path></svg><!-- <i class="icon fa fa-1.5x fa-user"></i> -->
                                     <div class="label-container ">
                                         <label for="card" id="label_name_es" style="display:table-cell">Nombre</label>
                                     </div>
                                     <div class="input-container">
                                         <input type="text" name="name" placeholder="Nombre propietario de tarjeta" value="ricardo saldarriaga">
                                     </div>
                                 </div>
                                  
                                 <div class="form-container">
                                 <svg class="svg-inline--fa fa-credit-card fa-w-18 icon" aria-hidden="true" data-prefix="fa" data-icon="credit-card" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M0 432c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V256H0v176zm192-68c0-6.6 5.4-12 12-12h136c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H204c-6.6 0-12-5.4-12-12v-40zm-128 0c0-6.6 5.4-12 12-12h72c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM576 80v48H0V80c0-26.5 21.5-48 48-48h480c26.5 0 48 21.5 48 48z"></path></svg><!-- <i class="icon fa fa-credit-card"></i> -->
                                    <div class="label-container ">
                                      <label for="card" id="label_card_es" style="display:table-cell">Tarjeta</label>
                                    </div>

                                    <div class="input-container">
                                      <div class="card-jss" data-icon-colour="#158CBA">
                                        <div class="card-number2-wrapper">
                                          <input class="card-number2" id="the-card-number2-element"
                                          data-epayco="card[number]" required="" name="card-number2" 
                                          placeholder="**** **** **** ****" type="tel" maxlength="19"
                                          x-autocompletetype="cc-number"
                                          autocompletetype="cc-number"
                                          autocorrect="off" spellcheck="off"
                                          autocapitalize="off" style="
                                          padding-left: 0px;
                                          ">
                                        </div>
                                      </div>
                                      <img class="img-card" src="https://msecure.epayco.co/img/credit-cards/disable.png" id="logo_franchise">
                                      <input type="hidden" name="valid_franchise" value="false">
                                    </div>
                                  </div>  
                                 <!-- End Name -->
                     
                                 <!-- Credit Card -->
   
                                 <!-- End Credit Card -->
                     
                     
                                 <div class="dosCampos">
                     
                                     <!-- Expiry Date -->
                                     <div class="form-container dateYex fecha-exp">
                                         <svg class="svg-inline--fa fa-calendar fa-w-14 icon" aria-hidden="true" data-prefix="fa" data-icon="calendar" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M12 192h424c6.6 0 12 5.4 12 12v260c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V204c0-6.6 5.4-12 12-12zm436-44v-36c0-26.5-21.5-48-48-48h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v36c0 6.6 5.4 12 12 12h424c6.6 0 12-5.4 12-12z"></path></svg><!-- <i class="icon fa fa-calendar "></i> -->
                                         <div class="label-cvv-container RO">
                                             <label for="expiry" id="label_expiry_es" style="display:table-cell">Vence</label>
                                         </div>
                                         <div class="input-cvv-container ">
                                             <input name="expiry" type="tel" placeholder="MM/YY" id="expiry-input" class="">
                                         </div>
                                     </div>
                                     <!-- End Expiry Date -->
                     
                                     <!-- Card Secret Code -->
                                     <div class="form-container dateYex  text-center line code-sec">
                                         <div class="label-cvv-container ">
                                             <svg class="svg-inline--fa fa-lock fa-w-14 icon" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path></svg><!-- <i class="fa fa-lock icon"></i> -->
                                             <label for="cvc">CVV</label>
                                         </div>
                                         <div class="input-cvv-container">
                                             <input type="tel" placeholder="123" maxlength="4" name="cvc" id="cvc-input" class="">
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
                 <button class="action-oneclick cancel-oneclick" style="background-color: #D8D8D8">Cancelar</button>
                 <button class="action-oneclick save-oneclik">Guardar</button>
               </div>
               
               <button id="continue-tdc" class="continue-container text-center btnpay" style="background-color: #28303d;" type="submit">
                Pagar
                 <svg class="svg-inline--fa fa-angle-right fa-w-8" aria-hidden="true" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" data-fa-i2svg=""><path fill="currentColor" d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg><!-- <i class="fas fa-angle-right"></i> -->
               </button>
         
               <div class="brand-footer">
                 <p style="color:#1C0E49">
                   <svg class="svg-inline--fa fa-lock fa-w-14 secure" aria-hidden="true" data-prefix="fa" data-icon="lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path></svg><!-- <i class="fa fa-lock secure"></i> --> Pago seguro por </p>
                 <img src="https://msecure.epayco.co/img/new_epayco_logo.png" alt="ePayco Logo" height="15px">
               </div>
             
             </footer>
 
             <div class="cancelT-modal dn" id="cancelT_modal" style="display:none">
               <div class="ventana dn">
                 <div class="icono">
                   <svg class="svg-inline--fa fa-exclamation-circle fa-w-16" aria-hidden="true" data-prefix="fa" data-icon="exclamation-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path></svg>
                 </div>
                   <p>¿Está seguro de Cancelar esta Transacción?</p>
                 <div class="acciones">
                   <button id="regresa-t">Regresar</button>
                   <button id="cancel-transaction">Cancelar Transacción</button>
                 </div>
               </div>
             </div>
              
 
             <div class="modal-expiration-time dn" id="mdlInactivityTime"  style="display:none">
             <div class="ventana dn" id="mdlInactivityTimeBody">
               <div class="mdl-expiration-time">
       
                 <p class="mdl-expiration-time-title">Cuidado</p>
       
                 <p class="mdl-expiration-time-content padding-10">
                   Su sesión va a expirar en:
                 </p>
       
                 <div class="text-center">
                   <span class="spinner"></span>
                   <h1 id="counterInactivity">45</h1>
                   <p class="mdl-expiration-time-content">Segundos</p>
                 </div>
               </div>
               
               <button type="button" class="btn btn-primary btn-block">Continuar</button>
             </div>
           </div>
 
           <div class="modal-expiration-time  dn" id="mdlTimeExpired"  style="display:none">
             <div class="ventana dn" id="mdlTimeExpiredBody">
               <div class="mdl-expiration-time">
                 <div class="text-center">
                     <img src="https://msecure.epayco.co/img/reloj.png" class="img-65x65" alt="icono-warning" style="
                     display: block;
                     margin: auto;
                     text-align: center;
                      ">
                 </div>
                   <p class="mdl-expiration-time-title">Su sesión ha expirado por inactividad</p>
                   <p class="mdl-expiration-time-content text-center">
                       De clic en cerrar para regresar e iniciar una nueva transacción.
                   </p>
                 <button type="button" class="btn btn-primary btn-block" id="btnMdlTimeExpired">Cerrar</button>
               </div>
             </div>
           </div>





                    <div id="p_c" hidden="true">'.$this->apiKey.'</div>
                    <div id="p_p" hidden="true">'.$this->privateKey.'</div>
                    <div id="lang_epayco" hidden="true">'.$lang.'</div>
                    <div class="loader-container">
                              <div class="loading"></div>
                    </div>
                        <p style="text-align: center;" class="epayco-title" id="epayco_title">
                            <span class="animated-points">Cargando métodos de pago</span>
                            <br>
                            <small class="epayco-subtitle"> Si no se cargan automáticamente, de clic en el botón "Pagar con ePayco"</small>
                        </p> 

                    <center>
                        <button data-modal-target="#centered" id="button_epayco" style="
                          background-image: url(https://multimedia.epayco.co/epayco-landing/btns/Boton-epayco-linea.png );
                          background-repeat:no-repeat;
                          height:39px;
                          width:144px;
                          background-position:center;
                          border-color: #28303d;
                          border-radius: 6px;
                          background-color: #28303d;
                          "></button>
                    </center>

                    <div class="middle-xs bg_onpage porcentbody m-0" style="margin: 0">
                
                      <div class="centered" id="centered">
                        <div class="loadoverlay" id="loadoverlay">
                          <div class="loader loadimg"></div>
                          <i class="fa fa-lock fa-lg loadshield2" style="color: gray" aria-hidden="true"></i>
                          <span class="loadtext">Procesando Pago</span>
                        </div>

                        <div class="onpage relative" id="web-checkout-content">
                                <div class="header-modal hidden-print">
                                  <div class="color hidden-print closeIcon" id="closeModal">
                                    <div data-close-button class="icon-cancel">&times;</div>
                                  </div>
                            
                                    <div class="logo-comercio">
                                      <img class="image-safari" width="90%" src="'.$logo_comercio.'">
                                    </div>
                                    <div class="margin-top visible-print"></div>
                          
                                    <h1 style="font-size: 17px;margin-bottom:3px;height: 20px;margin: -0.5rem  6.5rem !important;color: black;position: absolute;">'.$product_name_.'</h1>
                                    <h2 style="font-size: 12px;margin-bottom:3px;color: #848484;margin: 1rem 6.5rem !important;position: absolute;">'.$this->shop_name.'</h2>
                                      '.$amout_value.'
                                </div>
                              
                              <div class="body-modal fix-top-safari">

                                  <div class="bar-option hidden-print">
                                      <div class="dropdown select-pais pointer" id="sample">
                                        <a class="dropdown-toggle blockd" type="button" data-toggle="dropdown">
                                            <div class="flag flag-icon-background flag-icon-co" data-toggle="dropdown" id="flag"></div>
                                              <div id="countryName">Colombia</div>
                                                <i class="fa fa-caret-down caret-languaje" aria-hidden="true" ></i>
                                        </a>
                                        <dd>
                                           <ul id="foo">
                                           </ul>
                                        </dd>
                                        <ul class="dropdown-menu" id="dropdown-countries"></ul>
                                      </div>
                                      
                                      <span id="result" hidden>'.$str_countryCode.'</span>
                                      
                                      <a id="esButton" class="languaje pointer" data-es-button data-language="es">ES</a>
                                      <a id="enButton" class="languaje pointer" data-en-button data-language="en">EN</a>
                                  </div>
                                
                                  <div class="wc scroll-content">
                                    <div class="separate">                  
                                        <h2 class="title-body" style="text-align: left;width: calc(100% - 1.5em);
                                            margin: 0 auto 1em; font-size: 16px; font-weight: 500; color: #3a3a3a;" id="info_es">Información de la tarjeta
                                        </h2>
                                        <h2 class="title-body" style="text-align: left;width: calc(100% - 1.5em);
                                            margin: 0 auto 1em; font-size: 16px; font-weight: 500; color: #3a3a3a;" id="info_en">Credit card information
                                        </h2>
                                    </div>

                                  <div class="menu-select">
                                      <form id="token-credit" action="'.$redirect_url.'" method="post">
                                      <div class="card-js" data-icon-colour="#158CBA">
                                        <input class="name" id="the-card-name-element" data-epayco="card[name]" required value="'.$name_billing.'">
                                      <input class="card-number my-custom-class" data-epayco="card[number]" required id="the-card-number-element" name="card_number">
                                    </div>

                                    <div class="input-form" hidden>
                                        <span class="icon-credit-card color icon-input"><i class="fas fa-envelope"></i></span>
                                        <input type="tel" class="binding-input inspectletIgnore"  name="card_email"  autocomplete="off" hidden="true" data-epayco="card[email]" value="'.$email_billing.'">
                                    </div>

                                    <div class="select-option bordergray vencimiento" style="float:left" id="expiration">
                                        <div class="input-form full-width noborder monthcredit nomargin">         
                                            <span class="icon-date_range color icon-select">
                                              <i class="far fa-calendar-alt"></i>
                                            </span>

                                        <input class="binding-input inspectletIgnore" id="month-value" name="month" placeholder="MM" maxlength="2" autocomplete="off" data-epayco="card[exp_month]"  required>
                                            </div>
                                            <div class="" style="
                                                float:left;
                                                width:12%;
                                                margin:0;
                                                text-align:center;
                                                line-height: 40px;
                                                height: 37px;
                                                background-color: white;
                                                color:#a3a3a3;
                                                ">
                                                /
                                            </div>
                                        <div class="input-form full-width normalinput noborder yearcredit nomargin">
                                                
                                        <input class="binding-input inspectletIgnore" name="year" id="year-value" placeholder="YYYY" maxlength="4" autocomplete="off" data-epayco="card[exp_year]"  required >
                                  </div>
                                </div>

                                    <div class="input-form normalinput cvv_style" id="cvc_">
                                        <input type="password" placeholder="CVC" class="nomargin binding-input" name="cvc" id="card_cvc" autocomplete="off" maxlength="4" data-epayco="card[cvc]">
                                        <i class="fa color fa-question-circle pointer" aria-hidden="true" style="right: 5px; padding: 0; top: 0;" id="look-cvv"></i>
                                    </div>
                                    <br>
                                    <div class="clearfix"></div>

                                    <div class="call_action bgcolor white_font pointer load hidden-print" id="send-form">
                                      <h2 style="color: white;" id="pagar_es">Pagar</h2>
                                      <h2 style="color: white;" id="pagar_en">Pay</h2>
                                    </div>
                                  </form>
                               </div>

                              </div>
                            </div>
    
                          </div>

                          <div class="footer-modal hidden-print" id="footer-animated">
                            <p id="pagar_logo_es">
                              <i class="fa fa-lock fa-lg" style="color: #2ECC71" aria-hidden="true"></i>
                              Pago seguro por
                              <img src="https://secure.epayco.co/img/new_epayco_white.png" height="20" style="display: inline;">
                            </p>
                            <p id="pagar_logo_en">
                              <i class="fa fa-lock fa-lg" style="color: #2ECC71" aria-hidden="true"></i>
                              Secure payment by
                              <img src="https://secure.epayco.co/img/new_epayco_white.png" height="20" style="display: inline;">
                            </p>
                          </div>

                          </div>
                                <div id="overlay"></div>
                          </div>
    
                          <div id="style_min" hidden>'.$stylemin.'
                          </div>
                
                
                          </body>
                  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
                    <script src="'.$card_unmin.'"></script>
                      <script src="'.$epaycojs.'"></script>
                  <script src="'.$indexjs.'"></script>
                  <div id="movil" hidden>'.$appjs.'
                  </div>
                 <script src="'.$cardsjs.'"></script>
                </html>';
              }


        public function process_payment($order_id)
        {
            $params = $_POST;
            $params['id_order'] = $order_id;
            $order = new WC_Order($order_id);
            if (version_compare( WOOCOMMERCE_VERSION, '2.1', '>=')) {
                return array(
                    'result'    => 'success',
                    'redirect'  => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
                );
            } else {
                return array(
                    'result'    => 'success',
                    'redirect'  => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
                );
            }
        }

        /**
         * @param $validationData
         */
        function ePaycoSub_successful_request($validationData)
        {
            global $woocommerce;
            $subscription = new Subscription_Epayco_SE();
            $order_id= $_REQUEST["order_id"];

            if($_REQUEST["confirmation"]){
                $subscription->subscription_epayco_confirm($_REQUEST);
                die();
            }else{
              if(isset($_REQUEST["canceled"]) && $_REQUEST["canceled"] == "1")
              {
                $data = $subscription->cancelledPayment($order_id,null,null,null);
              }else{
                $data = $subscription->subscription_epayco($_REQUEST);
              }
            }
            if(!$data['status'] && empty($data['ref_payco'])){
                wc_add_notice( $data['message'], 'error' ); 
                $order = new WC_Order($order_id);
                if (version_compare( WOOCOMMERCE_VERSION, '2.1', '>=')) {
                    $redirect = array(
                        'result'    => 'success',
                        'redirect'  => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
                    );
                } else {
                    $redirect = array(
                        'result'    => 'success',
                        'redirect'  => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
                    );
                }
                wp_redirect($redirect["redirect"]);  

            }else{
                WC()->cart->empty_cart();
                $arguments=array();
                $arguments['ref_payco']=$data['ref_payco'];
                $redirect_url = $data['url'];
                $redirect_url = add_query_arg($arguments , $redirect_url );
                wp_redirect($redirect_url);
            }

            
        }

        public function string_sanitize($string, $force_lowercase = true, $anal = false) {
            $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]","}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;","â€”", "â€“", ",", "<", ".", ">", "/", "?");
            $clean = trim(str_replace($strip, "", strip_tags($string)));
            $clean = preg_replace('/\s+/', "_", $clean);
            $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
            return $clean;
        }
     

    }

