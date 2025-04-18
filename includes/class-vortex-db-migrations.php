<?php
/**
 * Database Migrations for Vortex AI Marketplace
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/includes
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Database Migrations Class
 *
 * Handles database table creation and updates for the plugin
 *
 * @since      1.0.0
 */
class Vortex_DB_Migrations {
    /**
     * The version of the database schema.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $db_version    The current version of the database schema.
     */
    private $db_version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->db_version = get_option('vortex_ai_db_version', '0.0.0');
        
        // Register activation hook for database setup
        add_action('vortex_ai_activate', array($this, 'setup_database'));
        
        // Check if we need to update database
        add_action('plugins_loaded', array($this, 'check_update_database'));
    }

    /**
     * Check if we need to update the database schema
     *
     * @since    1.0.0
     */
    public function check_update_database() {
        $current_version = VORTEX_VERSION;
        
        // if (version_compare($this->db_version, $current_version, '<')) {
            $this->setup_database();
            update_option('vortex_ai_db_version', $current_version);
        // }
    }

    /**
     * Set up all required database tables
     *
     * @since    1.0.0
     */
    public function setup_database() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create artwork themes table
        $this->create_artwork_themes_table($charset_collate);
        
        // Create user sessions table
        $this->create_user_sessions_table($charset_collate);
        
        // Create user activity table
        $this->create_user_activity_table($charset_collate);
        
        // Create CLOE analytics tables
        $this->create_cloe_analytics_tables($charset_collate);
        
        // Create artwork statistics table
        $this->create_artwork_statistics_table($charset_collate);
        
        // Create Thorius tables
        $this->create_thorius_tables($charset_collate);

        // Create art styles table
        $this->create_art_styles_table($charset_collate);

        // Create categories table
        $this->create_categories_table($charset_collate);

        // Create users table
        $this->create_users_table($charset_collate);

        // Create social shares table
        $this->create_social_shares_table($charset_collate);
        
        // Seed default data
        $this->seed_art_styles();
        $this->seed_categories();
        $this->import_wordpress_users();
    }

    /**
     * Seed the art_styles table with initial data
     *
     * @since    1.0.0
     */
    private function seed_art_styles() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_art_styles';
        
        // Check if the table is empty
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($count > 0) {
            return; // Table already has data, no need to seed
        }
        
        // List of common art styles to seed
        $art_styles = array(
            array(
                'style_name' => 'Impressionism',
                'style_slug' => 'impressionism',
                'style_description' => 'A 19th-century art movement characterized by small, thin, yet visible brush strokes, open composition, emphasis on accurate depiction of light, ordinary subject matter, and unusual visual angles.',
                'visual_characteristics' => 'Visible brush strokes, vibrant colors, open composition, emphasis on light and its changing qualities.',
                'historical_period' => '19th Century',
                'origin_region' => 'France',
                'popularity_score' => 9.5,
                'is_featured' => 1
            ),
            array(
                'style_name' => 'Cubism',
                'style_slug' => 'cubism',
                'style_description' => 'Early-20th-century avant-garde art movement that revolutionized European painting and sculpture by depicting subjects from multiple viewpoints simultaneously.',
                'visual_characteristics' => 'Geometric shapes, multiple viewpoints, fragmented forms, abstract representation.',
                'historical_period' => 'Early 20th Century',
                'origin_region' => 'France',
                'popularity_score' => 8.7,
                'is_featured' => 1
            ),
            array(
                'style_name' => 'Surrealism',
                'style_slug' => 'surrealism',
                'style_description' => 'A 20th-century avant-garde movement that sought to release the creative potential of the unconscious mind by juxtaposing irrational, bizarre imagery.',
                'visual_characteristics' => 'Dream-like scenes, unexpected juxtapositions, irrational elements, symbolic imagery.',
                'historical_period' => '20th Century',
                'origin_region' => 'Europe',
                'popularity_score' => 9.0,
                'is_featured' => 1
            ),
            array(
                'style_name' => 'Abstract Expressionism',
                'style_slug' => 'abstract-expressionism',
                'style_description' => 'Post-World War II art movement characterized by spontaneous creation, emotional intensity, and non-representational forms.',
                'visual_characteristics' => 'Gestural brush strokes, spontaneous mark-making, non-representational forms, large canvases.',
                'historical_period' => 'Mid-20th Century',
                'origin_region' => 'United States',
                'popularity_score' => 8.2,
                'is_featured' => 1
            ),
            array(
                'style_name' => 'Digital Surrealism',
                'style_slug' => 'digital-surrealism',
                'style_description' => 'A contemporary digital art style that combines surrealist concepts with digital techniques and tools.',
                'visual_characteristics' => 'Dream-like digital scenes, impossible physics, digital manipulation, futuristic elements.',
                'historical_period' => '21st Century',
                'origin_region' => 'Global',
                'popularity_score' => 9.3,
                'is_featured' => 1,
                'is_ai_generated' => 1
            ),
            array(
                'style_name' => 'Fractal Art',
                'style_slug' => 'fractal-art',
                'style_description' => 'An algorithmic art form created by calculating fractal objects and representing the calculation results as still images, animations, or media.',
                'visual_characteristics' => 'Self-similar patterns, infinite complexity, mathematical precision, vibrant colors.',
                'historical_period' => 'Contemporary',
                'origin_region' => 'Global',
                'popularity_score' => 7.8,
                'is_featured' => 0,
                'is_ai_generated' => 1
            ),
            array(
                'style_name' => 'AI Generated Art',
                'style_slug' => 'ai-generated-art',
                'style_description' => 'Art created with the assistance of artificial intelligence algorithms such as GANs, diffusion models, and neural networks.',
                'visual_characteristics' => 'Algorithm-influenced aesthetics, unpredictable combinations, unique textures, dreamlike qualities.',
                'historical_period' => '21st Century',
                'origin_region' => 'Global',
                'popularity_score' => 9.7,
                'is_featured' => 1,
                'is_ai_generated' => 1
            ),
            array(
                'style_name' => 'Neo-Renaissance',
                'style_slug' => 'neo-renaissance',
                'style_description' => 'A contemporary revival of Renaissance artistic techniques and themes, often with modern subjects or contexts.',
                'visual_characteristics' => 'Classical techniques, rich colors, detailed figures, balanced composition.',
                'historical_period' => 'Contemporary',
                'origin_region' => 'Global',
                'popularity_score' => 7.5,
                'is_featured' => 0
            ),
            array(
                'style_name' => 'Minimalism',
                'style_slug' => 'minimalism',
                'style_description' => 'A style characterized by extreme simplicity of form and a deliberate lack of expressive content.',
                'visual_characteristics' => 'Geometric forms, minimal color palette, clean lines, simplicity, negative space.',
                'historical_period' => 'Mid-20th Century to Present',
                'origin_region' => 'United States',
                'popularity_score' => 8.0,
                'is_featured' => 0
            ),
            array(
                'style_name' => 'Pop Art',
                'style_slug' => 'pop-art',
                'style_description' => 'Art movement that emerged in the 1950s that challenges traditions by including imagery from popular culture such as advertising, news, etc.',
                'visual_characteristics' => 'Bold colors, recognizable imagery, commercial techniques, irony, wit.',
                'historical_period' => 'Mid-20th Century',
                'origin_region' => 'United Kingdom and United States',
                'popularity_score' => 8.8,
                'is_featured' => 1
            )
        );
        
        // Insert styles into the database
        foreach ($art_styles as $style) {
            $wpdb->insert(
                $table_name,
                array(
                    'style_name' => $style['style_name'],
                    'style_slug' => $style['style_slug'],
                    'style_description' => $style['style_description'],
                    'visual_characteristics' => $style['visual_characteristics'],
                    'historical_period' => $style['historical_period'],
                    'origin_region' => $style['origin_region'],
                    'creation_date' => current_time('mysql'),
                    'last_updated' => current_time('mysql'),
                    'popularity_score' => $style['popularity_score'],
                    'trend_score' => rand(5, 10) / 10 * $style['popularity_score'],
                    'artwork_count' => rand(10, 100),
                    'is_featured' => $style['is_featured'],
                    'is_ai_generated' => isset($style['is_ai_generated']) ? $style['is_ai_generated'] : 0
                )
            );
        }
    }

    /**
     * Create art styles table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_art_styles_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_art_styles';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create categories table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_categories_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_categories';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create users table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_users_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_users';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            display_name varchar(191) NOT NULL,
            avatar_url varchar(255) DEFAULT NULL,
            user_type enum('artist','collector','gallery','admin') DEFAULT 'collector',
            artist_verified tinyint(1) DEFAULT '0',
            bio text,
            social_links text,
            preferred_styles text,
            preferred_categories text,
            price_range varchar(50) DEFAULT NULL,
            activity_score decimal(10,2) DEFAULT '0.00',
            last_login datetime DEFAULT NULL,
            login_count int(11) DEFAULT '0',
            registration_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            onboarding_completed tinyint(1) DEFAULT '0',
            preferences longtext,
            behavior_data longtext,
            ranking_score decimal(10,2) DEFAULT '0.00',
            is_featured tinyint(1) DEFAULT '0',
            subscription_id bigint(20) unsigned DEFAULT NULL,
            subscription_status varchar(50) DEFAULT NULL,
            subscription_expiry datetime DEFAULT NULL,
            tola_balance decimal(20,8) DEFAULT '0.00000000',
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id),
            KEY user_type (user_type),
            KEY artist_verified (artist_verified),
            KEY activity_score (activity_score),
            KEY ranking_score (ranking_score),
            KEY is_featured (is_featured),
            KEY subscription_status (subscription_status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create an index for improved search performance on display_name
        $wpdb->query("CREATE FULLTEXT INDEX IF NOT EXISTS display_name_fulltext ON $table_name (display_name)");
    }

    /**
     * Import existing WordPress users into the Vortex users table
     *
     * @since    1.0.0
     */
    private function import_wordpress_users() {
        global $wpdb;
        
        $vortex_users_table = $wpdb->prefix . 'vortex_users';
        
        // Check if there are any users in the vortex_users table
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $vortex_users_table");
        
        // If there are already users in the table, do not proceed with import
        if ($count > 0) {
            return;
        }
        
        // Get all WordPress users
        $users = get_users(array(
            'fields' => array('ID', 'display_name', 'user_registered')
        ));
        
        foreach ($users as $user) {
            // Check if user already exists in vortex_users
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $vortex_users_table WHERE user_id = %d",
                $user->ID
            ));
            
            if ($exists) {
                continue; // Skip if user already exists
            }
            
            // Determine user type based on WordPress role
            $user_data = get_userdata($user->ID);
            $user_type = 'collector'; // Default
            
            if (in_array('administrator', $user_data->roles)) {
                $user_type = 'admin';
            } elseif (in_array('vortex_artist', $user_data->roles) || in_array('author', $user_data->roles)) {
                $user_type = 'artist';
            }
            
            // Get avatar URL
            $avatar_url = get_avatar_url($user->ID, array('size' => 200));
            
            // Insert user into vortex_users table
            $wpdb->insert(
                $vortex_users_table,
                array(
                    'user_id' => $user->ID,
                    'display_name' => $user->display_name,
                    'avatar_url' => $avatar_url,
                    'user_type' => $user_type,
                    'artist_verified' => ($user_type === 'artist' && $user_type !== 'admin') ? 0 : 1,
                    'registration_date' => $user->user_registered,
                    'last_updated' => current_time('mysql'),
                    'onboarding_completed' => 0,
                    'activity_score' => 0.00,
                    'ranking_score' => 0.00,
                    'is_featured' => 0,
                    'tola_balance' => 50.00000000 // Give new users some initial TOLA tokens
                )
            );
        }
        
        // Log the import
        error_log('Vortex: WordPress users imported to vortex_users table.');
    }

    /**
     * Create social shares table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_social_shares_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_social_shares';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            artwork_id bigint(20) unsigned NOT NULL,
            platform varchar(50) NOT NULL,
            share_url varchar(255) DEFAULT NULL,
            share_message text,
            share_time datetime DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(100) DEFAULT NULL,
            user_agent text,
            share_status varchar(20) DEFAULT 'completed',
            engagement_count int(11) DEFAULT '0',
            conversion_count int(11) DEFAULT '0',
            click_count int(11) DEFAULT '0',
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            metadata longtext,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY artwork_id (artwork_id),
            KEY platform (platform),
            KEY share_time (share_time),
            KEY share_status (share_status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create index for platform+artwork_id combination
        $wpdb->query("CREATE INDEX IF NOT EXISTS platform_artwork ON $table_name (platform, artwork_id)");
    }

    /**
     * Seed the categories table with initial data
     *
     * @since    1.0.0
     */
    private function seed_categories() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_categories';
        
        // Check if the table is empty
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($count > 0) {
            return; // Table already has data, no need to seed
        }
        
        // List of main marketplace categories to seed
        $categories = array(
            array(
                'category_name' => 'Digital Art',
                'category_slug' => 'digital-art',
                'category_description' => 'Digital artwork created using digital tools and software, including AI-generated art, digital paintings, and illustrations.',
                'category_icon' => 'monitor',
                'category_color' => '#3498db',
                'popularity_score' => 9.8,
                'display_order' => 1,
                'is_featured' => 1,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Photography',
                'category_slug' => 'photography',
                'category_description' => 'Artistic photographs and photo manipulations, including landscape, portrait, abstract, and documentary photography.',
                'category_icon' => 'camera',
                'category_color' => '#2ecc71',
                'popularity_score' => 8.9,
                'display_order' => 2,
                'is_featured' => 1,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Traditional Art',
                'category_slug' => 'traditional-art',
                'category_description' => 'Artwork created using traditional mediums such as oil, acrylic, watercolor, charcoal, and other physical mediums.',
                'category_icon' => 'palette',
                'category_color' => '#e74c3c',
                'popularity_score' => 8.5,
                'display_order' => 3,
                'is_featured' => 1,
                'is_active' => 1
            ),
            array(
                'category_name' => 'AI-Generated Art',
                'category_slug' => 'ai-generated-art',
                'category_description' => 'Artwork created with the assistance of artificial intelligence algorithms, including generative art, style transfers, and collaborative human-AI creations.',
                'category_icon' => 'chip',
                'category_color' => '#9b59b6',
                'popularity_score' => 9.6,
                'display_order' => 4,
                'is_featured' => 1,
                'is_active' => 1
            ),
            array(
                'category_name' => '3D Art',
                'category_slug' => '3d-art',
                'category_description' => 'Three-dimensional digital creations, including 3D models, sculpts, renderings, and virtual environments.',
                'category_icon' => 'cube',
                'category_color' => '#f39c12',
                'popularity_score' => 8.7,
                'display_order' => 5,
                'is_featured' => 1,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Animation',
                'category_slug' => 'animation',
                'category_description' => 'Animated artwork, including GIFs, short animations, motion graphics, and animated sequences.',
                'category_icon' => 'film',
                'category_color' => '#1abc9c',
                'popularity_score' => 8.3,
                'display_order' => 6,
                'is_featured' => 0,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Mixed Media',
                'category_slug' => 'mixed-media',
                'category_description' => 'Artwork that combines multiple mediums or techniques, including digital and traditional combinations.',
                'category_icon' => 'layers',
                'category_color' => '#34495e',
                'popularity_score' => 7.9,
                'display_order' => 7,
                'is_featured' => 0,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Concept Art',
                'category_slug' => 'concept-art',
                'category_description' => 'Illustrative designs created to convey ideas for films, games, animation, or other media before final production.',
                'category_icon' => 'bulb',
                'category_color' => '#e67e22',
                'popularity_score' => 8.6,
                'display_order' => 8,
                'is_featured' => 0,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Illustration',
                'category_slug' => 'illustration',
                'category_description' => 'Artistic visualizations created to represent a story, idea, or concept, often used in books, magazines, and other media.',
                'category_icon' => 'pencil',
                'category_color' => '#8e44ad',
                'popularity_score' => 8.8,
                'display_order' => 9,
                'is_featured' => 0,
                'is_active' => 1
            ),
            array(
                'category_name' => 'Abstract',
                'category_slug' => 'abstract',
                'category_description' => 'Non-representational artwork that uses shapes, colors, forms and gestural marks to achieve its effect without attempting to represent external reality.',
                'category_icon' => 'shapes',
                'category_color' => '#d35400',
                'popularity_score' => 8.2,
                'display_order' => 10,
                'is_featured' => 0,
                'is_active' => 1
            )
        );
        
        // Insert categories into the database
        foreach ($categories as $category) {
            $wpdb->insert(
                $table_name,
                array(
                    'category_name' => $category['category_name'],
                    'category_slug' => $category['category_slug'],
                    'category_description' => $category['category_description'],
                    'category_icon' => $category['category_icon'],
                    'category_color' => $category['category_color'],
                    'creation_date' => current_time('mysql'),
                    'last_updated' => current_time('mysql'),
                    'popularity_score' => $category['popularity_score'],
                    'display_order' => $category['display_order'],
                    'is_featured' => $category['is_featured'],
                    'is_active' => $category['is_active'],
                    'item_count' => rand(15, 150)
                )
            );
            
            // Get the ID of the just inserted category
            $parent_id = $wpdb->insert_id;
            
            // If this is Digital Art, add subcategories
            if ($category['category_slug'] === 'digital-art') {
                $digital_subcategories = array(
                    array(
                        'category_name' => 'Digital Painting',
                        'category_slug' => 'digital-painting',
                        'category_description' => 'Digitally created artwork that simulates traditional painting techniques.',
                        'category_icon' => 'brush',
                        'category_color' => '#3498db',
                        'popularity_score' => 8.7
                    ),
                    array(
                        'category_name' => 'Pixel Art',
                        'category_slug' => 'pixel-art',
                        'category_description' => 'Digital art created using pixel-by-pixel editing with limited color palettes.',
                        'category_icon' => 'grid',
                        'category_color' => '#3498db',
                        'popularity_score' => 7.8
                    ),
                    array(
                        'category_name' => 'Vector Art',
                        'category_slug' => 'vector-art',
                        'category_description' => 'Artwork created using vector-based tools and techniques, resulting in scalable graphics.',
                        'category_icon' => 'bezier',
                        'category_color' => '#3498db',
                        'popularity_score' => 8.3
                    )
                );
                
                foreach ($digital_subcategories as $subcategory) {
                    $wpdb->insert(
                        $table_name,
                        array(
                            'category_name' => $subcategory['category_name'],
                            'category_slug' => $subcategory['category_slug'],
                            'category_description' => $subcategory['category_description'],
                            'parent_id' => $parent_id,
                            'category_icon' => $subcategory['category_icon'],
                            'category_color' => $subcategory['category_color'],
                            'creation_date' => current_time('mysql'),
                            'last_updated' => current_time('mysql'),
                            'popularity_score' => $subcategory['popularity_score'],
                            'display_order' => rand(1, 10),
                            'is_featured' => 0,
                            'is_active' => 1,
                            'item_count' => rand(5, 50)
                        )
                    );
                }
            }
            
            // If this is AI-Generated Art, add subcategories
            if ($category['category_slug'] === 'ai-generated-art') {
                $ai_subcategories = array(
                    array(
                        'category_name' => 'Text-to-Image',
                        'category_slug' => 'text-to-image',
                        'category_description' => 'Artwork created using AI models that generate images from text descriptions.',
                        'category_icon' => 'text-image',
                        'category_color' => '#9b59b6',
                        'popularity_score' => 9.4
                    ),
                    array(
                        'category_name' => 'Style Transfer',
                        'category_slug' => 'style-transfer',
                        'category_description' => 'Images created by applying the style of one image to the content of another using AI.',
                        'category_icon' => 'switch',
                        'category_color' => '#9b59b6',
                        'popularity_score' => 8.9
                    ),
                    array(
                        'category_name' => 'GAN Art',
                        'category_slug' => 'gan-art',
                        'category_description' => 'Artwork created using Generative Adversarial Networks and similar generative models.',
                        'category_icon' => 'network',
                        'category_color' => '#9b59b6',
                        'popularity_score' => 8.7
                    )
                );
                
                foreach ($ai_subcategories as $subcategory) {
                    $wpdb->insert(
                        $table_name,
                        array(
                            'category_name' => $subcategory['category_name'],
                            'category_slug' => $subcategory['category_slug'],
                            'category_description' => $subcategory['category_description'],
                            'parent_id' => $parent_id,
                            'category_icon' => $subcategory['category_icon'],
                            'category_color' => $subcategory['category_color'],
                            'creation_date' => current_time('mysql'),
                            'last_updated' => current_time('mysql'),
                            'popularity_score' => $subcategory['popularity_score'],
                            'display_order' => rand(1, 10),
                            'is_featured' => 0,
                            'is_active' => 1,
                            'item_count' => rand(5, 50)
                        )
                    );
                }
            }
        }
    }

    /**
     * Create artwork themes table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_artwork_themes_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_artwork_themes';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            theme_name varchar(191) NOT NULL,
            theme_slug varchar(191) NOT NULL,
            theme_description text,
            theme_parent bigint(20) unsigned DEFAULT NULL,
            popularity_score decimal(10,2) DEFAULT '0.00',
            creation_date datetime DEFAULT CURRENT_TIMESTAMP,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            artwork_count int(11) DEFAULT '0',
            trending_score decimal(10,2) DEFAULT '0.00',
            PRIMARY KEY  (id),
            UNIQUE KEY theme_slug (theme_slug),
            KEY theme_parent (theme_parent),
            KEY popularity_score (popularity_score),
            KEY trending_score (trending_score)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create user sessions table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_user_sessions_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_user_sessions';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create user activity table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_user_activity_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_user_activity';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            session_id bigint(20) unsigned DEFAULT NULL,
            activity_type varchar(64) NOT NULL,
            activity_time datetime DEFAULT CURRENT_TIMESTAMP,
            object_id bigint(20) unsigned DEFAULT NULL,
            object_type varchar(64) DEFAULT NULL,
            activity_data longtext,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY session_id (session_id),
            KEY activity_type (activity_type),
            KEY activity_time (activity_time),
            KEY object_id (object_id),
            KEY object_type (object_type)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create CLOE analytics tables
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_cloe_analytics_tables($charset_collate) {
        global $wpdb;
        
        // User preferences table
        $table_name = $wpdb->prefix . 'vortex_user_preferences';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            preference_key varchar(191) NOT NULL,
            preference_value longtext,
            preference_score decimal(10,2) DEFAULT '0.00',
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_preference (user_id,preference_key),
            KEY preference_key (preference_key),
            KEY preference_score (preference_score)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Trend analytics table
        $table_name = $wpdb->prefix . 'vortex_trend_analytics';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            trend_type varchar(64) NOT NULL,
            trend_key varchar(191) NOT NULL,
            trend_score decimal(10,2) DEFAULT '0.00',
            sample_size int(11) DEFAULT '0',
            start_date datetime DEFAULT NULL,
            end_date datetime DEFAULT NULL,
            trend_data longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY trend_unique_key (trend_type,trend_key,start_date,end_date),
            KEY trend_type (trend_type),
            KEY trend_key (trend_key),
            KEY trend_score (trend_score),
            KEY start_date (start_date),
            KEY end_date (end_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create artwork statistics table
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_artwork_statistics_table($charset_collate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'vortex_artwork_statistics';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            artwork_id bigint(20) unsigned NOT NULL,
            views int(11) DEFAULT '0',
            likes int(11) DEFAULT '0',
            shares int(11) DEFAULT '0',
            comments int(11) DEFAULT '0',
            avg_view_time int(11) DEFAULT '0',
            bounce_rate decimal(5,2) DEFAULT '0.00',
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY artwork_id (artwork_id),
            KEY views (views),
            KEY likes (likes),
            KEY shares (shares),
            KEY comments (comments)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create Thorius tables
     *
     * @since    1.0.0
     * @param    string    $charset_collate    Database charset and collation
     */
    private function create_thorius_tables($charset_collate) {
        global $wpdb;
        
        // Thorius sessions table
        $table_name = $wpdb->prefix . 'vortex_thorius_sessions';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(64) NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            last_activity datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            session_data longtext,
            ip_address varchar(40) DEFAULT NULL,
            user_agent text,
            PRIMARY KEY  (id),
            UNIQUE KEY session_id (session_id),
            KEY user_id (user_id),
            KEY created_at (created_at),
            KEY last_activity (last_activity)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Thorius interaction history table
        $table_name = $wpdb->prefix . 'vortex_thorius_interaction_history';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id varchar(64) NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            message_type enum('user','assistant') NOT NULL,
            message_content longtext NOT NULL,
            message_embedding longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY message_type (message_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Thorius user context table
        $table_name = $wpdb->prefix . 'vortex_thorius_user_context';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            context_key varchar(191) NOT NULL,
            context_value longtext,
            last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY user_context (user_id,context_key),
            KEY context_key (context_key),
            KEY last_updated (last_updated)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the migrations class
new Vortex_DB_Migrations(); 