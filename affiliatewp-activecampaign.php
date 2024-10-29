<?php
/**
 * Plugin Name:     AffiliateWP - ActiveCampaign
 * Plugin URI:      https://wordpress.org/plugins/affiliatewp-activecampaign/
 * Description:     Easily add ActiveCampaign integration to AffiliateWP
 * Version:         1.0.1
 * Author:          Daniel J Griffiths
 * Author URI:      https://evertiro.com
 * Text Domain:     affiliatewp-activecampaign
 *
 * @package         AffiliateWP\ActiveCampaign
 * @author          Daniel J Griffiths <dgriffiths@evertiro.com>
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}



if( ! class_exists( 'AffiliateWP_ActiveCampaign' ) ) {


	/**
	 * Main AffiliateWP_ActiveCampaign class
	 *
	 * @since       1.0.0
	 */
	class AffiliateWP_ActiveCampaign {


		/**
		 * @var         AffiliateWP_ActiveCampaign $instance The one true AffiliateWP_ActiveCampaign
		 * @since       1.0.0
		 */
		private static $instance;


		/**
		 * @var         object $activecampaign The ActiveCampaign API connector object
		 * @since       1.0.0
		 */
		public $activecampaign = false;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      self::$instance The one true AffiliateWP_ActiveCampaign
		 */
		public static function instance() {
			if( ! self::$instance ) {
				self::$instance = new AffiliateWP_ActiveCampaign();
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->hooks();

				if( function_exists( 'affiliate_wp' ) ) {
					$api_url = affiliate_wp()->settings->get( 'activecampaign_api_url', false );
					$api_key = affiliate_wp()->settings->get( 'activecampaign_api_key', false );

					if( $api_url && $api_key ) {
						self::$instance->activecampaign = new ActiveCampaign( $api_url, $api_key );
					}
				}
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function setup_constants() {
			// Plugin version
			define( 'AFFILIATEWP_ACTIVECAMPAIGN_VER', '1.0.0' );

			// Plugin path
			define( 'AFFILIATEWP_ACTIVECAMPAIGN_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'AFFILIATEWP_ACTIVECAMPAIGN_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			//require_once AFFILIATEWP_ACTIVECAMPAIGN_DIR . 'includes/scripts.php';
			require_once AFFILIATEWP_ACTIVECAMPAIGN_DIR . 'includes/functions.php';
			require_once AFFILIATEWP_ACTIVECAMPAIGN_DIR . 'includes/actions.php';

			// Load the ActiveCampaign classes
			if( ! class_exists( 'ActiveCampaign' ) ) {
				$ac_classes = array( 'Connector', 'ActiveCampaign', 'Account', 'Auth', 'Automation', 'Campaign', 'Contact', 'Deal', 'Design', 'Form', 'Group', 'List', 'Message', 'Settings', 'Subscriber', 'Tracking', 'User', 'Webhook' );

				foreach( $ac_classes as $class ) {
					require_once AFFILIATEWP_ACTIVECAMPAIGN_DIR . 'includes/libraries/activecampaign-api/' . $class . '.class.php';
				}
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function hooks() {
			// Add settings
			add_filter( 'affwp_settings_integrations', array( $this, 'settings' ) );
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_activecampaign_language_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), '' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'affiliatewp-activecampaign', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/affiliatewp-activecampaign/' . $mofile;

			if( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-activecampaign/ folder
				load_textdomain( 'affiliatewp-activecampaign', $mofile_global );
			} elseif( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-activecampaign/languages/ folder
				load_textdomain( 'affiliatewp-activecampaign', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-activecampaign', false, $lang_dir );
			}
		}


		/**
		 * Add settings
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $settings The existing settings
		 * @return      array The updated settings
		 */
		public function settings( $settings ) {
			$new_settings = array(
				'activecampaign_header' => array(
					'name' => '<strong>' . __( 'ActiveCampaign Settings', 'affiliatewp-activecampaign' ) . '</strong>',
					'desc' => '',
					'type' => 'header'
				),
				'activecampaign_api_url' => array(
					'name' => __( 'API URL', 'affiliatewp-activecampaign' ),
					'desc' => __( 'Enter your ActiveCampaign API URL, which can be found on the ActiveCampaign Settings &rarr; Developer page.', 'affiliatewp-activecampaign' ),
					'type' => 'text'
				),
				'activecampaign_api_key' => array(
					'name' => __( 'API Key', 'affiliatewp-activecampaign' ),
					'desc' => __( 'Enter your ActiveCampaign API key, which can be found on the ActiveCampaign Settings &rarr; Developer page.', 'affiliatewp-activecampaign' ),
					'type' => 'text'
				),
				'activecampaign_list' => array(
					'name' => __( 'Campaign List', 'affiliatewp-activecampaign' ),
					'desc' => __( 'Select the list to add affiliates to when they register.', 'affiliatewp-activecampaign' ),
					'type' => 'select',
					'options' => affiliatewp_activecampaign_get_lists()
				),
				'activecampaign_optin' => array(
					'name' => __( 'Auto Register', 'affiliatewp-activecampaign' ),
					'desc' => __( 'Select this to hide the opt-in checkbox on the registration page.', 'affiliatewp-activecampaign' ),
					'type' => 'checkbox'
				)
			);

			return array_merge( $settings, $new_settings );
		}
	}
}


/**
 * The main function responsible for returning the one true AffiliateWP_ActiveCampaign
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      AffiliateWP_ActiveCampaign The one true AffiliateWP_ActiveCampaign
 */
function affiliatewp_activecampaign() {
	if( ! class_exists( 'Affiliate_WP' ) ) {
		if( ! class_exists( 'S214_AffWP_Activation' ) ) {
			require_once 'includes/libraries/class.s214-affwp-activation.php';
		}

		$activation = new S214_AffWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation = $activation->run();

		return AffiliateWP_ActiveCampaign::instance();
	} else {
		return AffiliateWP_ActiveCampaign::instance();
	}
}
add_action( 'plugins_loaded', 'affiliatewp_activecampaign' );