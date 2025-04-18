<?php
/**
 * Core database schema for VORTEX AI Marketplace
 *
 * @link       https://vortexartec.com
 * @since      1.0.0
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes/schema
 */

/**
 * Create core database tables for the VORTEX AI Marketplace.
 *
 * @since    1.0.0
 * @return   array    Array of success/error messages.
 */
function vortex_create_core_schema() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $results = array();

    // Array of SQL statements to create the core tables
    $sql = array();

    // Artists table
    $table_name = $wpdb->prefix . 'vortex_artists';
    $sql[] = "CREATE TABLE $table_name (
        artist_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        display_name varchar(100) NOT NULL,
        bio text,
        profile_image varchar(255),
        wallet_address varchar(255),
        social_links text,
        specialties text,
        website varchar(255),
        verified tinyint(1) DEFAULT 0,
        status varchar(50) DEFAULT 'pending',
        status_updated datetime,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (artist_id),
        KEY user_id (user_id),
        KEY status (status),
        KEY date_created (date_created)
    ) $charset_collate;";

    // Artworks table
    $table_name = $wpdb->prefix . 'vortex_artworks';
    $sql[] = "CREATE TABLE $table_name (
        artwork_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id bigint(20) UNSIGNED,
        artist_id bigint(20) UNSIGNED NOT NULL,
        category_id bigint(20) UNSIGNED NOT NULL,        
        style_id bigint(20) UNSIGNED NOT NULL,
        title varchar(255) NOT NULL,
        description text,
        short_description text,
        thumbnail varchar(255),
        full_image varchar(255),
        price decimal(18,8) DEFAULT 0,
        currency varchar(10) DEFAULT 'TOLA',
        is_for_sale tinyint(1) DEFAULT 0,
        is_minted tinyint(1) DEFAULT 0,
        status varchar(50) DEFAULT 'draft',
        artist_name varchar(100),
        ai_generated tinyint(1) DEFAULT 0,
        ai_model varchar(100),
        ai_prompt text,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (artwork_id),
        KEY post_id (post_id),
        KEY artist_id (artist_id),
        KEY category_id (category_id),
        KEY style_id (style_id),
        KEY status (status),
        KEY is_for_sale (is_for_sale),
        KEY is_minted (is_minted),
        KEY date_created (date_created)
    ) $charset_collate;";

    // Sales/Orders table
    $table_name = $wpdb->prefix . 'vortex_sales';
    $sql[] = "CREATE TABLE $table_name (
        sale_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        artwork_id bigint(20) UNSIGNED NOT NULL,
        artist_id bigint(20) UNSIGNED NOT NULL,
        buyer_id bigint(20) UNSIGNED,
        transaction_id varchar(255),
        payment_method varchar(100),
        price decimal(18,8) NOT NULL,
        currency varchar(10) DEFAULT 'TOLA',
        commission decimal(18,8) DEFAULT 0,
        artist_payout decimal(18,8) DEFAULT 0,
        status varchar(50) DEFAULT 'pending',
        blockchain_tx_hash varchar(255),
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (sale_id),
        KEY artwork_id (artwork_id),
        KEY artist_id (artist_id),
        KEY buyer_id (buyer_id),
        KEY status (status),
        KEY date_created (date_created)
    ) $charset_collate;";

    // Artist followers table
    $table_name = $wpdb->prefix . 'vortex_artist_followers';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        artist_id bigint(20) UNSIGNED NOT NULL,
        user_id bigint(20) UNSIGNED NOT NULL,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY artist_user (artist_id, user_id),
        KEY artist_id (artist_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    // Artwork favorites/likes table
    $table_name = $wpdb->prefix . 'vortex_artwork_likes';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        artwork_id bigint(20) UNSIGNED NOT NULL,
        user_id bigint(20) UNSIGNED NOT NULL,
        like_time datetime NOT NULL,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY artwork_user (artwork_id, user_id),
        KEY artwork_id (artwork_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    // AI Generation logs
    $table_name = $wpdb->prefix . 'vortex_ai_generation_logs';
    $sql[] = "CREATE TABLE $table_name (
        log_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        prompt text NOT NULL,
        ai_model varchar(100) NOT NULL,
        parameters text,
        result_image varchar(255),
        status varchar(50) DEFAULT 'completed',
        execution_time float DEFAULT 0,
        credits_used int DEFAULT 0,
        artwork_id bigint(20) UNSIGNED,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (log_id),
        KEY user_id (user_id),
        KEY ai_model (ai_model),
        KEY status (status),
        KEY date_created (date_created)
    ) $charset_collate;";

    // User credits table
    $table_name = $wpdb->prefix . 'vortex_user_credits';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(20) UNSIGNED NOT NULL,
        credits_balance int DEFAULT 0,
        last_purchase_date datetime,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY user_id (user_id)
    ) $charset_collate;";

    // Settings table
    $table_name = $wpdb->prefix . 'vortex_settings';
    $sql[] = "CREATE TABLE $table_name (
        setting_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        setting_name varchar(100) NOT NULL,
        setting_value text,
        autoload tinyint(1) DEFAULT 1,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        date_modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (setting_id),
        UNIQUE KEY setting_name (setting_name),
        KEY autoload (autoload)
    ) $charset_collate;";

    // Cart Item Table
    $table_name = $wpdb->prefix . 'vortex_cart_items';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        cart_id bigint(20) unsigned NOT NULL,
        artwork_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) UNSIGNED NOT NULL,
        quantity int(11) unsigned NOT NULL DEFAULT 1,
        price decimal(20,8) unsigned NOT NULL DEFAULT 0.00000000,
        variation_id bigint(20) unsigned DEFAULT NULL,
        variation_data text DEFAULT NULL,
        added_date datetime DEFAULT CURRENT_TIMESTAMP,
        last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        custom_options text DEFAULT NULL,
        metadata text DEFAULT NULL,
        PRIMARY KEY (id),
        KEY cart_id (cart_id),
        KEY artwork_id (artwork_id),
        KEY user_id (user_id),
        KEY variation_id (variation_id)
    ) $charset_collate;";

    // Cart Table
    $table_name = $wpdb->prefix . 'vortex_carts';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned DEFAULT NULL,
        session_id varchar(32) DEFAULT NULL,
        cart_token varchar(64) NOT NULL,
        created datetime DEFAULT CURRENT_TIMESTAMP,
        last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        cart_status varchar(20) DEFAULT 'active',
        cart_total decimal(10,2) DEFAULT 0.00,
        items_count int(11) DEFAULT 0,
        currency varchar(3) DEFAULT 'USD',
        converted_to_order tinyint(1) DEFAULT 0,
        order_id bigint(20) unsigned DEFAULT NULL,
        abandoned tinyint(1) DEFAULT 0,
        abandoned_time datetime DEFAULT NULL,
        recovery_email_sent tinyint(1) DEFAULT 0,
        recovery_email_time datetime DEFAULT NULL,
        recovered tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY cart_token (cart_token),
        KEY user_id (user_id),
        KEY cart_status (cart_status),
        KEY created (created),
        KEY last_updated (last_updated),
        KEY abandoned (abandoned),
        KEY recovered (recovered)
    ) $charset_collate;";

    // Search Table
    $table_name = $wpdb->prefix . 'vortex_searches';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned DEFAULT NULL,
        session_id varchar(32) DEFAULT NULL,
        search_query varchar(255) NOT NULL,
        search_term varchar(255) NOT NULL,
        search_time datetime DEFAULT CURRENT_TIMESTAMP,
        results_count int(11) DEFAULT 0,
        result_clicked tinyint(1) DEFAULT 0,
        clicked_position int(11) DEFAULT NULL,
        clicked_result_id bigint(20) unsigned DEFAULT NULL,
        search_filters text DEFAULT NULL,
        search_category varchar(50) DEFAULT NULL,
        search_location varchar(100) DEFAULT NULL,
        ip_address varchar(45) DEFAULT NULL,
        user_agent text DEFAULT NULL,
        search_page varchar(100) DEFAULT 'main',
        conversion tinyint(1) DEFAULT 0,
        converted tinyint(1) DEFAULT 0,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY search_time (search_time),
        KEY search_query (search_query(191)),
        KEY search_category (search_category),
        KEY result_clicked (result_clicked),
        KEY conversion (conversion)
    ) $charset_collate;";

    // Tags Table
    $table_name = $wpdb->prefix . 'vortex_tags';
    $sql[] = "CREATE TABLE $table_name (
        tag_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tag_name varchar(100) NOT NULL,
        tag_slug varchar(100) NOT NULL,
        tag_description text DEFAULT NULL,
        parent_tag_id bigint(20) unsigned DEFAULT NULL,
        tag_type varchar(50) DEFAULT 'general',
        count int(11) DEFAULT 0,
        popularity_score decimal(10,2) DEFAULT 0.00,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (tag_id),
        UNIQUE KEY tag_slug (tag_slug),
        KEY parent_tag_id (parent_tag_id),
        KEY tag_type (tag_type),
        KEY popularity_score (popularity_score),
        KEY count (count)
    ) $charset_collate;";

    // Artwork Theme Mapping Table
    $table_name = $wpdb->prefix . 'vortex_artwork_theme_mapping';
    $sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        artwork_id bigint(20) unsigned NOT NULL,
        theme_id bigint(20) unsigned NOT NULL,
        relevance decimal(5,2) DEFAULT 1.00,
        added_by bigint(20) unsigned DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY artwork_theme (artwork_id, theme_id),
        KEY artwork_id (artwork_id),
        KEY theme_id (theme_id),
        KEY relevance (relevance)
    ) $charset_collate;";

    // Search RESULTS Table
    $table_name = $wpdb->prefix . 'vortex_search_results';
    $sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        search_id bigint(20) unsigned NOT NULL,
        result_type varchar(50) NOT NULL DEFAULT 'artwork',
        result_id bigint(20) unsigned NOT NULL,
        relevance_score decimal(5,2) DEFAULT 1.00,
        display_position int(11) DEFAULT NULL,
        style_id bigint(20) unsigned DEFAULT NULL,
        theme_id bigint(20) unsigned DEFAULT NULL,
        was_clicked tinyint(1) DEFAULT 0,
        click_position int(11) DEFAULT NULL,
        click_time datetime DEFAULT NULL,
        impression_time datetime DEFAULT CURRENT_TIMESTAMP,
        metadata text DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY search_id (search_id),
        KEY result_type (result_type),
        KEY result_id (result_id),
        KEY style_id (style_id),
        KEY theme_id (theme_id),
        KEY was_clicked (was_clicked),
        KEY impression_time (impression_time)
    ) $charset_collate;";

    // Referrers Table
    $table_name = $wpdb->prefix . 'vortex_referrers';
    $sql[] = "CREATE TABLE $table_name (
        visit_id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) DEFAULT NULL,
        session_id varchar(255) DEFAULT NULL,
        referrer_url text,
        referrer_domain varchar(255) DEFAULT NULL,
        campaign_id bigint(20) DEFAULT NULL,
        visit_time datetime DEFAULT CURRENT_TIMESTAMP,
        page_url text,
        converted tinyint(1) DEFAULT 0,
        conversion_time datetime DEFAULT NULL,
        device_type varchar(50) DEFAULT NULL,
        browser varchar(50) DEFAULT NULL,
        ip_address varchar(50) DEFAULT NULL,
        metadata text,
        PRIMARY KEY (visit_id),
        KEY user_id (user_id),
        KEY campaign_id (campaign_id),
        KEY referrer_domain (referrer_domain),
        KEY visit_time (visit_time)
    ) $charset_collate;";

    // Campaigns Table
    $campaigns_table = $wpdb->prefix . 'vortex_campaigns';
    $sql[] = "CREATE TABLE $campaigns_table (
        campaign_id bigint(20) NOT NULL AUTO_INCREMENT,
        campaign_name varchar(255) NOT NULL,
        campaign_type varchar(50) DEFAULT NULL,
        start_date datetime DEFAULT CURRENT_TIMESTAMP,
        end_date datetime DEFAULT NULL,
        campaign_cost decimal(10,2) DEFAULT 0.00,
        campaign_budget decimal(10,2) DEFAULT 0.00,
        campaign_status varchar(20) DEFAULT 'active',
        target_audience text,
        utm_parameters text,
        created_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (campaign_id),
        KEY campaign_status (campaign_status),
        KEY created_by (created_by)
    ) $charset_collate;";

    $table_name = $wpdb->prefix . 'vortex_social_hashtags';
    $sql[] = "CREATE TABLE $table_name (
        hashtag_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        hashtag varchar(255) NOT NULL,
        category varchar(50) DEFAULT 'general',
        description text DEFAULT NULL,
        usage_count int(11) DEFAULT 0,
        engagement_score decimal(5,2) DEFAULT 0.00,
        first_used datetime DEFAULT CURRENT_TIMESTAMP,
        last_used datetime DEFAULT CURRENT_TIMESTAMP,
        is_trending tinyint(1) DEFAULT 0,
        is_featured tinyint(1) DEFAULT 0,
        is_blocked tinyint(1) DEFAULT 0,
        relevance_score decimal(5,2) DEFAULT 0.00,
        created_by bigint(20) unsigned DEFAULT NULL,
        PRIMARY KEY  (hashtag_id),
        UNIQUE KEY hashtag (hashtag(191)),
        KEY category (category),
        KEY usage_count (usage_count),
        KEY engagement_score (engagement_score),
        KEY is_trending (is_trending),
        KEY is_featured (is_featured),
        KEY is_blocked (is_blocked)
    ) $charset_collate;";

    $table_name = $wpdb->prefix . 'vortex_search_transactions';
    $sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        search_id bigint(20) unsigned NOT NULL,
        transaction_id bigint(20) unsigned NOT NULL,
        relation_type varchar(50) DEFAULT 'direct',
        time_between_search_transaction int(11) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        metadata text DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY search_transaction (search_id, transaction_id),
        KEY search_id (search_id),
        KEY transaction_id (transaction_id),
        KEY relation_type (relation_type)
    ) $charset_collate;";

    $table_name = $wpdb->prefix . 'vortex_search_artwork_clicks';
    $sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        search_id bigint(20) unsigned NOT NULL,
        artwork_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        session_id varchar(32) DEFAULT NULL,
        click_time datetime DEFAULT CURRENT_TIMESTAMP,
        click_position int(11) DEFAULT NULL,
        search_page varchar(100) DEFAULT 'main',
        result_type varchar(50) DEFAULT 'search',
        time_spent_viewing int(11) DEFAULT NULL,
        converted tinyint(1) DEFAULT 0,
        conversion_type varchar(50) DEFAULT NULL,
        conversion_value decimal(10,2) DEFAULT 0.00,
        conversion_time datetime DEFAULT NULL,
        ip_address varchar(45) DEFAULT NULL,
        user_agent text DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY search_id (search_id),
        KEY artwork_id (artwork_id),
        KEY user_id (user_id),
        KEY click_time (click_time),
        KEY converted (converted),
        KEY click_position (click_position)
    ) $charset_collate;";

    $table_name = $wpdb->prefix . 'vortex_artwork_tags';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        artwork_id bigint(20) unsigned NOT NULL,
        tag_id bigint(20) unsigned NOT NULL,
        confidence decimal(5,2) DEFAULT 1.00,
        added_by bigint(20) unsigned DEFAULT NULL,
        is_auto_generated tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY artwork_tag (artwork_id, tag_id),
        KEY artwork_id (artwork_id),
        KEY tag_id (tag_id),
        KEY confidence (confidence),
        KEY is_auto_generated (is_auto_generated)
    ) $charset_collate;";

    $table_name = $wpdb->prefix . 'vortex_hashtag_share_mapping';
    $sql[] = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        hashtag_id bigint(20) unsigned NOT NULL,
        share_id bigint(20) unsigned NOT NULL,
        artwork_id bigint(20) unsigned DEFAULT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY hashtag_share (hashtag_id, share_id),
        KEY hashtag_id (hashtag_id),
        KEY share_id (share_id),
        KEY artwork_id (artwork_id),
        KEY user_id (user_id)
    ) $charset_collate;";


    // Execute the SQL statements
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    foreach ($sql as $query) {
        dbDelta($query);
        
        // Check for errors
        if ($wpdb->last_error) {
            $results[] = array(
                'status' => 'error',
                'message' => $wpdb->last_error,
                'query' => $query
            );
        } else {
            $table = preg_match('/CREATE TABLE ([^\s(]+)/', $query, $matches) ? $matches[1] : 'Unknown table';
            $results[] = array(
                'status' => 'success',
                'message' => "Table $table created or updated successfully",
                'query' => $query
            );
        }
    }

    return $results;
}

/**
 * Setup initial data for core tables
 *
 * @since    1.0.0
 */
function vortex_setup_core_initial_data() {
    global $wpdb;
    
    // Insert default settings
    $settings_table = $wpdb->prefix . 'vortex_settings';
    
    $default_settings = array(
        array(
            'setting_name' => 'commission_rate',
            'setting_value' => '10',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'default_currency',
            'setting_value' => 'TOLA',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'marketplace_status',
            'setting_value' => 'active',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'free_credits_new_user',
            'setting_value' => '10',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'credits_per_generation',
            'setting_value' => '1',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'featured_artworks_count',
            'setting_value' => '8',
            'autoload' => 1
        ),
        array(
            'setting_name' => 'artist_verification_required',
            'setting_value' => '1',
            'autoload' => 1
        )
    );
    
    foreach ($default_settings as $setting) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $settings_table WHERE setting_name = %s",
            $setting['setting_name']
        ));
        
        if (!$exists) {
            $wpdb->insert(
                $settings_table,
                $setting
            );
        }
    }
} 