<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes
 */

class Vortex_AI_Marketplace {

    /**
     * The single instance of the class.
     *
     * @var Vortex_Scheduler_Shortcodes
     */
    protected static $instance = null;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Vortex_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Blockchain instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      Vortex_Blockchain    $blockchain    The Blockchain instance.
     */
    private $blockchain;

    /**
     * Wallet instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      Vortex_Wallet    $wallet    The Wallet instance.
     */
    private $wallet;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->version = VORTEX_AI_MARKETPLACE_VERSION;
        $this->plugin_name = 'vortex-ai-marketplace';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_post_types();
        $this->define_metrics_hooks();
        $this->define_huraii_hooks();
        $this->define_blockchain_hooks();
        $this->define_tola_hooks();
        $this->initialize_database();
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Vortex_Loader. Orchestrates the hooks of the plugin.
     * - Vortex_i18n. Defines internationalization functionality.
     * - Vortex_Admin. Defines all hooks for the admin area.
     * - Vortex_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-theme-compatibility.php';

        // The class responsible for orchestrating the actions and filters of the core plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-loader.php';

        // The class responsible for defining internationalization functionality of the plugin.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-i18n.php';

        // The class responsible for defining all actions that occur in the admin area.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vortex-admin.php';

        // The class responsible for defining all actions that occur in the public-facing side of the site.
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vortex-public.php';

        // Load post types
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-vortex-artwork.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-vortex-artist.php';

        // Load metrics and rankings
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-metrics.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-rankings.php';

        // Load HURAII AI integration
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-huraii.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ai-models/class-vortex-img2img.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ai-models/class-vortex-model-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-image-processor.php';

        /**
         * Analytics and metrics.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-metrics.php';
        // require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-analytics.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-rankings.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/class-vortex-metrics-api.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/class-vortex-rankings-api.php';

        /**
         * Translation and internationalization.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'api/class-vortex-translation-api.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/class-vortex-language-db.php';

        // Load blockchain integration
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vortex-blockchain.php';

        // Load TOLA token integration
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/blockchain/class-vortex-tola.php';

        $this->loader = new Vortex_Loader( $this->get_plugin_name(), $this->get_version() );
    }

    /**
     * Initialize the database tables for the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function initialize_database() {
        // Check if tables need to be created/updated
        $this->loader->add_action('plugins_loaded', $this, 'check_database_version');
    }

    /**
     * Check the database version and update if necessary.
     *
     * @since    1.0.0
     */
    public function check_database_version() {
        $current_db_version = get_option('vortex_database_version', '0');
        
        // if (version_compare($current_db_version, $this->version, '<')) {
            $this->create_database_tables();
            update_option('vortex_database_version', $this->version);
        // }
    }

    /**
     * Create the database tables for the plugin.
     *
     * @since    1.0.0
     */
    public function create_database_tables() {
        global $wpdb;
        
        // Get collation
        $charset_collate = $wpdb->get_charset_collate();
        
        // Include database schema files
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/schemas/core-schema.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/schemas/tola-schema.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/schemas/metrics-schema.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/schemas/rankings-schema.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'database/schemas/language-schema.php';
        
        // Execute core schema
        vortex_create_core_schema($charset_collate);
        
        // Execute TOLA schema
        vortex_create_tola_schema($charset_collate);
        
        // Execute metrics schema
        vortex_create_metrics_schema($charset_collate);
        
        // Execute rankings schema
        vortex_create_rankings_schema($charset_collate);
        
        // Execute language schema
        vortex_create_language_schema($charset_collate);
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Vortex_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Vortex_i18n();
        $plugin_i18n->set_domain( $this->get_plugin_name() );

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Vortex_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Vortex_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

    /**
     * Register custom post types and taxonomies.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_post_types() {
        // Include post type classes
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-vortex-artwork.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-vortex-huraii-template.php';
        
        // Initialize post type objects
        $artwork = new Vortex_Artwork( $this->get_plugin_name(), $this->get_version() );
        $huraii_template = new Vortex_Huraii_Template();

        // Register post types on WordPress init
        $this->loader->add_action('init', $artwork, 'register_post_type');
        $this->loader->add_action('init', $huraii_template, 'register');
    }

    /**
     * Register metrics and rankings hooks.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_metrics_hooks() {
        $metrics = new Vortex_Metrics($this->get_plugin_name(), $this->get_version());
        $rankings = new Vortex_Rankings($this->get_plugin_name(), $this->get_version());

        // $this->loader->add_action('init', $metrics, 'initialize_metrics');
        // $this->loader->add_action('init', $rankings, 'initialize_rankings');
    }

    /**
     * Register HURAII AI integration hooks.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_huraii_hooks() {
        // $huraii = new Vortex_Huraii($this->get_plugin_name(), $this->get_version());
        $image_processor = new Vortex_Image_Processor($this->get_plugin_name(), $this->get_version());

        // $this->loader->add_action('init', $huraii, 'initialize_huraii');
        // $this->loader->add_action('init', $image_processor, 'initialize_processor');
    }

    /**
     * Register blockchain integration hooks.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_blockchain_hooks() {
        $blockchain = new Vortex_Blockchain($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $blockchain, 'initialize_blockchain');
    }

    /**
     * Register TOLA token integration hooks.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_tola_hooks() {

        // Load Blockchain Integration
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/blockchain/class-vortex-blockchain-integration.php';
        
        // Load Wallet 
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/blockchain/class-vortex-wallet-connect.php';

        $this->blockchain = new Vortex_Blockchain_Integration();
        $this->wallet = new Vortex_Wallet_Connect($this->blockchain);

        // Initialize TOLA token integration
        $tola = new Vortex_TOLA( $this->plugin_name, $this->version, $this->blockchain, $this->wallet, true );
        
        // Register admin settings
        $this->loader->add_action('admin_init', $tola, 'register_settings');
        
        // Add meta boxes for product pricing
        $this->loader->add_action('add_meta_boxes', $tola, 'add_product_meta_boxes');
        
        // Save meta box data
        $this->loader->add_action('save_post_vortex_product', $tola, 'save_tola_pricing_meta_box');
        
        // Filter content to check access
        // $this->loader->add_filter('the_content', $tola, 'check_content_access', 20);
        
        // AJAX handlers are already defined in the TOLA class constructor
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Vortex_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
} 

new Vortex_AI_Marketplace();