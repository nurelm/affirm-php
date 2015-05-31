<?php

/**
 * @file
 * AffirmCurl Class
 *
 * Handles requests going out to Affirm using cURL
 *
 * @category Request Handling
 * @package Affirm API
 * @author Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright Copyright (c) 2015
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @version Release: @package_version@
 *
 */
class AffirmCurl {
  public $url; /**< URL to send the request */
  public $post_body; /**< Post body to send to Affirm */
  public $method; /**< HTTP Method to send */
  public $curl; /**< cURL handle keeping track of this */
  public $status; /**< stores the response code from the API */
  public $response; /**< stores the response body */
  public $options; /**< stores the cURL options */

  /**
   * Constructor
   *
   * @param string $command Command to send to Affirm
   * @param array $data Data to pack and send to Affirm
   */
  public function __construct($url, $method='GET', $data){
    if(is_array($data)){
      $this->post_body = json_encode($data, JSON_UNESCAPED_SLASHES);
    }
    else{
      $this->post_body = $data;
    }
    if(is_null($url)){
      throw new Exception('You need a URL!');
    }
    $this->method = $method;
    $this->url = $url;
    $this-> options = array(
      CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)',
      CURLOPT_POST => true,
      CURLOPT_URL => $this->url,
      CURLOPT_POSTFIELDS => $this->post_body,
      CURLOPT_HTTPHEADER => array("Content-Type: application/json"),
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_CUSTOMREQUEST => $this->method,
    );
    $this->curl = curl_init();
  }

  public function send(){
    ob_start();
    curl_setopt_array($this->curl, $this->options);
    $success = curl_exec($this->curl);
    curl_close($this->curl);
    if ($success){
      $this->response = ob_get_clean();
    }
    else{
      throw new Exception('Really bad Thing');
    }
  }
}
