<?php
/**
 * Activation handler
 *
 * @package     AffWP\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Section214 AffWP Activation Handler Class
 *
 * @since       1.0.0
 */
class S214_AffWP_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_affwp;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        $this->plugin_name = str_replace( 'AffiliateWP - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );

        // Is EDD installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'AffiliateWP' ) {
                $this->has_affwp = true;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_affwp_notice' ) );
    }


    /**
     * Display notice if EDD isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_affwp_notice() {
        if( $this->has_affwp ) {
            echo '<div class="error"><p>' . $this->plugin_name . __( ' requires AffiliateWP! Please activate it to continue!', 's214-affwp-activation' ) . '</p></div>';
        } else {
            echo '<div class="error"><p>' . $this->plugin_name . __( ' requires AffiliateWP! Please install it to continue!', 's214-affwp-activation' ) . '</p></div>';
        }
    }
}
