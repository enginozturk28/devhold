<?php
/**
 * Plugin Name: DevHold - Maintenance Mode
 * Plugin URI: https://enginozturk.tr
 * Description: DevHold allows you to quickly put your WordPress site into maintenance mode while creating, editing, or developing content.
 * Version: 1.0.0
 * Requires at least: 5.9
 * Tested up to: 6.8.1
 * Requires PHP: 7.4
 * Author: Engin ÖZTÜRK & Claude AI
 * Author URI: https://enginozturk.tr
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: devhold
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'DEVHOLD_VERSION', '1.0.0' );
define( 'DEVHOLD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEVHOLD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin activation hook
 */
function devhold_activation() {
    // Add default settings
    $default_options = array(
        'enabled' => false,
        'title' => __( 'Under Development', 'devhold' ),
        'message' => __( 'Our site is currently under development. Please visit again later.', 'devhold' ),
        'countdown_enabled' => false,
        'countdown_date' => '',
        'social_links' => array(),
        'custom_css' => '',
        'logo_url' => '',
        'background_color' => '#667eea',
        'text_color' => '#ffffff',
        'bypass_roles' => array( 'administrator' ),
        'design_style' => 'minimal',
        'background_image' => '',
        'subtitle' => __( 'Under Development', 'devhold' )
    );
    
    if ( false === get_option( 'devhold_options' ) ) {
        add_option( 'devhold_options', $default_options );
    }
}
register_activation_hook( __FILE__, 'devhold_activation' );

/**
 * Plugin deactivation hook
 */
function devhold_deactivation() {
    // Clean up operations can be performed if needed
}
register_deactivation_hook( __FILE__, 'devhold_deactivation' );

/**
 * Main plugin class
 */
class DevHold {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Plugin settings
     */
    private $options;
    
    /**
     * Return singleton instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Default values
        $default_options = array(
            'enabled' => false,
            'title' => __( 'Under Development', 'devhold' ),
            'message' => __( 'Our site is currently under development. Please visit again later.', 'devhold' ),
            'countdown_enabled' => false,
            'countdown_date' => '',
            'social_links' => array(),
            'custom_css' => '',
            'logo_url' => '',
            'background_color' => '#667eea',
            'text_color' => '#ffffff',
            'bypass_roles' => array( 'administrator' ),
            'design_style' => 'minimal',
            'background_image' => '',
            'subtitle' => __( 'Under Development', 'devhold' )
        );
        
        $this->options = get_option( 'devhold_options', array() );
        $this->options = wp_parse_args( $this->options, $default_options );
        
        // Add hooks
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'template_redirect', array( $this, 'check_maintenance_mode' ) );
        
        // For admin
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            add_action( 'admin_init', array( $this, 'register_settings' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
        }
        
        // AJAX operations
        add_action( 'wp_ajax_devhold_toggle_status', array( $this, 'ajax_toggle_status' ) );
    }
    
    /**
     * Plugin initialization
     */
    public function init() {
        // Load language files
        load_plugin_textdomain( 'devhold', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
    
    /**
     * Check maintenance mode
     */
    public function check_maintenance_mode() {
        // If not in admin panel and maintenance mode is active
        if ( ! is_admin() && ! empty( $this->options['enabled'] ) ) {
            
            // Check bypass
            if ( $this->user_can_bypass() ) {
                return;
            }
            
            // Check login page
            if ( $this->is_login_page() ) {
                return;
            }
            
            // Show maintenance page
            $this->show_maintenance_page();
        }
    }
    
    /**
     * Check if user can bypass maintenance mode
     */
    private function user_can_bypass() {
        if ( ! is_user_logged_in() ) {
            return false;
        }
        
        $user = wp_get_current_user();
        $bypass_roles = ! empty( $this->options['bypass_roles'] ) ? $this->options['bypass_roles'] : array( 'administrator' );
        
        foreach ( $bypass_roles as $role ) {
            if ( in_array( $role, $user->roles ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Login page check
     */
    private function is_login_page() {
        return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
    }
    
    /**
     * Show maintenance page
     */
    private function show_maintenance_page() {
        // HTTP status code
        header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
        header( 'Status: 503 Service Temporarily Unavailable' );
        header( 'Retry-After: 3600' );
        
        // Load template file
        include DEVHOLD_PLUGIN_DIR . 'templates/maintenance.php';
        exit();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'DevHold Settings', 'devhold' ),
            __( 'DevHold', 'devhold' ),
            'manage_options',
            'devhold',
            array( $this, 'admin_page' ),
            'dashicons-hammer',
            80
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        include DEVHOLD_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting( 'devhold_settings', 'devhold_options', array( $this, 'sanitize_options' ) );
    }
    
    /**
     * Sanitize and validate settings
     */
    public function sanitize_options( $input ) {
        $sanitized = array();
        
        // Boolean values
        $sanitized['enabled'] = ! empty( $input['enabled'] );
        $sanitized['countdown_enabled'] = ! empty( $input['countdown_enabled'] );
        
        // Text values
        $sanitized['title'] = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : '';
        $sanitized['message'] = isset( $input['message'] ) ? wp_kses_post( $input['message'] ) : '';
        $sanitized['countdown_date'] = isset( $input['countdown_date'] ) ? sanitize_text_field( $input['countdown_date'] ) : '';
        $sanitized['custom_css'] = isset( $input['custom_css'] ) ? sanitize_textarea_field( $input['custom_css'] ) : '';
        $sanitized['logo_url'] = isset( $input['logo_url'] ) ? esc_url_raw( $input['logo_url'] ) : '';
        $sanitized['subtitle'] = isset( $input['subtitle'] ) ? sanitize_text_field( $input['subtitle'] ) : '';
        $sanitized['background_image'] = isset( $input['background_image'] ) ? esc_url_raw( $input['background_image'] ) : '';
        $sanitized['design_style'] = isset( $input['design_style'] ) && in_array( $input['design_style'], array( 'minimal', 'detailed' ) ) ? $input['design_style'] : 'minimal';
        
        // Colors
        $sanitized['background_color'] = isset( $input['background_color'] ) ? sanitize_hex_color( $input['background_color'] ) : '#667eea';
        $sanitized['text_color'] = isset( $input['text_color'] ) ? sanitize_hex_color( $input['text_color'] ) : '#ffffff';
        
        // Roles
        $sanitized['bypass_roles'] = ! empty( $input['bypass_roles'] ) ? array_map( 'sanitize_text_field', $input['bypass_roles'] ) : array();
        
        // Social links
        if ( ! empty( $input['social_links'] ) && is_array( $input['social_links'] ) ) {
            foreach ( $input['social_links'] as $key => $link ) {
                if ( ! empty( $link['url'] ) ) {
                    $sanitized['social_links'][] = array(
                        'platform' => sanitize_text_field( $link['platform'] ),
                        'url' => esc_url_raw( $link['url'] )
                    );
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'toplevel_page_devhold' !== $hook ) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        wp_enqueue_style( 
            'devhold-admin', 
            DEVHOLD_PLUGIN_URL . 'assets/css/admin.css', 
            array(), 
            DEVHOLD_VERSION 
        );
        
        wp_enqueue_script( 
            'devhold-admin', 
            DEVHOLD_PLUGIN_URL . 'assets/js/admin.js', 
            array( 'jquery', 'wp-color-picker' ), 
            DEVHOLD_VERSION, 
            true 
        );
        
        wp_localize_script( 'devhold-admin', 'devhold', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'devhold_nonce' )
        ) );
    }
    
    /**
     * Add admin bar menu
     */
    public function add_admin_bar_menu( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $status = ! empty( $this->options['enabled'] ) ? 'active' : 'inactive';
        $status_text = ! empty( $this->options['enabled'] ) ? __( 'Active', 'devhold' ) : __( 'Inactive', 'devhold' );
        
        $wp_admin_bar->add_node( array(
            'id' => 'devhold-status',
            'title' => '<span class="ab-icon"></span><span class="ab-label">DevHold: ' . $status_text . '</span>',
            'href' => admin_url( 'admin.php?page=devhold' ),
            'meta' => array(
                'class' => 'devhold-admin-bar-' . $status,
                'title' => __( 'DevHold Maintenance Mode', 'devhold' )
            )
        ) );
        
        // Quick toggle button
        $wp_admin_bar->add_node( array(
            'id' => 'devhold-toggle',
            'parent' => 'devhold-status',
            'title' => ! empty( $this->options['enabled'] ) ? __( 'Disable', 'devhold' ) : __( 'Enable', 'devhold' ),
            'href' => '#',
            'meta' => array(
                'onclick' => 'devholdToggleStatus(); return false;'
            )
        ) );
    }
    
    /**
     * Toggle status via AJAX
     */
    public function ajax_toggle_status() {
        if ( ! check_ajax_referer( 'devhold_nonce', 'nonce', false ) ) {
            wp_die( 'Security check failed!' );
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have permission!' );
        }
        
        // Reload options
        $default_options = array(
            'enabled' => false,
            'title' => __( 'Under Development', 'devhold' ),
            'message' => __( 'Our site is currently under development. Please visit again later.', 'devhold' ),
            'countdown_enabled' => false,
            'countdown_date' => '',
            'social_links' => array(),
            'custom_css' => '',
            'logo_url' => '',
            'background_color' => '#667eea',
            'text_color' => '#ffffff',
            'bypass_roles' => array( 'administrator' ),
            'design_style' => 'minimal',
            'background_image' => '',
            'subtitle' => __( 'Under Development', 'devhold' )
        );
        
        $this->options = get_option( 'devhold_options', array() );
        $this->options = wp_parse_args( $this->options, $default_options );
        
        $this->options['enabled'] = ! $this->options['enabled'];
        update_option( 'devhold_options', $this->options );
        
        wp_send_json_success( array(
            'enabled' => $this->options['enabled'],
            'message' => $this->options['enabled'] ? __( 'Maintenance mode enabled.', 'devhold' ) : __( 'Maintenance mode disabled.', 'devhold' )
        ) );
    }
}

// Initialize plugin
DevHold::get_instance();