<?php
/**
 *
 * @package mobileAppIntegration
 *
 * Plugin Name: Combopos Mobile App Integration
 * Plugin URI: http://combopos.co.uk/mobileAppIntegration
 * Description: This is a custom Mobile App Integration Plugin By Combosoft.
 * Version: 1.0.0
 * Author: Combosoft
 * Author URI: http://combosoft.co.uk
 * License: GPLv2 or latter
 * Text Domain: combopos
 *
 * Created by PhpStorm.
 * User: siam
 * Date: 2020-08-09
 * Time: 17:05
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/inc/loader.php';

if ( !class_exists('mobileAppIntegration')):

    class mobileAppIntegration{

        function __construct(){
            $custom_function = new CustomFunction();
            $custom_function->register_hook();
        }

        public function activate(){
            flush_rewrite_rules();
        }

        public function deactivate(){
            flush_rewrite_rules();
        }
    }

    $app_integration = new mobileAppIntegration();
    global $app_integration;

    // Activation Plugin
    register_activation_hook( __FILE__, array($app_integration,'activate') );

    // Deactivation Plugin
    register_deactivation_hook( __FILE__, array($app_integration,'deactivate') );

endif;