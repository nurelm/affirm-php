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
  public $private_key; /**< Affirm Private Key */
  public $public_key; /**< Affirm Public Key */
  public $product_code; /**< Affirm Financial Product Code */
  public $base_url; /**< Affirm base url for making request */
  public $curl; /**< AffirmCurl object */

  public $response; /**< Data from Affirm as an object */

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
    $this->product_code = $config->product_code; 

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

  public function create_charge($checkout_token){
    if ($checkout_token == ''){
      throw new Exception('Checkout token is empty');
    }
    else{
      $auth_array = array('checkout_token' => $checkout_token);
      $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}", 'POST', $auth_array);
      $this->curl->send();
      $this->curl->unpack();
      $this->response = $this->curl->response_object;
    }
  }
}
