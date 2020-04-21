<?php

class ModelExtensionPaymentInterswitch extends Model {

    private $apiMethodName = "interswitch.trade.page.pay";
    private $postCharset = "UTF-8";
    private $interswitchSdkVersion = "interswitch-sdk-php-20161101";
    private $apiVersion = "1.0";
    private $logFileName = "interswitch.log";
    private $gateway_url = "https://openapi.interswitch.com/gateway.do";
    private $domain;
    private $termid;
    private $merchantId;
    private $notifyUrl;
    private $returnUrl;
    private $format = "json";
    private $signtype = "RSA2";
    private $apiParas = array();

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/interswitch');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('payment_interswitch_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_interswitch_total') > 0 && $this->config->get('payment_interswitch_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_interswitch_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'interswitch',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('payment_interswitch_sort_order')
            );
        }

        return $method_data;
    }

    private function setParams($interswitch_config) {

        $this->gateway_url = $interswitch_config['gateway_url'];
        $this->merchantId = $interswitch_config['merchant_code'];
        $this->termid = $interswitch_config['terminal_id'];
        $this->domain = $interswitch_config['domain'];
        $this->signtype = $interswitch_config['sign_type'];
        $this->notifyUrl = $interswitch_config['notify_url'];


        if (empty($this->merchantId) || trim($this->merchantId) == "") {
            throw new Exception("merchantId should not be NULL!");
        }
        if (empty($this->termid) || trim($this->termid) == "") {
            throw new Exception("termid should not be NULL!");
        }
        if (empty($this->domain) || trim($this->domain) == "") {
            throw new Exception("domain should not be NULL!");
        }
        if (empty($this->postCharset) || trim($this->postCharset) == "") {
            throw new Exception("charset should not be NULL!");
        }
        if (empty($this->gateway_url) || trim($this->gateway_url) == "") {
            throw new Exception("gateway_url should not be NULL!");
        }
    }

    function pagePay($builder, $config) {

        $this->setParams($config);
        $biz_content = null;
        if (!empty($builder)) {
            $biz_content = json_encode($builder, JSON_UNESCAPED_UNICODE);
        }

        $log = new Log($this->logFileName);
        $log->write($biz_content);

        $this->apiParas["biz_content"] = $biz_content;

        $response = $this->pageExecute($this, "post");
        $log = new Log($this->logFileName);
        $log->write("response: " . var_export($response, true));

        return $response;
    }



    public function pageExecute($request, $httpmethod = "POST") {

       $iv=$this->apiVersion;

		$sysParams["merchant_code"] = $this->merchant_code;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signtype;
		$sysParams["method"] = $this->apiMethodName;
		$sysParams["timestamp"] = date("Y-m-d H:i:s");

		$sysParams["notify_url"] = $this->notifyUrl;
	
		$sysParams["gateway_url"] = $this->gateway_url;

	

		$totalParams = array_merge($apiParams, $sysParams);

    }

    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    

    function getPostCharset() {
        return trim($this->postCharset);
    }

}
