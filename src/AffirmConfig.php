<?php

namespace NuRelm\Affirm;

/**
 * @file
 * AffirmConfig Class
 *
 * This object loads default site settings
 *
 * @category Configuration
 * @package Affirm API
 * @author Michael Sypolt <michael.sypolt@nurelm.com>
 * @copyright Copyright (c) 2015
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @version Release: @package_version@
 *
 */
class AffirmConfig {
  public $live_baseurl = 'api.affirm.com/api/v2/charges'; /**< Live API's base URL */
  public $sandbox_baseurl = 'sandbox.affirm.com/api/v2/charges'; /**< Sandbox API's base URL */
}