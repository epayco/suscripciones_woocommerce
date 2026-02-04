<?php

namespace EpaycoSubscription\Woocommerce\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

use Exception;

use EpaycoSubscription\Woocommerce\Gateways\EpaycoSuscription;

class Subscription extends EpaycoSuscription
{
    public function __construct()
    {
        parent::__construct();
    }

    public function subscriptionCreate(array $plans, array $customer, $confirm_url, $order)
    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
        foreach ($plans as $plan) {
            try {
                $suscriptioncreted = $this->epaycoSdk->subscriptions->create(
                    [
                        "id_plan" => $plan['id_plan'],
                        "customer" => $customer['customer_id'],
                        "token_card" => $customer['token_card'],
                        "doc_type" => $customer['type_document'],
                        "doc_number" => $customer['doc_number'],
                        "url_confirmation" => $confirm_url,
                        "method_confirmation" => "POST"
                    ]
                );
                
                if (!$suscriptioncreted->status) {
                    $planJson = json_decode(json_encode($suscriptioncreted), true);
                    $dataError = $planJson;
                    $logger->error('ERROR - Crear Suscripción Falló: ' . print_r($dataError, true), ['source' => 'EpaycoSubscription_Gateway']);
                    $error = $this->errorMessages($dataError);
                    
                    $errorParts = array();
                    if (!empty($dataError['message'])) {
                        $errorParts[] = $dataError['message'];
                    }
                    if (!empty($dataError['data']['description'])) {
                        $errorParts[] = $dataError['data']['description'];
                    }
                    if (!empty($dataError['data']['errors'])) {
                        $errorParts[] = $dataError['data']['errors'];
                    }
                    
                    $userErrorMsg = !empty($errorParts) ? implode(' - ', $errorParts) : $error;
                    wc_add_notice($userErrorMsg, 'error');
                    $redirect_url = $order->get_checkout_payment_url(true);
                    wp_safe_redirect($redirect_url);
                    exit; 
                }
                
                return $suscriptioncreted;
            } catch (Exception $exception) {
                $logger->error('Excepción - Crear Suscripción: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
                wc_add_notice('Error al crear suscripción: ' . $exception->getMessage(), 'error');
                // wc_add_notice('<pre>Detalles: ' . print_r($exception, true) . '</pre>', 'error');
                $redirect_url = $order->get_checkout_payment_url(true);
                wp_safe_redirect($redirect_url);
                exit;  
            }
        }
    }


    public function subscriptionCharge(array $plan, array $customer, $confirm_url, $epayco_subscription_id,$order, $subscriptions)
    {

        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }

       try {
            $sub = $this->epaycoSdk->subscriptions->charge(
                [
                    "id_plan" => $plan['id_plan'],
                    "customer" => $customer['customer_id'],
                    "token_card" => $customer['token_card'],
                    "doc_type" => $customer['type_document'],
                    "doc_number" => $customer['doc_number'],
                    "ip" => $this->getIP(),
                    "url_confirmation" => $confirm_url,
                    "method_confirmation" => "POST",
                    "idSubscription" => $epayco_subscription_id
                ]
            );
          
            
            $validation = isset($sub->success) ? $sub->success : (isset($sub->status) ? $sub->status == 'active' :  false);
            
            if (!$validation) {
              
                $subscriptionJson = json_decode(json_encode($sub), true);
            
                if (is_string($subscriptionJson)) {
                    $subscriptionJson = json_decode($subscriptionJson, true);
                }  
                $dataError = $subscriptionJson;
                $logger->error('ERROR - Validación de Pago Falló: ' . print_r($dataError, true), ['source' => 'EpaycoSubscription_Gateway']);
                
                // Messaje error construction with multiple details
                $errorParts = array();
                if (!empty($dataError['message'])) {
                    $errorParts[] = $dataError['message'];
                }
                if (!empty($dataError['data']['description'])) {
                    $errorParts[] = $dataError['data']['description'];
                }
                if (!empty($dataError['data']['errors'])) {
                    $errorParts[] = $dataError['data']['errors'];
                }
                
                $userErrorMsg = !empty($errorParts) ? implode(' - ', $errorParts) : 'Error al procesar el pago';
                wc_add_notice($userErrorMsg, 'error');
                $redirect_url = $order->get_checkout_payment_url(true);
                wp_safe_redirect($redirect_url);
                exit; 
            }else{
                $this->handleSubscriptions($order,$sub,$subscriptions);
            }
            
        } catch (Exception $exception) {
            $logger->error('Excepción - Procesar Pago: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
            wc_add_notice('Error al procesar el pago: ' . $exception->getMessage(), 'error');
            $redirect_url = $order->get_checkout_payment_url(true);
            wp_safe_redirect($redirect_url);
            exit;  
        }
    }

    private function handleSubscriptions($order,$sub,$subscriptions){
        try{
            $logger = null;
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
               // $logger->info('Procesando suscripción con estado: ' . ($sub->data->estado ?? $sub->data->status ?? 'DESCONOCIDO'), ['source' => 'EpaycoSubscription_Gateway']);
            }
            
            $refPayco = null;
            $isTestTransaction = $sub->data->enpruebas == 1 ? "yes" : "no";
            update_option('epayco_order_status', $isTestTransaction);
            
            if (is_array($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    $is_payment_approved = (
                        ( isset($sub->data->estado) && (
                            $sub->data->estado == 'aceptada' ||
                            $sub->data->estado == 'Aceptada'
                            )
                        )||
                        ($sub->data->status === 'aceptada' || $sub->data->status === 'Aceptada') 
                    );
                    $is_payment_pending = (
                        ( isset($sub->data->estado) && (
                            $sub->data->estado == 'pendiente' ||
                            $sub->data->estado == 'Pendiente'
                            )
                        )||
                        ($sub->data->status === 'pendiente' || $sub->data->status === 'Pendiente') 
                    );
                    $is_payment_rejected = (
                        ( isset($sub->data->estado) && (
                            $sub->data->estado == 'rechazada' ||
                            $sub->data->estado == 'Rechazada'
                            )
                        )||
                        ($sub->data->status === 'rechazada' || $sub->data->status === 'Rechazada') 
                    );
                    
                    if ($is_payment_approved) {
                       // $logger->info('Estado de Pago: APROBADO', ['source' => 'EpaycoSubscription_Gateway']);
                        $this->completedPayment($order,$subscription,$sub);
                        $refPayco = isset($sub->data->ref_payco) ? esc_html($sub->data->ref_payco) : 'N/A';
                    }else{
                        if( $is_payment_rejected ) {
                          //  $logger->warning('Estado de Pago: RECHAZADO', ['source' => 'EpaycoSubscription_Gateway']);
                            $response = isset($sub->data->respuesta) ? esc_html($sub->data->respuesta) : 'Rechazada';
                            $errorMsg = "La transacción {$response}, por favor intente de nuevo.";
                            wc_add_notice(__($errorMsg, 'epayco-subscriptions-for-woocommerce'), 'error');
                            wp_safe_redirect($order->get_checkout_payment_url(true));
                            exit;
                        }else{
                            if( $is_payment_pending ) {
                                // $logger->info('Estado de Pago: PENDIENTE', ['source' => 'EpaycoSubscription_Gateway']);
                                $this->pendingPayment($order,$subscription,$sub);
                                $refPayco = isset($sub->data->ref_payco) ? esc_html($sub->data->ref_payco) : 'N/A';
                            }
                        }
                    }
                }
            }else{
                throw new Exception(__('Objeto no es una instancia de WC_Subscription.', 'epayco-subscriptions-for-woocommerce'));
            }
            WC()->cart->empty_cart();
            $arguments = array();
            $arguments['ref_payco'] = $refPayco;
            $redirect_url = add_query_arg($arguments, $order->get_checkout_order_received_url());
            wp_redirect($redirect_url);
            exit;
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger->error('ERROR - Procesar Suscripciones: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
            }
            wc_add_notice('Error al procesar la suscripción: ' . $exception->getMessage(), 'error');
            $redirect_url = $order->get_checkout_payment_url(true);
            wp_safe_redirect($redirect_url);
            exit;  
        }
    }

    public function cancelSubscription($subscription_id)
    {
        try {
            $result = $this->epaycoSdk->subscriptions->cancel($subscription_id);
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->error("ERROR - Cancelar Suscripción: " . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
            }
            throw $exception;
        }
    }

}