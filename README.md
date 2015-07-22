# Affirm PHP API Connector

This is a stand-alone PHP library that connects to the
[Affirm](http://affirm.com) API. The intent is to be able to plug this
library into the e-commerce platform of choice, provided it can use PHP. It
can either be installed as a library to your PHP driven website or your
commandline PHP scripting library.

# Installation

Unpack or clone this git repository into a directory where you desire to place
the files. When you use this for your library, all you need is a one line
include to the `affirm-api.php` file in the root level of this directory.
This file will include all the other ones needed to load the classes required
for this API to work properly.

```php
<?php

/* Choose a directory or path to your application */
define('DOC_ROOT', '/home/website/public_html/');

/* Include the main file (which will include all others needed) */
include(DOC_ROOT . '/affirm-php/affirm-api.php');

```

## Dependencies

This library depends on [PHP](http://php.net/) 5.5 or later. Most
GNU/Linux distributions provide them in an easily downloaded and installed
repository. The [PHP cURL Library](http://php.net/manual/en/book.curl.php) and
the [PHP JSON Library](http://php.net/manual/en/book.json.php) are also needed,
and are often packaged separately. Installation instructions are shown below
for each of the major GNU/Linux distribution types. These commands must all
be run as root, or use `sudo` utility.

## APT (Debian/Ubuntu based distributions)

```shell

# Update the apt cache
apt-get update

# Install the dependencies
apt-get install php5 php5-curl php5-json
```

## YUM (Fedora/CentOS/Red Hat based distributions)

```shell

# No need to update cache, it's automatic

# Install the dependencies
yum install php5 php5-json php5-curl
```

## Affirm Account

A current [Affirm](https://www.affirm.com/) account is required for this
library. You may sign up [here](https://www.affirm.com/merchants/).
Once signed up for Affirm, a public/private key pair that serves as your
username and password would be provided, along with a Financial Product Key
and your API URL. In another area of your application that faces
the web, you should follow the steps outlined in the Affirm
[Site Integration](http://docs.affirm.com/v2/api/#site-integration)
documentation. Your server should also be set up to receive the webhook that
sends the `checkout_token` to your application as this checkout token is
used for initializing your charge (see *Usage* below).

## Configuration

One final step before starting to call objects in the application is to copy
`config.php.default` to `config.php` and update the information for your
credentials. This configuration file is used to create the `AffirmConfig` class
which is required for the application. If you choose not to enter your details
in this file because it is stored in a database, this file is still required
because the `AffirmConfig` class provides the current API base URLs.

# Usage

This library contains a few PHP classes that are used for interfacing with
Affirm and your application. You may either hard-code your details in
`config.php` or if stored in your application's database, you can enter your
credentials into the arguments of the `create_charge()` method of the
`AffirmAPI` class. The `$live_baseurl` and `$sandbox_baseurl` properties
of the `AffirmConfig` class must be left as shown in the `config.php.default`
 The methods of the `AffirmAPI` class and how to instantiate the class is shown
below. Unless you are doing testing, your code should only interface with
instances of the `AffirmAPI` class.

## Create an Instance of The AffirmAPI Class

Once the library is installed and properly linked as shown in *Installation*
above, you may create an instance of the `AffirmAPI` class in two ways. The
first way shows if you used the `AffirmConfig` class to store all of your
API keys and other credentials:

```php

// Create a new AffirmAPI with a fully configured AffirmConfig class
$affirm = new AffirmAPI();
```

The alternative is to add arguments so that it will construct the object
with these credentials overriding the ones stored in the `AffirmConfig`
class:

```php

// These would really be in a database, but showing this for example
$public_key = 'secretkey'; /**< Public API key, also used on Site Integration */
$private_key = 'supersecretkey'; /**< Private API key, never share this! */
$product_key = 'financialkey'; /**< Financial product key */
$production = true; /**< Set this to false if in sandbox mode */

// Create a new AffirmAPI with the above information included
$affirm = new AffirmAPI($public_key, $private_key, $product_key, $production);
```

## Error Handling

All methods (except the constructor) of the AffirmAPI class return a status
code while updating the object with the reponse from Affirm. The method returns
an integer status that corresponds with the Affirm status code if an error is
returned from Affirm. It returns the integer 1 if the API catches an error
before sending a request to Affirm. If a non-zero status is sent, the `response`
object in the AffirmAPI object is an Error object as defined in the 
[Affirm Documentation](http://docs.affirm.com/v2/api/errors/#status-codes). If
no error is observed, the status returned is 0 (instead of the HTTP status 200)
to behave similar to other APIs where zero mean no error.

## Create a new Charge

Your web-facing application would harvest a `checkout_token` which is required
for creating a new charge. The information for the new charge is stored in the
instance of the `AffirmAPI` class and the data can be used to do further
operations with the charge. You should also store the `charge_id` in your
application's database if you intend on updating information at any time in
the future. Once you use your `checkout_token` it is safe to discard as it is
a one-time use token and is no longer usable once a new charge is created.

```php

$token = 'exampleaffirmtoken' /**< checkout_token from Affirm */
// Creating a charge, storing data in the $affirm object
$error = $affirm->create_charge($token);
if(!$error){
  $charge_object = $affirm->response;
  $charge_id = $affirm->response->id;
}
else{
  $error_object = $affirm->response;
}

```

Once you have a `charge_id`, you can use it to perform further actions to the
charge as needed.

## Read a Charge

When you read a charge, you retreive the information that Affirm has on your
charge. The output that is registered in the instance of the `AffirmAPI` is
the same as the output from the `create_charge()` method, except this
uses the `charge_id` since you can only create a charge once.

```php

// Given a $charge_id request a charge object from Affirm
$error = $affirm->read_charge($charge_id);
if(!$error){
  $charge_object = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

```

## Capture a Charge

To capture a charge, use the `capture_charge()` method with your `charge_id`
in addition to a few optional parameters. These additional parameters are
optional:

* `order_id` - A string field for your own reference
* `shipping_carrier` - A string that contains the shipping carrier
* `shipping_confirmation` - A string that contains the shipping confirmation

```php

// Given a $charge_id and no optional fields desired
$error = $affirm->capture_charge($charge_id);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

// If adding the optional fields, include them as follows
$order_id = 'yourownappid'; /**< Order ID for your own records */
$shipping_carrier = 'USPS'; /**< Carrier shipping the goods */
$shipping_confirmation = 'someconfnumber'; /**< Shipping confirmation number */

$error = $affirm->capture_charge($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

// If you provide only a few optional fields, set others to null
$order_id = 'yourownappid';
$shipping_carrier = 'USPS';
$shipping_confirmation = null; //Shipping confirmation was not supplied

$error = $affirm->capture_charge($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

```

## Void a Charge

To void a charge, use the `void_charge()` method with your `charge_id`. No
other parameters are required.

```php

// Given a $charge_id void a charge
$error = $affirm->void_charge($charge_id);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}
```

## Refund a Charge

To refund a charge, use the `refund_charge()` method with your `charge_id` and
the amount to be refunded. This amount must either be in cents, or omitted. If
the value is omitted or `null`, the Affirm side will attempt to refund the
entire amount of the charge.

```php

// Given a $charge_id refund a charge and an amount
$amount = 399; /**< refund amount MUST be in cents */
$error = $affirm->refund_charge($charge_id, $amount);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

// Omit amount to refund full amount
$error = $affirm->refund_charge($charge_id);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}
```

## Update Shipping Information

To update shipping information, use the `updates_shipping()` method with your
`charge_id` and the optional fields mentioned in *Capture a Charge*:

* `order_id` - A string field for your own reference
* `shipping_carrier` - A string that contains the shipping carrier
* `shipping_confirmation` - A string that contains the shipping confirmation

```php

// Given a $charge_id and the fields desired to be updated
$order_id = 'yourownappid'; /**< Order ID for your own records */
$shipping_carrier = 'USPS'; /**< Carrier shipping the goods */
$shipping_confirmation = 'someconfnumber'; /**< Shipping confirmation number */

$error = $affirm->update_shipping($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}

// If you provide only a few optional fields, set others to null
$order_id = null; //Was not changed
$shipping_carrier = 'UPS'; //Changed carrier
$shipping_confirmation = 'someconfnumber'; //Shipping confirmation changed

$error = $affirm->update_shipping($charge_id, $order_id, $shipping_carrier, $shipping_confirmation);
if(!$error){
  $charge_event = $affirm->response;
}
else{
  $error_object = $affirm->response;
}
```
