<?php
namespace Vortex;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes
 * @author     Marianne Nems
 */
class Vortex_Activator {

    /**
     * Activate the plugin.
     *
     * Initialize database tables, settings, and initial content during plugin activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Check minimum requirements
        self::check_requirements();
        
        // Create database tables
        self::create_tables();
        
        // Create default options
        self::create_options();
        
        // Schedule recurring events
        self::schedule_events();
        
        // Create default content if needed
        self::create_initial_content();
        
        // Create rewrite rules and flush them
        self::create_rewrite_rules();
        
        // Set activation flag
        update_option( 'vortex_activated', 'yes' );
        update_option( 'vortex_activation_time', time() );

        // Trigger vortex_ai_activate action to run database migrations
        do_action('vortex_ai_activate');
        
        // Set admin notice for database update
        $notices = get_transient('vortex_admin_notices');
        if (!$notices) {
            $notices = array();
        }
        
        $notices['db_update_required'] = array(
            'class' => 'notice-warning',
            'message' => sprintf(
                __('VORTEX AI Marketplace: Please run a database update to ensure all tables are created correctly. <a href="%s">Go to Settings</a>', 'vortex-ai-marketplace'),
                admin_url('admin.php?page=vortex-settings&tab=advanced')
            ),
            'dismissible' => true
        );
        
        set_transient('vortex_admin_notices', $notices, 60 * 60 * 24 * 7); // 1 week expiration
    }

    /**
     * Create necessary database tables during plugin activation.
     *
     * @since    1.0.0
     */
    private static function create_tables() {
        self::create_database_tables();
        self::create_required_pages();
        self::set_user_roles();
    }

    /**
     * Create the plugin's database tables.
     *
     * @since    1.0.0
     */
    private static function create_database_tables() {
        global $wpdb;
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Metrics table
        $table_metrics = $wpdb->prefix . 'vortex_metrics';
        $sql_metrics = "CREATE TABLE $table_metrics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            metric_type varchar(50) NOT NULL,
            metric_name varchar(100) NOT NULL,
            metric_value float NOT NULL,
            entity_id bigint(20) NOT NULL,
            entity_type varchar(50) NOT NULL,
            metric_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY metric_type (metric_type),
            KEY entity_id (entity_id),
            KEY entity_type (entity_type)
        ) $charset_collate;";
        dbDelta($sql_metrics);
        
        // Rankings table
        $table_rankings = $wpdb->prefix . 'vortex_rankings';
        $sql_rankings = "CREATE TABLE $table_rankings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ranking_type varchar(50) NOT NULL,
            entity_id bigint(20) NOT NULL,
            entity_type varchar(50) NOT NULL,
            rank int(11) NOT NULL,
            score float NOT NULL,
            timestamp datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY ranking_type (ranking_type),
            KEY entity_id (entity_id),
            KEY entity_type (entity_type)
        ) $charset_collate;";
        dbDelta($sql_rankings);
        
        // TOLA Points table
        $table_tola = $wpdb->prefix . 'vortex_tola_points';
        $sql_tola = "CREATE TABLE $table_tola (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            balance float NOT NULL DEFAULT 0,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_tola);

        $table_vortex_cart_abandonment_feedback = $wpdb->prefix . 'vortex_cart_abandonment_feedback';
        $sql_vortex_cart_abandonment_feedback = "CREATE TABLE IF NOT EXISTS $table_vortex_cart_abandonment_feedback (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            cart_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            session_id varchar(32) DEFAULT NULL,
            abandonment_time datetime DEFAULT CURRENT_TIMESTAMP,
            reason_category varchar(50) DEFAULT NULL,
            abandonment_reason varchar(50) DEFAULT NULL,
            reason_details text DEFAULT NULL,
            feedback_time datetime DEFAULT NULL,
            feedback_provided tinyint(1) DEFAULT 0,
            items_in_cart int(11) DEFAULT 0,
            cart_value decimal(10,2) DEFAULT 0.00,
            resolved tinyint(1) DEFAULT 0,
            resolution_notes text DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY cart_id (cart_id),
            KEY user_id (user_id),
            KEY abandonment_time (abandonment_time),
            KEY reason_category (reason_category),
            KEY abandonment_reason (abandonment_reason),
            KEY feedback_provided (feedback_provided)
        ) $charset_collate;";
        dbDelta($sql_vortex_cart_abandonment_feedback);

        $table_search_transactions = $wpdb->prefix . 'vortex_search_transactions';
        $sql_search_transactions = "CREATE TABLE IF NOT EXISTS $table_search_transactions (
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
        dbDelta($sql_search_transactions);

        $table_search_artwork_clicks = $wpdb->prefix . 'vortex_search_artwork_clicks';
        $sql_search_artwork_clicks = "CREATE TABLE IF NOT EXISTS $table_search_artwork_clicks (
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
        dbDelta($sql_search_artwork_clicks);

        $table_cart_items = $wpdb->prefix . 'vortex_cart_items';
        $sql_cart_items = "CREATE TABLE $table_cart_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            cart_id bigint(20) unsigned NOT NULL,
            artwork_id bigint(20) unsigned NOT NULL,
            quantity int(11) unsigned NOT NULL DEFAULT 1,
            price decimal(20,8) unsigned NOT NULL DEFAULT 0.00000000,
            variation_id bigint(20) unsigned DEFAULT NULL,
            variation_data text DEFAULT NULL,
            added_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            custom_options text DEFAULT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            metadata text DEFAULT NULL,
            PRIMARY KEY (id),
            KEY cart_id (cart_id),
            KEY artwork_id (artwork_id),
            KEY variation_id (variation_id)
        ) $charset_collate;";
        dbDelta($sql_cart_items);

        $table_search_results = $wpdb->prefix . 'vortex_search_results';
        $sql_search_results = "CREATE TABLE IF NOT EXISTS $table_search_results (
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
        dbDelta($sql_search_results);

        $table_social_hashtags = $wpdb->prefix . 'vortex_social_hashtags';
        $sql_social_hashtags = "CREATE TABLE IF NOT EXISTS $table_social_hashtags (
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
        dbDelta($sql_social_hashtags);
        
        // Transactions table
        $table_transactions = $wpdb->prefix . 'vortex_transactions';
        $sql_transactions = "CREATE TABLE IF NOT EXISTS $table_transactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            artwork_id mediumint(9) NOT NULL,
            user_id bigint(20) NOT NULL,
            transaction_id varchar(100) NOT NULL,
            from_address varchar(255) NOT NULL,
            to_address varchar(255) NOT NULL,
            amount float NOT NULL,
            token_type varchar(20) DEFAULT 'TOLA',
            transaction_data text,
            status varchar(50) NOT NULL,
            blockchain_tx_hash varchar(100) DEFAULT '',
            transaction_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY transaction_id (transaction_id),
            KEY from_address (from_address),
            KEY to_address (to_address),
            KEY token_type (token_type)
        ) $charset_collate;";
        dbDelta($sql_transactions);
        
        // Artwork ownership table
        $table_ownership = $wpdb->prefix . 'vortex_artwork_ownership';
        $sql_ownership = "CREATE TABLE IF NOT EXISTS $table_ownership (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            artwork_id bigint(20) NOT NULL,
            owner_id bigint(20) NOT NULL,
            token_id varchar(100),
            purchase_price float,
            purchase_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_current tinyint(1) NOT NULL DEFAULT 1,
            transaction_id varchar(100),
            PRIMARY KEY  (id),
            KEY artwork_id (artwork_id),
            KEY owner_id (owner_id),
            KEY is_current (is_current)
        ) $charset_collate;";
        dbDelta($sql_ownership);

        $table_artworks = $wpdb->prefix . 'vortex_artworks';
        $sql_artworks = "CREATE TABLE IF NOT EXISTS $table_artworks (
            artwork_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id bigint(20) UNSIGNED,
            category_id bigint(20) UNSIGNED,
            artist_id bigint(20) UNSIGNED NOT NULL,
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
            KEY category_id (category_id),
            KEY artist_id (artist_id),
            KEY status (status),
            KEY is_for_sale (is_for_sale),
            KEY is_minted (is_minted),
            KEY date_created (date_created)
        ) $charset_collate;";
        dbDelta($sql_artworks);

        $table_artist = $wpdb->prefix . 'vortex_artists';
        $sql_artist = "CREATE TABLE IF NOT EXISTS $table_artist (
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
        dbDelta($sql_artist);

        $table_categories = $wpdb->prefix . 'vortex_categories';        
        $sql_categories = "CREATE TABLE IF NOT EXISTS $table_categories (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            category_name varchar(191) NOT NULL,
            category_slug varchar(191) NOT NULL,
            category_description text,
            parent_id bigint(20) unsigned DEFAULT NULL,
            popularity_score decimal(10,2) DEFAULT '0.00',
            category_icon varchar(100) DEFAULT NULL,
            category_color varchar(20) DEFAULT NULL,
            creation_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            display_order int(11) DEFAULT '0',
            is_featured tinyint(1) DEFAULT '0',
            is_active tinyint(1) DEFAULT '1',
            item_count int(11) DEFAULT '0',
            PRIMARY KEY  (id),
            UNIQUE KEY category_slug (category_slug),
            KEY parent_id (parent_id),
            KEY popularity_score (popularity_score),
            KEY is_featured (is_featured),
            KEY is_active (is_active),
            KEY display_order (display_order)
        ) $charset_collate;";
        dbDelta($sql_categories);

        $table_art_style = $wpdb->prefix . 'vortex_art_styles';        
        $sql_art_style = "CREATE TABLE IF NOT EXISTS $table_art_style (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            style_name varchar(191) NOT NULL,
            style_slug varchar(191) NOT NULL,
            style_description text,
            parent_style_id bigint(20) unsigned DEFAULT NULL,
            visual_characteristics text,
            historical_period varchar(100) DEFAULT NULL,
            origin_region varchar(100) DEFAULT NULL,
            creation_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            popularity_score decimal(10,2) DEFAULT '0.00',
            trend_score decimal(10,2) DEFAULT '0.00',
            artwork_count int(11) DEFAULT '0',
            is_featured tinyint(1) DEFAULT '0',
            is_ai_generated tinyint(1) DEFAULT '0',
            PRIMARY KEY  (id),
            UNIQUE KEY style_slug (style_slug),
            KEY parent_style_id (parent_style_id),
            KEY popularity_score (popularity_score),
            KEY trend_score (trend_score),
            KEY is_featured (is_featured),
            KEY is_ai_generated (is_ai_generated)
        ) $charset_collate;";
        dbDelta($sql_art_style);

        $table_views = $wpdb->prefix . 'vortex_artwork_views';
        $sql_views = "CREATE TABLE IF NOT EXISTS $table_views (
            view_id bigint(20) NOT NULL AUTO_INCREMENT,
            artwork_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            view_duration int(11) DEFAULT 0,
            view_time datetime NOT NULL,
            source_search_id bigint(20) DEFAULT NULL,
            PRIMARY KEY (view_id),
            KEY artwork_id (artwork_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_views);

        // Artwork likes table
        $table_likes = $wpdb->prefix . 'vortex_artwork_likes';
        $sql_likes = "CREATE TABLE IF NOT EXISTS $table_likes (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,            
            artwork_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            like_time datetime NOT NULL,
            date_created datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY artwork_user (artwork_id, user_id),
            KEY artwork_id (artwork_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_likes);

        $table_vortex_referrers = $wpdb->prefix . 'vortex_referrers';
        $table_created = false;
        
        $sql_vortex_referrers = "CREATE TABLE $table_vortex_referrers (
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
        dbDelta($sql_vortex_referrers);

        $table_user_sessions = $wpdb->prefix . 'vortex_user_sessions';        
        $sql_user_sessions = "CREATE TABLE IF NOT EXISTS $table_user_sessions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            session_key varchar(64) NOT NULL,
            session_start datetime DEFAULT CURRENT_TIMESTAMP,
            session_end datetime DEFAULT NULL,
            session_duration int(11) DEFAULT '0',
            session_data longtext,
            client_ip varchar(40) DEFAULT NULL,
            user_agent text,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY session_key (session_key),
            KEY session_start (session_start)
        ) $charset_collate;";        
        dbDelta($sql_user_sessions);

        $table_user_geo_data = $wpdb->prefix . 'vortex_user_geo_data';
        $sql_user_geo_data = "CREATE TABLE IF NOT EXISTS $table_user_geo_data (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            ip_address varchar(45) DEFAULT NULL,
            country_code varchar(2) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            region varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            postal_code varchar(20) DEFAULT NULL,
            latitude decimal(10,8) DEFAULT NULL,
            longitude decimal(11,8) DEFAULT NULL,
            timezone varchar(50) DEFAULT NULL,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id),
            KEY country_code (country_code),
            KEY region (region(20)),
            KEY city (city(20))
        ) $charset_collate;";        
        dbDelta($sql_user_geo_data);

        $table_user_demographics = $wpdb->prefix . 'vortex_user_demographics';        
        $sql_user_demographics = "CREATE TABLE IF NOT EXISTS $table_user_demographics (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            gender varchar(20) DEFAULT NULL,
            age_group varchar(20) DEFAULT NULL,
            income_range varchar(20) DEFAULT NULL,
            education_level varchar(50) DEFAULT NULL,
            occupation varchar(100) DEFAULT NULL,
            interests text DEFAULT NULL,
            self_disclosed tinyint(1) DEFAULT 0,
            ai_predicted tinyint(1) DEFAULT 0,
            confidence_score decimal(4,3) DEFAULT 0.000,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id),
            KEY gender (gender),
            KEY age_group (age_group),
            KEY income_range (income_range)
        ) $charset_collate;";
        dbDelta($sql_user_demographics);

        $table_user_languages = $wpdb->prefix . 'vortex_user_languages';
        $sql_user_languages = "CREATE TABLE IF NOT EXISTS $table_user_languages (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            language_code varchar(10) NOT NULL,
            language_name varchar(50) NOT NULL,
            proficiency varchar(20) DEFAULT 'native',
            is_primary tinyint(1) DEFAULT 0,
            source varchar(20) DEFAULT 'browser',
            last_updated datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_language (user_id, language_code),
            KEY language_code (language_code),
            KEY is_primary (is_primary)
        ) $charset_collate;";
        dbDelta($sql_user_languages);

        $table_vortex_artwork_views = $wpdb->prefix . 'vortex_artwork_views';
        $sql_vortex_artwork_views = "CREATE TABLE IF NOT EXISTS $table_vortex_artwork_views (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            artwork_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            session_id varchar(32) DEFAULT NULL,
            view_time datetime DEFAULT CURRENT_TIMESTAMP,
            view_duration int(11) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            referrer varchar(255) DEFAULT NULL,
            is_unique tinyint(1) DEFAULT 1,
            platform varchar(20) DEFAULT 'web',
            device_type varchar(20) DEFAULT NULL,
            engagement_score decimal(4,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY artwork_id (artwork_id),
            KEY user_id (user_id),
            KEY view_time (view_time),
            KEY is_unique (is_unique)
        ) $charset_collate;";        
        dbDelta($sql_vortex_artwork_views);

        $table_vortex_art_styles = $wpdb->prefix . 'vortex_art_styles';
        $sql_vortex_art_styles = "CREATE TABLE IF NOT EXISTS $table_vortex_art_styles (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            style_name varchar(100) NOT NULL,
            style_slug varchar(100) NOT NULL,
            style_description text DEFAULT NULL,
            parent_style_id bigint(20) unsigned DEFAULT NULL,
            visual_characteristics text DEFAULT NULL,
            historical_period varchar(100) DEFAULT NULL,
            origin_region varchar(100) DEFAULT NULL,
            creation_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            popularity_score decimal(5,2) DEFAULT 0.00,
            trend_score decimal(5,2) DEFAULT 0.00,
            artwork_count int(11) DEFAULT 0,
            is_featured tinyint(1) DEFAULT 0,
            is_ai_generated tinyint(1) DEFAULT 0,
            thumbnail_url varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY style_slug (style_slug),
            KEY parent_style_id (parent_style_id),
            KEY popularity_score (popularity_score),
            KEY trend_score (trend_score),
            KEY is_featured (is_featured),
            KEY is_ai_generated (is_ai_generated)
        ) $charset_collate;";        
        dbDelta($sql_vortex_art_styles);

        $table_vortex_cart_abandonment_feedback = $wpdb->prefix . 'vortex_cart_abandonment_feedback';
        $sql_vortex_cart_abandonment_feedback = "CREATE TABLE IF NOT EXISTS $table_vortex_cart_abandonment_feedback (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            cart_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            session_id varchar(32) DEFAULT NULL,
            abandonment_time datetime DEFAULT CURRENT_TIMESTAMP,
            reason_category varchar(50) DEFAULT NULL,
            reason_details text DEFAULT NULL,
            feedback_time datetime DEFAULT NULL,
            feedback_provided tinyint(1) DEFAULT 0,
            items_in_cart int(11) DEFAULT 0,
            cart_value decimal(10,2) DEFAULT 0.00,
            resolved tinyint(1) DEFAULT 0,
            resolution_notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY cart_id (cart_id),
            KEY user_id (user_id),
            KEY abandonment_time (abandonment_time),
            KEY reason_category (reason_category),
            KEY feedback_provided (feedback_provided)
        ) $charset_collate;";        
        dbDelta($sql_vortex_cart_abandonment_feedback);
        
        $table_hashtag_share_mapping = $wpdb->prefix . 'vortex_hashtag_share_mapping';
        $sql_hashtag_share_mapping = "CREATE TABLE $table_hashtag_share_mapping (
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
        dbDelta($sql_hashtag_share_mapping);

        $table_vortex_searches = $wpdb->prefix . 'vortex_searches';
        $sql_vortex_searches = "CREATE TABLE IF NOT EXISTS $table_vortex_searches (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            session_id varchar(32) DEFAULT NULL,
            search_term varchar(255) NOT NULL,
            search_time datetime DEFAULT CURRENT_TIMESTAMP,
            result_count int(11) DEFAULT 0,
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
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY search_time (search_time),
            KEY search_term (search_term(191)),
            KEY search_category (search_category),
            KEY result_clicked (result_clicked),
            KEY conversion (conversion)
        ) $charset_collate;";        
        dbDelta($sql_vortex_searches);

        $table_vortex_carts = $wpdb->prefix . 'vortex_carts';
        $sql_vortex_carts = "CREATE TABLE IF NOT EXISTS $table_vortex_carts (
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
            PRIMARY KEY  (id),
            UNIQUE KEY cart_token (cart_token),
            KEY user_id (user_id),
            KEY cart_status (cart_status),
            KEY created (created),
            KEY last_updated (last_updated),
            KEY abandoned (abandoned),
            KEY recovered (recovered)
        ) $charset_collate;";        
        dbDelta($sql_vortex_carts);

        $campaigns_table = $wpdb->prefix . 'vortex_campaigns';
        $sql_campaigns_table = "CREATE TABLE $campaigns_table (
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
        
        dbDelta($sql_campaigns_table);

        $table_tags = $wpdb->prefix . 'vortex_tags';
        $sql_tags = "CREATE TABLE IF NOT EXISTS $table_tags (
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
        dbDelta($sql_tags);

        $table_artwork_tags = $wpdb->prefix . 'vortex_artwork_tags';
        $sql_artwork_tags = "CREATE TABLE IF NOT EXISTS $table_artwork_tags (
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
        dbDelta($sql_artwork_tags);

        $table_artwork_theme_mapping = $wpdb->prefix . 'vortex_artwork_theme_mapping';
        $sql_artwork_theme_mapping = "CREATE TABLE IF NOT EXISTS $table_artwork_theme_mapping (
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
        dbDelta($sql_artwork_theme_mapping);
    }

    /**
     * Set default plugin options.
     *
     * @since    1.0.0
     */
    private static function create_options() {
        // Set blockchain options if they don't exist
        if ( false === get_option( 'vortex_blockchain_network' ) ) {
            add_option( 'vortex_blockchain_network', 'solana' );
        }
        
        if ( false === get_option( 'vortex_solana_rpc_url' ) ) {
            add_option( 'vortex_solana_rpc_url', 'https://api.mainnet-beta.solana.com' );
        }
        
        if ( false === get_option( 'vortex_solana_network' ) ) {
            add_option( 'vortex_solana_network', 'mainnet-beta' );
        }
        
        if ( false === get_option( 'vortex_solana_decimals' ) ) {
            add_option( 'vortex_solana_decimals', 9 );
        }
        
        // Legacy options
        if ( false === get_option( 'vortex_web3_provider_url' ) ) {
            add_option( 'vortex_web3_provider_url', 'https://mainnet.infura.io/v3/your-project-id' );
        }
        
        // Marketplace options
        if ( false === get_option( 'vortex_marketplace_enabled' ) ) {
            add_option( 'vortex_marketplace_enabled', 'yes' );
        }
        
        if ( false === get_option( 'vortex_marketplace_currency' ) ) {
            add_option( 'vortex_marketplace_currency', 'SOL' );
        }
        
        if ( false === get_option( 'vortex_marketplace_commission' ) ) {
            add_option( 'vortex_marketplace_commission', 5 );
        }
    }

    /**
     * Create required pages if they don't exist.
     *
     * @since    1.0.0
     */
    private static function create_required_pages() {
        $pages = array(
            'marketplace' => array(
                'title' => 'AI Art Marketplace',
                'content' => '<!-- wp:shortcode -->[vortex_marketplace]<!-- /wp:shortcode -->',
            ),
            'huraii' => array(
                'title' => 'HURAII AI Creator',
                'content' => '<!-- wp:shortcode -->[vortex_huraii]<!-- /wp:shortcode -->',
            ),
            'artists' => array(
                'title' => 'VORTEX Artists',
                'content' => '<!-- wp:shortcode -->[vortex_artists]<!-- /wp:shortcode -->',
            ),
            'wallet' => array(
                'title' => 'TOLA Wallet',
                'content' => '<!-- wp:shortcode -->[vortex_tola_wallet]<!-- /wp:shortcode -->',
            ),
            'metrics' => array(
                'title' => 'Marketplace Metrics',
                'content' => '<!-- wp:shortcode -->[vortex_metrics]<!-- /wp:shortcode -->',
            ),
        );
        
        foreach ($pages as $slug => $page_data) {
            // Check if page exists
            $page_exists = get_page_by_path($slug);
            
            if (!$page_exists) {
                // Create page
                $page_id = wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug,
                ));
                
                // Save page ID in options
                update_option('vortex_page_' . $slug, $page_id);
            }
        }
    }

    /**
     * Set up user roles and capabilities.
     *
     * @since    1.0.0
     */
    private static function set_user_roles() {
        // Add Artist role
        add_role('vortex_artist', 'VORTEX Artist', array(
            'read' => true,
            'upload_files' => true,
            'publish_posts' => true,
            'edit_posts' => true,
            'delete_posts' => true,
        ));
        
        // Add Collector role
        add_role('vortex_collector', 'VORTEX Collector', array(
            'read' => true,
        ));
    }

    /**
     * Flush rewrite rules to make custom post types work.
     *
     * @since    1.0.0
     */
    private static function create_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Check minimum requirements.
     *
     * @since    1.0.0
     */
    private static function check_requirements() {
        // Implementation of check_requirements method
    }

    /**
     * Schedule recurring events.
     *
     * @since    1.0.0
     */
    private static function schedule_events() {
        // Implementation of schedule_events method
    }

    /**
     * Create initial content if needed.
     *
     * @since    1.0.0
     */
    private static function create_initial_content() {
        // Implementation of create_initial_content method
    }
}