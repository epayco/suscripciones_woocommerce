<?php

namespace EpaycoSubscription\Woocommerce\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

use Exception;

use EpaycoSubscription\Woocommerce\Gateways\EpaycoSuscription;

class Plan extends EpaycoSuscription
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $plans
     */
    public function getPlans(array $plans)
    {
        foreach ($plans as $key => $plan) {
            try {
                $plan = $this->epaycoSdk->plan->get(strtolower($plans[$key]['id_plan']));
                if ($plan->status) {
                    unset($plans[$key]);
                    return $plan;
                } else {
                    return false;
                }
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
                    $logger->info("getPlans" . $exception->getMessage());
                }
                //echo esc_html($exception->getMessage());
                if (class_exists('WC_Logger')) {
                    $logger = wc_get_logger();
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


    public function plansCreate($plans,$order)
    {
        if (class_exists('WC_Logger')) {
            $logger = wc_get_logger();
        }
        foreach ($plans as $plan) {
            try {
                $body = [
                    "id_plan" => (string)strtolower($plan['id_plan']),
                    "name" => (string)$plan['name'],
                    "description" => (string)$plan['description'],
                    "amount" => $plan['amount'],
                    "currency" => $plan['currency'],
                    "interval" => $plan['interval'],
                    "interval_count" => $plan['interval_count'],
                    "trial_days" => $plan['trial_days'],
                    "iva" => $plan['iva'],
                ];
                 if($order->get_total() > 0){
                    $body = array_merge($body,
                        [
                            "firstPaymentAdditionalCost" => $plan['firstPaymentAdditionalCost'],
                            "greetMessage" => "gracias por tu compra con epayco"
                        ],
                    );
                }
                $plan_ = $this->epaycoSdk->plan->create($body);
                if (!$plan_->status) {
                    $planJson = json_decode(json_encode($plan_), true);
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
                    //$logger->info(json_encode($plan_));
                }
                return $plan_;
            } catch (Exception $exception) {
                if (class_exists('WC_Logger')) {
                    $logger->info("plansCreate" . $exception->getMessage());
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

}