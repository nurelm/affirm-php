<?php

/**
 * @file
 * Bootstrap file for Affirm API Connector
 *
 * This includes all the necessary
 * to access the router, which will then access appropriate controllers
 *
 * @category   Bootstrap
 * @package    Affirm API
 * @author     Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    0.5.0
 */
//Include the affirm includer
define('TEST_ROOT', __DIR__);
require_once(TEST_ROOT . '/../affirm_api.php');

if (isset($_SERVER['argv'][1])){
  echo $_SERVER['argv'][1];
}
else{
  echo "php affirm_api.php <token>\n";
  echo "  You need a token for the tests to work\n";
}
