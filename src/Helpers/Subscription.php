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
                //$logger->info("customer_id : " . json_encode($customer));
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
                    $error = $this->errorMessages($dataError);
                    //$error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                    wc_add_notice($error, 'error');
                    //wp_redirect(wc_get_checkout_url());
                    $redirect_url = $order->get_checkout_payment_url(true);
                    wp_safe_redirect($redirect_url);
                    exit; 
                }
                if (class_exists('WC_Logger')) {
                    //$logger->info("subscriptionCreate : " . json_encode($suscriptioncreted));
                }
                return $suscriptioncreted;
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger->info("subscriptionCreate" . $exception->getMessage());
                }
                //echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger->info("Error : " . $exception->getMessage());
                }
                wc_add_notice($exception->getMessage(), 'error');
                //wp_redirect(wc_get_checkout_url());
                $redirect_url = $order->get_checkout_payment_url(true);
                wp_safe_redirect($redirect_url);
                exit;  
            }
        }
    }

    public function subscriptionCharge(array $plan, array $customer, $confirm_url, $epayco_subscription_id,$order)
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
            
            $validation = isset($sub->success) ? $sub->success : false;
            if (!$validation) {
                $subscriptionJson = json_decode(json_encode($sub), true);
                $dataError = $subscriptionJson;
                $error = $this->errorMessages($dataError);
                //$error = isset($dataError['message']) ? $dataError['message'] : (isset($dataError["message"]) ? $dataError["message"] : __('El token no se puede asociar al cliente, verifique que: el token existe, el cliente no esté asociado y que el token no este asociado a otro cliente.', 'epayco-subscriptions-for-woocommerce'));
                wc_add_notice($error, 'error');
                //wp_redirect(wc_get_checkout_url());
                $redirect_url = $order->get_checkout_payment_url(true);
                wp_safe_redirect($redirect_url);
                exit; 
            }else{
                return $sub;
            }
            
            //return $subs;
        } catch (Exception $exception) {
            if (class_exists('WC_Logger')) {
                $logger->info("subscriptionCharge" . $exception->getMessage());
            }
            //echo esc_html($exception->getMessage());
            if (class_exists('WC_Logger')) {
                $logger->info("Error : " . $exception->getMessage());
            }
            wc_add_notice($exception->getMessage(), 'error');
            //wp_redirect(wc_get_checkout_url());
            $redirect_url = $order->get_checkout_payment_url(true);
            wp_safe_redirect($redirect_url);
            exit;  
        }
    }

}