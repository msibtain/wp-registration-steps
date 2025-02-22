<?php
/*
Plugin Name: WP Registration Steps
Plugin URI: https://innovisionlab.com
Description: WordPress User Registration Steps
Author: innovisionlab
Version: 1.0.0
Author URI: https://innovisionlab.com
*/

require 'stripe-php/vendor/autoload.php';
require_once __DIR__ . '/src/wpRegistrationSteps.php';
require_once __DIR__ . '/src/clsGoHighLevelV2.php';
require_once __DIR__ . '/src/clsWpAdmin.php';
require_once __DIR__ . '/src/clsStripePayments.php';