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
            $general=plugin_dir_url(__FILE__).'assets/css/general.css';
            $animate=plugin_dir_url(__FILE__).'assets/css/animate.min.css';
            $card_style=plugin_dir_url(__FILE__).'assets/css/card-js.min.css';
            $card_unmin=plugin_dir_url(__FILE__).'assets/js/card-js-unmin.js';
            $indexjs=plugin_dir_url(__FILE__).'assets/js/index.js';
           // $epaycojs=plugin_dir_url(__FILE__).'assets/js/epayco.js';
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
                  <link rel="stylesheet" type="text/css"  href="'.$style.'">
                  <link rel="stylesheet" type="text/css"  href="'.$general.'">
                  <link  rel="stylesheet" type="text/css" href="'.$card_style.'" />
                  <link  rel="stylesheet" type="text/css" href="'.$animate.'" /> 
                <title>Pasarela de pagos | ePayco</title>
                  <meta charset="utf-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
                </head>
                <body>
                    <div id="p_c" hidden="true">'.$this->apiKey.'</div>
                    <div id="p_p" hidden="true">'.$this->privateKey.'</div>
                    <div id="lang_epayco" hidden="true">'.$lang.'</div>
                    <div class="loader-container">
                              <div class="loading"></div>
                    </div>
                        <p style="text-align: center;" class="epayco-title">
                            <span class="animated-points">Cargando metodos de pago</span>
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

                                    <div class="input-form">
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
                                                width:10%;
                                                margin:0;
                                                text-align:center;
                                                line-height: 40px;
                                                height: 38px;
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
                            <p>
                              <i class="fa fa-lock fa-lg" style="color: #2ECC71" aria-hidden="true"></i>
                              Pago seguro por
                              <img src="'.$logo_white.'" height="20" style="display: inline;">
                            </p>
                          </div>

                          </div>
                                <div id="overlay"></div>
                          </div>
    
                </body>
                  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
                    <script src="'.$card_unmin.'"></script>
                      <script src="'.$epaycojs.'"></script>
                  <script src="'.$indexjs.'"></script>

                  <script src="https://kit.fontawesome.com/fc569eac4d.js" crossorigin="anonymous"></script>
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
                $data = $subscription->subscription_epayco($_REQUEST);
                }
               
            if($data['status']){
               // wc_reduce_stock_levels($order_id);
                WC()->cart->empty_cart();
                $arguments=array();
                $arguments['ref_payco']=$data['ref_payco'];
                $redirect_url = $data['url'];
                $redirect_url = add_query_arg($arguments , $redirect_url );
                wp_redirect($redirect_url);
            }else{
                $order = new WC_Order($order_id);
                if (version_compare( WOOCOMMERCE_VERSION, '2.1', '>=')) {
                    $redirect = array(
                        'result'    => 'success',
                        'redirect'  => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key,get_permalink(woocommerce_get_page_id('pay' ))))
                    );
                } else {
                    $redirect = array(
                        'result'    => 'success',
                        'redirect'  => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key,get_permalink(woocommerce_get_page_id('pay' ))))
                    );
                }
                wc_add_notice(implode(PHP_EOL, $data['message']), 'error' );
                wp_redirect($redirect["redirect"]);
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



