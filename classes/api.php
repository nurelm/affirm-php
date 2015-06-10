<?php

/**
 * @file
 * AffirmAPI Class
 *
 * Handles user input and provides output
 *
 * @category Request Handling
 * @package Affirm API
 * @author Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright Copyright (c) 2015
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @version Release: @package_version@
 *
 */
class AffirmAPI {
  public $private_key;    /**< Affirm Private Key */
  public $public_key;     /**< Affirm Public Key */
  public $product_code;   /**< Affirm Financial Product Code */
  public $base_url;       /**< Affirm base url for making request */
  public $curl;           /**< AffirmCurl object */

  public $response;       /**< Data from Affirm as an object */
  public $status;         /**< HTTP Status from Affirm */

  /**
   * Constructor, uses default configuration file if left empty
   *
   * @param boolean $production Set this to false for sandbox mode
   * @param string $public_key Public Key for Affirm API
   * @param string $private_key Private Key for Affirm API
   * @param string $product_code Finanical product code for Affirm
   */
  public function __construct($production = null, $public_key = null, $private_key = null, $product_code = null){
    // Set up configuration, use override for production mode if needed
    if (!class_exists('AffirmConfig')){
      throw new Exception('AffirmConfig class does not exist');
    }
    $config = new AffirmConfig();
    if (is_bool($production)){
      $config->production = $production;
    }
    if ($config->production == true){
      $this->public_key = $config->live_public_key;
      $this->private_key = $config->live_private_key;
      $this->base_url = $config->live_baseurl;
    }
    else{
      $this->public_key = $config->sandbox_public_key;
      $this->private_key = $config->sandbox_private_key;
      $this->base_url = $config->sandbox_baseurl;
    }
    $this->product_code = $config->product_key;

    // Add any overrides to keys and codes
    if (is_bool($public_key)){
      $this->public_key = $public_key;
    }
    if (is_bool($private_key)){
      $this->private_key = $private_key;
    }
    if (is_bool($product_code)){
      $this->product_code = $product_code;
    }
  }

  /**
   * Creates a new charge from the checkout_token
   *
   * @param string $checkout_token Authentication token from affirm.js
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function create_charge($checkout_token){
    if (is_null($checkout_token) || $checkout_token == ''){
      throw new Exception('Checkout token is empty');
    }
    else{
      $auth_array = array('checkout_token' => $checkout_token);
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}", 'POST', $auth_array);
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }

  /**
   * Reads a charge from a previously created charge
   *
   * @param string $charge_id Unique Charge ID from Affirm
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function read_charge($charge_id){
    if ($charge_id == ''){
      throw new Exception('Charge ID is empty');
    }
    else{
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}");
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }

  /**
   * Captures the charge
   *
   * @param string $charge_id Unique Charge ID from Affirm
   * @param string $order_id Optional order ID for your records
   * @param string $carrier Optional shipping carrier name
   * @param string $confirmation Optional confirmation number
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function capture_charge($charge_id, $order_id = null, $carrier = null, $confirmation = null){
    if ($charge_id == ''){
      throw new Exception('Charge ID is empty');
    }
    else{
      $inputs = array();
      if (!is_null($order_id)){
        $inputs['order_id'] = "{$order_id}";
      }
      if (!is_null($carrier)){
        $inputs['shipping_carrier'] = "{$carrier}";
      }
      if (!is_null($order_id)){
        $inputs['shipping_confirmation'] = "{$confirmation}";
      }
      if(count($inputs) == 0){
        $inputs = '{}';
      }
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/capture", 'POST', $inputs);
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }

  /**
   * Voids the charge
   *
   * @param string $charge_id Unique Charge ID from Affirm
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function void_charge($charge_id){
    if ($charge_id == ''){
      throw new Exception('Charge ID is empty');
    }
    else{
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/void", 'POST', '{}');
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }

  /**
   * Refunds the charge
   *
   * @param string $charge_id Unique Charge ID from Affirm
   * @param float $amount Amount of refund in decimal dollars
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function refund_charge($charge_id, $amount){
    if ($charge_id == ''){
      throw new Exception('Charge ID is empty');
    }
    else{
      if (is_null($amount) || !is_numeric($amount)){
        throw new Exception('Amount is not a number');
      }
      else{
        $cents = $amount * 100;
        $inputs = array('amount' => $cents);
      }
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/refund", 'POST', $inputs);
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }

  /**
   * Updates the shipping information
   *
   * @param string $charge_id Unique Charge ID from Affirm
   * @param string $order_id Optional order ID for your records
   * @param string $carrier Optional shipping carrier name
   * @param string $confirmation Optional confirmation number
   *
   * @return int $status Status as an integer, 0 meaning success
   */
  public function update_shipping($charge_id, $order_id = null, $carrier = null, $confirmation = null){
    if ($charge_id == ''){
      throw new Exception('Charge ID is empty');
    }
    else{
      $inputs = array();
      if (!is_null($order_id)){
        $inputs['order_id'] = "{$order_id}";
      }
      if (!is_null($carrier)){
        $inputs['shipping_carrier'] = "{$carrier}";
      }
      if (!is_null($order_id)){
        $inputs['shipping_confirmation'] = "{$confirmation}";
      }
      if(count($inputs) == 0){
        $inputs = '{}';
      }
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/update", 'POST', $inputs);
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
      $this->status = $this->curl->status;
      if ($this->curl->status == 200){
        return 0;
      }
      else{
        return 1; //For now, use "true" meaning there is an error
      }
    }
  }
}
