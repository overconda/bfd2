<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.facebook.com/kergrit.robkop
 * @since      1.0.0
 *
 * @package    Singhabeerfinder
 * @subpackage Singhabeerfinder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Singhabeerfinder
 * @subpackage Singhabeerfinder/includes
 * @author     KERGRIT ROBKOP <kergrit@gmail.com>
 */
class Singhabeerfinder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'singhabeerfinder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
