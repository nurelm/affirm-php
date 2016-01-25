<?php

namespace NuRelm\Affirm;

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
class AffirmAPI
{
    public $private_key;
    /**< Affirm Private Key */
    public $public_key;
    /**< Affirm Public Key */
    public $product_key;
    /**< Affirm Financial Product Key */
    public $base_url;
    /**< Affirm base url for making request */
    public $curl;
    /**< AffirmCurl object */

    public $response;
    /**< Data from Affirm as an object */
    public $status;
    /**< HTTP Status from Affirm */

    /**
     * Constructor
     *
     * @param boolean $production Set this to false for sandbox mode
     * @param string $public_key Public Key for Affirm API
     * @param string $private_key Private Key for Affirm API
     * @param string $product_key Finanical product keyfor Affirm
     */
    public function __construct($production = false, $public_key, $private_key, $product_key)
    {
        $config = new AffirmConfig();

        if ($production == true) {
            $this->base_url = $config->live_baseurl;
        } else {
            $this->base_url = $config->sandbox_baseurl;
        }

        $this->public_key = $public_key;
        $this->private_key = $private_key;
        $this->product_key = $product_key;
    }

    /**
     * Creates a new charge from the checkout_token
     *
     * @param string $checkout_token Authentication token from affirm.js
     *
     * @return int $status Status as an integer, 0 meaning success
     */
    public function create_charge($checkout_token)
    {
        if (is_null($checkout_token) || $checkout_token == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Checkout Token is empty, could not send to Affirm',
                'field' => 'checkout_token',
            );
            return 1;
        } else {
            $auth_array = array('checkout_token' => $checkout_token);
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}", 'POST',
                $auth_array);
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
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
    public function read_charge($charge_id)
    {
        if ($charge_id == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Charge ID is empty, could not send to Affirm',
                'field' => 'charge_id',
            );
            return 1;
        } else {
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}");
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
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
    public function capture_charge($charge_id, $order_id = null, $carrier = null, $confirmation = null)
    {
        if ($charge_id == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Charge ID is empty, could not send to Affirm',
                'field' => 'charge_id',
            );
            return 1;
        } else {
            $inputs = array();
            if (!is_null($order_id)) {
                $inputs['order_id'] = "{$order_id}";
            }
            if (!is_null($carrier)) {
                $inputs['shipping_carrier'] = "{$carrier}";
            }
            if (!is_null($order_id)) {
                $inputs['shipping_confirmation'] = "{$confirmation}";
            }
            if (count($inputs) == 0) {
                $inputs = '{}';
            }
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/capture",
                'POST', $inputs);
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
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
    public function void_charge($charge_id)
    {
        if ($charge_id == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Charge ID is empty, could not send to Affirm',
                'field' => 'charge_id',
            );
            return 1;
        } else {
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/void",
                'POST', '{}');
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
            }
        }
    }

    /**
     * Refunds the charge
     *
     * @param string $charge_id Unique Charge ID from Affirm
     * @param int $amount Amount of refund in cents
     *
     * @return int $status Status as an integer, 0 meaning success
     */
    public function refund_charge($charge_id, $amount = null)
    {
        if ($charge_id == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Charge ID is empty, could not send to Affirm',
                'field' => 'charge_id',
            );
            return 1;
        } else {
            $inputs = array('amount' => $amount);
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/refund",
                'POST', $inputs);
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
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
    public function update_shipping($charge_id, $order_id = null, $carrier = null, $confirmation = null)
    {
        if ($charge_id == '') {
            // Make something look like the Affirm error object
            $this->response = (object)array(
                'status_code' => 1,
                'type' => 'invalid_request',
                'code' => 'invalid_field',
                'message' => 'Charge ID is empty, could not send to Affirm',
                'field' => 'charge_id',
            );
            return 1;
        } else {
            $inputs = array();
            if (!is_null($order_id)) {
                $inputs['order_id'] = "{$order_id}";
            }
            if (!is_null($carrier)) {
                $inputs['shipping_carrier'] = "{$carrier}";
            }
            if (!is_null($order_id)) {
                $inputs['shipping_confirmation'] = "{$confirmation}";
            }
            if (count($inputs) == 0) {
                $inputs = '{}';
            }
            $this->curl = new AffirmCurl("https://{$this->public_key}:{$this->private_key}@{$this->base_url}/{$charge_id}/update",
                'POST', $inputs);
            $this->curl->send();
            $this->curl->unpack();
            $this->response = $this->curl->response_object;
            $this->status = $this->curl->status;
            if ($this->curl->status == 200) {
                // Zero is always a good number to return when there is no error
                return 0;
            } else {
                // Just pass the status code from Affirm through the API
                return $this->curl->status;
            }
        }
    }
}
