# Affirm PHP API Connector

This is a stand-alone PHP library that connects to the
[![Affirm](http://affirm.com)] API. The intent is to be able to plug this
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

This library depends on [![PHP](http://php.net/)] 5.5 or later. Most
GNU/Linux distributions provide them in an easily downloaded and installed
repository. The [![PHP cURL Library](http://php.net/manual/en/book.curl.php)]
is also needed, and is often packaged separately. Installation instructions
are shown below for each of the major GNU/Linux distribution types. These
commands must all be run as root, or use `sudo` utility.

## APT (Debian/Ubuntu based distributions)

```shell

# Update the apt cache
apt-get update

# Install the dependencies
apt-get install php5 php5-curl
```

## YUM (Fedora/CentOS/Red Hat based distributions)

```shell

# No need to update cache, it's automatic

# Install the dependencies
yum install php5 php5-curl
```

## Affirm Account

A current [![Affirm](https://www.affirm.com/)] account is required for this
library to function. Once signed up for Affirm, a public/private key pair that
serves as your username and password would be provided, along with a Financial
Product Code and your API URL. In another area of your application that faces
the web, you should follow the steps outlined in the Affirm
[![Site Integration](http://docs.affirm.com/v2/api/#site-integration)]
documentation. Your server should also be set up to receive the webhook that
sends the `checkout_token` to your application as this checkout token is
used for initializing your charge (see *Usage* below).

# Usage

This library contains a few PHP classes that are used for interfacing with
Affirm and your application. You may either hard-code your details in
`config.php` or if stored in your application's database, you can enter your
credentials into the arguments of the `create_charge()` method of the
`AffirmAPI` class.
