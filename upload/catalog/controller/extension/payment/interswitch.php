<?php

class ControllerExtensionPaymentInterswitch extends Controller {

    public function index() {


        $data['button_confirm'] = $this->language->get('button_confirm');


        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $billin_info = $this->session->data['payment_address'];

        $subject = trim($this->config->get('config_name'));


        $total_amount = trim($order_info['total']) * 100;
        $order_id = $order_info['order_id'];
        $currency_code = $order_info['currency_code'] == 'KEN' ? 'KES' : $order_info['currency_code'];
        $datePayment = date('Y-m-d\TH:i:s');
        $customerInfor = $order_info['email'] . '|' . $billin_info['firstname'] . '|' . $billin_info['lastname'] . '|' . $order_info['email'] . '|' . $order_info['telephone'] . '|' . $billin_info['city'] . '|' . $billin_info['country'] . '|' . $billin_info['postcode'] . '|' . $billin_info['address_1'] . '|' . $billin_info['city'];
        $config = array(
            'merchant_code' => $this->config->get('payment_interswitch_merchant_code'),
            'terminal_id' => $this->config->get('payment_interswitch_terminal_id'),
            'notify_url' => $this->url->link('checkout/interswitch'),
            'orderId' => $order_id,
            "currency_code" => $currency_code,
            "total_amount" => $total_amount,
            "dateOfPayment" => $datePayment,
            "transactionReference" => $this->generaterTransactionReference($order_id),
            'gateway_script' => $this->getUrl($this->config->get('payment_interswitch_enviroment')) . "/webpay/button/functions.js",
            'client_id' => $this->config->get('payment_interswitch_client_id'),
            'client_secret' => $this->config->get('payment_interswitch_client_secret'),
            'domain' => $this->config->get('payment_interswitch_domain'),
            'customerInfor' => $customerInfor,
            'preauth' => "1",
            'fee' => "0"
        );


        $data['config'] = $config;

        //$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);
        return $this->load->view('extension/payment/interswitch', $data);
    }

    function getUrl($env) {

        return $env == "sandbox" ? "https://testmerchant.interswitch-ke.com" : "https://merchant.interswitch-ke.com";
    }

    function generaterTransactionReference($orderId) {
        $characters = '0abcd' . time() . 'efz1nrstu2o' . time() . '123456' . time() . 'pqghijk' . time() . 'lm3456vwxy' . time() . '789';
        $characters = strtoupper($characters);
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $orderId;
    }

}
