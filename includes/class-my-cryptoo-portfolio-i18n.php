<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.linkedin.com/in/robertmorel/
 * @since      1.0.0
 *
 * @package    My_Cryptoo_Portfolio
 * @subpackage My_Cryptoo_Portfolio/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    My_Cryptoo_Portfolio
 * @subpackage My_Cryptoo_Portfolio/includes
 * @author     Robert Christopher Morel <robertchristophermorel@gmail.com>
 */
class My_Cryptoo_Portfolio_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'my-cryptoo-portfolio',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
