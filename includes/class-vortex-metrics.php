<?php
/**
 * The metrics collection and processing functionality.
 *
 * @link       https://vortexartec.com
 * @since      1.0.0
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes
 */

/**
 * The metrics collection and processing functionality.
 *
 * This class handles collecting, storing, and retrieving various marketplace metrics
 * such as sales figures, user engagement, artwork popularity, and platform growth.
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes
 * @author     Marianne Nems <Marianne@VortexArtec.com>
 */
class Vortex_Metrics {

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
     * Database table name for metrics data.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $metrics_table    Database table name.
     */
    private $metrics_table;

    /**
     * Database table name for time-series metrics.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $timeseries_table    Database table name.
     */
    private $timeseries_table;

    /**
     * Logger instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $logger    Logger instance.
     */
    private $logger;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     * @param    object    $logger            Optional. Logger instance.
     */
    public function __construct( $plugin_name, $version, $logger = null ) {
        global $wpdb;
        
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->logger = $logger;
        
        // Set database table names
        $this->metrics_table = $wpdb->prefix . 'vortex_metrics';
        $this->timeseries_table = $wpdb->prefix . 'vortex_metrics_timeseries';
        
        // Register hooks
        $this->register_hooks();
    }

    /**
     * Log debugging info to the error log.
     *
     * Enabled when WP_DEBUG_LOG is enabled (and WP_DEBUG, since according to
     * core, "WP_DEBUG_DISPLAY and WP_DEBUG_LOG perform no function unless
     * WP_DEBUG is true), but can be disabled via the akismet_debug_log filter.
     *
     * @param mixed $akismet_debug The data to log.
     */
    public static function log( $message, $status = 'info' ) {
        error_log( print_r( compact( 'message' ), true ) );
    }

    /**
     * Register hooks for metrics collection and processing.
     *
     * @since    1.0.0
     */
    private function register_hooks() {
        // Schedule daily metrics collection
        if ( ! wp_next_scheduled( 'vortex_daily_metrics_update' ) ) {
            wp_schedule_event( time(), 'daily', 'vortex_daily_metrics_update' );
        }
        
        // Register hooks for metrics collection
        add_action( 'vortex_daily_metrics_update', array( $this, 'collect_daily_metrics' ) );
        add_action( 'vortex_artwork_purchased', array( $this, 'record_sale_metrics' ), 10, 3 );
        add_action( 'wp_ajax_vortex_update_view_count', array( $this, 'ajax_update_view_count' ) );
        add_action( 'wp_ajax_nopriv_vortex_update_view_count', array( $this, 'ajax_update_view_count' ) );
        add_action( 'user_register', array( $this, 'record_user_registration' ) );
        
        // Marketplace-specific metrics
        add_action( 'post_updated', array( $this, 'record_content_metrics' ), 10, 3 );
        add_action( 'wp_login', array( $this, 'record_user_login' ), 10, 2 );
        add_action( 'vortex_artist_verified', array( $this, 'record_artist_verification' ), 10, 1 );
        
        // REST API routes
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        
        // Admin hooks
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Register plugin settings for metrics configuration.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting( 'vortex_metrics_settings', 'vortex_metrics_collection_enabled', array(
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
        
        register_setting( 'vortex_metrics_settings', 'vortex_metrics_retention_days', array(
            'type' => 'integer',
            'default' => 365,
            'sanitize_callback' => 'absint',
        ));
        
        register_setting( 'vortex_metrics_settings', 'vortex_metrics_excluded_roles', array(
            'type' => 'array',
            'default' => array( 'administrator' ),
            'sanitize_callback' => function( $roles ) {
                return is_array( $roles ) ? array_map( 'sanitize_text_field', $roles ) : array();
            },
        ));
        
        register_setting( 'vortex_metrics_settings', 'vortex_metrics_anonymize_ips', array(
            'type' => 'boolean',
            'default' => true,
            'sanitize_callback' => 'rest_sanitize_boolean',
        ));
    }

    /**
     * Register REST API routes.
     *
     * @since    1.0.0
     */
    public function register_rest_routes() {
        register_rest_route( 'vortex/v1', '/metrics/summary', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'api_get_metrics_summary' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            }
        ));
        
        register_rest_route( 'vortex/v1', '/metrics/timeseries/(?P<metric>[a-zA-Z0-9_]+)', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'api_get_metric_timeseries' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
            'args' => array(
                'metric' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'start_date' => array(
                    'type' => 'string',
                    'format' => 'date',
                ),
                'end_date' => array(
                    'type' => 'string',
                    'format' => 'date',
                ),
                'interval' => array(
                    'type' => 'string',
                    'default' => 'day',
                    'enum' => array( 'hour', 'day', 'week', 'month' ),
                ),
            ),
        ));
        
        register_rest_route( 'vortex/v1', '/metrics/custom', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'api_get_custom_metrics' ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
            'args' => array(
                'metrics' => array(
                    'required' => true,
                    'type' => 'array',
                ),
                'filters' => array(
                    'type' => 'object',
                ),
                'group_by' => array(
                    'type' => 'string',
                ),
                'start_date' => array(
                    'type' => 'string',
                    'format' => 'date',
                ),
                'end_date' => array(
                    'type' => 'string',
                    'format' => 'date',
                ),
            ),
        ));
    }

    /**
     * Collect and store daily marketplace metrics.
     *
     * @since    1.0.0
     */
    public function collect_daily_metrics() {
        $this->log( 'Starting daily metrics collection', 'info' );
        
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            $this->log( 'Metrics collection is disabled', 'info' );
            return;
        }
        
        // Get current date
        $date = current_time( 'Y-m-d' );
        
        // Collect sales metrics
        $this->collect_sales_metrics( $date );
        
        // Collect user metrics
        $this->collect_user_metrics( $date );
        
        // Collect content metrics
        $this->collect_content_metrics( $date );
        
        // Collect platform performance metrics
        $this->collect_performance_metrics( $date );
        
        // Clean up old metrics data based on retention settings
        $this->cleanup_old_metrics();
        
        $this->log( 'Daily metrics collection completed', 'info' );
    }

    /**
     * Collect and store sales metrics.
     *
     * @since    1.0.0
     * @param    string    $date    The date to collect metrics for.
     */
    private function collect_sales_metrics( $date ) {
        global $wpdb;
        
        // Get sales data for the day
        $sales_query = $wpdb->prepare("
            SELECT COUNT(*) as total_sales, 
                   SUM(meta_value) as total_revenue
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_amount'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} 
                WHERE post_id = p.ID 
                AND meta_key = '_vortex_transaction_type' 
                AND meta_value = 'artwork_purchase'
            )
        ", $date . '%' );
        
        $sales_data = $wpdb->get_row( $sales_query );
        
        // Store sales metrics
        $this->store_metric( 'daily_sales_count', $sales_data->total_sales, $date );
        $this->store_metric( 'daily_sales_revenue', $sales_data->total_revenue, $date );
        
        // Get artist earnings
        $artist_earnings_query = $wpdb->prepare("
            SELECT SUM(pm2.meta_value) as artist_earnings
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_type' AND pm.meta_value = 'artist_payout'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_vortex_transaction_amount'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
        ", $date . '%' );
        
        $artist_earnings = $wpdb->get_var( $artist_earnings_query );
        $this->store_metric( 'daily_artist_earnings', $artist_earnings ? $artist_earnings : 0, $date );
        
        // Get marketplace fee revenue
        $fee_query = $wpdb->prepare("
            SELECT SUM(meta_value) as fee_revenue
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_fee'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
        ", $date . '%' );
        
        $fee_revenue = $wpdb->get_var( $fee_query );
        $this->store_metric( 'daily_marketplace_fees', $fee_revenue ? $fee_revenue : 0, $date );
        
        // Get top selling artworks
        $top_artworks_query = $wpdb->prepare("
            SELECT pm.meta_value as artwork_id, COUNT(*) as sales_count, SUM(pm2.meta_value) as revenue
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_artwork_id'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_vortex_transaction_amount'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
            GROUP BY pm.meta_value
            ORDER BY revenue DESC
            LIMIT 10
        ", $date . '%' );
        
        $top_artworks = $wpdb->get_results( $top_artworks_query );
        $this->store_metric( 'top_selling_artworks', wp_json_encode( $top_artworks ), $date );
        
        // Get top selling artists
        $top_artists_query = $wpdb->prepare("
            SELECT pm.meta_value as artist_id, COUNT(*) as sales_count, SUM(pm2.meta_value) as revenue
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_seller_id'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_vortex_transaction_amount'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
            GROUP BY pm.meta_value
            ORDER BY revenue DESC
            LIMIT 10
        ", $date . '%' );
        
        $top_artists = $wpdb->get_results( $top_artists_query );
        $this->store_metric( 'top_selling_artists', wp_json_encode( $top_artists ), $date );
    }

    /**
     * Collect and store user metrics.
     *
     * @since    1.0.0
     * @param    string    $date    The date to collect metrics for.
     */
    private function collect_user_metrics( $date ) {
        global $wpdb;
        
        // Get new user registrations for the day
        $registrations_query = $wpdb->prepare("
            SELECT COUNT(*) as new_users
            FROM {$wpdb->users}
            WHERE user_registered LIKE %s
        ", $date . '%' );
        
        $new_users = $wpdb->get_var( $registrations_query );
        $this->store_metric( 'daily_new_users', $new_users, $date );
        
        // Get active users (users who logged in)
        $active_users = get_option( 'vortex_daily_active_users_' . str_replace( '-', '', $date ), 0 );
        $this->store_metric( 'daily_active_users', $active_users, $date );
        
        // Get total registered users
        $total_users_query = "SELECT COUNT(*) FROM {$wpdb->users}";
        $total_users = $wpdb->get_var( $total_users_query );
        $this->store_metric( 'total_users', $total_users, $date );
        
        // Get new artist registrations
        $new_artists_query = $wpdb->prepare("
            SELECT COUNT(*) as new_artists
            FROM {$wpdb->posts}
            WHERE post_type = 'vortex_artist'
            AND post_status = 'publish'
            AND post_date LIKE %s
        ", $date . '%' );
        
        $new_artists = $wpdb->get_var( $new_artists_query );
        $this->store_metric( 'daily_new_artists', $new_artists, $date );
        
        // Get total artists
        $total_artists_query = "
            SELECT COUNT(*) as total_artists
            FROM {$wpdb->posts}
            WHERE post_type = 'vortex_artist'
            AND post_status = 'publish'
        ";
        
        $total_artists = $wpdb->get_var( $total_artists_query );
        $this->store_metric( 'total_artists', $total_artists, $date );
        
        // Get verified artists
        $verified_artists_query = "
            SELECT COUNT(*) as verified_artists
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'vortex_artist'
            AND p.post_status = 'publish'
            AND pm.meta_key = '_vortex_artist_verified'
            AND pm.meta_value = '1'
        ";
        
        $verified_artists = $wpdb->get_var( $verified_artists_query );
        $this->store_metric( 'verified_artists', $verified_artists, $date );
        
        // Calculate user retention (users who were active 7 days ago and are active today)
        $previous_week = date( 'Y-m-d', strtotime( $date . ' -7 days' ) );
        $previous_active_users = get_option( 'vortex_daily_active_users_' . str_replace( '-', '', $previous_week ), 0 );
        
        $retention_rate = $previous_active_users > 0 ? ( $active_users / $previous_active_users ) * 100 : 0;
        $this->store_metric( 'weekly_retention_rate', $retention_rate, $date );
    }

    /**
     * Collect and store content metrics.
     *
     * @since    1.0.0
     * @param    string    $date    The date to collect metrics for.
     */
    private function collect_content_metrics( $date ) {
        global $wpdb;
        
        // Get new artworks published
        $new_artworks_query = $wpdb->prepare("
            SELECT COUNT(*) as new_artworks
            FROM {$wpdb->posts}
            WHERE post_type = 'vortex_artwork'
            AND post_status = 'publish'
            AND post_date LIKE %s
        ", $date . '%' );
        
        $new_artworks = $wpdb->get_var( $new_artworks_query );
        $this->store_metric( 'daily_new_artworks', $new_artworks, $date );
        
        // Get total artworks
        $total_artworks_query = "
            SELECT COUNT(*) as total_artworks
            FROM {$wpdb->posts}
            WHERE post_type = 'vortex_artwork'
            AND post_status = 'publish'
        ";
        
        $total_artworks = $wpdb->get_var( $total_artworks_query );
        $this->store_metric( 'total_artworks', $total_artworks, $date );
        
        // Get total artwork views
        $artwork_views = get_option( 'vortex_daily_artwork_views_' . str_replace( '-', '', $date ), 0 );
        $this->store_metric( 'daily_artwork_views', $artwork_views, $date );
        
        // Get AI-generated artworks
        $ai_artworks_query = "
            SELECT COUNT(*) as ai_artworks
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'vortex_artwork'
            AND p.post_status = 'publish'
            AND pm.meta_key = '_vortex_artwork_ai_generated'
            AND pm.meta_value = '1'
        ";
        
        $ai_artworks = $wpdb->get_var( $ai_artworks_query );
        $this->store_metric( 'ai_generated_artworks', $ai_artworks, $date );
        
        // Calculate AI artwork percentage
        $ai_percentage = $total_artworks > 0 ? ( $ai_artworks / $total_artworks ) * 100 : 0;
        $this->store_metric( 'ai_artwork_percentage', $ai_percentage, $date );
        
        // Get most popular categories
        $categories_query = "
            SELECT t.term_id, t.name, COUNT(tr.object_id) as artwork_count
            FROM {$wpdb->terms} t
            JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            JOIN {$wpdb->posts} p ON tr.object_id = p.ID
            WHERE tt.taxonomy = 'vortex_artwork_category'
            AND p.post_type = 'vortex_artwork'
            AND p.post_status = 'publish'
            GROUP BY t.term_id
            ORDER BY artwork_count DESC
            LIMIT 10
        ";
        
        $popular_categories = $wpdb->get_results( $categories_query );
        $this->store_metric( 'popular_categories', wp_json_encode( $popular_categories ), $date );
        
        // Get most used AI models
        $ai_models_query = "
            SELECT pm.meta_value as model, COUNT(*) as usage_count
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'vortex_artwork'
            AND p.post_status = 'publish'
            AND pm.meta_key = '_vortex_artwork_ai_model'
            GROUP BY pm.meta_value
            ORDER BY usage_count DESC
            LIMIT 10
        ";
        
        $popular_ai_models = $wpdb->get_results( $ai_models_query );
        $this->store_metric( 'popular_ai_models', wp_json_encode( $popular_ai_models ), $date );
    }

    /**
     * Collect and store platform performance metrics.
     *
     * @since    1.0.0
     * @param    string    $date    The date to collect metrics for.
     */
    private function collect_performance_metrics( $date ) {
        // Get conversion rate (sales / views)
        $views = get_option( 'vortex_daily_artwork_views_' . str_replace( '-', '', $date ), 0 );
        
        global $wpdb;
        $sales_query = $wpdb->prepare("
            SELECT COUNT(*) as sales
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_type' AND pm.meta_value = 'artwork_purchase'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
        ", $date . '%' );
        
        $sales = $wpdb->get_var( $sales_query );
        
        $conversion_rate = $views > 0 ? ( $sales / $views ) * 100 : 0;
        $this->store_metric( 'daily_conversion_rate', $conversion_rate, $date );
        
        // Get average transaction value
        $avg_value_query = $wpdb->prepare("
            SELECT AVG(meta_value) as avg_value
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_vortex_transaction_amount'
            WHERE p.post_type = 'vortex_transaction'
            AND p.post_status = 'publish'
            AND p.post_date LIKE %s
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} 
                WHERE post_id = p.ID 
                AND meta_key = '_vortex_transaction_type' 
                AND meta_value = 'artwork_purchase'
            )
        ", $date . '%' );
        
        $avg_value = $wpdb->get_var( $avg_value_query );
        $this->store_metric( 'average_transaction_value', $avg_value ? $avg_value : 0, $date );
        
        // Get active TOLA wallets
        $wallets_query = "
            SELECT COUNT(DISTINCT meta_value) as active_wallets
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'vortex_wallet_address'
            AND meta_value != ''
        ";
        
        $active_wallets = $wpdb->get_var( $wallets_query );
        $this->store_metric( 'active_wallets', $active_wallets, $date );
        
        // Get TOLA token metrics
        $total_staked = get_option( 'vortex_tola_total_staked', 0 );
        $this->store_metric( 'total_staked_tola', $total_staked, $date );
        
        $distributed_rewards = get_option( 'vortex_tola_rewards_distributed_' . str_replace( '-', '', $date ), 0 );
        $this->store_metric( 'daily_tola_rewards', $distributed_rewards, $date );
    }

    /**
     * Record metrics when an artwork is sold.
     *
     * @since    1.0.0
     * @param    int       $artwork_id      The artwork post ID.
     * @param    float     $price           The sale price.
     * @param    int       $buyer_user_id   The buyer user ID.
     */
    public function record_sale_metrics( $artwork_id, $price, $buyer_user_id ) {
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            return;
        }
        
        $artwork = get_post( $artwork_id );
        if ( ! $artwork || 'vortex_artwork' !== $artwork->post_type ) {
            return;
        }
        
        $artist_id = $artwork->post_author;
        $date = current_time( 'Y-m-d' );
        
        // Store sales metrics for the artwork
        $this->increment_metric( 'artwork_' . $artwork_id . '_sales_count', 1, $date );
        $this->increment_metric( 'artwork_' . $artwork_id . '_sales_revenue', $price, $date );
        
        // Store sales metrics for the artist
        $this->increment_metric( 'artist_' . $artist_id . '_sales_count', 1, $date );
        $this->increment_metric( 'artist_' . $artist_id . '_sales_revenue', $price, $date );
        
        // Store sales metrics for the buyer
        $this->increment_metric( 'user_' . $buyer_user_id . '_purchases_count', 1, $date );
        $this->increment_metric( 'user_' . $buyer_user_id . '_purchases_spent', $price, $date );
        
        // Check if this is AI artwork
        $is_ai_generated = get_post_meta( $artwork_id, '_vortex_artwork_ai_generated', true );
        if ( $is_ai_generated ) {
            $this->increment_metric( 'daily_ai_artwork_sales', 1, $date );
            $this->increment_metric( 'daily_ai_artwork_revenue', $price, $date );
            
            // Track AI model performance
            $ai_model = get_post_meta( $artwork_id, '_vortex_artwork_ai_model', true );
            if ( $ai_model ) {
                $this->increment_metric( 'ai_model_' . sanitize_title( $ai_model ) . '_sales', 1, $date );
                $this->increment_metric( 'ai_model_' . sanitize_title( $ai_model ) . '_revenue', $price, $date );
            }
        }
        
        // Track category performance
        $categories = wp_get_post_terms( $artwork_id, 'vortex_artwork_category', array( 'fields' => 'ids' ) );
        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
            foreach ( $categories as $category_id ) {
                $this->increment_metric( 'category_' . $category_id . '_sales', 1, $date );
                $this->increment_metric( 'category_' . $category_id . '_revenue', $price, $date );
            }
        }
    }

    /**
     * Update view count via AJAX.
     *
     * @since    1.0.0
     */
    public function ajax_update_view_count() {
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            wp_send_json_success();
            return;
        }
        
        // Verify nonce
        if ( ! check_ajax_referer( 'vortex_metrics_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed', 'vortex-ai-marketplace' ) ) );
            return;
        }
        
        // Get and validate post ID
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        if ( $post_id <= 0 ) {
            wp_send_json_error( array( 'message' => __( 'Invalid post ID', 'vortex-ai-marketplace' ) ) );
            return;
        }
        
        // Get and validate post type
        $post_type = get_post_type( $post_id );
        if ( ! $post_type || ! in_array( $post_type, array( 'vortex_artwork', 'vortex_artist' ), true ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid post type', 'vortex-ai-marketplace' ) ) );
            return;
        }
        
        // Check if this is admin or excluded role
        // if ( $this->is_excluded_user() ) {
        //     wp_send_json_success();
        //     return;
        // }
        
        // Get visitor IP if tracking is enabled
        $visitor_ip = '';
        if ( ! get_option( 'vortex_metrics_anonymize_ips', true ) ) {
            $visitor_ip = $this->get_visitor_ip();
        }
        
        // Check for duplicate views using transients to avoid double counting
        $visitor_key = 'vortex_view_' . md5( $post_id . '_' . $visitor_ip . '_' . date( 'Y-m-d' ) );
        if ( get_transient( $visitor_key ) ) {
            wp_send_json_success();
            return;
        }
        
        // Set transient to prevent duplicate counts (expires after 1 day)
        set_transient( $visitor_key, 1, DAY_IN_SECONDS );
        
        // Update the post view count
        $view_count_meta_key = '_vortex_' . $post_type . '_view_count';
        $current_views = get_post_meta( $post_id, $view_count_meta_key, true );
        $new_views = ( is_numeric( $current_views ) ? intval( $current_views ) : 0 ) + 1;
        update_post_meta( $post_id, $view_count_meta_key, $new_views );
        
        // Update daily view count for analytics
        $this->increment_daily_view_count( $post_type );
        
        // Record specific view metrics
        $date = current_time( 'Y-m-d' );
        $this->increment_metric( $post_type . '_' . $post_id . '_views', 1, $date );
        
        if ( 'vortex_artwork' === $post_type ) {
            // Record artist views if this is an artwork
            $artist_id = get_post_field( 'post_author', $post_id );
            if ( $artist_id ) {
                $this->increment_metric( 'artist_' . $artist_id . '_artwork_views', 1, $date );
            }
            
            // Record category views
            $categories = wp_get_post_terms( $post_id, 'vortex_artwork_category', array( 'fields' => 'ids' ) );
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                foreach ( $categories as $category_id ) {
                    $this->increment_metric( 'category_' . $category_id . '_views', 1, $date );
                }
            }
        }
        
        wp_send_json_success( array( 'views' => $new_views ) );
    }

    /**
     * Record user registration metrics.
     *
     * @since    1.0.0
     * @param    int    $user_id    The user ID.
     */
    public function record_user_registration( $user_id ) {
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            return;
        }
        
        $date = current_time( 'Y-m-d' );
        $this->increment_metric( 'daily_new_users', 1, $date );
    }

    /**
     * Record content metrics when posts are updated.
     *
     * @since    1.0.0
     * @param    int       $post_id      The post ID.
     * @param    WP_Post   $post_after   Post object after update.
     * @param    WP_Post   $post_before  Post object before update.
     */
    public function record_content_metrics( $post_id, $post_after, $post_before ) {
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            return;
        }
        
        // Only track vortex post types
        if ( ! in_array( $post_after->post_type, array( 'vortex_artwork', 'vortex_artist' ), true ) ) {
            return;
        }
        
        $date = current_time( 'Y-m-d' );
        
        // New post published
        if ( 'publish' === $post_after->post_status && 'publish' !== $post_before->post_status ) {
            $metric_key = 'daily_new_' . $post_after->post_type . 's';
            $this->increment_metric( $metric_key, 1, $date );
            
            if ( 'vortex_artwork' === $post_after->post_type ) {
                // Check if AI generated
                $is_ai_generated = get_post_meta( $post_id, '_vortex_artwork_ai_generated', true );
                if ( $is_ai_generated ) {
                    $this->increment_metric( 'daily_new_ai_artworks', 1, $date );
                    
                    // Track AI model usage
                    $ai_model = get_post_meta( $post_id, '_vortex_artwork_ai_model', true );
                    if ( $ai_model ) {
                        $this->increment_metric( 'ai_model_' . sanitize_title( $ai_model ) . '_usage', 1, $date );
                    }
                }
            }
        }
    }

    /**
     * Record user login metrics.
     *
     * @since    1.0.0
     * @param    string    $user_login    The user's login name.
     * @param    WP_User   $user          The user object.
     */
    public function record_user_login( $user_login, $user ) {
        // Check if metrics collection is enabled
        if ( ! get_option( 'vortex_metrics_collection_enabled', true ) ) {
            return;
        }
        
        // Check if this is an excluded role
        // if ( $this->is_excluded_user( $user ) ) {
        //     return;
        // }
        
        $date = current_time( 'Y-m-d' );
        $date_key = str_replace( '-', '', $date );
        
        // Update daily active users
        // $active_users = get_option( 'vortex_daily_active_users_' . $date_key, array
    }
} 