<?php

/**
 * @file
 * Bootstrap file for Affirm API Connector
 *
 * This includes all the necessary
 * to access the router, which will then access appropriate controllers
 *
 * @category   Bootstrap
 * @package    Serverphu
 * @author     Michael Sypolt <michael.sypolt@transitguru.limited>
 * @copyright  Copyright (c) 2015
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt
 * @version    0.5.0
 */
// Make sure this directory is application root
define('DOC_ROOT', __DIR__);

// Load the settings
require_once (DOC_ROOT . '/config.php');

// Load the classes
$PATH = DOC_ROOT . '/classes';
$includes = scandir($PATH);
foreach ($includes as $include){
  if (is_file($PATH . '/' . $include) && $include != '.' && $include != '..' && fnmatch("*.php", $include)){
    require_once ($PATH . '/' . $include);
  }
}
