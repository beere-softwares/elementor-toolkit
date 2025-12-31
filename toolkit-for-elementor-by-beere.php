<?php
/**
 * Plugin Name: Toolkit for Elementor by Beere
 * Description: Custom Elementor widgets for logout and other features.
 * Plugin URI:  https://github.com/beere-softwares/toolkit-for-elementor-by-beere
 * Version:     1.3.0
 * Author:      Beere Softwares
 * Author URI:  https://github.com/beere-softwares
 * Text Domain: toolkit-for-elementor-by-beere
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Elementor tested up to: 3.21.0
 * Elementor Pro tested up to: 3.21.0
 * WordPress tested up to: 6.9
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'TOOLKIT_FOR_ELEMENTOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TOOLKIT_FOR_ELEMENTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Toolkit for Elementor Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Toolkit_For_Elementor_Plugin {

    /**
     * Plugin Version
     *
     * @since 1.0.0
     * @var string The plugin version.
     */
    const VERSION = '1.3.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Instance
     *
     * @since 1.0.0
     * @access private
     * @static
     * @var \Toolkit_For_Elementor_Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @access public
     * @static
     * @return \Toolkit_For_Elementor_Plugin An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct() {

        add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * @since 1.0.0
     * @access public
     */
    public function i18n() {

        // WordPress.org automatically loads translations since WP 4.6.

    }

    /**
     * On Plugins Loaded
     *
     * Checks if Elementor has loaded, and performs some compatibility checks.
     *
     * @since 1.0.0
     * @access public
     */
    public function on_plugins_loaded() {

        if ( $this->is_compatible() ) {
            add_action( 'elementor/init', [ $this, 'init' ] );
        }

    }

    /**
     * Compatibility Checks
     *
     * Checks if the installed version of Elementor is compatible.
     *
     * @since 1.0.0
     * @access public
     */
    public function is_compatible() {

        // Check if Elementor is installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return false;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return false;
        }

        return true;

    }

    /**
     * Initialize the plugin
     *
     * @since 1.0.0
     * @access public
     */
    public function init() {

        $this->i18n();

        // Register Widget Styles
        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );

        // Register Widget Scripts
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );

        // Register widgets
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

        // Register container
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_missing_main_plugin() {

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'toolkit-for-elementor-by-beere' ),
            '<strong>' . esc_html__( 'Toolkit for Elementor by Beere', 'toolkit-for-elementor-by-beere' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'toolkit-for-elementor-by-beere' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'toolkit-for-elementor-by-beere' ),
            '<strong>' . esc_html__( 'Toolkit for Elementor by Beere', 'toolkit-for-elementor-by-beere' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'toolkit-for-elementor-by-beere' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );

    }

    /**
     * Register Widgets
     *
     * @since 1.0.0
     * @access public
     */
    public function register_widgets( $widgets_manager ) {
        require_once( TOOLKIT_FOR_ELEMENTOR_PLUGIN_PATH . 'widgets/logout-widget.php' );
        $widgets_manager->register( new \Elementor_Logout_Widget() );

        require_once( TOOLKIT_FOR_ELEMENTOR_PLUGIN_PATH . 'widgets/typing-animation-widget.php' );
        $widgets_manager->register( new \Elementor_Typing_Animation_Widget() );

        require_once( TOOLKIT_FOR_ELEMENTOR_PLUGIN_PATH . 'widgets/video-background-widget.php' );
        $widgets_manager->register( new \Elementor_Video_Background_Widget() );
    }

    /**
     * Add Elementor widget categories.
     *
     * @since 1.2.0
     * @access public
     */
    public function add_elementor_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'toolkit-for-elementor-by-beere',
            [
                'title' => esc_html__( 'Toolkit for Elementor by Beere', 'toolkit-for-elementor-by-beere' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    /**
     * Widget Styles
     *
     * @since 1.0.0
     * @access public
     */
    public function widget_styles() {
        wp_enqueue_style( 'toolkit-for-elementor-by-beere', TOOLKIT_FOR_ELEMENTOR_PLUGIN_URL . 'css/style.css', [], self::VERSION );
    }

    /**
     * Widget Scripts
     *
     * @since 1.1.0
     * @access public
     */
    public function widget_scripts() {
        wp_register_script( 'toolkit-for-elementor-by-beere', TOOLKIT_FOR_ELEMENTOR_PLUGIN_URL . 'js/typing-animation.js', [ 'jquery' ], self::VERSION, true );
    }


}

Toolkit_For_Elementor_Plugin::instance();
