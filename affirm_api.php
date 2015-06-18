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
 * @version    0.9.0
 */

// Make sure this directory is application root
$AFFIRM_ROOT = __DIR__;

// Load the settings
require_once ($AFFIRM_ROOT . '/config.php');

// Load the classes
$PATH = $AFFIRM_ROOT . '/classes';
$includes = scandir($PATH);
foreach ($includes as $include){
  if (is_file($PATH . '/' . $include) && $include != '.' && $include != '..' && fnmatch("*.php", $include)){
    require_once ($PATH . '/' . $include);
  }
}
