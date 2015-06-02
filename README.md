# Affirm PHP API Connector

This is a stand-alone PHP library that connects to the
[![Affirm](http://affirm.com)] API. The intent is to be able to plug this
library into the e-commerce platform of choice, provided it can use PHP. It
can either be installed as a library to your PHP driven website or your
commandline PHP scripting library.

# Usage

## Installation

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

This library depends on [![PHP](http://php.net/) 5.5 or later. Most
GNU/Linux distributions provide them in an easily downloaded and installed
repository. In addition to core php, another php library is also required
to [![PHP cURL Library](http://php.net/manual/en/book.curl.php)]
