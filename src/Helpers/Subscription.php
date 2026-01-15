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
                $logger->info('=== CREAR SUSCRIPCION - INICIADO ===', ['source' => 'EpaycoSubscription_Gateway']);
                $logger->info('Parámetros enviados: ' . json_encode([
                    "id_plan" => $plan['id_plan'],
                    "customer" => $customer['customer_id'],
                    "token_card" => $customer['token_card'],
                    "doc_type" => $customer['type_document'],
                    "doc_number" => $customer['doc_number'],
                    "url_confirmation" => $confirm_url,
                ]), ['source' => 'EpaycoSubscription_Gateway']);
                
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
                
                // Log de la respuesta pura de la API
                $logger->info('✓ Respuesta API - Crear Suscripción (RAW): ' . json_encode($suscriptioncreted), ['source' => 'EpaycoSubscription_Gateway']);
                
                if (!$suscriptioncreted->status) {
                    $planJson = json_decode(json_encode($suscriptioncreted), true);
                    $dataError = $planJson;
                    $logger->error('✗ ERROR en respuesta de API - Status FALSE: ' . json_encode($dataError), ['source' => 'EpaycoSubscription_Gateway']);
                    $error = $this->errorMessages($dataError);
                    wc_add_notice($error, 'error');
                    $redirect_url = $order->get_checkout_payment_url(true);
                    wp_safe_redirect($redirect_url);
                    exit; 
                }
                
                $logger->info('✓ Suscripción creada exitosamente', ['source' => 'EpaycoSubscription_Gateway']);
                return $suscriptioncreted;
            } catch (Exception $exception) {
                $logger->error('✗ Exception en subscriptionCreate: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
                $logger->error('Stack trace: ' . $exception->getTraceAsString(), ['source' => 'EpaycoSubscription_Gateway']);
                wc_add_notice($exception->getMessage(), 'error');
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
            $logger->info('=== PROCESAR PAGO - INICIADO ===', ['source' => 'EpaycoSubscription_Gateway']);
            $logger->info('Parámetros de pago: ' . json_encode([
                "id_plan" => $plan['id_plan'],
                "customer" => $customer['customer_id'],
                "token_card" => $customer['token_card'],
                "doc_type" => $customer['type_document'],
                "doc_number" => $customer['doc_number'],
                "url_confirmation" => $confirm_url,
                "idSubscription" => $epayco_subscription_id
            ]), ['source' => 'EpaycoSubscription_Gateway']);
            
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
            
            // Log de la respuesta pura de la API
            $logger->info('✓ Respuesta API - Procesar Pago (RAW): ' . json_encode($sub), ['source' => 'EpaycoSubscription_Gateway']);
            
            $validation = isset($sub->success) ? $sub->success : (isset($sub->status) ? $sub->status == 'active' :  false);
            $logger->info('Validación de pago: success=' . (isset($sub->success) ? $sub->success : 'N/A') . ', status=' . (isset($sub->status) ? $sub->status : 'N/A'), ['source' => 'EpaycoSubscription_Gateway']);
            
            if (!$validation) {
                $subscriptionJson = json_decode(json_encode($sub), true);
                $dataError = $subscriptionJson;
                $logger->error('✗ ERROR en validación de pago: ' . json_encode($dataError), ['source' => 'EpaycoSubscription_Gateway']);
                $error = $this->errorMessages($dataError);
                wc_add_notice($error, 'error');
                $redirect_url = $order->get_checkout_payment_url(true);
                wp_safe_redirect($redirect_url);
                exit; 
            }else{
                $logger->info('✓ Pago validado correctamente, procesando suscripción', ['source' => 'EpaycoSubscription_Gateway']);
                $this->handleSubscriptions($order,$sub,$subscriptions);
            }
            
        } catch (Exception $exception) {
            $logger->error('✗ Exception en subscriptionCharge: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
            $logger->error('Stack trace: ' . $exception->getTraceAsString(), ['source' => 'EpaycoSubscription_Gateway']);
            wc_add_notice($exception->getMessage(), 'error');
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
            }
            
            $logger->info('=== PROCESAR RESPUESTA DE PAGO ===', ['source' => 'EpaycoSubscription_Gateway']);
            $logger->info('Datos completos de respuesta: ' . json_encode($sub), ['source' => 'EpaycoSubscription_Gateway']);
            
            $refPayco = null;
            $isTestTransaction = $sub->data->enpruebas == 1 ? "yes" : "no";
            $logger->info('Es transacción de prueba: ' . $isTestTransaction, ['source' => 'EpaycoSubscription_Gateway']);
            $logger->info('Estado de pago: ' . (isset($sub->data->estado) ? $sub->data->estado : $sub->data->status), ['source' => 'EpaycoSubscription_Gateway']);
            $logger->info('Referencia ePayco: ' . (isset($sub->data->ref_payco) ? $sub->data->ref_payco : 'N/A'), ['source' => 'EpaycoSubscription_Gateway']);
            
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
                    
                    $logger->info('Estados validados - Aprobado: ' . ($is_payment_approved ? 'SI' : 'NO') . ', Pendiente: ' . ($is_payment_pending ? 'SI' : 'NO') . ', Rechazado: ' . ($is_payment_rejected ? 'SI' : 'NO'), ['source' => 'EpaycoSubscription_Gateway']);
                    
                    if ($is_payment_approved) {
                        $logger->info('✓ PAGO APROBADO - Actualizando suscripción', ['source' => 'EpaycoSubscription_Gateway']);
                        $this->completedPayment($order,$subscription,$sub);
                        $refPayco = isset($sub->data->ref_payco) ? esc_html($sub->data->ref_payco) : 'N/A';
                    }else{
                        if( $is_payment_rejected ) {
                            $logger->warning('⚠ PAGO RECHAZADO', ['source' => 'EpaycoSubscription_Gateway']);
                            $response = isset($sub->data->respuesta) ? esc_html($sub->data->respuesta) : 'Rechazada';
                            $logger->info('Motivo del rechazo: ' . $response, ['source' => 'EpaycoSubscription_Gateway']);
                            wc_add_notice(__("La transacción {$response}, por favor intente de nuevo.", 'epayco-subscriptions-for-woocommerce'), 'error');
                            wp_safe_redirect($order->get_checkout_payment_url(true));
                            exit;
                        }else{
                            if( $is_payment_pending ) {
                                $logger->info('⏳ PAGO PENDIENTE DE APROBACIÓN', ['source' => 'EpaycoSubscription_Gateway']);
                                $this->pendingPayment($order,$subscription,$sub);
                                $refPayco = isset($sub->data->ref_payco) ? esc_html($sub->data->ref_payco) : 'N/A';
                            }
                        }
                    }
                }
            }else{
                $logger->error('ERROR: Objeto subscriptions no es un array', ['source' => 'EpaycoSubscription_Gateway']);
                throw new Exception(__('Objeto no es una instancia de WC_Subscription.', 'epayco-subscriptions-for-woocommerce'));
            }
            WC()->cart->empty_cart();
            $arguments = array();
            $arguments['ref_payco'] = $refPayco;
            $redirect_url = add_query_arg($arguments, $order->get_checkout_order_received_url());
            $logger->info('✓ Redirigiendo a página de agradecimiento con referencia: ' . $refPayco, ['source' => 'EpaycoSubscription_Gateway']);
            wp_redirect($redirect_url);
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger->error('✗ Exception en handleSubscriptions: ' . $exception->getMessage(), ['source' => 'EpaycoSubscription_Gateway']);
                $logger->error('Stack trace: ' . $exception->getTraceAsString(), ['source' => 'EpaycoSubscription_Gateway']);
            }
            wc_add_notice($exception->getMessage(), 'error');
            $redirect_url = $order->get_checkout_payment_url(true);
            wp_safe_redirect($redirect_url);
            exit;  
        }
    }

    public function cancelSubscription($subscription_id)
    {
        try {
            $result = $this->epaycoSdk->subscriptions->cancel($subscription_id);
            error_log("ePayco cancel result: " . print_r($result, true));
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            }
            error_log("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger = wc_get_logger();
                $logger->info("Error al cancelar la suscripción $subscription_id: " . $exception->getMessage());
            }
            throw $exception;
        }
    }

}