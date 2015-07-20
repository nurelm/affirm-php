<?php

/**
 * @file
 * Tester file for Affirm API Connector
 *
 * @category   Testing
 * @package    Affirm API
 * @author     Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    0.5.0
 */
//Include the affirm includer
define('TEST_ROOT', __DIR__);
require_once(TEST_ROOT . '/../affirm_api.php');

$error = 0;     /**< Try to make some errors if set to 1 */
$help = 0;      /**< Show help screen if set to 1 */
$long = 0;      /**< Build object in long form if set to 1 */
$nocapture = 0; /**< Do not capture charge, test voiding */
$ship = 0;      /**< If set to 1, use shipping info tests for updating */
$token = '';    /**< Token to be sent to Affirm in testing, only once */
$verbose = 0;   /**< If set to 1, shows var_dump() of data from Affirm */

$failures = 0;  /**< Number of failures observed in this test */

if (count($_SERVER['argv']) > 10){
  echo "Don't be so argumentative!\n";
  $help = 1;
}
else{
  foreach($_SERVER['argv'] as $i => $argument){
    if($i > 0){
      $subargs = explode('=', $argument);
      // Find the optional flags
      if (strpos('error', $subargs[0]) === 0){$error = 1;}
      if (strpos('help', $subargs[0]) === 0){$help = 1;}
      if (strpos('long', $subargs[0]) === 0){$long = 1;}
      if (strpos('nocapture', $subargs[0]) === 0){$nocapture = 1;}
      if (strpos('shipping', $subargs[0]) === 0){$ship = 1;}
      if (strpos('verbose', $subargs[0]) === 0){$verbose = 1;}

      // Find the token
      if (strpos('token', $subargs[0]) === 0 && count($subargs)>1){
        $token = $subargs[1];
      }
    }
  }
}
if ($token == '' || $help == 1){
  echo "Usage: php affirm_api.php [option(s)]\n";
  echo "Test the affirm library\n\n";
  echo "The following are the available options\n\n";
  echo "  error         Coerce as many errors as possible, for further testing\n";
  echo "  help          Shows this help screen and exits\n";
  echo "  long          Build the object with arguments instead of using AffirmConfig\n";
  echo "  nocapture     Do not capture to test voiding a charge\n";
  echo "  shipping      Use the additional optional shipping info for capture/update\n";
  echo "  token=TOKEN   REQUIRED: checkout_token from Affirm \n";
  echo "  verbose       Verbose option to var_dump the objects received\n\n";
  echo "Any of the first few letters can be used as a shortcut for all options\n";
  exit(1);
}

if($long == 1){
  // Create a new AffirmAPI with the above information included
  $config = new AffirmConfig();

  $public_key = $config->sandbox_pubic_key;
  $private_key = $config->sandbox_private_key;
  $product_key = $config->product_key;
  $production = false; /**< Set this to false if in sandbox mode */

  $affirm = new AffirmAPI($public_key, $private_key, $product_key, $production);
}
else{
  // Create a new AffirmAPI with a fully configured AffirmConfig class
  $affirm = new AffirmAPI();
}

// Creating a charge, storing data in the $affirm object
echo "Creating Charge from token {$token}: ";
$status = $affirm->create_charge($token);
if ($status != 0){
  $failures += 1;
  $success = 'FAIL';
}
else{
  $success = 'SUCCESS';
}
echo "{$status} {$success}\n";
if ($verbose == 1){
  var_dump($affirm->response);
}
if ($status == 0){
  $charge_id = $affirm->response->id;
}
else{
  echo "Token was already used or invalid, no point in further testing :(\n";
  echo "Please use your site integration (contents of web directory) to make a token\n";
  exit(2);
}

if($error == 1){
  echo "Trying to make Affirm send an error by reusing the token: ";
  $status = $affirm->create_charge($token);
  if ($status != 400){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }

  echo "Trying to make Affirm send an error by using a silly string: ";
  $status = $affirm->create_charge("somesillyinvalidstring");
  if ($status != 400){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }

}

// Given a $charge_id request a charge object from Affirm
echo "Now reading a Charge: ";
$status = $affirm->read_charge($charge_id);
if ($status != 0){
  $failures += 1;
  $success = 'FAIL';
}
else{
  $success = 'SUCCESS';
}
echo "{$status} {$success}\n";
if($verbose == 1){
  var_dump($affirm->response);
}

if ($error == 1){
  echo "Trying to refund $3.99, before capturing to generate an error: ";
  // Given a $charge_id refund a charge
  $refund = 3.99; /**< refund amount is in dollars */
  $status = $affirm->refund_charge($charge_id, $refund);
  if ($status != 400){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }
}

if ($nocapture == 1){
  echo "Voiding a Charge: ";
  // Given a $charge_id void a charge
  $status = $affirm->void_charge($charge_id);
  if ($status != 0){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }
}

echo "Capturing a charge ";
if ($ship == 1){
  echo "with shipping info fields: ";
  // If adding the optional fields, include them as follows
  $order_id = 'yourownappid'; /**< Order ID for your own records */
  $shipping_carrier = 'UPS'; /**< Carrier shipping the goods */
  $shipping_confirmation = 'someconfnumber'; /**< Shipping confirmation number */

  $status = $affirm->capture_charge($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
}
else{
  echo ": ";
  // Given a $charge_id and no optional fields desired
  $status = $affirm->capture_charge($charge_id);
}
if ($status != 0){
  $failures += 1;
  $success = 'FAIL';
}
else{
  $success = 'SUCCESS';
}
echo "{$status} {$success}\n";
if ($verbose == 1){
  var_dump($affirm->response);
}

echo "Trying to refund $3.99, after capturing: ";
$refund = 399; 
$status = $affirm->refund_charge($charge_id, $refund);
if ($status != 0){
  $failures += 1;
  $success = 'FAIL';
}
else{
  $success = 'SUCCESS';
}
echo "{$status} {$success}\n";
if ($verbose == 1){
  var_dump($affirm->response);
}

if ($error == 1){
  echo "Trying to voiding a charge after capturing: ";
  // Given a $charge_id void a charge
  $status = $affirm->void_charge($charge_id);
  if ($status != 400){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }
}
if ($ship == 1){
  echo "Since we have some shipping Info, let's try to update it: ";
  // Given a $charge_id and the fields desired to be updated
  $order_id = 'yourownappid'; /**< Order ID for your own records */
  $shipping_carrier = 'USPS'; /**< Carrier shipping the goods */
  $shipping_confirmation = 'somenewconf'; /**< Shipping confirmation number */

  $status = $affirm->update_shipping($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
  if ($status != 0){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }

  echo "Try updating with a limited set of optional fields: ";
  // If you provide only a few optional fields, set others to null
  $order_id = null; //Was not changed
  $shipping_carrier = 'UPS'; //Changed carrier
  $shipping_confirmation = 'someconfnumber'; //Shipping confirmation changed

  $status = $affirm->update_shipping($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
  if ($status != 0){
    $failures += 1;
    $success = 'FAIL';
  }
  else{
    $success = 'SUCCESS';
  }
  echo "{$status} {$success}\n";
  if ($verbose == 1){
    var_dump($affirm->response);
  }
}

echo "Trying to refund balance by not passing an amount: ";
$status = $affirm->refund_charge($charge_id);
if ($status != 0){
  $failures += 1;
  $success = 'FAIL';
}
else{
  $success = 'SUCCESS';
}
echo "{$status} {$success}\n";
if ($verbose == 1){
  var_dump($affirm->response);
}

if ($failures == 0){
  $success = 'SUCCESS';
}
else{
  $success = 'FAIL';
}
echo "{$success}! Test complete with {$failures} errors.\n";
