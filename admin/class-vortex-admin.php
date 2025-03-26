<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://vortexartec.com
 * @since      1.0.0
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * admin area display, settings, and functionality.
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/admin
 * @author     Marianne Nems <Marianne@VortexArtec.com>
 */
class Vortex_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The plugin admin pages.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $admin_pages    List of admin pages.
     */
    private $admin_pages = array();

    /**
     * The plugin admin tabs.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $admin_tabs    List of admin tabs for pages.
     */
    private $admin_tabs = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->setup_admin_hooks();
        $this->setup_admin_pages();
    }

    /**
     * Register all hooks related to the admin area functionality.
     *
     * @since    1.0.0
     */
    private function setup_admin_hooks() {
        // Admin menu and pages
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        
        // Admin notices
        add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
        
        // Plugin actions and links
        // add_filter( 'plugin_action_links_' . $this->plugin_name . '/' . $this->plugin_name . '.php', 
        //             array( $this, 'add_plugin_action_links' ) );
        
        // Admin AJAX handlers
        add_action( 'wp_ajax_vortex_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
        add_action( 'wp_ajax_vortex_admin_action', array( $this, 'ajax_admin_action' ) );
        
        // Dashboard widgets
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
        
        // Admin footer text
        // add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 10, 1 );
        
        // Admin customizations
        // add_action( 'current_screen', array( $this, 'customize_admin_screens' ) );
        
        // TinyMCE customizations
        add_filter( 'mce_buttons', array( $this, 'register_mce_buttons' ) );
        add_filter( 'mce_external_plugins', array( $this, 'register_mce_javascript' ) );
    }

    /**
     * Add analytics dashboard widgets.
     *
     * @since    1.0.0
     */
    public function add_dashboard_widgets() {
        // Check if analytics is enabled
        if ( ! get_option( 'vortex_analytics_enabled', true ) ) {
            return;
        }
        
        // Check user permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Get enabled widgets from settings
        $widgets = get_option( 'vortex_analytics_dashboard_widgets', array(
            'sales_summary' => true,
            'artist_growth' => true,
            'artwork_statistics' => true,
            'marketplace_health' => true,
        ));
        
        // Add enabled widgets
        if ( ! empty( $widgets['sales_summary'] ) ) {
            wp_add_dashboard_widget(
                'vortex_analytics_sales_summary',
                __( 'VORTEX Marketplace: Sales Summary', 'vortex-ai-marketplace' ),
                array( $this, 'render_sales_summary_widget' )
            );
        }
        
        if ( ! empty( $widgets['artist_growth'] ) ) {
            wp_add_dashboard_widget(
                'vortex_analytics_artist_growth',
                __( 'VORTEX Marketplace: Artist Growth', 'vortex-ai-marketplace' ),
                array( $this, 'render_artist_growth_widget' )
            );
        }
        
        if ( ! empty( $widgets['artwork_statistics'] ) ) {
            wp_add_dashboard_widget(
                'vortex_analytics_artwork_statistics',
                __( 'VORTEX Marketplace: Artwork Statistics', 'vortex-ai-marketplace' ),
                array( $this, 'render_artwork_statistics_widget' )
            );
        }
        
        if ( ! empty( $widgets['marketplace_health'] ) ) {
            wp_add_dashboard_widget(
                'vortex_analytics_marketplace_health',
                __( 'VORTEX Marketplace: Health Metrics', 'vortex-ai-marketplace' ),
                array( $this, 'render_marketplace_health_widget' )
            );
        }
    }

    /**
     * Set up admin pages configuration.
     *
     * @since    1.0.0
     */
    private function setup_admin_pages() {
        // Main Dashboard Page
        $this->admin_pages['dashboard'] = array(
            'page_title' => __( 'VORTEX AI Marketplace', 'vortex-ai-marketplace' ),
            'menu_title' => __( 'VORTEX Marketplace', 'vortex-ai-marketplace' ),
            'capability' => 'manage_options',
            'menu_slug' => 'vortex_marketplace',
            'function' => 'display_dashboard_page',
            'icon_url' => 'dashicons-cart',
            'position' => 25,
            'tabs' => false,
        );
        
        // Settings Page
        $this->admin_pages['settings'] = array(
            'page_title' => __( 'Marketplace Settings', 'vortex-ai-marketplace' ),
            'menu_title' => __( 'Settings', 'vortex-ai-marketplace' ),
            'capability' => 'manage_options',
            'menu_slug' => 'vortex_marketplace_settings',
            'function' => 'display_settings_page',
            'parent_slug' => 'vortex_marketplace',
            'tabs' => true,
        );
        
        // Settings Tabs
        $this->admin_tabs['settings'] = array(
            'general' => array(
                'title' => __( 'General', 'vortex-ai-marketplace' ),
                'function' => 'display_general_settings_tab',
            ),
            'artwork' => array(
                'title' => __( 'Artwork', 'vortex-ai-marketplace' ),
                'function' => 'display_artwork_settings_tab',
            ),
            'artists' => array(
                'title' => __( 'Artists', 'vortex-ai-marketplace' ),
                'function' => 'display_artists_settings_tab',
            ),
            'payments' => array(
                'title' => __( 'Payments', 'vortex-ai-marketplace' ),
                'function' => 'display_payments_settings_tab',
            ),
            'blockchain' => array(
                'title' => __( 'Blockchain', 'vortex-ai-marketplace' ),
                'function' => 'display_blockchain_settings_tab',
            ),
            'ai' => array(
                'title' => __( 'AI Models', 'vortex-ai-marketplace' ),
                'function' => 'display_ai_settings_tab',
            ),
            'advanced' => array(
                'title' => __( 'Advanced', 'vortex-ai-marketplace' ),
                'function' => 'display_advanced_settings_tab',
            ),
        );
        
        // Tools Page
        $this->admin_pages['tools'] = array(
            'page_title' => __( 'Marketplace Tools', 'vortex-ai-marketplace' ),
            'menu_title' => __( 'Tools', 'vortex-ai-marketplace' ),
            'capability' => 'manage_options',
            'menu_slug' => 'vortex_marketplace_tools',
            'function' => 'display_tools_page',
            'parent_slug' => 'vortex_marketplace',
            'tabs' => true,
        );
        
        // Tools Tabs
        $this->admin_tabs['tools'] = array(
            'import' => array(
                'title' => __( 'Import', 'vortex-ai-marketplace' ),
                'function' => 'display_import_tools_tab',
            ),
            'export' => array(
                'title' => __( 'Export', 'vortex-ai-marketplace' ),
                'function' => 'display_export_tools_tab',
            ),
            'maintenance' => array(
                'title' => __( 'Maintenance', 'vortex-ai-marketplace' ),
                'function' => 'display_maintenance_tools_tab',
            ),
            'blockchain' => array(
                'title' => __( 'Blockchain', 'vortex-ai-marketplace' ),
                'function' => 'display_blockchain_tools_tab',
            ),
        );
        
        // Status Page
        $this->admin_pages['status'] = array(
            'page_title' => __( 'Marketplace Status', 'vortex-ai-marketplace' ),
            'menu_title' => __( 'Status', 'vortex-ai-marketplace' ),
            'capability' => 'manage_options',
            'menu_slug' => 'vortex_marketplace_status',
            'function' => 'display_status_page',
            'parent_slug' => 'vortex_marketplace',
            'tabs' => false,
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     * @param    string    $hook    The current admin page.
     */
    public function enqueue_styles( $hook ) {
        // Only load on our admin pages
        if ( $this->is_vortex_admin_page( $hook ) ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wp-jquery-ui-dialog' );
            
            wp_enqueue_style(
                $this->plugin_name . '-admin',
                plugin_dir_url( __FILE__ ) . 'css/vortex-admin.css',
                array(), 
                $this->version, 
                'all'
            );
            
            // Load dashboard specific styles
            if ( strpos( $hook, 'vortex_marketplace' ) === 0 ) {
                wp_enqueue_style(
                    $this->plugin_name . '-dashboard',
                    plugin_dir_url( __FILE__ ) . 'css/vortex-dashboard.css',
                    array( $this->plugin_name . '-admin' ), 
                    $this->version, 
                    'all'
                );
            }
        }
        
        // Load post edit styles for custom post types
        $screen = get_current_screen();
        if ( isset( $screen->post_type ) && in_array( $screen->post_type, array( 'vortex_artwork', 'vortex_artist', 'vortex_huraii_template' ) ) ) {
            wp_enqueue_style(
                $this->plugin_name . '-post-edit',
                plugin_dir_url( __FILE__ ) . 'css/vortex-post-edit.css',
                array(), 
                $this->version, 
                'all'
            );
        }

        /**
         * An instance of this class should be passed to the run() function
         * defined in Vortex_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Vortex_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $screen = get_current_screen();
        
        // Main admin styles for all admin pages
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/vortex-admin.css', array(), $this->version, 'all');
        
        // Dashboard specific styles
        if ($screen && $screen->id === 'toplevel_page_vortex-dashboard') {
            wp_enqueue_style($this->plugin_name . '-dashboard', plugin_dir_url(__FILE__) . 'css/vortex-dashboard.css', array(), $this->version, 'all');
        }
        
        // Artwork edit page styles
        if ($screen && $screen->id === 'vortex_artwork') {
            wp_enqueue_style($this->plugin_name . '-post-edit', plugin_dir_url(__FILE__) . 'css/vortex-post-edit.css', array(), $this->version, 'all');
            wp_enqueue_style('wp-color-picker');
        }
        
        // Settings page styles
        if ($screen && strpos($screen->id, 'vortex-settings') !== false) {
            wp_enqueue_style('wp-color-picker');
        }
        
        // Metrics dashboard styles
        if ($screen && strpos($screen->id, 'page_vortex-tools') !== false) {
            wp_enqueue_style($this->plugin_name . '-metrics', plugin_dir_url(__FILE__) . 'css/vortex-metrics-dashboard.css', array(), $this->version, 'all');
        }
        
        // Language admin styles
        if ($screen && (strpos($screen->id, 'page_vortex-settings') !== false || strpos($screen->id, 'page_vortex-status') !== false)) {
            wp_enqueue_style($this->plugin_name . '-language', plugin_dir_url(__FILE__) . 'css/vortex-language-admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     * @param    string    $hook    The current admin page.
     */
    public function enqueue_scripts( $hook ) {
        // Only load on our admin pages
        if ( $this->is_vortex_admin_page( $hook ) ) {
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            
            wp_enqueue_script(
                $this->plugin_name . '-admin',
                plugin_dir_url( __FILE__ ) . 'js/vortex-admin.js',
                array( 'jquery' ), 
                $this->version, 
                false
            );
            
            // Localize admin script with required data
            wp_localize_script(
                $this->plugin_name . '-admin',
                'vortexAdmin',
                array(
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'vortex_admin_nonce' ),
                    'strings' => array(
                        'confirmDelete' => __( 'Are you sure you want to delete this item? This action cannot be undone.', 'vortex-ai-marketplace' ),
                        'confirmReset' => __( 'Are you sure you want to reset these settings to defaults? This action cannot be undone.', 'vortex-ai-marketplace' ),
                        'success' => __( 'Operation completed successfully!', 'vortex-ai-marketplace' ),
                        'error' => __( 'An error occurred. Please try again.', 'vortex-ai-marketplace' ),
                        'saving' => __( 'Saving...', 'vortex-ai-marketplace' ),
                        'loading' => __( 'Loading...', 'vortex-ai-marketplace' ),
                    ),
                    'currencies' => $this->get_currency_list(),
                    'currentTab' => isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general',
                )
            );
            
            // Load chart.js for analytics and dashboard
            if ( strpos( $hook, 'vortex_marketplace' ) === 0 || strpos( $hook, 'vortex_analytics' ) === 0 ) {
                wp_enqueue_script(
                    'chartjs',
                    'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js',
                    array(),
                    '3.7.1',
                    false
                );
                
                wp_enqueue_script(
                    $this->plugin_name . '-dashboard',
                    plugin_dir_url( __FILE__ ) . 'js/vortex-dashboard.js',
                    array( 'jquery', 'chartjs', $this->plugin_name . '-admin' ), 
                    $this->version, 
                    false
                );
            }
        }
        
        // Load post edit scripts for custom post types
        $screen = get_current_screen();
        if ( isset( $screen->post_type ) && in_array( $screen->post_type, array( 'vortex_artwork', 'vortex_artist', 'vortex_huraii_template' ) ) ) {
            wp_enqueue_media();
            
            wp_enqueue_script(
                $this->plugin_name . '-post-edit',
                plugin_dir_url( __FILE__ ) . 'js/vortex-post-edit.js',
                array( 'jquery' ), 
                $this->version, 
                false
            );
            
            // Localize post edit script
            wp_localize_script(
                $this->plugin_name . '-post-edit',
                'vortexPostEdit',
                array(
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'vortex_post_edit_nonce' ),
                    'postType' => $screen->post_type,
                    'strings' => array(
                        'mediaTitle' => __( 'Select or Upload Image', 'vortex-ai-marketplace' ),
                        'mediaButton' => __( 'Use this image', 'vortex-ai-marketplace' ),
                        'confirmDelete' => __( 'Are you sure you want to delete this item? This action cannot be undone.', 'vortex-ai-marketplace' ),
                    ),
                )
            );
        }

        /**
         * An instance of this class should be passed to the run() function
         * defined in Vortex_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Vortex_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $screen = get_current_screen();
        
        // Dashboard specific scripts
        if ($screen && $screen->id === 'toplevel_page_vortex-dashboard') {
            wp_enqueue_script($this->plugin_name . '-dashboard', plugin_dir_url(__FILE__) . 'js/vortex-dashboard.js', array('jquery', $this->plugin_name), $this->version, false);
        }
        
        // Artwork edit page scripts
        if ($screen && $screen->id === 'vortex_artwork') {
            wp_enqueue_script($this->plugin_name . '-post-edit', plugin_dir_url(__FILE__) . 'js/vortex-post-edit.js', array('jquery', 'wp-color-picker', $this->plugin_name), $this->version, false);
        }
        
        // Settings page scripts
        if ($screen && strpos($screen->id, 'vortex-settings') !== false) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_media();
        }
        
        // Metrics dashboard scripts
        if ($screen && strpos($screen->id, 'page_vortex-tools') !== false) {
            wp_enqueue_script($this->plugin_name . '-metrics', plugin_dir_url(__FILE__) . 'js/vortex-metrics-dashboard.js', array('jquery', $this->plugin_name), $this->version, false);
        }
        
        // Language admin scripts
        if ($screen && (strpos($screen->id, 'page_vortex-settings') !== false || strpos($screen->id, 'page_vortex-status') !== false)) {
            wp_enqueue_script($this->plugin_name . '-language', plugin_dir_url(__FILE__) . 'js/vortex-language-admin.js', array('jquery', $this->plugin_name), $this->version, false);
        }
    }

    /**
     * Register the admin menu items.
     *
     * @since    1.0.0
     */
    public function register_admin_menu() {
        // Register main menu pages
        foreach ( $this->admin_pages as $page_key => $page ) {
            if ( ! isset( $page['parent_slug'] ) ) {
                add_menu_page(
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    array( $this, $page['function'] ),
                    $page['icon_url'],
                    $page['position']
                );
            }
        }
        
        // Register submenu pages
        foreach ( $this->admin_pages as $page_key => $page ) {
            if ( isset( $page['parent_slug'] ) ) {
                add_submenu_page(
                    $page['parent_slug'],
                    $page['page_title'],
                    $page['menu_title'],
                    $page['capability'],
                    $page['menu_slug'],
                    array( $this, $page['function'] )
                );
            }
        }
    }

    /**
     * Check if current page is one of our admin pages.
     *
     * @since    1.0.0
     * @param    string    $hook    The current admin page.
     * @return   boolean            True if is our admin page.
     */
    private function is_vortex_admin_page( $hook ) {
        // Check if it's one of our admin pages
        foreach ( $this->admin_pages as $page ) {
            $page_hook = isset( $page['parent_slug'] ) 
                ? $page['parent_slug'] . '_page_' . $page['menu_slug']
                : $page['menu_slug'];
            
            if ( $hook === $page_hook ) {
                return true;
            }
        }
        
        // Check for custom post type screens
        $screen = get_current_screen();
        if ( isset( $screen->post_type ) && in_array( $screen->post_type, array( 
            'vortex_artwork', 
            'vortex_artist', 
            'vortex_huraii_template', 
            'vortex_transaction' 
        ) ) ) {
            return true;
        }
        
        return false;
    }

    /**
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // General Settings
        register_setting( 'vortex_general_settings', 'vortex_marketplace_title' );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_description' );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_currency', array(
            'default' => 'USD',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_currency_symbol', array(
            'default' => '$',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_commission_rate', array(
            'default' => '10',
            'sanitize_callback' => array( $this, 'sanitize_percentage' ),
        ) );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_featured_items', array(
            'default' => '8',
            'sanitize_callback' => 'absint',
        ) );
        register_setting( 'vortex_general_settings', 'vortex_marketplace_enable_reviews', array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        
        // Artwork Settings
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_min_price', array(
            'default' => '5',
            'sanitize_callback' => 'floatval',
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_max_price', array(
            'default' => '10000',
            'sanitize_callback' => 'floatval',
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_default_license', array(
            'default' => 'standard',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_enable_licensing', array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_image_sizes', array(
            'default' => array(
                'thumbnail' => array( 'width' => 300, 'height' => 300, 'crop' => true ),
                'medium' => array( 'width' => 600, 'height' => 600, 'crop' => true ),
                'large' => array( 'width' => 1200, 'height' => 1200, 'crop' => false ),
            ),
            'sanitize_callback' => array( $this, 'sanitize_image_sizes' ),
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_watermark', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_artwork_settings', 'vortex_artwork_watermark_image', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );
        
        // Artists Settings
        register_setting( 'vortex_artists_settings', 'vortex_artists_require_verification', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_artists_settings', 'vortex_artists_verification_method', array(
            'default' => 'manual',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_artists_settings', 'vortex_artists_commission_rate', array(
            'default' => '85',
            'sanitize_callback' => array( $this, 'sanitize_percentage' ),
        ) );
        register_setting( 'vortex_artists_settings', 'vortex_artists_featured_count', array(
            'default' => '5',
            'sanitize_callback' => 'absint',
        ) );
        register_setting( 'vortex_artists_settings', 'vortex_artists_profile_fields', array(
            'default' => array(
                'bio' => true,
                'specialties' => true,
                'social_links' => true,
                'website' => true,
            ),
            'sanitize_callback' => array( $this, 'sanitize_profile_fields' ),
        ) );
        
        // Payment Settings
        register_setting( 'vortex_payments_settings', 'vortex_payments_methods', array(
            'default' => array(
                'tola' => true,
                'credit_card' => true,
                'paypal' => false,
                'crypto' => false,
            ),
            'sanitize_callback' => array( $this, 'sanitize_payment_methods' ),
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_stripe_enabled', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_stripe_public_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_stripe_secret_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_paypal_enabled', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_paypal_client_id', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_paypal_secret', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_payments_settings', 'vortex_payments_payout_schedule', array(
            'default' => 'monthly',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        
        // Blockchain Settings
        register_setting( 'vortex_blockchain_settings', 'vortex_blockchain_enabled', array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_blockchain_network', array(
            'default' => 'solana',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_blockchain_rpc_url', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_tola_contract_address', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_marketplace_wallet_address', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_blockchain_nft_enabled', array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_blockchain_settings', 'vortex_blockchain_nft_creator_royalty', array(
            'default' => '10',
            'sanitize_callback' => array( $this, 'sanitize_percentage' ),
        ) );
        
        // AI Model Settings
        register_setting( 'vortex_ai_settings', 'vortex_ai_enabled', array(
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_default_model', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_api_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_api_endpoint', array(
            'sanitize_callback' => 'esc_url_raw',
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_max_tokens', array(
            'default' => '1000',
            'sanitize_callback' => 'absint',
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_allowed_models', array(
            'default' => array(),
            'sanitize_callback' => array( $this, 'sanitize_ai_models' ),
        ) );
        register_setting( 'vortex_ai_settings', 'vortex_ai_public_generation', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        
        // Advanced Settings
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_debug_mode', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_cache_ttl', array(
            'default' => '3600',
            'sanitize_callback' => 'absint',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_recaptcha_enabled', array(
            'default' => false,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_recaptcha_site_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_recaptcha_secret', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_custom_css', array(
            'sanitize_callback' => 'wp_strip_all_tags',
        ) );
        register_setting( 'vortex_advanced_settings', 'vortex_advanced_custom_js', array(
            'sanitize_callback' => 'wp_strip_all_tags',
        ) );
    }

    /**
     * Display the main dashboard page.
     *
     * @since    1.0.0
     */
    public function display_dashboard_page() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/vortex-admin-dashboard.php';
    }

    /**
     * Display the settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        $current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
        
        include_once plugin_dir_path( __FILE__ ) . 'partials/vortex-admin-settings.php';
    }

    /**
     * Display the general settings tab.
     *
     * @since    1.0.0
     */
    public function display_general_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/general-settings.php';
    }

    /**
     * Display the artwork settings tab.
     *
     * @since    1.0.0
     */
    public function display_artwork_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/artwork-settings.php';
    }

    /**
     * Display the artists settings tab.
     *
     * @since    1.0.0
     */
    public function display_artists_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/artists-settings.php';
    }

    /**
     * Display the payments settings tab.
     *
     * @since    1.0.0
     */
    public function display_payments_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/payments-settings.php';
    }

    /**
     * Display the blockchain settings tab.
     *
     * @since    1.0.0
     */
    public function display_blockchain_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/blockchain-settings.php';
    }

    /**
     * Display the AI settings tab.
     *
     * @since    1.0.0
     */
    public function display_ai_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/ai-settings.php';
    }

    /**
     * Display the advanced settings tab.
     *
     * @since    1.0.0
     */
    public function display_advanced_settings_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/settings/advanced-settings.php';
    }

    /**
     * Display the tools page.
     *
     * @since    1.0.0
     */
    public function display_tools_page() {
        $current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'import';
        
        include_once plugin_dir_path( __FILE__ ) . 'partials/vortex-admin-tools.php';
    }

    /**
     * Display the import tools tab.
     *
     * @since    1.0.0
     */
    public function display_import_tools_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/tools/import-tools.php';
    }

    /**
     * Display the export tools tab.
     *
     * @since    1.0.0
     */
    public function display_export_tools_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/tools/export-tools.php';
    }

    /**
     * Display the maintenance tools tab.
     *
     * @since    1.0.0
     */
    public function display_maintenance_tools_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/tools/maintenance-tools.php';
    }

    /**
     * Display the blockchain tools tab.
     *
     * @since    1.0.0
     */
    public function display_blockchain_tools_tab() {
        include_once plugin_dir_path( __FILE__ ) . 'partials/tools/blockchain-tools.php';
    }

    /**
     * Display the status page.
     *
     * @since    1.0.0
     */
    public function display_status_page() {
        // include_once plugin_dir_path( __FILE__ ) . 'partials/vortex-admin-status.php';
    }

    /**
     * Display admin notices.
     *
     * @since    1.0.0
     */
    public function display_admin_notices() {
        // Display activation notice
        if ( get_transient( 'vortex_activation_notice' ) ) {
            ?>
            <div class="notice notice-success is-dismissible vortex-admin-notice" data-notice="activation">
                <p>
                    <?php 
                    printf(
                        __( 'Thank you for installing VORTEX AI Marketplace! <a href="%s">Click here</a> to configure your marketplace settings.', 'vortex-ai-marketplace' ),
                        admin_url( 'admin.php?page=vortex_marketplace_settings' )
                    ); 
                    ?>
                </p>
            </div>
            <?php
            delete_transient( 'vortex_activation_notice' );
        }
        
        // Display update notice
        if ( get_transient( 'vortex_update_notice' ) ) {
            ?>
            <div class="notice notice-info is-dismissible vortex-admin-notice" data-notice="update">
                <p>
                    <?php 
                    printf(
                        __( 'VORTEX AI Marketplace has been updated to version %s. <a href="%s">View the changelog</a> to see what\'s new.', 'vortex-ai-marketplace' ),
                        $this->version,
                        admin_url( 'admin.php?page=vortex_marketplace_status' )
                    ); 
                    ?>
                </p>
            </div>
            <?php
            delete_transient( 'vortex_update_notice' );
        }
        
        // Display blockchain configuration notice
        if ( ! get_option( 'vortex_tola_contract_address' ) && get_option( 'vortex_blockchain_enabled', true ) ) {
            ?>
            <div class="notice notice-warning is-dismissible vortex-admin-notice" data-notice="blockchain_config">
                <p>
                    <?php 
                    printf(
                        __( 'VORTEX AI Marketplace: Blockchain is enabled but TOLA token contract address is not configured. <a href="%s">Configure now</a>.', 'vortex-ai-marketplace' ),
                        admin_url( 'admin.php?page=vortex_marketplace_settings&tab=blockchain' )
                    ); 
                    ?>
                </p>
            </div>
            <?php
        }
        
        // Display AI configuration notice
        if ( ! get_option( 'vortex_ai_api_key' ) && get_option( 'vortex_ai_enabled', true ) ) {
            ?>
            <div class="notice notice-warning is-dismissible vortex-admin-notice" data-notice="ai_config">
                <p>
                    <?php 
                    printf(
                        __( 'VORTEX AI Marketplace: AI generation is enabled but API key is not configured. <a href="%s">Configure now</a>.', 'vortex-ai-marketplace' ),
                        admin_url( 'admin.php?page=vortex_marketplace_settings&tab=ai' )
                    ); 
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * AJAX handler for dismissing admin notices.
     *
     * @since    1.0.0
     */
    public function ajax_dismiss_notice() {
        // Check nonce
        if ( ! check_ajax_referer( 'vortex_admin_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'vortex-ai-marketplace' ) ) );
        }
        
        // Get notice
    }
}
