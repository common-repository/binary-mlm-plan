<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BMP_Admin_Assets', false ) ) :

	
	class BMP_Admin_Assets {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function admin_styles() {
			global $wp_scripts;

			wp_enqueue_style( 'bmp_admin_styles', BMP()->plugin_url() . '/assets/css/admin/admin.css', array(), BMP_VERSION );
			
		}


		/**
		 * Enqueue scripts.
		 */
		public function admin_scripts() {
			global $wp_query, $post;
			// Register scripts.
			wp_enqueue_script( 'bmp_admin_jquery', BMP()->plugin_url() . '/assets/js/admin/admin.js', array(), BMP_VERSION );
			
	}
}
endif;

return new BMP_Admin_Assets();
