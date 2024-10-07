<?php

class Subscription_Epayco_SE extends WC_Payment_Epayco_Subscription
{
    public $epayco;

    public function __construct()
    {
        parent::__construct();
        $lang = get_locale();
        $lang = explode('_', $lang);
        $lang = $lang[0];
        $this->epayco = new Epayco\Epayco(
            [
                "apiKey" => $this->apiKey,
                "privateKey" => $this->privateKey,
                "lenguage" => strtoupper($lang),
                "test" => $this->isTest
            ]
        );
    }

    public function subscription_epayco(array $params)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $table_name_setings = $wpdb->prefix . 'epayco_setings';
        $order_id = $params['order_id'];
        $order = new WC_Order($order_id);
        $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
        $token = $params['epaycoToken'];
        $customerName = $params['card-number'] ? $params['card-number'] : $params['name'];
        $customerData = $this->paramsBilling($subscriptions, $order, $customerName);
        $customerData['token_card'] = $token;
        $sql_ = 'SELECT * FROM ' . $table_name_setings . ' WHERE id_payco = ' . $this->custIdCliente . ' AND email = ' . $customerData['email'];
        $customerGetData = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table_name_setings WHERE id_payco = %d AND email = %s",
                $this->custIdCliente,
                $customerData['email']
            )
        );
        if (count($customerGetData) == 0) {
            $customer = $this->customerCreate($customerData);
            if ($customer->data->status == 'error' || !$customer->status) {
                $response_status = [
                    'status' => false,
                    'message' => __($customer->message, 'epayco-subscription')
                ];
                return $response_status;
            }
            $inserCustomer = $wpdb->insert(
                $table_name_setings,
                [
                    'id_payco' => $this->custIdCliente,
                    'customer_id' => $customer->data->customerId,
                    'token_id' => $customerData['token_card'],
                    'email' => $customerData['email']
                ]
            );
            if (!$inserCustomer) {
                $response_status = [
                    'status' => false,
                    'message' => __('internar error, tray again', 'epayco-subscription')
                ];
                return $response_status;
            }
            $customerData['customer_id'] = $customer->data->customerId;
        } else {
            $count_customers = 0;
            for ($i = 0; $i < count($customerGetData); $i++) {
                if ($customerGetData[$i]->email == $customerData['email']) {
                    $count_customers += 1;
                }
            }
            if ($count_customers == 0) {
                $customer = $this->customerCreate($customerData);
                if ($customer->data->status == 'error') {
                    $response_status = [
                        'status' => false,
                        'message' => __($customer->message, 'epayco-subscription')
                    ];
                    return $response_status;
                }
                $inserCustomer = $wpdb->insert(
                    $table_name_setings,
                    [
                        'id_payco' => $this->custIdCliente,
                        'customer_id' => $customer->data->customerId,
                        'token_id' => $customerData['token_card'],
                        'email' => $customerData['email']
                    ]
                );
                if (!$inserCustomer) {
                    $response_status = [
                        'status' => false,
                        'message' => __('internar error, tray again', 'epayco-subscription')
                    ];
                    return $response_status;
                }
                $customerData['customer_id'] = $customer->data->customerId;
            } else {
                for ($i = 0; $i < count($customerGetData); $i++) {
                    if ($customerGetData[$i]->email == $customerData['email'] && $customerGetData[$i]->token_id != $token) {
                        $customerAddtoken = $this->customerAddToken($customerGetData[$i]->customer_id, $customerData['token_card']);
                    }
                    $customerData['customer_id'] = $customerGetData[$i]->customer_id;
                }
            }
        }
        $confirm_url = $this->getUrlNotify($order_id);
        $plans = $this->getPlansBySubscription($subscriptions);
        $getPlans = $this->getPlans($plans);
        //$getPlansList = $this->getPlansList();

        if (!$getPlans) {
            $validatePlan_ = $this->validatePlan(true, $order_id, $plans, $subscriptions, $customerData, $confirm_url, $order, false, false, null);
        } else {
            $validatePlan_ = $this->validatePlan(false, $order_id, $plans, $subscriptions, $customerData, $confirm_url, $order, true, false, $getPlans);
        }

        if ($validatePlan_) {
            try {
                return $validatePlan_;
            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('getPlans: ' . $exception->getMessage());
            }
        }

    }

    public function customerCreate(array $data)
    {
        $customer = false;
        try {
            $customer = $this->epayco->customer->create(
                [
                    "token_card" => $data['token_card'],
                    "name" => $data['name'],
                    "email" => $data['email'],
                    "phone" => $data['phone'],
                    "cell_phone" => $data['phone'],
                    "country" => $data['country'],
                    "city" => $data['city'],
                    "address" => $data['address'],
                    "default" => true
                ]
            );
        } catch (Exception $exception) {
            echo 'create client: ' . $exception->getMessage();
            die();
        }

        return $customer;
    }

    public function customerAddToken($customer_id, $token_card)
    {
        $customer = false;
        try {
            $customer = $this->epayco->customer->addNewToken(
                [
                    "token_card" => $token_card,
                    "customer_id" => $customer_id
                ]
            );
        } catch (Exception $exception) {
            echo 'add token: ' . $exception->getMessage();
            die();
        }

        return $customer;
    }

    public function getPlans(array $plans)
    {
        foreach ($plans as $key => $plan) {
            try {
                $plan = $this->epayco->plan->get($plans[$key]['id_plan']);
                if ($plan->status) {
                    unset($plans[$key]);
                    return $plan;
                } else {
                    return false;
                }

            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('getPlans: ' . $exception->getMessage());
            }
        }
    }

    public function getPlansList()
    {
        try {
            $plan = $this->epayco->plan->getList();
            if ($plan->status) {
                return $plan;
            } else {
                return false;
            }

        } catch (Exception $exception) {
            var_dump($exception->getMessage());
            die();
            subscription_epayco_se()->log('getPlansList: ' . $exception->getMessage());
        }

    }

    public function getPlanById($plan_id)
    {
        try {
            $plan = $this->epayco->plan->get($plan_id);
            if ($plan->status) {
                return $plan;
            } else {
                return false;
            }

        } catch (Exception $exception) {
            var_dump($exception->getMessage());
                die();
            subscription_epayco_se()->log('getPlans ' . $exception->getMessage());
        }
    }


    public function validatePlan($create, $order_id, array $plans, $subscriptions, $customer, $confirm_url, $order, $confirm = null, $update = null, $getPlans = null)
    {

        if ($create) {
            $newPLan = $this->plansCreate($plans);
            if ($newPLan->status) {
                $getPlans_ = $this->getPlans($plans);
                if ($getPlans_) {
                    $eXistPLan = $this->validatePlanData($plans, $getPlans_, $order_id, $subscriptions, $customer, $confirm_url, $order);
                } else {
                    $this->validatePlan(true, $order_id, $plans, $subscriptions, $customer, $confirm_url, $order, false, false, null);
                }
            } else {
                $response_status = [
                    'status' => false,
                    'message' => __($newPLan->message, 'epayco-subscription')
                ];
                return $response_status;
            }
        } else {
            if ($confirm) {
                $eXistPLan = $this->validatePlanData($plans, $getPlans, $order_id, $subscriptions, $customer, $confirm_url, $order);
            }
        }
        return $eXistPLan;
    }

    public function validatePlanData($plans, $getPlans, $order_id, $subscriptions, $customer, $confirm_url, $order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $wc_order_product_lookup = $wpdb->prefix . "wc_order_product_lookup";
        foreach ($plans as $plan) {
            $plan_amount_cart = $plan['amount'];
            $plan_id_cart = $plan['id_plan'];
            $plan_currency_cart = $plan['currency'];
        }
        $plan_amount_epayco = $getPlans->plan->amount;
        $plan_id_epayco = $getPlans->plan->id_plan;
        $plan_currency_epayco = $getPlans->plan->currency;
        //validar que el id del plan del carrito concuerda con el plan creado
        if ($plan_id_cart == $plan_id_epayco) {
            //validar que el valor del carrito de compras concuerda con el del plan creado
            try {
                if (intval($plan_amount_cart) == $plan_amount_epayco) {
                    return $this->process_payment_epayco($plans, $customer, $confirm_url, $subscriptions, $order);
                } else {
                    return $this->validateNewPlanData($subscriptions, $order_id, true, false,$plans,$customer, $confirm_url, $order);
                }
            } catch (Exception $exception) {
                echo $exception->getMessage();
                var_dump($exception->getMessage());
                die();
                return false;
            }
        } else {
            echo 'el id del plan creado no concuerda!';
            die();
        }
    }

    public function validateNewPlanData($subscriptions, $order_id, $value, $currency, $plans, $customer, $confirm_url, $order)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'epayco_plans';
        $wc_order_product_lookup = $wpdb->prefix . "wc_order_product_lookup";
        /*valida la actualizacion del precio del plan*/
        foreach ($subscriptions as $key => $subscription) {
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $product_id_ = $product_plan['id'];
            $porciones = explode("-", $product_id_);
            $product_id = $porciones[0];
        }
        $sql = 'SELECT * FROM ' . $wc_order_product_lookup . ' WHERE order_id =' . intval($order_id);
        $results = $wpdb->get_results($sql, OBJECT);
        $product_id = $results[0]->product_id ? $results[0]->product_id : $product_id;
        $query = 'SELECT * FROM ' . $table_name . ' WHERE order_id =' . intval($order_id);
        $orderData = $wpdb->get_results($query, OBJECT);
        if (count($orderData) == 0) {
            if ($value) {
                $savePlanId_ = $this->savePlanId($order_id, $plans, $subscriptions, null, $product_id);
                if ($savePlanId_) {
                    $orderData = $wpdb->get_results($query, OBJECT);
                    if (count($orderData) == 0) {
                        return false;
                    } else {
                        foreach ($plans as $plan) {
                            $plan_currency_cart = $plan['currency'];
                            $plan_interval_cart = $plan['interval'];
                            $plan_interval_count_cart = $plan['interval_count'];
                            $plan_trial_days_cart = $plan['trial_days'];
                            $plan_name = $plan['name'];
                            $plan_description = $plan['description'];
                        }

                        $newPlanToCreated[0] = [
                            "id_plan" => (string)$orderData[0]->plan_id,
                            "name" => $plan_name,
                            "description" => $plan_description,
                            "currency" => $plan_currency_cart,
                            "trial_days" => intval($plan_trial_days_cart),
                            "amount" => $orderData[0]->amount,
                            "interval" => $plan_interval_cart,
                            "interval_count" => $plan_interval_count_cart,
                        ];

                        //crear nuevo plan con precio actualizado
                        $newPLan = $this->plansCreate($newPlanToCreated);
                        if ($newPLan->status) {
                            $getPlans_ = $this->getPlans($newPlanToCreated);
                            if ($getPlans_) {
                                return $this->process_payment_epayco($newPlanToCreated, $customer, $confirm_url, $subscriptions, $order);
                            }
                        }
                    }

                } else {
                    return false;
                }

            }
        } else {

            $plan_id_s = $orderData[0]->plan_id;
            $getPlanById_ = $this->getPlanById($plan_id_s);
            if ($getPlanById_->status) {
                $newPlanToCreated_[0] = [
                    "id_plan" => $getPlanById_->plan->id_plan,
                    "name" => $getPlanById_->plan->name,
                    "description" => $getPlanById_->plan->description,
                    "amount" => $getPlanById_->plan->amount,
                    "currency" => $getPlanById_->plan->currency,
                    "interval_count" => $getPlanById_->plan->interval_count,
                    "interval" => $getPlanById_->plan->interval,
                    "trial_days" => $getPlanById_->plan->id_plan,
                ];

                return $this->process_payment_epayco($newPlanToCreated_, $customer, $confirm_url, $subscriptions, $order);
            }
        }
    }


    public function plansCreate(array $plans)
    {

        foreach ($plans as $plan) {
            try {
                $plan_ = $this->epayco->plan->create(
                    [
                        "id_plan" => (string)$plan['id_plan'],
                        "name" => (string)$plan['name'],
                        "description" => (string)$plan['description'],
                        "amount" => $plan['amount'],
                        "currency" => $plan['currency'],
                        "interval" => $plan['interval'],
                        "interval_count" => $plan['interval_count'],
                        "trial_days" => $plan['trial_days']
                    ]
                );

                return $plan_;
            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('create plan: ' . $exception->getMessage());
            }
        }
    }

    public function subscriptionCreate(array $plans, array $customer, $confirm_url)

    {
        foreach ($plans as $plan) {
            try {
                $suscriptioncreted = $this->epayco->subscriptions->create(
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

                return $suscriptioncreted;

            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('subscriptionCreate: ' . $exception->getMessage());
            }
        }
    }

    public function subscriptionCharge(array $plans, array $customer, $confirm_url)
    {
        $subs = [];

        foreach ($plans as $plan) {
            try {
                $subs[] = $this->epayco->subscriptions->charge(
                    [
                        "id_plan" => $plan['id_plan'],
                        "customer" => $customer['customer_id'],
                        "token_card" => $customer['token_card'],
                        "doc_type" => $customer['type_document'],
                        "doc_number" => $customer['doc_number'],
                        "ip" => $this->getIP(),
                        "url_confirmation" => $confirm_url,
                        "method_confirmation" => "POST"
                    ]
                );

            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('subscriptionCharge: ' . $exception->getMessage());
            }
        }

        return $subs;
    }

    public function cancelSubscription($subscription_id)
    {
        try {
            $this->epayco->subscriptions->cancel($subscription_id);
        } catch (Exception $exception) {
            var_dump($exception->getMessage());
                die();
            subscription_epayco_se()->log('cancelSubscription: ' . $exception->getMessage());
        }
    }

    private function getWooCommerceSubscriptionFromOrderId($orderId)
    {
        $subscriptions = wcs_get_subscriptions_for_order($orderId);

        return $subscriptions;
    }


    public function paramsBilling($subscriptions, $order, $customer_name)
    {
        $data = [];
        $subscription = end($subscriptions);
        if ($subscription) {
            $doc_number = get_post_meta($subscription->get_id(), '_billing_dni', true) != null?get_post_meta($subscription->get_id(), '_billing_dni', true):$order->get_meta('_billing_dni');
            $type_document = get_post_meta($subscription->get_id(), '_billing_type_document', true)!=null?get_post_meta($subscription->get_id(), '_billing_type_document', true):$order->get_meta('_billing_type_document');
            $data['name'] = $customer_name;
            $data['email'] = $subscription->get_billing_email();
            $data['phone'] = $subscription->get_billing_phone();
            $data['country'] = $subscription->get_shipping_country() ? $subscription->get_shipping_country() : $subscription->get_billing_country();
            $data['city'] = $subscription->get_shipping_city() ? $subscription->get_shipping_city() : $subscription->get_billing_city();
            $data['address'] = $subscription->get_shipping_address_1() ? $subscription->get_shipping_address_1() . " " . $subscription->get_shipping_address_2() : $subscription->get_billing_address_1() . " " . $subscription->get_billing_address_2();
            $data['doc_number'] = $doc_number;
            $data['type_document'] = $type_document;

            return $data;
        } else {
            $redirect = array(
                'result' => 'success',
                'redirect' => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
            wc_add_notice('EL producto que intenta pagar no es permitido', 'error');
            wp_redirect($redirect["redirect"]);
            die();
        }

    }

    public function getPlansBySubscription(array $subscriptions)
    {
        $plans = [];
        foreach ($subscriptions as $key => $subscription) {
            $total_discount = $subscription->get_total_discount();
            $order_currency = $subscription->get_currency();
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $quantity = $product_plan['quantity'];
            $product_name = $product_plan['name'];
            $product_id = $product_plan['id'];
            $trial_days = $this->getTrialDays($subscription);
            $plan_code = "$product_name-$product_id";
            $plan_code = $trial_days > 0 ? "$product_name-$product_id-$trial_days" : $plan_code;
            $plan_code = $this->currency !== $order_currency ? "$plan_code-$order_currency" : $plan_code;
            $plan_code = $quantity > 1 ? "$plan_code-$quantity" : $plan_code;
            $plan_code = $total_discount > 0 ? "$plan_code-$total_discount" : $plan_code;
            $plan_code = rtrim($plan_code, "-");
            $plan_id = str_replace(array("-", "--"), array("_", ""), $plan_code);
            $plan_name = trim(str_replace("-", " ", $product_name));
            $plans[] = array_merge(
                [
                    "id_plan" => str_replace("__", "_", $plan_id),
                    "name" => "Plan $plan_name",
                    "description" => "Plan $plan_name",
                    "currency" => $order_currency,
                ],
                [
                    "trial_days" => $trial_days
                ],
                $this->intervalAmount($subscription)
            );
        }
        return $plans;
    }

    public function updatePlansBySubscription(array $subscriptions)
    {
        $ran = rand(1, 999);
        $plans = [];
        foreach ($subscriptions as $key => $subscription) {
            $total_discount = $subscription->get_total_discount();
            $order_currency = $subscription->get_currency();
            $products = $subscription->get_items();
            $product_plan = $this->getPlan($products);
            $quantity = $product_plan['quantity'];
            $product_name = $product_plan['name'];
            $product_id = $product_plan['id'];
            $trial_days = $this->getTrialDays($subscription);
            $plan_code = "$product_name-$product_id";
            $plan_code = $trial_days > 0 ? "$product_name-$product_id-$trial_days" : $plan_code;
            $plan_code = $this->currency !== $order_currency ? "$plan_code-$order_currency" : $plan_code;
            $plan_code = $quantity > 1 ? "$plan_code-$quantity" : $plan_code;
            $plan_code = $total_discount > 0 ? "$plan_code-$total_discount" : $plan_code;
            $plan_code = rtrim($plan_code, "-");
            $plans[] = array_merge(
                [
                    "id_plan" => $plan_code . '-' . $ran,
                    "name" => "Plan $plan_code-$ran",
                    "description" => "Plan $plan_code-$ran",
                    "currency" => $order_currency,
                ],
                [
                    "trial_days" => $trial_days
                ],
                $this->intervalAmount($subscription)
            );
        }
        return $plans;
    }

    public function getPlan($products)
    {
        $product_plan = [];

        $product_plan['name'] = '';
        $product_plan['id'] = 0;
        $product_plan['quantity'] = 0;

        foreach ($products as $product) {
            $product_plan['name'] .= "{$product['name']}-";
            $product_plan['id'] .= "{$product['product_id']}-";
            $product_plan['quantity'] .= $product['quantity'];
        }

        $product_plan['name'] = $this->cleanCharacters($product_plan['name']);

        return $product_plan;
    }

    public function intervalAmount(WC_Subscription $subscription)
    {
        return [
            "interval" => $subscription->get_billing_period(),
            "amount" => $subscription->get_total(),
            "interval_count" => $subscription->get_billing_interval()
        ];
    }

    public function getTrialDays(WC_Subscription $subscription)
    {
        $trial_days = "0";
        $trial_start = $subscription->get_date('start');
        $trial_end = $subscription->get_date('trial_end');

        if ($trial_end > 0)
            $trial_days = (string)(strtotime($trial_end) - strtotime($trial_start)) / (60 * 60 * 24);

        return $trial_days;
    }

    public function cleanCharacters($string)
    {
        $string = str_replace(' ', '-', $string);
        $patern = '/[^A-Za-z0-9\-]/';
        return preg_replace($patern, '', $string);
    }

    public function getUrlNotify($order_id)
    {
        $confirm_url = get_site_url() . "/";
        $confirm_url = add_query_arg('wc-api', 'WC_Payment_Epayco_Subscription', $confirm_url);
        $confirm_url = add_query_arg('order_id', $order_id, $confirm_url);
        $confirm_url = $confirm_url . '&confirmation=1';
        return $confirm_url;
    }

    public function handleStatusSubscriptions(array $subscriptionsStatus, array $subscriptions, array $customer, $order, $customerId, $suscriptionId, $planId)
    {

        global $wpdb;
        $table_subscription_epayco = $wpdb->prefix . 'epayco_subscription';

        $count = 0;
        $messageStatus = [];
        $messageStatus['status'] = true;
        $messageStatus['message'] = [];
        $messageStatus['ref_payco'] = [];
        $quantitySubscriptions = count($subscriptionsStatus);
        $current_state = $order->get_status();
        foreach ($subscriptions as $subscription) {

            $sub = $subscriptionsStatus[$count];
            $data = count(get_object_vars($sub->data));
            if ($data < 10) {
                $isTestTransaction = $this->isTest == true ? "yes" : "no";
                update_option('epayco_order_status', $isTestTransaction);
                $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";

                if ($isTestMode == "true") {
                    $message = 'Pago pendiente de aprobación Prueba';
                    $orderStatus = "epayco_on_hold";
                    if ($current_state != "epayco_on_hold" ||
                        $current_state == "pending") {
                        $this->restore_order_stock($order->id, '+');
                    }
                } else {
                    $message = 'Pago pendiente de aprobación';
                    $orderStatus = "epayco-on-hold";
                    if ($current_state != "epayco-on-hold" ||
                        $current_state == "pending") {
                        $this->restore_order_stock($order->id, '+');
                    }
                }
                $order->update_status($orderStatus);
                $order->add_order_note($message);
                $subscription->update_status('on-hold');
            } else {

                $isTestTransaction = $sub->data->enpruebas == 1 ? "yes" : "no";
                update_option('epayco_order_status', $isTestTransaction);
                $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
                if (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 2 || $sub->data->cod_respuesta === 4) {
                    $messageStatus['message'] = array_merge($messageStatus['message'], ["estado: {$sub->data->respuesta}"]);
                    if ($isTestMode == "true") {
                        $message = 'Pago rechazado Prueba: ' . $sub->data->ref_payco;
                        if ($current_state == "epayco_failed" ||
                            $current_state == "epayco_cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco_processing" ||
                            $current_state == "epayco_completed" ||
                            $current_state == "processing_test" ||
                            $current_state == "completed_test"
                        ) {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('cancelled');
                        } else {
                            $messageClass = 'woocommerce-error';
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('cancelled');
                        }

                    } else {
                        if ($current_state == "epayco-failed" ||
                            $current_state == "epayco-cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco-processing" ||
                            $current_state == "epayco-completed" ||
                            $current_state == "processing" ||
                            $current_state == "completed"
                        ) {
                            $subscription->payment_failed();
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note('Pago fallido');
                        } else {
                            $message = 'Pago rechazado' . $sub->data->ref_payco;
                            $messageClass = 'woocommerce-error';
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note('Pago fallido');
                            $subscription->payment_failed();
                        }
                    }
                }

                if (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 1) {
                    if ($isTestMode == "true") {
                        $message = 'Pago exitoso Prueba';
                        switch ($this->epayco_endorder_state) {
                            case 'epayco-processing':
                                {
                                    $orderStatus = 'epayco_processing';
                                }
                                break;
                            case 'epayco-completed':
                                {
                                    $orderStatus = 'epayco_completed';
                                }
                                break;
                            case 'processing':
                                {
                                    $orderStatus = 'processing_test';
                                }
                                break;
                            case 'completed':
                                {
                                    $orderStatus = 'completed_test';
                                }
                                break;
                        }
                    } else {
                        $message = 'Pago exitoso';
                        $orderStatus = $this->epayco_endorder_state;
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $note = sprintf(__('Successful subscription (subscription ID: %s), reference (%s)', 'epayco-subscription'),
                        $sub->subscription->_id, $sub->data->ref_payco);
                    $subscription->add_order_note($note);
                    $messageStatus['ref_payco'] = array_merge($messageStatus['ref_payco'], [$sub->data->ref_payco]);
                    $subscription->payment_complete();
                    $this->restore_order_stock($order->get_id(), "+");
                } elseif (isset($sub->data->cod_respuesta) && $sub->data->cod_respuesta === 3) {
                    if ($isTestMode == "true") {
                        $message = 'Pago pendiente de aprobación Prueba';
                        $orderStatus = "epayco_on_hold";
                        if ($current_state != "epayco_on_hold") {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago pendiente de aprobación';
                        $orderStatus = "epayco-on-hold";
                        if ($current_state != "epayco_on_hold") {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');

                    $wpdb->insert(
                        $table_subscription_epayco,
                        [
                            'order_id' => $subscription->get_id(),
                            'ref_payco' => $sub->data->ref_payco
                        ]
                    );
                }
            }
            $messageStatus['ref_payco'] = array_merge($messageStatus['ref_payco'], [$sub->data->ref_payco]);
            $count++;

            if ($count === $quantitySubscriptions && count($messageStatus['message']) >= $count)
                $messageStatus['status'] = false;


            update_post_meta($subscription->get_id(), 'subscription_id', $suscriptionId);
            update_post_meta($subscription->get_id(), 'id_client', $customerId);
            update_post_meta($subscription->get_id(), 'plan_id', $planId);
            update_post_meta($order->get_id(), 'subscription_id', $suscriptionId);
            update_post_meta($order->get_id(), 'id_client', $customerId);
            update_post_meta($order->get_id(), 'plan_id', $planId);
        }
        return $messageStatus;

    }

    public function savePlanId($order_id, array $plans, array $subscriptions, $update = null, $product_id = null)
    {
        $ran = rand(1, 9999);

        global $wpdb;
        $table_subscription_epayco = $wpdb->prefix . 'epayco_plans';

        if ($update) {

            foreach ($plans as $plan) {
                try {
                    $plan_id_ = (string)$plan['id_plan'];
                    $plan_amount = floatval($plan['amount']);
                    $plan_currency = (string)$plan['currency'];
                    $result = $wpdb->update(
                        $table_subscription_epayco,
                        [
                            'order_id' => intval($order_id),
                            'plan_id' => $plan_id_,
                            'amount' => $plan_amount,
                            'product_id' => $product_id,
                            'currency' => $plan_currency,
                        ], [
                            'order_id' => intval($order_id),
                            'product_id' => $product_id,
                        ]
                    );
                } catch (Exception $exception) {
                    var_dump($exception->getMessage());
                die();
                    subscription_epayco_se()->log('save plan: ' . $exception->getMessage());
                }
            }
        } else {

            try {
                foreach ($plans as $plan) {
                    $plan_id_ = (string)$plan['id_plan'] . "_" . $ran;
                    $plan_amount = floatval($plan['amount']);
                    $plan_currency = (string)$plan['currency'];
                }

                $dataToSave = [
                    'order_id' => intval($order_id),
                    'plan_id' => $plan_id_,
                    'amount' => $plan_amount,
                    'product_id' => $product_id,
                    'currency' => $plan_currency,
                ];

                $result = $wpdb->insert(
                    $table_subscription_epayco,
                    $dataToSave
                );
                $result = 1;

            } catch (Exception $exception) {
                var_dump($exception->getMessage());
                die();
                subscription_epayco_se()->log('save plan: ' . $exception->getMessage());
            }
        }
        return $result;
    }

    public function process_payment_epayco(array $plans, array $customerData, $confirm_url, $subscriptions, $order)
    {
        $subsCreated = $this->subscriptionCreate($plans, $customerData, $confirm_url);
        if ($subsCreated->status) {
            $subs = $this->subscriptionCharge($plans, $customerData, $confirm_url);
            foreach ($subs as $sub) {
                $customerId = isset($subsCreated->customer->_id) ? $subsCreated->customer->_id : null;
                $suscriptionId = isset($subsCreated->id) ? $subsCreated->id : null;
                $planId = isset($subsCreated->data->idClient) ? $subsCreated->data->idClient : null;
                $validation = !is_null($sub->status) ? $sub->status : $sub->success;
                if ($validation) {
                    $messageStatus = $this->handleStatusSubscriptions($subs, $subscriptions, $customerData, $order, $customerId, $suscriptionId, $planId);

                    $response_status = [
                        'ref_payco' => $messageStatus['ref_payco'][0],
                        'status' => $messageStatus['status'],
                        'message' => $messageStatus['message'],
                        'url' => $order->get_checkout_order_received_url()
                    ];
                } else {

                    $errorMessage = $sub->data->errors;
                    $response_status = [
                        'ref_payco' => null,
                        'status' => false,
                        'message' => $errorMessage,
                        'url' => $order->get_checkout_order_received_url()
                    ];
                }
            }
        } else {
            $errorMessage = $subsCreated->data->description;
            $response_status = [
                'ref_payco' => null,
                'status' => false,
                'message' => $errorMessage,
                'url' => $order->get_checkout_order_received_url()
            ];
        }
        return $response_status;
    }

    public function getIP()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = '127.0.0.1';

        return $ipaddress;
    }

    public function authSignature($x_ref_payco, $x_transaction_id, $x_amount, $x_currency_code)
    {
        $signature = hash('sha256',
            trim($this->custIdCliente) . '^'
            . trim($this->pKey) . '^'
            . $x_ref_payco . '^'
            . $x_transaction_id . '^'
            . $x_amount . '^'
            . $x_currency_code
        );
        return $signature;
    }

    /**
     * @param $order_id
     */
    public function restore_order_stock($order_id, $operation = 'increase')
    {
        $order = wc_get_order($order_id);
        if (!get_option('woocommerce_manage_stock') == 'yes' && !sizeof($order->get_items()) > 0) {
            return;
        }

        foreach ($order->get_items() as $item) {
            // Get an instance of corresponding the WC_Product object
            $product = $item->get_product();
            $qty = $item->get_quantity(); // Get the item quantity
            wc_update_product_stock($product, $qty, $operation);
        }
    }

    public function cancelledPayment($order_id, $id_client, $subscription_id, $planId)
    {
        $order = new WC_Order($order_id);
        $current_state = $order->get_status();
        $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
        $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
        foreach ($subscriptions as $subscription) {
            if ($isTestMode == "true") {
                $message = 'Pago rechazado Prueba';
                if ($current_state == "epayco_failed" ||
                    $current_state == "epayco_cancelled" ||
                    $current_state == "failed" ||
                    $current_state == "epayco_processing" ||
                    $current_state == "epayco_completed" ||
                    $current_state == "processing_test" ||
                    $current_state == "completed_test"
                ) {
                    $order->update_status('epayco_cancelled');
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                } else {
                    $messageClass = 'woocommerce-error';
                    $order->update_status('epayco_cancelled');
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                }

            } else {
                if ($current_state == "epayco-failed" ||
                    $current_state == "epayco-cancelled" ||
                    $current_state == "failed" ||
                    $current_state == "epayco-processing" ||
                    $current_state == "epayco-completed" ||
                    $current_state == "processing" ||
                    $current_state == "completed"
                ) {
                    $subscription->payment_failed();
                    $order->update_status('epayco-cancelled');
                    $order->add_order_note('Pago fallido');
                } else {
                    $message = 'Pago rechazado';
                    $messageClass = 'woocommerce-error';
                    $order->update_status('epayco-cancelled');
                    $order->add_order_note('Pago fallido');
                    $subscription->payment_failed();
                }
            }
            update_post_meta($subscription->get_id(), 'subscription_id', $subscription_id);
            update_post_meta($subscription->get_id(), 'id_client', $id_client);
            update_post_meta($subscription->get_id(), 'plan_id', $planId);
            update_post_meta($order->get_id(), 'subscription_id', $subscription_id);
            update_post_meta($order->get_id(), 'id_client', $id_client);
            update_post_meta($order->get_id(), 'plan_id', $planId);
            $response_status = [
                'ref_payco' => null,
                'status' => true,
                'message' => null,
                'url' => $order->get_checkout_order_received_url()
            ];

            return $response_status;

        }
    }

    public function subscription_epayco_confirm(array $params)
    {

        $order_id = trim(sanitize_text_field($params['order_id']));
        $order = new WC_Order($order_id);
        $current_state = $order->get_status();
        if (isset($params['x_signature'])) {

            $x_ref_payco = trim(sanitize_text_field($_REQUEST['x_ref_payco']));
            $x_transaction_id = trim(sanitize_text_field($_REQUEST['x_transaction_id']));
            $x_amount = trim(sanitize_text_field($_REQUEST['x_amount']));
            $x_currency_code = trim(sanitize_text_field($_REQUEST['x_currency_code']));
            $x_signature = trim(sanitize_text_field($_REQUEST['x_signature']));
            $x_cod_transaction_state = (int)trim(sanitize_text_field($_REQUEST['x_cod_transaction_state']));
            if ($order_id != "" && $x_ref_payco != "") {
                $authSignature = $this->authSignature($x_ref_payco, $x_transaction_id, $x_amount, $x_currency_code);
            }
        }

        $current_state = $order->get_status();
        if ($authSignature == $x_signature) {
            $subscriptions = $this->getWooCommerceSubscriptionFromOrderId($order_id);
            $x_test_request = trim(sanitize_text_field($_REQUEST['x_test_request']));
            $isTestTransaction = $x_test_request == "TRUE" ? "yes" : "no";
            update_option('epayco_order_status', $isTestTransaction);
            $isTestMode = get_option('epayco_order_status') == "yes" ? "true" : "false";
            foreach ($subscriptions as $subscription) {
                if ($x_cod_transaction_state == 1) {
                    if ($isTestMode == "true") {
                        $message = 'Pago exitoso Prueba';
                        switch ($this->epayco_endorder_state) {
                            case 'epayco-processing':
                                {
                                    $orderStatus = 'epayco_processing';
                                }
                                break;
                            case 'epayco-completed':
                                {
                                    $orderStatus = 'epayco_completed';
                                }
                                break;
                            case 'processing':
                                {
                                    $orderStatus = 'processing_test';
                                }
                                break;
                            case 'completed':
                                {
                                    $orderStatus = 'completed_test';
                                }
                                break;
                        }

                        if (!($current_state == "epayco_on_hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago exitoso';
                        $orderStatus = $this->epayco_endorder_state;
                        if (!($current_state == "epayco-on-hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $subscription->payment_complete();
                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $note = sprintf(__('Successful subscription (subscription ID: %s), reference (%s)', 'epayco-subscription'),
                        $subscription->get_data()['id'], $x_ref_payco);
                    $subscription->add_order_note($note);

                    echo "1";
                }

                if ($x_cod_transaction_state == 2 ||
                    $x_cod_transaction_state == 4 ||
                    $x_cod_transaction_state == 6 ||
                    $x_cod_transaction_state == 9 ||
                    $x_cod_transaction_state == 10 ||
                    $x_cod_transaction_state == 11
                ) {
                    if ($isTestMode == "true") {
                        $message = 'Pago rechazado Prueba: ' . $x_ref_payco;
                        if ($current_state == "epayco_failed" ||
                            $current_state == "epayco_cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco_processing" ||
                            $current_state == "epayco_completed" ||
                            $current_state == "processing_test" ||
                            $current_state == "completed_test"
                        ) {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                        } else {
                            $order->update_status('epayco_cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                            if (
                                $current_state = "epayco-on-hold" ||
                                    $current_state = "epayco-on-hold"
                            ) {
                                $this->restore_order_stock($order->get_id());
                            }

                        }

                    } else {
                        $message = 'Pago rechazado: ' . $x_ref_payco;
                        if ($current_state == "epayco-failed" ||
                            $current_state == "epayco-cancelled" ||
                            $current_state == "failed" ||
                            $current_state == "epayco-processing" ||
                            $current_state == "epayco-completed" ||
                            $current_state == "processing" ||
                            $current_state == "completed"
                        ) {
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');

                        } else {
                            $order->update_status('epayco-cancelled');
                            $order->add_order_note($message);
                            $subscription->update_status('on-hold');
                            if (
                                $current_state = "epayco-on-hold" ||
                                    $current_state = "epayco-on-hold"
                            ) {
                                $this->restore_order_stock($order->get_id());
                            }

                        }
                    }
                    echo $x_cod_transaction_state;
                }

                if ($x_cod_transaction_state == 3) {
                    if ($isTestMode == "true") {
                        $message = 'Pago pendiente de aprobación Prueba';
                        $orderStatus = "epayco_on_hold";
                        if (!($current_state == "epayco_on_hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    } else {
                        $message = 'Pago pendiente de aprobación';
                        $orderStatus = "epayco-on-hold";
                        if (!($current_state == "epayco-on-hold")) {
                            $this->restore_order_stock($order->get_id(), "+");
                        }
                    }

                    $order->update_status($orderStatus);
                    $order->add_order_note($message);
                    $subscription->update_status('on-hold');
                    echo "3";
                    die();
                }
            }

        } else {
            $message = 'Firma no valida';
            echo $message;
        }
    }
}