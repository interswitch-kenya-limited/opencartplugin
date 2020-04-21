<?php

class ControllerCheckoutInterswitch extends Controller {

    public function index() {
        if (isset($_GET['response'])) {

            $result = json_decode(base64_decode($_GET['response']));

            $this->transactionRef = $result->transactionRef;
            $this->order_id = $result->orderId;
            $this->load->model('checkout/order');
            //$this->model_checkout_order->update($this->order_id, 1, "Pending to be processed", false);
            $this->getResponse($result);


            exit;
        }
    }

    function getUrl($env) {

        return $env == "sandbox" ? "https://testmerchant.interswitch-ke.com" : "https://merchant.interswitch-ke.com";
    }

    function getResponse($result) {




        if (isset($result->responseCode) && $result->responseCode != '0') {

            header("Location: " . $this->url->link('checkout/cart'));
        } else {
            $url = $this->getUrl($this->config->get('payment_interswitch_enviroment')) . "/merchant/transaction/query?transactionRef=" . $result->transactionRef . "&merchantId=" . $this->config->get('payment_interswitch_merchant_code') . "&provider=prv";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYPEER => false
            ));

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Curl error: ' . curl_error($curl);
            }

            curl_close($curl);
            $result = json_decode($response);
            $this->load->model('checkout/order');
            if (isset($result->transactionResponseCode) && $result->transactionResponseCode == '0') {
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 2);
                //$this->model_checkout_order->update($this->order_id, 2, "Pending to be processed",false);
            } else {
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], 1);
            }
            header("Location: " . $this->url->link('checkout/success'));
        }
    }

}
