<?php 
/*
 * Plugin Name: Ninja Forms - bwp PayPal Payments
 * Plugin URI: https://github.com/8riaN/bwp-ninja-forms-paypal-payments
 * Description: A Paypal payment gateway custom action for ninjaforms version 3.0 and above
 * Version: 2.0.1
 * Author: 8riaN Page
 * Author URI: https://github.com/8riaN
 * Text Domain: bwp-ninja-forms-paypal-payments
 *
 * Ninja Forms - bwp PayPal Payments is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * Ninja Forms - bwp PayPal Payments is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with {Plugin Name}. If not, see {URI to Plugin License}.
 * 
 * Copyright 2021 BriaN Page.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '&lt;' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    //include 'deprecated/bwp-ninja-forms-paypal-payments.php';
} else {

    /**
     * Class bwp-ninja-forms-paypal-payments
     */
    final class bwp-ninja-forms-paypal-payments extends NF_ 
    {
        const VERSION = '2.0.1';
        const SLUG    = 'bwp-ninja-forms-paypal-payments';
        const NAME    = 'EZ PayPal Payment';
        const AUTHOR  = 'BriaN Page';
        const PREFIX  = 'bwp-ninja-forms-paypal-payments';
        
        /**
         * @var bwp-ninja-forms-paypal-payments
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return bwp-ninja-forms-paypal-payments Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) &amp;&amp; !(self::$instance instanceof bwp-ninja-forms-paypal-payments)) {
                self::$instance = new bwp-ninja-forms-paypal-payments();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }
            
            return self::$instance;
        }

        public function __construct()
        {
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license') );

            /*
             * Optional. If your extension creates a new field interaction or display template...
            add_filter( 'ninja_forms_register_fields', array($this, 'register_fields'));
             */

            /*
             * Optional. If your extension processes or alters form submission data on a per form basis...
             */
            add_filter( 'ninja_forms_register_actions', array($this, 'register_actions'));

            /*
             * Optional. If your extension collects a payment (ie Strip, PayPal, etc)...
             */
            add_filter( 'ninja_forms_register_payment_gateways', array($this, 'register_payment_gateways'));
        }

        /**
         * Optional. If your extension creates a new field interaction or display template...
        public function register_fields($actions)
        {
            $actions[ 'bwp-ninja-forms-paypal-payments-_nffields' ] = new NF_EZPayPalPayment_Fields_EZPayPalPaymentExample(); // includes/Fields/EZPayPalPaymentExample.php

            return $actions;
        }
         */

        /**
         * Optional. If your extension processes or alters form submission data on a per form basis...
         */
        public function register_actions($actions)
        {
            $actions[ 'bwp-ninja-forms-paypal-payments-_nfsubmissions' ] = new NF_EZPayPalPayment_Actions_EZPayPalPaymentAction(); // includes/Actions/EZPayPalPaymentAction.php

            return $actions;
        }

        /**
         * Optional. If your extension collects a payment (ie Strip, PayPal, etc)...
         */
        public function register_payment_gateways($payment_gateways)
        {
            $payment_gateways[ 'bwp-ninja-forms-paypal-payments_nf-gateways' ] = new NF_EZPayPalPayment_PaymentGateways_EZPayPalPaymentExample(); // includes/PaymentGateways/EZPayPalProcessPaymentExample
        }

        /*
         * Optional methods for convenience.
         */

        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }
        
        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template( $file_name = '', array $data = array() )
        {
            if( ! $file_name ) return;

            extract( $data );

            include self::$dir . 'includes/Templates/' . $file_name;
        }
        
        /**
         * Config
         *
         * @param $file_name
         * @return mixed
         */
        public static function config( $file_name )
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
        }

        /*
         * Required methods for all extension.
         */

        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }
    }

    /**
     * The main function responsible for returning The bwp-ninja-forms-paypal-payments Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return {class} Highlander Instance
     */
    function bwp-ninja-forms-paypal-payments()
    {
        return bwp-ninja-forms-paypal-payments::instance();
    }

    bwp-ninja-forms-paypal-payments();
}
