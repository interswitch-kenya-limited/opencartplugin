<?php

class ControllerExtensionPaymentInterswitch extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/interswitch');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_interswitch', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant_code'])) {
            $data['error_merchant_code'] = $this->error['merchant_code'];
        } else {
            $data['error_merchant_code'] = '';
        }

        if (isset($this->error['terminal_id'])) {
            $data['error_terminal_id'] = $this->error['terminal_id'];
        } else {
            $data['error_terminal_id'] = '';
        }

        if (isset($this->error['domain'])) {
            $data['error_domain'] = $this->error['domain'];
        } else {
            $data['error_domain'] = '';
        }
       
        if (isset($this->error['country'])) {
            $data['error_country'] = $this->error['country'];
        } else {
            $data['error_country'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/interswitch', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/interswitch', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_interswitch_merchant_code'])) {
            $data['payment_interswitch_merchant_code'] = $this->request->post['payment_interswitch_merchant_code'];
        } else {
            $data['payment_interswitch_merchant_code'] = $this->config->get('payment_interswitch_merchant_code');
        }

        if (isset($this->request->post['payment_interswitch_terminal_id'])) {
            $data['payment_interswitch_terminal_id'] = $this->request->post['payment_interswitch_terminal_id'];
        } else {
            $data['payment_interswitch_terminal_id'] = $this->config->get('payment_interswitch_terminal_id');
        }

        if (isset($this->request->post['payment_interswitch_domain'])) {
            $data['payment_interswitch_domain'] = $this->request->post['payment_interswitch_domain'];
        } else {
            $data['payment_interswitch_domain'] = $this->config->get('payment_interswitch_domain');
        }

       

        if (isset($this->request->post['payment_interswitch_order_status_id'])) {
            $data['payment_interswitch_order_status_id'] = $this->request->post['payment_interswitch_order_status_id'];
        } else {
            $data['payment_interswitch_order_status_id'] = $this->config->get('payment_interswitch_order_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

       
        if (isset($this->request->post['payment_interswitch_country'])) {
            $data['payment_interswitch_country'] = $this->request->post['payment_interswitch_country'];
        } else {
            $data['payment_interswitch_country'] = $this->config->get('payment_interswitch_country');
        }



        if (isset($this->request->post['payment_interswitch_enviroment'])) {
            $data['payment_interswitch_enviroment'] = $this->request->post['payment_interswitch_enviroment'];
        } else {
            $data['payment_interswitch_enviroment'] = $this->config->get('payment_interswitch_enviroment');
        }

        if (isset($this->request->post['payment_interswitch_status'])) {
            $data['payment_interswitch_status'] = $this->request->post['payment_interswitch_status'];
        } else {
            $data['payment_interswitch_status'] = $this->config->get('payment_interswitch_status');
        }

       

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/interswitch', $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/interswitch')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_interswitch_merchant_code']) {
            $this->error['merchant_code'] = $this->language->get('error_merchant_code');
        }

        if (!$this->request->post['payment_interswitch_terminal_id']) {
            $this->error['terminal_id'] = $this->language->get('error_terminal_id');
        }

        if (!$this->request->post['payment_interswitch_domain']) {
            $this->error['domain'] = $this->language->get('error_domain');
        }
       
        
        if (!$this->request->post['payment_interswitch_country']) {
            $this->error['country'] = $this->language->get('error_country');
        }


        return !$this->error;
    }

}
