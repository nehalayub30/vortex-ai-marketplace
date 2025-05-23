<?php
/**
 * VORTEX CLOE AI Curation Agent
 *
 * @package VORTEX_AI_Marketplace
 * @subpackage AI
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * VORTEX_CLOE Class
 * 
 * CLOE (Curation & Learning Optimization Engine) handles personalized 
 * user experiences, behavioral analysis, and trend correlation.
 */
class VORTEX_CLOE {
    /**
     * Instance of this class.
     */
    protected static $instance = null;
    
    /**
     * Learning models and capabilities
     */
    private $learning_models = array();
    
    /**
     * User data tracking categories
     */
    private $tracking_categories = array();
    
    /**
     * Greeting templates
     */
    private $greeting_templates = array();
    
    /**
     * Current trends data
     */
    private $current_trends = array();
    
    /**
     * User behavior metrics
     */
    private $behavior_metrics = array();
    
    /**
     * Marketing intelligence data
     */
    private $marketing_data = array();

    /**
     * Marketing intelligence data
     */
    private $deep_learning_enabled;

    /**
     * Marketing intelligence data
     */
    private $learning_rate;

    /**
     * Marketing intelligence data
     */
    private $context_window;

   /**
     * Marketing intelligence data
     */
    private $model_config;


    /**
     * Marketing intelligence data
     */
    private $continuous_learning;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        // Initialize learning models
        $this->initialize_learning_models();
        
        // Initialize tracking categories
        $this->initialize_tracking_categories();
        
        // Initialize greeting templates
        $this->initialize_greeting_templates();
        
        // Set up hooks
        $this->setup_hooks();
        
        // Initialize behavioral data collection
        $this->initialize_behavior_metrics();
        
        // Initialize marketing data
        $this->initialize_marketing_data();
        
        // Initialize trends tracking
        $this->initialize_trend_tracking();
    }
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize learning models
     */
    private function initialize_learning_models() {
        $this->learning_models = array(
            'user_preferences' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/user_preferences.model',
                'last_trained' => get_option('vortex_cloe_user_preferences_trained', 0),
                'batch_size' => 32,
                'learning_rate' => 0.001
            ),
            'curation' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/curation.model',
                'last_trained' => get_option('vortex_cloe_curation_trained', 0),
                'batch_size' => 24,
                'learning_rate' => 0.002
            ),
            'behavior_analysis' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/behavior_analysis.model',
                'last_trained' => get_option('vortex_cloe_behavior_analysis_trained', 0),
                'batch_size' => 48,
                'learning_rate' => 0.0015
            ),
            'demographic_insights' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/demographic_insights.model',
                'last_trained' => get_option('vortex_cloe_demographic_insights_trained', 0),
                'batch_size' => 32,
                'learning_rate' => 0.001
            ),
            'trend_correlation' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/trend_correlation.model',
                'last_trained' => get_option('vortex_cloe_trend_correlation_trained', 0),
                'batch_size' => 64,
                'learning_rate' => 0.0005
            ),
            'seo_optimization' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/seo_optimization.model',
                'last_trained' => get_option('vortex_cloe_seo_optimization_trained', 0),
                'batch_size' => 32,
                'learning_rate' => 0.001
            ),
            'content_personalization' => array(
                'path' => VORTEX_PLUGIN_PATH . 'models/cloe/content_personalization.model',
                'last_trained' => get_option('vortex_cloe_content_personalization_trained', 0),
                'batch_size' => 16,
                'learning_rate' => 0.002
            )
        );
        
        // Check for missing model files and create placeholders
        foreach ($this->learning_models as $model_name => $model_data) {
            if (!file_exists($model_data['path'])) {
                $model_dir = dirname($model_data['path']);
                if (!file_exists($model_dir)) {
                    wp_mkdir_p($model_dir);
                }
                file_put_contents($model_data['path'], 'CLOE Model Placeholder: ' . $model_name);
            }
        }
    }
    
    /**
     * Initialize tracking categories
     */
    private function initialize_tracking_categories() {
        $this->tracking_categories = array(
            'temporal' => array(
                'hour_of_day' => array(
                    'type' => 'numeric',
                    'range' => range(0, 23),
                    'tracking' => 'automatic'
                ),
                'day_of_week' => array(
                    'type' => 'numeric',
                    'range' => range(0, 6),
                    'tracking' => 'automatic'
                ),
                'session_duration' => array(
                    'type' => 'numeric',
                    'tracking' => 'automatic'
                ),
                'return_frequency' => array(
                    'type' => 'categorical',
                    'values' => array('daily', 'weekly', 'monthly', 'occasional'),
                    'tracking' => 'derived'
                )
            ),
            'demographic' => array(
                'region' => array(
                    'type' => 'categorical',
                    'tracking' => 'ip_based',
                    'privacy' => 'anonymized'
                ),
                'country' => array(
                    'type' => 'categorical',
                    'tracking' => 'ip_based',
                    'privacy' => 'anonymized'
                ),
                'gender' => array(
                    'type' => 'categorical',
                    'values' => array('male', 'female', 'non_binary', 'other', 'undisclosed'),
                    'tracking' => 'profile_based',
                    'privacy' => 'user_controlled'
                ),
                'age_group' => array(
                    'type' => 'categorical',
                    'values' => array('under_18', '18_24', '25_34', '35_44', '45_54', '55_64', '65_plus', 'undisclosed'),
                    'tracking' => 'profile_based',
                    'privacy' => 'user_controlled'
                ),
                'language' => array(
                    'type' => 'categorical',
                    'tracking' => 'browser_based'
                )
            ),
            'behavioral' => array(
                'view_duration' => array(
                    'type' => 'numeric',
                    'tracking' => 'automatic'
                ),
                'click_patterns' => array(
                    'type' => 'complex',
                    'tracking' => 'automatic'
                ),
                'search_patterns' => array(
                    'type' => 'complex',
                    'tracking' => 'automatic'
                ),
                'style_preferences' => array(
                    'type' => 'categorical',
                    'tracking' => 'derived'
                ),
                'price_sensitivity' => array(
                    'type' => 'numeric',
                    'range' => array(0, 10),
                    'tracking' => 'derived'
                ),
                'social_sharing' => array(
                    'type' => 'boolean',
                    'tracking' => 'action_based'
                )
            ),
            'marketing' => array(
                'referral_source' => array(
                    'type' => 'categorical',
                    'tracking' => 'url_based'
                ),
                'utm_campaign' => array(
                    'type' => 'categorical',
                    'tracking' => 'url_based'
                ),
                'keyword_effectiveness' => array(
                    'type' => 'complex',
                    'tracking' => 'derived'
                ),
                'conversion_path' => array(
                    'type' => 'complex',
                    'tracking' => 'derived'
                )
            ),
            'content' => array(
                'preferred_formats' => array(
                    'type' => 'categorical',
                    'values' => array('2d_image', '3d_model', 'video', 'audio', 'interactive'),
                    'tracking' => 'derived'
                ),
                'style_affinities' => array(
                    'type' => 'complex',
                    'tracking' => 'derived'
                ),
                'artist_followings' => array(
                    'type' => 'complex',
                    'tracking' => 'action_based'
                ),
                'theme_interests' => array(
                    'type' => 'complex',
                    'tracking' => 'derived'
                )
            )
        );
    }
    
    /**
     * Initialize greeting templates with humor and motivation
     */
    private function initialize_greeting_templates() {
        $this->greeting_templates = array(
            'time_based' => array(
                'morning' => array(
                    'Welcome, %s! Ready to make art as amazing as your morning coffee?',
                    'Good morning, %s! The creative sun is shining just for you today.',
                    'Morning, %s! Let\'s turn those sleepy dreams into stunning visuals.',
                    'Rise and design, %s! The art world awaits your morning brilliance.'
                ),
                'afternoon' => array(
                    'Afternoon inspiration calling, %s! Ready to answer with your creativity?',
                    'Hi %s! Fighting that afternoon slump with some creative therapy?',
                    'Afternoon, %s! Perfect time to make something that will make tomorrow jealous.',
                    'The afternoon muse has arrived, %s! Let\'s create something extraordinary.'
                ),
                'evening' => array(
                    'Evening, %s! Time to create by the glow of inspiration (and your screen).',
                    'Good evening, %s! Let\'s end the day on a creative high note.',
                    'Evening creativity hits different, doesn\'t it, %s? Let\'s make magic happen.',
                    'Stars are out, %s, and so is your creative potential tonight!'
                ),
                'night' => array(
                    'Night owl or just inspired, %s? Either way, let\'s make this midnight magic count!',
                    'Creating after dark, %s? That\'s when the best ideas come out to play.',
                    'The night is young and so are your ideas, %s! Let\'s bring them to life.',
                    'Burning the creative midnight oil, %s? Your dedication is inspiring!'
                )
            ),
            'returning_user' => array(
                'short_absence' => array(
                    'Welcome back, %s! Your creative projects missed you (almost as much as I did)!',
                    'Look who\'s back! %s has returned to bless us with more creative brilliance!',
                    'Missed you, %s! The creative void in your absence was palpable.',
                    'The creative prodigal returns! Welcome back, %s – ready to pick up where you left off?'
                ),
                'long_absence' => array(
                    'Is that really YOU, %s?! The creative world has been wondering where you\'ve been!',
                    'Well, well, well... look who finally remembered their password! Welcome back, %s!',
                    '%s has returned! Should we alert the art media, or keep your comeback our secret?',
                    'After your extended creative sabbatical, %s, you\'re back! The art world can resume now.'
                )
            ),
            'achievement_based' => array(
                'new_milestone' => array(
                    'Look at you go, %s! %d creations and still breaking boundaries!',
                    'Creative milestone unlocked, %s! %d pieces and counting – you\'re on fire!',
                    'Achievement unlocked: %s has created %d pieces of brilliance! What can\'t you do?',
                    '%d creations, %s? That\'s not just talent – that\'s dedication!'
                ),
                'first_sale' => array(
                    'STOP EVERYTHING! %s just made their first sale! The art world will never be the same!',
                    'Someone just recognized your genius, %s! First sale complete – fame and fortune await!',
                    'Breaking news: %s just made their first sale! Next stop: artistic world domination!',
                    'First sale alert! %s, this calls for a creative victory dance!'
                )
            ),
            'trend_based' => array(
                'following_trends' => array(
                    'I see you\'re riding the %s wave, %s! Your timing is as impeccable as your taste.',
                    'Jumping on the %s trend, %s? Your take is refreshingly original!',
                    'Everyone\'s talking about %s, and now you\'re joining in, %s! Can\'t wait to see your spin on it.',
                    'The %s trend was missing something – turns out it was your contribution, %s!'
                ),
                'trend_setter' => array(
                    'Move over influencers, %s is setting trends with %s before it\'s even cool!',
                    'Always ahead of the curve, aren\'t you, %s? Your work in %s is setting tomorrow\'s trends!',
                    'The %s movement called – they want to thank you, %s, for showing them the future!',
                    'Not following trends but MAKING them – %s, your %s work is what everyone will be copying tomorrow!'
                )
            ),
            'style_based' => array(
                'consistent_style' => array(
                    'That signature %s style of yours, %s – it\'s becoming as recognizable as a Picasso!',
                    'There\'s that %s touch that could only come from you, %s! It\'s becoming your creative fingerprint.',
                    'I\'d recognize your %s style anywhere, %s! It\'s becoming legendary around here.',
                    'The %s master returns! %s, your consistent style is building you quite the reputation!'
                ),
                'style_explorer' => array(
                    'From %s to %s – is there any style you can\'t conquer, %s?',
                    'Creative chameleon alert! %s is switching from %s to %s with impressive versatility!',
                    'Genre-hopping from %s to %s? %s, your creative range is showing (and it\'s impressive)!',
                    'Experimenting from %s to %s? %s, your artistic curiosity is truly inspiring!'
                )
            ),
            'suggestion_based' => array(
                'new_features' => array(
                    'Have you tried the new %s feature yet, %s? I think it has your creative name all over it!',
                    'Psst, %s! The new %s feature just dropped, and it\'s practically begging for your creative touch.',
                    '%s, meet %s – our newest feature that I\'m pretty sure was inspired by creative minds like yours!',
                    'Creative recommendation: %s should check out our new %s feature! It matches your style perfectly.'
                ),
                'trending_content' => array(
                    'Everyone\'s talking about %s right now, %s! Curious to see your take on it.',
                    'The creative world is obsessed with %s this week, %s. Care to join the conversation?',
                    '%s is trending in the art world, %s! Your unique perspective would make an amazing contribution.',
                    'Word on the street is that %s is the next big thing, %s. Seems right up your creative alley!'
                )
            )
        );
    }
    
    /**
     * Set up hooks
     */
    private function setup_hooks() {
        // User interaction tracking
        add_action('wp_login', array($this, 'track_user_login'), 10, 2);
        add_action('vortex_artwork_viewed', array($this, 'track_artwork_view'), 10, 2);
        add_action('vortex_artwork_liked', array($this, 'track_artwork_like'), 10, 2);
        add_action('vortex_artwork_shared', array($this, 'track_artwork_share'), 10, 3);
        add_action('vortex_artwork_purchased', array($this, 'track_artwork_purchase'), 10, 3);
        add_action('vortex_artist_followed', array($this, 'track_artist_follow'), 10, 2);
        add_action('vortex_search_performed', array($this, 'track_search_query'), 10, 2);
        add_action('vortex_swipe_action', array($this, 'track_swipe_action'), 10, 3);
        
        // Session tracking
        add_action('wp_login', array($this, 'start_session_tracking'), 10, 2);
        add_action('wp_logout', array($this, 'end_session_tracking'));
        add_action('init', array($this, 'continue_session_tracking'));
        
        // Admin reporting
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
        
        // AJAX handlers
        add_action('wp_ajax_vortex_cloe_get_greeting', array($this, 'ajax_get_greeting'));
        add_action('wp_ajax_nopriv_vortex_cloe_get_greeting', array($this, 'ajax_get_greeting'));
        add_action('wp_ajax_vortex_cloe_get_recommendations', array($this, 'ajax_get_recommendations'));
        add_action('wp_ajax_vortex_cloe_get_trends', array($this, 'ajax_get_trends'));
        
        // Scheduled tasks
        add_action('vortex_daily_trend_update', array($this, 'update_trend_data'));
        add_action('vortex_weekly_seo_report', array($this, 'generate_seo_report'));
        add_action('vortex_monthly_analytics', array($this, 'generate_monthly_analytics'));
        
        if (!wp_next_scheduled('vortex_daily_trend_update')) {
            wp_schedule_event(time(), 'daily', 'vortex_daily_trend_update');
        }
        
        if (!wp_next_scheduled('vortex_weekly_seo_report')) {
            wp_schedule_event(time(), 'weekly', 'vortex_weekly_seo_report');
        }
        
        if (!wp_next_scheduled('vortex_monthly_analytics')) {
            wp_schedule_event(time(), 'monthly', 'vortex_monthly_analytics');
        }
    }

    /**
     * End session tracking
     */
    public function end_session_tracking() {
        try {
            // Check if user is logged in
            if (!is_user_logged_in()) {
                return;
            }
            
            $user_id = get_current_user_id();
            
            // Get current session
            $session_id = get_user_meta($user_id, 'vortex_current_session', true);
            
            if (empty($session_id)) {
                return;
            }
            
            // Get session start time
            $start_time = (int)get_user_meta($user_id, 'vortex_session_start', true);
            $end_time = time();
            $duration = $end_time - $start_time;
            
            // Update session in database
            global $wpdb;
            $session_table = $wpdb->prefix . 'vortex_user_sessions';
            
            // Check if table exists before attempting update
            if ($this->table_exists('vortex_user_sessions')) {
                $result = $wpdb->update(
                    $session_table,
                    array(
                        'end_time' => date('Y-m-d H:i:s', $end_time),
                        'duration' => $duration,
                        'active' => 0
                    ),
                    array('session_id' => $session_id),
                    array('%s', '%d', '%d'),
                    array('%s')
                );
                
                if ($result === false) {
                    // Log error but don't throw exception
                    error_log('VORTEX_CLOE: Failed to update session in database. Session ID: ' . $session_id);
                }
            }
            
            // Record session end event
            $this->record_user_event($user_id, 'session_end', array(
                'session_id' => $session_id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'duration' => $duration
            ));
            
            // Clear session data
            delete_user_meta($user_id, 'vortex_current_session');
            delete_user_meta($user_id, 'vortex_session_start');
        } catch (Exception $e) {
            // Log error but prevent it from breaking the page
            error_log('VORTEX_CLOE: Error in end_session_tracking: ' . $e->getMessage());
        }
    }

    /**
     * Add dashboard widgets for CLOE analytics
     */
    public function add_dashboard_widgets() {
        // Only add widgets for administrators
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Add CLOE recommendations widget
        wp_add_dashboard_widget(
            'vortex_cloe_recommendations',
            __('CLOE AI Recommendations', 'vortex-marketplace'),
            array($this, 'render_recommendations_widget')
        );
        
        // Add CLOE trends widget
        wp_add_dashboard_widget(
            'vortex_cloe_trends',
            __('CLOE Market Trends', 'vortex-marketplace'),
            array($this, 'render_trends_widget')
        );
        
        // Add CLOE user insights widget
        wp_add_dashboard_widget(
            'vortex_cloe_user_insights',
            __('CLOE User Insights', 'vortex-marketplace'),
            array($this, 'render_user_insights_widget')
        );
    }

    /**
     * Check if a database table exists
     * 
     * @param string $table_name Full table name including prefix
     * @return bool True if table exists, false otherwise
     */
    private function table_exists($table_name) {
        global $wpdb;
        return $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    }

    /**
     * Start session tracking
     * 
     * @param string $user_login User login name
     * @param WP_User $user User object
     */
    public function start_session_tracking($user_login, $user) {
        try {
            $user_id = $user->ID;
            
            // Generate unique session ID
            $session_id = md5(uniqid($user_id . '_', true));
            
            // Store session info
            update_user_meta($user_id, 'vortex_current_session', $session_id);
            
            // Record session start time
            $start_time = time();
            update_user_meta($user_id, 'vortex_session_start', $start_time);
            
            // Get user's IP and user agent
            $ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
            
            // Store session data in the database
            global $wpdb;
            $session_table = $wpdb->prefix . 'vortex_user_sessions';
            
            // Check if the table exists before attempting to insert
            if ($this->table_exists('vortex_user_sessions')) {
                $current_time = date('Y-m-d H:i:s', $start_time);
                
                // Insert session record
                $wpdb->insert(
                    $session_table,
                    array(
                        'session_id' => $session_id,
                        'user_id' => $user_id,
                        'start_time' => $current_time,
                        'last_activity' => $current_time,
                        'activity_time' => $current_time,
                        'ip_address' => $ip_address,
                        'user_agent' => $user_agent,
                        'active' => 1
                    ),
                    array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d')
                );
            }
            
            // Record the session start event
            $this->record_user_event($user_id, 'session_start', array(
                'session_id' => $session_id,
                'timestamp' => $start_time,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent
            ));
        } catch (Exception $e) {
            // Log error but prevent it from breaking the page
            error_log('VORTEX_CLOE: Error in start_session_tracking: ' . $e->getMessage());
        }
    }

    /**
     * Get current session ID for a user
     *
     * @since    1.0.0
     * @param    int       $user_id    User ID
     * @return   string                Current session ID or empty if no active session
     */
    private function get_current_session_id($user_id) {
        return get_user_meta($user_id, '_vortex_current_session_id', true);
    }

    /**
     * Continue session tracking
     * 
     * Tracks user activity during the session and updates AI learning models
     * Called on init hook for logged-in users
     *
     * @since    1.0.0
     * @return   void
     */
    public function continue_session_tracking() {
        // Only process for logged-in users
        if (!is_user_logged_in()) {
            return;
        }
        
        $user_id = get_current_user_id();
        $session_id = $this->get_current_session_id($user_id);
        
        // If no active session, don't continue
        if (empty($session_id)) {
            return;
        }
        
        // Get session data
        $session_data = get_user_meta($user_id, '_vortex_session_data_' . $session_id, true);
        if (empty($session_data) || !is_array($session_data)) {
            $session_data = array(
                'start_time' => time(),
                'last_activity' => time(),
                'page_views' => array(),
                'interactions' => array(),
                'referrer' => isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '',
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : ''
            );
        }
        
        // Update last activity time
        $session_data['last_activity'] = time();
        
        // Track current page view if not an AJAX request
        if (!wp_doing_ajax() && !is_admin()) {
            $current_url = home_url($_SERVER['REQUEST_URI']);
            $page_id = get_the_ID();
            
            // Add page view data
            $session_data['page_views'][] = array(
                'timestamp' => time(),
                'url' => $current_url,
                'page_id' => $page_id,
                'title' => get_the_title($page_id)
            );
            
            // Limit stored page views to prevent data bloat
            if (count($session_data['page_views']) > 100) {
                $session_data['page_views'] = array_slice($session_data['page_views'], -100);
            }
        }
        
        // Update session data
        update_user_meta($user_id, '_vortex_session_data_' . $session_id, $session_data);
        
        // Calculate session duration
        $session_duration = time() - $session_data['start_time'];
        update_user_meta($user_id, '_vortex_current_session_duration', $session_duration);
        
        // Check for session timeout
        $timeout = apply_filters('vortex_session_timeout', 30 * 60); // 30 minutes default
        if ($session_duration > $timeout && (!isset($session_data['last_activity']) || (time() - $session_data['last_activity']) > $timeout)) {
            $this->end_session_tracking();
            $this->start_session_tracking('', $user_id);
        }
        
        // Process data for AI learning
        $this->process_session_data_for_learning($user_id, $session_data);
    }

    /**
     * Get peak activity hours from user behavior
     * 
     * @param string $period Time period for calculation
     * @return array Peak activity hours data
     */
    private function get_peak_activity_hours($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            // $query = $wpdb->prepare(
            //     "SELECT 
            //         HOUR(activity_time) as hour,
            //         COUNT(*) as activity_count
            //     FROM {$wpdb->prefix}vortex_user_activity
            //     WHERE activity_time >= %s
            //     GROUP BY HOUR(activity_time)
            //     ORDER BY activity_count DESC",
            //     $time_constraint
            // );
            
            // $results = $wpdb->get_results($query);
            
            // Prepare hour distribution
            $hour_distribution = array();
            for ($i = 0; $i < 24; $i++) {
                $hour_distribution[$i] = 0;
            }
            
            // Populate with actual data
            foreach ($results as $row) {
                $hour_distribution[$row->hour] = intval($row->activity_count);
            }
            
            // Find peak hours (top 3)
            $peak_hours = array();
            $temp_distribution = $hour_distribution;
            arsort($temp_distribution);
            $peak_hours = array_slice(array_keys($temp_distribution), 0, 3);
            
            return array(
                'hour_distribution' => $hour_distribution,
                'peak_hours' => $peak_hours,
                'total_activity' => array_sum($hour_distribution)
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get peak activity hours: ' . $e->getMessage());
            return array(
                'hour_distribution' => array(),
                'peak_hours' => array(),
                'total_activity' => 0
            );
        }
    }

    /**
     * Get region distribution of users
     * 
     * @param string $period Time period for analysis
     * @return array Region distribution data
     */
    private function get_region_distribution($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    region,
                    COUNT(DISTINCT user_id) as user_count
                FROM {$wpdb->prefix}vortex_user_geo_data
                WHERE last_updated >= %s
                GROUP BY region
                ORDER BY user_count DESC",
                $time_constraint
            );
            
            $results = $wpdb->get_results($query);
            
            // Total users in the period
            $total_users = 0;
            foreach ($results as $row) {
                $total_users += $row->user_count;
            }
            
            // Calculate percentages
            $region_distribution = array();
            foreach ($results as $row) {
                $region_distribution[] = array(
                    'region' => $row->region,
                    'user_count' => $row->user_count,
                    'percentage' => $total_users > 0 ? round(($row->user_count / $total_users) * 100, 2) : 0
                );
            }
            
            // Get dominant regions (top 5)
            $dominant_regions = array_slice($region_distribution, 0, 5);
            
            return array(
                'distribution' => $region_distribution,
                'dominant_regions' => $dominant_regions,
                'total_users' => $total_users
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get region distribution: ' . $e->getMessage());
            return array(
                'distribution' => array(),
                'dominant_regions' => array(),
                'total_users' => 0
            );
        }
    }
    
    /**
     * Get age group distribution of users
     * 
     * @param string $period Time period for analysis
     * @return array Age group distribution data
     */
    private function get_age_group_distribution($period = 'month') {
        try {
            global $wpdb;
            
            // Define age groups
            $age_groups = array(
                'under_18' => 'Under 18',
                '18_24' => '18-24',
                '25_34' => '25-34',
                '35_44' => '35-44',
                '45_54' => '45-54',
                '55_64' => '55-64',
                '65_plus' => '65+',
                'undisclosed' => 'Undisclosed'
            );
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            // $query = $wpdb->prepare(
            //     "SELECT 
            //         age_group,
            //         COUNT(DISTINCT user_id) as user_count
            //     FROM {$wpdb->prefix}vortex_user_demographics
            //     WHERE update_time >= %s
            //     GROUP BY age_group
            //     ORDER BY FIELD(age_group, 'under_18', '18_24', '25_34', '35_44', '45_54', '55_64', '65_plus', 'undisclosed')",
            //     $time_constraint
            // );
            
            // $results = $wpdb->get_results($query);
            
            // Initialize all age groups with zero
            $age_distribution = array();
            foreach ($age_groups as $key => $label) {
                $age_distribution[$key] = array(
                    'label' => $label,
                    'count' => 0,
                    'percentage' => 0
                );
            }
            
            // Fill in the actual data
            $total_users = 0;
            foreach ($results as $row) {
                if (isset($age_distribution[$row->age_group])) {
                    $age_distribution[$row->age_group]['count'] = intval($row->user_count);
                    $total_users += intval($row->user_count);
                }
            }
            
            // Calculate percentages
            if ($total_users > 0) {
                foreach ($age_distribution as $key => $data) {
                    $age_distribution[$key]['percentage'] = round(($data['count'] / $total_users) * 100, 2);
                }
            }
            
            return array(
                'distribution' => $age_distribution,
                'total_users' => $total_users
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get age group distribution: ' . $e->getMessage());
            return array(
                'distribution' => array(),
                'total_users' => 0
            );
        }
    }

    /**
     * Get gender distribution of users
     * 
     * @param string $period Time period for analysis
     * @return array Gender distribution data
     */
    private function get_gender_distribution($period = 'month') {
        try {
            global $wpdb;
            
            // Define gender categories
            $gender_categories = array(
                'male' => 'Male',
                'female' => 'Female',
                'non_binary' => 'Non-Binary',
                'other' => 'Other',
                'undisclosed' => 'Undisclosed'
            );
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            // $query = $wpdb->prepare(
            //     "SELECT 
            //         gender,
            //         COUNT(DISTINCT user_id) as user_count
            //     FROM {$wpdb->prefix}vortex_user_demographics
            //     WHERE update_time >= %s
            //     GROUP BY gender",
            //     $time_constraint
            // );
            
            // $results = $wpdb->get_results($query);
            
            // Initialize all gender categories with zero
            $gender_distribution = array();
            foreach ($gender_categories as $key => $label) {
                $gender_distribution[$key] = array(
                    'label' => $label,
                    'count' => 0,
                    'percentage' => 0
                );
            }
            
            // Fill in the actual data
            $total_users = 0;
            foreach ($results as $row) {
                if (isset($gender_distribution[$row->gender])) {
                    $gender_distribution[$row->gender]['count'] = intval($row->user_count);
                    $total_users += intval($row->user_count);
                }
            }
            
            // Calculate percentages
            if ($total_users > 0) {
                foreach ($gender_distribution as $key => $data) {
                    $gender_distribution[$key]['percentage'] = round(($data['count'] / $total_users) * 100, 2);
                }
            }
            
            return array(
                'distribution' => $gender_distribution,
                'total_users' => $total_users
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get gender distribution: ' . $e->getMessage());
            return array(
                'distribution' => array(),
                'total_users' => 0
            );
        }
    }

    /**
     * Get language preferences of users
     * 
     * @param string $period Time period for analysis
     * @return array Language preferences data
     */
    private function get_language_preferences($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            // $query = $wpdb->prepare(
            //     "SELECT 
            //         language_code,
            //         language_name,
            //         COUNT(DISTINCT user_id) as user_count
            //     FROM {$wpdb->prefix}vortex_user_languages
            //     WHERE last_used >= %s
            //     GROUP BY language_code, language_name
            //     ORDER BY user_count DESC",
            //     $time_constraint
            // );
            
            // $results = $wpdb->get_results($query);
            
            // Total users in the period
            $total_users = 0;
            foreach ($results as $row) {
                $total_users += $row->user_count;
            }
            
            // Calculate percentages
            $language_distribution = array();
            foreach ($results as $row) {
                $language_distribution[] = array(
                    'code' => $row->language_code,
                    'name' => $row->language_name,
                    'user_count' => $row->user_count,
                    'percentage' => $total_users > 0 ? round(($row->user_count / $total_users) * 100, 2) : 0
                );
            }
            
            // Get primary languages (top 5)
            $primary_languages = array_slice($language_distribution, 0, 5);
            
            return array(
                'distribution' => $language_distribution,
                'primary_languages' => $primary_languages,
                'total_users' => $total_users
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get language preferences: ' . $e->getMessage());
            return array(
                'distribution' => array(),
                'primary_languages' => array(),
                'total_users' => 0
            );
        }
    }

    /**
     * Calculate view to like ratio for artworks
     * 
     * @param string $period Time period for analysis
     * @return array View to like ratio data
     */
    private function calculate_view_to_like_ratio($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get overall stats
            $overall_stats = [];
            $query = $wpdb->prepare(
                "SELECT 
                    COUNT(DISTINCT v.view_id) as total_views,
                    COUNT(DISTINCT l.id) as total_likes
                FROM {$wpdb->prefix}vortex_artwork_views v
                LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                    v.artwork_id = l.artwork_id AND 
                    v.user_id = l.user_id AND 
                    l.like_time >= v.view_time AND
                    l.like_time <= DATE_ADD(v.view_time, INTERVAL 24 HOUR)
                WHERE v.view_time >= %s",
                $time_constraint
            );
            
            $overall_stats = $wpdb->get_row($query);
            
            // Calculate overall ratio
            $overall_ratio = 0;
            if ($overall_stats && $overall_stats->total_views > 0) {
                $overall_ratio = round(($overall_stats->total_likes / $overall_stats->total_views) * 100, 2);
            }
            
            // Get category-specific ratios
            $category_ratios = [];
            $category_query = $wpdb->prepare(
                "SELECT 
                    c.id,
                    c.category_name,
                    COUNT(DISTINCT v.view_id) as views,
                    COUNT(DISTINCT l.id) as likes,
                    CASE 
                        WHEN COUNT(DISTINCT v.view_id) > 0 
                        THEN (COUNT(DISTINCT l.id) / COUNT(DISTINCT v.view_id)) * 100
                        ELSE 0
                    END as ratio
                FROM {$wpdb->prefix}vortex_categories c
                JOIN {$wpdb->prefix}vortex_artworks a ON c.id = a.artwork_id
                JOIN {$wpdb->prefix}vortex_artwork_views v ON a.artwork_id = v.artwork_id
                LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                    v.artwork_id = l.artwork_id AND 
                    v.user_id = l.user_id AND 
                    l.like_time >= v.view_time AND
                    l.like_time <= DATE_ADD(v.view_time, INTERVAL 24 HOUR)
                WHERE v.view_time >= %s
                GROUP BY c.id
                HAVING views > 10
                ORDER BY ratio DESC",
                $time_constraint
            );
            
            $category_ratios = $wpdb->get_results($category_query);
            
            // Process category ratios
            $processed_categories = array();
            foreach ($category_ratios as $category) {
                $processed_categories[] = array(
                    'category_id' => $category->id,
                    'category_name' => $category->category_name,
                    'views' => $category->views,
                    'likes' => $category->likes,
                    'ratio' => round($category->ratio, 2)
                );
            }
            
            return array(
                'overall_views' => $overall_stats ? $overall_stats->total_views : 0,
                'overall_likes' => $overall_stats ? $overall_stats->total_likes : 0,
                'overall_ratio' => $overall_ratio,
                'category_ratios' => $processed_categories
            );
        } catch (Exception $e) {
            $this->log_error('Failed to calculate view to like ratio: ' . $e->getMessage());
            return array(
                'overall_views' => 0,
                'overall_likes' => 0,
                'overall_ratio' => 0,
                'category_ratios' => array()
            );
        }
    }

    /**
     * Get average view duration for artworks
     * 
     * @param string $period Time period for analysis
     * @return array View duration data
     */
    private function get_average_view_duration($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get overall average view duration
            $avg_duration = 0;
            // $query = $wpdb->prepare(
            //     "SELECT AVG(view_duration) as avg_duration
            //     FROM {$wpdb->prefix}vortex_artwork_views
            //     WHERE view_time >= %s
            //     AND view_duration > 0
            //     AND view_duration < 3600", // Exclude sessions over 1 hour (likely left open)
            //     $time_constraint
            // );
            
            // $avg_duration = $wpdb->get_var($query);
            $avg_duration_seconds = $avg_duration ? round($avg_duration, 2) : 0;
            
            // Get category-specific average view durations
            $category_durations = [];
            $category_query = $wpdb->prepare(
                "SELECT 
                    c.id,
                    c.category_name,
                    AVG(v.view_duration) as avg_duration,
                    COUNT(v.view_id) as view_count
                FROM {$wpdb->prefix}vortex_categories c
                JOIN {$wpdb->prefix}vortex_artworks a ON c.id = a.artwork_id
                JOIN {$wpdb->prefix}vortex_artwork_views v ON a.artwork_id = v.artwork_id
                WHERE v.view_time >= %s
                AND v.view_duration > 0
                AND v.view_duration < 3600
                GROUP BY c.id
                HAVING view_count > 10
                ORDER BY avg_duration DESC",
                $time_constraint
            );
            
            $category_durations = $wpdb->get_results($category_query);
            
            // Process category durations
            $processed_categories = array();
            foreach ($category_durations as $category) {
                $processed_categories[] = array(
                    'category_id' => $category->id,
                    'category_name' => $category->category_name,
                    'avg_duration_seconds' => round($category->avg_duration, 2),
                    'view_count' => $category->view_count
                );
            }
            
            // Get view duration distribution by time ranges
            $ranges = array(
                array('min' => 0, 'max' => 10, 'label' => '<10s'), 
                array('min' => 10, 'max' => 30, 'label' => '10-30s'),
                array('min' => 30, 'max' => 60, 'label' => '30-60s'),
                array('min' => 60, 'max' => 120, 'label' => '1-2m'),
                array('min' => 120, 'max' => 300, 'label' => '2-5m'),
                array('min' => 300, 'max' => 3600, 'label' => '>5m')
            );
            
            $distribution = array();
            // foreach ($ranges as $range) {
            //     $range_query = $wpdb->prepare(
            //         "SELECT COUNT(*) as count
            //         FROM {$wpdb->prefix}vortex_artwork_views
            //         WHERE view_time >= %s
            //         AND view_duration > %d
            //         AND view_duration <= %d",
            //         $time_constraint,
            //         $range['min'],
            //         $range['max']
            //     );
                
            //     $count = $wpdb->get_var($range_query);
                
            //     $distribution[] = array(
            //         'range' => $range['label'],
            //         'count' => intval($count)
            //     );
            // }
            
            return array(
                'avg_duration_seconds' => $avg_duration_seconds,
                'avg_duration_formatted' => $this->format_duration($avg_duration_seconds),
                'category_durations' => $processed_categories,
                'duration_distribution' => $distribution
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get average view duration: ' . $e->getMessage());
            return array(
                'avg_duration_seconds' => 0,
                'avg_duration_formatted' => '0s',
                'category_durations' => array(),
                'duration_distribution' => array()
            );
        }
    }

    /**
     * Helper function to format duration in seconds to a readable format
     * 
     * @param int $seconds Duration in seconds
     * @return string Formatted duration
     */
    private function format_duration($seconds) {
        if ($seconds < 60) {
            return round($seconds) . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remaining_seconds = $seconds % 60;
            return $minutes . 'm ' . $remaining_seconds . 's';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . 'h ' . $minutes . 'm';
        }
    }

    /**
     * Get style affinity clusters for user preferences
     * 
     * @param string $period Time period for analysis
     * @return array Style affinity data
     */
    private function get_style_affinity_clusters($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get overall style popularity
            $style_popularity = [];
            $style_query = $wpdb->prepare(
                "SELECT 
                    s.id,
                    s.style_name,
                    COUNT(DISTINCT a.artwork_id) as artwork_count,
                    COUNT(DISTINCT v.user_id) as viewer_count,
                    COUNT(DISTINCT l.user_id) as liker_count,
                    COUNT(DISTINCT p.transaction_id) as purchase_count
                FROM {$wpdb->prefix}vortex_art_styles s
                JOIN {$wpdb->prefix}vortex_artworks a ON s.id = a.style_id
                LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                    a.artwork_id = v.artwork_id AND 
                    v.view_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                    a.artwork_id = l.artwork_id AND 
                    l.like_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_transactions p ON 
                    a.artwork_id = p.artwork_id AND 
                    p.transaction_time >= %s AND
                    p.status = 'completed'
                GROUP BY s.id
                HAVING artwork_count > 0
                ORDER BY viewer_count DESC",
                $time_constraint,
                $time_constraint,
                $time_constraint
            );
            
            $style_popularity = $wpdb->get_results($style_query);
            
            // Calculate engagement scores for each style
            $styles_with_scores = array();
            foreach ($style_popularity as $style) {
                // Engagement score = (liker_count/viewer_count) + 5*(purchase_count/viewer_count)
                $engagement_rate = $style->viewer_count > 0 ? $style->liker_count / $style->viewer_count : 0;
                $conversion_rate = $style->viewer_count > 0 ? $style->purchase_count / $style->viewer_count : 0;
                $engagement_score = $engagement_rate + (5 * $conversion_rate);
                
                $styles_with_scores[] = array(
                    'id' => $style->id,
                    'style_name' => $style->style_name,
                    'artwork_count' => $style->artwork_count,
                    'viewer_count' => $style->viewer_count,
                    'liker_count' => $style->liker_count,
                    'purchase_count' => $style->purchase_count,
                    'engagement_score' => round($engagement_score, 4)
                );
            }
            
            // Sort by engagement score
            usort($styles_with_scores, function($a, $b) {
                return $b['engagement_score'] <=> $a['engagement_score'];
            });
            
            // Get co-occurrence of style preferences (which styles are liked together)
            $style_cooccurrences = [];
            // $cooccurrence_query = $wpdb->prepare(
            //     "SELECT 
            //         s1.id as style1_id,
            //         s1.style_name as style1_name,
            //         s2.id as style2_id,
            //         s2.style_name as style2_name,
            //         COUNT(DISTINCT l1.user_id) as common_users
            //     FROM {$wpdb->prefix}vortex_artwork_likes l1
            //     JOIN {$wpdb->prefix}vortex_artwork_likes l2 ON 
            //         l1.user_id = l2.user_id AND
            //         l1.artwork_id <> l2.artwork_id
            //     JOIN {$wpdb->prefix}vortex_artworks a1 ON l1.artwork_id = a1.artwork_id
            //     JOIN {$wpdb->prefix}vortex_artworks a2 ON l2.artwork_id = a2.artwork_id
            //     JOIN {$wpdb->prefix}vortex_art_styles s1 ON a1.style_id = s1.id
            //     JOIN {$wpdb->prefix}vortex_art_styles s2 ON a2.style_id = s2.id
            //     WHERE l1.like_time >= %s
            //     AND l2.like_time >= %s
            //     AND s1.id < s2.id
            //     GROUP BY s1.id, s2.id
            //     HAVING common_users > 5
            //     ORDER BY common_users DESC
            //     LIMIT 20",
            //     $time_constraint,
            //     $time_constraint
            // );
            
            // $style_cooccurrences = $wpdb->get_results($cooccurrence_query);
            
            // Identify distinct affinity clusters
            $clusters = array();
            $processed_styles = array();
            
            // First pass: seed clusters with strongest co-occurrences
            foreach ($style_cooccurrences as $cooccurrence) {
                if (count($clusters) >= 5) break; // Limit to 5 clusters
                
                // Skip if both styles already in a cluster
                if (
                    in_array($cooccurrence->style1_id, $processed_styles) && 
                    in_array($cooccurrence->style2_id, $processed_styles)
                ) {
                    continue;
                }
                
                // Create new cluster
                $cluster = array(
                    'styles' => array(
                        array(
                            'id' => $cooccurrence->style1_id,
                            'name' => $cooccurrence->style1_name
                        ),
                        array(
                            'id' => $cooccurrence->style2_id,
                            'name' => $cooccurrence->style2_name
                        )
                    ),
                    'strength' => $cooccurrence->common_users
                );
                
                $clusters[] = $cluster;
                $processed_styles[] = $cooccurrence->style1_id;
                $processed_styles[] = $cooccurrence->style2_id;
            }
            
            // Second pass: grow existing clusters
            foreach ($style_cooccurrences as $cooccurrence) {
                $style1_in_cluster = in_array($cooccurrence->style1_id, $processed_styles);
                $style2_in_cluster = in_array($cooccurrence->style2_id, $processed_styles);
                
                // If one style is in a cluster and the other isn't
                if ($style1_in_cluster && !$style2_in_cluster) {
                    foreach ($clusters as $key => $cluster) {
                        foreach ($cluster['styles'] as $style) {
                            if ($style['id'] == $cooccurrence->style1_id) {
                                // Add style2 to this cluster
                                $clusters[$key]['styles'][] = array(
                                    'id' => $cooccurrence->style2_id,
                                    'name' => $cooccurrence->style2_name
                                );
                                $processed_styles[] = $cooccurrence->style2_id;
                                break 2;
                            }
                        }
                    }
                } elseif (!$style1_in_cluster && $style2_in_cluster) {
                    foreach ($clusters as $key => $cluster) {
                        foreach ($cluster['styles'] as $style) {
                            if ($style['id'] == $cooccurrence->style2_id) {
                                // Add style1 to this cluster
                                $clusters[$key]['styles'][] = array(
                                    'id' => $cooccurrence->style1_id,
                                    'name' => $cooccurrence->style1_name
                                );
                                $processed_styles[] = $cooccurrence->style1_id;
                                break 2;
                            }
                        }
                    }
                }
            }
            
            return array(
                'style_popularity' => $styles_with_scores,
                'cooccurrences' => $style_cooccurrences,
                'affinity_clusters' => $clusters
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get style affinity clusters: ' . $e->getMessage());
            return array(
                'style_popularity' => array(),
                'cooccurrences' => array(),
                'affinity_clusters' => array()
            );
        }
    }

    /**
     * Get purchase funnel metrics
     * 
     * @param string $period Time period for analysis
     * @return array Purchase funnel data
     */
    private function get_purchase_funnel_metrics($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get overall funnel metrics
            $funnel_metrics = [];
            $query = $wpdb->prepare(
                "SELECT 
                    COUNT(DISTINCT v.user_id) as unique_viewers,
                    COUNT(DISTINCT l.user_id) as users_who_liked,
                    COUNT(DISTINCT c.user_id) as users_who_added_to_cart,
                    COUNT(DISTINCT t.user_id) as users_who_purchased
                FROM 
                    (SELECT DISTINCT user_id FROM {$wpdb->prefix}vortex_artwork_views 
                     WHERE view_time >= %s) as v
                    LEFT JOIN (
                        SELECT DISTINCT user_id FROM {$wpdb->prefix}vortex_artwork_likes 
                        WHERE like_time >= %s
                    ) as l ON v.user_id = l.user_id
                    LEFT JOIN (
                        SELECT DISTINCT user_id FROM {$wpdb->prefix}vortex_cart_items 
                        WHERE added_date >= %s
                    ) as c ON v.user_id = c.user_id
                    LEFT JOIN (
                        SELECT DISTINCT user_id FROM {$wpdb->prefix}vortex_transactions 
                        WHERE transaction_time >= %s AND status = 'completed'
                    ) as t ON v.user_id = t.user_id",
                $time_constraint,
                $time_constraint,
                $time_constraint,
                $time_constraint
            );
            
            $funnel_metrics = $wpdb->get_row($query);
            
            // Calculate conversion rates
            $view_to_like_rate = 0;
            $like_to_cart_rate = 0;
            $cart_to_purchase_rate = 0;
            $overall_conversion_rate = 0;
            
            if ($funnel_metrics) {
                if ($funnel_metrics->unique_viewers > 0) {
                    $view_to_like_rate = round(($funnel_metrics->users_who_liked / $funnel_metrics->unique_viewers) * 100, 2);
                    $overall_conversion_rate = round(($funnel_metrics->users_who_purchased / $funnel_metrics->unique_viewers) * 100, 2);
                }
                
                if ($funnel_metrics->users_who_liked > 0) {
                    $like_to_cart_rate = round(($funnel_metrics->users_who_added_to_cart / $funnel_metrics->users_who_liked) * 100, 2);
                }
                
                if ($funnel_metrics->users_who_added_to_cart > 0) {
                    $cart_to_purchase_rate = round(($funnel_metrics->users_who_purchased / $funnel_metrics->users_who_added_to_cart) * 100, 2);
                }
            }
            
            // Get funnel metrics per price range
            $price_ranges = array(
                array('min' => 0, 'max' => 50, 'label' => 'Under $50'),
                array('min' => 50, 'max' => 100, 'label' => '$50-$100'),
                array('min' => 100, 'max' => 250, 'label' => '$100-$250'),
                array('min' => 250, 'max' => 500, 'label' => '$250-$500'),
                array('min' => 500, 'max' => 1000, 'label' => '$500-$1000'),
                array('min' => 1000, 'max' => PHP_INT_MAX, 'label' => 'Over $1000')
            );
            
            $price_range_metrics = array();
            
            foreach ($price_ranges as $range) {
                $range_metrics = [];
                $range_query = $wpdb->prepare(
                    "SELECT 
                        COUNT(DISTINCT v.user_id) as unique_viewers,
                        COUNT(DISTINCT l.user_id) as users_who_liked,
                        COUNT(DISTINCT c.user_id) as users_who_added_to_cart,
                        COUNT(DISTINCT t.user_id) as users_who_purchased
                    FROM {$wpdb->prefix}vortex_artworks a
                    LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                        a.artwork_id = v.artwork_id AND
                        v.view_time >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                        a.artwork_id = l.artwork_id AND
                        l.like_time >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_cart_items c ON 
                        a.artwork_id = c.artwork_id AND
                        c.added_date >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                        a.artwork_id = t.artwork_id AND
                        t.transaction_time >= %s AND
                        t.status = 'completed'
                    WHERE a.price > %f AND a.price <= %f",
                    $time_constraint,
                    $time_constraint,
                    $time_constraint,
                    $time_constraint,
                    $range['min'],
                    $range['max']
                );
                
                $range_metrics = $wpdb->get_row($range_query);
                
                // Calculate conversion rate for this price range
                $range_conversion = 0;
                if ($range_metrics && $range_metrics->unique_viewers > 0) {
                    $range_conversion = round(($range_metrics->users_who_purchased / $range_metrics->unique_viewers) * 100, 2);
                }
                
                $price_range_metrics[] = array(
                    'range' => $range['label'],
                    'viewers' => $range_metrics ? $range_metrics->unique_viewers : 0,
                    'likers' => $range_metrics ? $range_metrics->users_who_liked : 0,
                    'cart_adds' => $range_metrics ? $range_metrics->users_who_added_to_cart : 0,
                    'purchasers' => $range_metrics ? $range_metrics->users_who_purchased : 0,
                    'conversion_rate' => $range_conversion
                );
            }
            
            return array(
                'unique_viewers' => $funnel_metrics ? $funnel_metrics->unique_viewers : 0,
                'users_who_liked' => $funnel_metrics ? $funnel_metrics->users_who_liked : 0,
                'users_who_added_to_cart' => $funnel_metrics ? $funnel_metrics->users_who_added_to_cart : 0,
                'users_who_purchased' => $funnel_metrics ? $funnel_metrics->users_who_purchased : 0,
                'view_to_like_rate' => $view_to_like_rate,
                'like_to_cart_rate' => $like_to_cart_rate,
                'cart_to_purchase_rate' => $cart_to_purchase_rate,
                'overall_conversion_rate' => $overall_conversion_rate,
                'price_range_metrics' => $price_range_metrics
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get purchase funnel metrics: ' . $e->getMessage());
            return array(
                'unique_viewers' => 0,
                'users_who_liked' => 0,
                'users_who_added_to_cart' => 0,
                'users_who_purchased' => 0,
                'view_to_like_rate' => 0,
                'like_to_cart_rate' => 0,
                'cart_to_purchase_rate' => 0,
                'overall_conversion_rate' => 0,
                'price_range_metrics' => array()
            );
        }
    }

    /**
     * Initialize behavior metrics
     */
    private function initialize_behavior_metrics() {
        $this->behavior_metrics = array(
            'temporal_patterns' => array(
                'peak_hours' => $this->get_peak_activity_hours(),
                'weekday_distribution' => $this->get_weekday_distribution(),
                'session_duration_avg' => $this->get_average_session_duration()
            ),
            'demographic_insights' => array(
                'region_distribution' => $this->get_region_distribution(),
                'age_group_distribution' => $this->get_age_group_distribution(),
                'gender_distribution' => $this->get_gender_distribution(),
                'language_preferences' => $this->get_language_preferences()
            ),
            'engagement_metrics' => array(
                'view_to_like_ratio' => $this->calculate_view_to_like_ratio(),
                'average_view_duration' => $this->get_average_view_duration(),
                'style_affinity_clusters' => $this->get_style_affinity_clusters()
            ),
            'conversion_metrics' => array(
                'browse_to_purchase_funnel' => $this->get_purchase_funnel_metrics(),
                'abandoned_carts' => $this->get_abandoned_cart_stats(),
                'price_sensitivity_curve' => $this->get_price_sensitivity_data()
            )
        );
    }

    /**
     * Get abandoned cart statistics
     * 
     * @param string $period Time period for analysis
     * @return array Abandoned cart data
     */
    private function get_abandoned_cart_stats($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get overall abandoned cart stats
            $cart_stats = [];
            $query = $wpdb->prepare(
                "SELECT 
                    COUNT(DISTINCT c.id) as total_carts,
                    COUNT(DISTINCT CASE WHEN t.transaction_id IS NULL THEN c.id ELSE NULL END) as abandoned_carts,
                    COUNT(DISTINCT CASE WHEN t.transaction_id IS NOT NULL THEN c.id ELSE NULL END) as converted_carts,
                    SUM(c.cart_total) as total_cart_value,
                    SUM(CASE WHEN t.transaction_id IS NULL THEN c.cart_total ELSE 0 END) as abandoned_value
                FROM {$wpdb->prefix}vortex_carts c
                LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                    c.user_id = t.user_id AND 
                    t.transaction_time >= c.last_updated AND
                    t.transaction_time <= DATE_ADD(c.last_updated, INTERVAL 24 HOUR) AND
                    t.status = 'completed'
                WHERE c.created_at >= %s",
                $time_constraint
            );
            
            $cart_stats = $wpdb->get_row($query);
            
            // Calculate abandonment rate and recovery potential
            $abandonment_rate = 0;
            $avg_abandoned_value = 0;
            $recovery_potential = 0;
            
            if ($cart_stats) {
                if ($cart_stats->total_carts > 0) {
                    $abandonment_rate = round(($cart_stats->abandoned_carts / $cart_stats->total_carts) * 100, 2);
                }
                
                if ($cart_stats->abandoned_carts > 0) {
                    $avg_abandoned_value = round($cart_stats->abandoned_value / $cart_stats->abandoned_carts, 2);
                }
                
                $recovery_potential = round($cart_stats->abandoned_value * 0.25, 2); // Assume 25% recovery rate
            }
            
            // Get abandonment by price range
            $price_ranges = array(
                array('min' => 0, 'max' => 50, 'label' => 'Under $50'),
                array('min' => 50, 'max' => 100, 'label' => '$50-$100'),
                array('min' => 100, 'max' => 250, 'label' => '$100-$250'),
                array('min' => 250, 'max' => 500, 'label' => '$250-$500'),
                array('min' => 500, 'max' => 1000, 'label' => '$500-$1000'),
                array('min' => 1000, 'max' => PHP_INT_MAX, 'label' => 'Over $1000')
            );
            
            $price_range_stats = array();
            
            foreach ($price_ranges as $range) {
                $range_stats = [];
                $range_query = $wpdb->prepare(
                    "SELECT 
                        COUNT(DISTINCT c.id) as total_carts,
                        COUNT(DISTINCT CASE WHEN t.transaction_id IS NULL THEN c.id ELSE NULL END) as abandoned_carts
                    FROM {$wpdb->prefix}vortex_carts c
                    JOIN {$wpdb->prefix}vortex_cart_items i ON c.id = i.cart_id
                    JOIN {$wpdb->prefix}vortex_artworks a ON i.artwork_id = a.artwork_id
                    LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                        c.user_id = t.user_id AND 
                        t.transaction_time >= c.last_updated AND
                        t.transaction_time <= DATE_ADD(c.last_updated, INTERVAL 24 HOUR) AND
                        t.status = 'completed'
                    WHERE c.created_at >= %s
                    AND a.price > %f AND a.price <= %f
                    GROUP BY a.price > %f AND a.price <= %f",
                    $time_constraint,
                    $range['min'],
                    $range['max'],
                    $range['min'],
                    $range['max']
                );
                
                $range_stats = $wpdb->get_row($range_query);
                
                // Calculate abandonment rate for this price range
                $range_abandonment_rate = 0;
                if ($range_stats && $range_stats->total_carts > 0) {
                    $range_abandonment_rate = round(($range_stats->abandoned_carts / $range_stats->total_carts) * 100, 2);
                }
                
                $price_range_stats[] = array(
                    'range' => $range['label'],
                    'total_carts' => $range_stats ? $range_stats->total_carts : 0,
                    'abandoned_carts' => $range_stats ? $range_stats->abandoned_carts : 0,
                    'abandonment_rate' => $range_abandonment_rate
                );
            }
            
            // Get top reasons for abandonment (from user feedback and exit surveys)
            $abandonment_reasons = [];
            // $reasons_query = $wpdb->prepare(
            //     "SELECT 
            //         abandonment_reason,
            //         COUNT(*) as count
            //     FROM {$wpdb->prefix}vortex_cart_abandonment_feedback
            //     WHERE feedback_time >= %s
            //     GROUP BY abandonment_reason
            //     ORDER BY count DESC
            //     LIMIT 5",
            //     $time_constraint
            // );
            
            // $abandonment_reasons = $wpdb->get_results($reasons_query);
            
            return array(
                'total_carts' => $cart_stats ? $cart_stats->total_carts : 0,
                'abandoned_carts' => $cart_stats ? $cart_stats->abandoned_carts : 0,
                'converted_carts' => $cart_stats ? $cart_stats->converted_carts : 0,
                'total_cart_value' => $cart_stats ? $cart_stats->total_cart_value : 0,
                'abandoned_value' => $cart_stats ? $cart_stats->abandoned_value : 0,
                'abandonment_rate' => $abandonment_rate,
                'avg_abandoned_value' => $avg_abandoned_value,
                'recovery_potential' => $recovery_potential,
                'price_range_stats' => $price_range_stats,
                'abandonment_reasons' => $abandonment_reasons
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get abandoned cart stats: ' . $e->getMessage());
            return array(
                'total_carts' => 0,
                'abandoned_carts' => 0,
                'converted_carts' => 0,
                'total_cart_value' => 0,
                'abandoned_value' => 0,
                'abandonment_rate' => 0,
                'avg_abandoned_value' => 0,
                'recovery_potential' => 0,
                'price_range_stats' => array(),
                'abandonment_reasons' => array()
            );
        }
    }

    /**
     * Get price sensitivity data
     * 
     * @param string $period Time period for analysis
     * @return array Price sensitivity data
     */
    private function get_price_sensitivity_data($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Define price ranges for analysis
            $price_ranges = array(
                array('min' => 0, 'max' => 50, 'label' => 'Under $50'),
                array('min' => 50, 'max' => 100, 'label' => '$50-$100'),
                array('min' => 100, 'max' => 250, 'label' => '$100-$250'),
                array('min' => 250, 'max' => 500, 'label' => '$250-$500'),
                array('min' => 500, 'max' => 1000, 'label' => '$500-$1000'),
                array('min' => 1000, 'max' => PHP_INT_MAX, 'label' => 'Over $1000')
            );
            
            $price_sensitivity = array();
            
            foreach ($price_ranges as $range) {
                $range_stats = [];
                $range_query = $wpdb->prepare(
                    "SELECT 
                        COUNT(DISTINCT v.view_id) as view_count,
                        COUNT(DISTINCT l.id) as like_count,
                        COUNT(DISTINCT c.id) as cart_add_count,
                        COUNT(DISTINCT t.transaction_id) as purchase_count,
                        COALESCE(SUM(t.amount), 0) as total_revenue
                    FROM {$wpdb->prefix}vortex_artworks a
                    LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                        a.artwork_id = v.artwork_id AND
                        v.view_time >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                        a.artwork_id = l.artwork_id AND
                        l.like_time >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_cart_items c ON 
                        a.artwork_id = c.artwork_id AND
                        c.added_date >= %s
                    LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                        a.artwork_id = t.artwork_id AND
                        t.transaction_time >= %s AND
                        t.status = 'completed'
                    WHERE a.price > %f AND a.price <= %f",
                    $time_constraint,
                    $time_constraint,
                    $time_constraint,
                    $time_constraint,
                    $range['min'],
                    $range['max']
                );
                
                $range_stats = $wpdb->get_row($range_query);
                
                // Calculate engagement metrics for this price range
                $view_to_like_rate = 0;
                $like_to_cart_rate = 0;
                $cart_to_purchase_rate = 0;
                $view_to_purchase_rate = 0;
                
                if ($range_stats) {
                    if ($range_stats->view_count > 0) {
                        $view_to_like_rate = round(($range_stats->like_count / $range_stats->view_count) * 100, 2);
                        $view_to_purchase_rate = round(($range_stats->purchase_count / $range_stats->view_count) * 100, 2);
                    }
                    
                    if ($range_stats->like_count > 0) {
                        $like_to_cart_rate = round(($range_stats->cart_add_count / $range_stats->like_count) * 100, 2);
                    }
                    
                    if ($range_stats->cart_add_count > 0) {
                        $cart_to_purchase_rate = round(($range_stats->purchase_count / $range_stats->cart_add_count) * 100, 2);
                    }
                }
                
                $price_sensitivity[] = array(
                    'range' => $range['label'],
                    'view_count' => $range_stats ? $range_stats->view_count : 0,
                    'like_count' => $range_stats ? $range_stats->like_count : 0,
                    'cart_add_count' => $range_stats ? $range_stats->cart_add_count : 0,
                    'purchase_count' => $range_stats ? $range_stats->purchase_count : 0,
                    'total_revenue' => $range_stats ? $range_stats->total_revenue : 0,
                    'view_to_like_rate' => $view_to_like_rate,
                    'like_to_cart_rate' => $like_to_cart_rate,
                    'cart_to_purchase_rate' => $cart_to_purchase_rate,
                    'view_to_purchase_rate' => $view_to_purchase_rate
                );
            }
            
            // Get optimal price points (where conversion rate is highest) by category
            $category_results = [];
            $category_query = $wpdb->prepare(
                "SELECT 
                    c.id,
                    c.category_name,
                    AVG(a.price) as avg_price,
                    ROUND(AVG(a.price), -1) as price_bracket,
                    COUNT(DISTINCT v.view_id) as view_count,
                    COUNT(DISTINCT t.transaction_id) as purchase_count,
                    CASE 
                        WHEN COUNT(DISTINCT v.view_id) > 0 
                        THEN (COUNT(DISTINCT t.transaction_id) / COUNT(DISTINCT v.view_id)) * 100
                        ELSE 0
                    END as conversion_rate
                FROM {$wpdb->prefix}vortex_categories c
                JOIN {$wpdb->prefix}vortex_artworks a ON c.id = a.artwork_id
                LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                    a.artwork_id = v.artwork_id AND
                    v.view_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                    a.artwork_id = t.artwork_id AND
                    t.transaction_time >= %s AND
                    t.status = 'completed'
                GROUP BY c.id, ROUND(a.price, -1)
                HAVING view_count > 20
                ORDER BY c.category_name, conversion_rate DESC",
                $time_constraint,
                $time_constraint
            );
            
            $category_results = $wpdb->get_results($category_query);
            
            // Process to find optimal price points per category
            $optimal_prices = array();
            $current_category = null;
            
            foreach ($category_results as $result) {
                if ($current_category !== $result->id) {
                    $current_category = $result->id;
                    $optimal_prices[$result->id] = array(
                        'category_name' => $result->category_name,
                        'optimal_price' => $result->price_bracket,
                        'conversion_rate' => $result->conversion_rate
                    );
                }
            }
            
            return array(
                'price_sensitivity_by_range' => $price_sensitivity,
                'optimal_price_points' => array_values($optimal_prices)
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get price sensitivity data: ' . $e->getMessage());
            return array(
                'price_sensitivity_by_range' => array(),
                'optimal_price_points' => array()
            );
        }
    }

    /**
     * Get weekday distribution of user activity
     * 
     * @param string $period Time period for calculation
     * @return array Weekday distribution data
     */
    private function get_weekday_distribution($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    DAYOFWEEK(activity_time) as day_of_week,
                    COUNT(*) as activity_count
                FROM {$wpdb->prefix}vortex_user_activity
                WHERE activity_time >= %s
                GROUP BY DAYOFWEEK(activity_time)
                ORDER BY DAYOFWEEK(activity_time)",
                $time_constraint
            );
            
            $results = $wpdb->get_results($query);
            
            // MySQL's DAYOFWEEK returns 1 for Sunday, 2 for Monday, etc.
            // Let's convert to 0 for Sunday, 1 for Monday, etc. to match PHP's convention
            $days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            
            // Initialize all days with zero
            $weekday_distribution = array();
            foreach ($days as $index => $day) {
                $weekday_distribution[$day] = 0;
            }
            
            // Fill in the actual data
            foreach ($results as $row) {
                $day_index = $row->day_of_week - 1; // Adjust for 0-based index
                $weekday_distribution[$days[$day_index]] = intval($row->activity_count);
            }
            
            // Calculate percentages
            $total_activity = array_sum($weekday_distribution);
            $weekday_percentages = array();
            
            if ($total_activity > 0) {
                foreach ($weekday_distribution as $day => $count) {
                    $weekday_percentages[$day] = round(($count / $total_activity) * 100, 2);
                }
            }
            
            return array(
                'distribution' => $weekday_distribution,
                'percentages' => $weekday_percentages,
                'total_activity' => $total_activity
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get weekday distribution: ' . $e->getMessage());
            return array(
                'distribution' => array(),
                'percentages' => array(),
                'total_activity' => 0
            );
        }
    }

    /**
     * Get average session duration for users
     * 
     * @param string $period Time period for calculation
     * @return float Average session duration in minutes
     */
    private function get_average_session_duration($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $avg_duration = 0;
            $query = $wpdb->prepare(
                "SELECT AVG(session_duration) as avg_duration
                FROM {$wpdb->prefix}vortex_user_sessions
                WHERE session_start >= %s
                AND session_duration > 0",
                $time_constraint
            );
            
            $avg_duration = $wpdb->get_var($query);
            
            // Convert to minutes and round to 2 decimal places
            $avg_duration_minutes = round($avg_duration / 60, 2);
            
            return $avg_duration_minutes ? $avg_duration_minutes : 0;
        } catch (Exception $e) {
            $this->log_error('Failed to get average session duration: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get top performing keywords
     * 
     * @param string $period Time period for analysis
     * @return array Top performing keywords data
     */
    private function get_top_performing_keywords($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get top search keywords by search volume
            $top_search_terms = [];
            $search_query = $wpdb->prepare(
                "SELECT 
                    search_term,
                    COUNT(*) as search_count,
                    COUNT(DISTINCT user_id) as unique_searchers,
                    AVG(results_count) as avg_results,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                    CASE 
                        WHEN COUNT(*) > 0 
                        THEN (SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100
                        ELSE 0
                    END as conversion_rate
                FROM {$wpdb->prefix}vortex_searches
                WHERE search_time >= %s
                GROUP BY search_term
                HAVING search_count > 5
                ORDER BY search_count DESC
                LIMIT 20",
                $time_constraint
            );
            
            $top_search_terms = $wpdb->get_results($search_query);
            
            // Get top converting keywords
            $top_converting_terms = [];
            $conversion_query = $wpdb->prepare(
                "SELECT 
                    search_term,
                    COUNT(*) as search_count,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                    CASE 
                        WHEN COUNT(*) > 0 
                        THEN (SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100
                        ELSE 0
                    END as conversion_rate
                FROM {$wpdb->prefix}vortex_searches
                WHERE search_time >= %s
                GROUP BY search_term
                HAVING search_count > 5 AND conversions > 0
                ORDER BY conversion_rate DESC, conversions DESC
                LIMIT 20",
                $time_constraint
            );
            
            $top_converting_terms = $wpdb->get_results($conversion_query);
            
            // Get keywords associated with highest revenue
            $top_revenue_terms = [];
            $revenue_query = $wpdb->prepare(
                "SELECT 
                    s.search_term,
                    COUNT(DISTINCT t.transaction_id) as purchases,
                    SUM(t.amount) as total_revenue,
                    SUM(t.amount) / COUNT(DISTINCT t.transaction_id) as avg_order_value
                FROM {$wpdb->prefix}vortex_searches s
                JOIN {$wpdb->prefix}vortex_search_transactions st ON s.id = st.search_id
                JOIN {$wpdb->prefix}vortex_transactions t ON st.transaction_id = t.transaction_id
                WHERE s.search_time >= %s
                AND t.status = 'completed'
                GROUP BY s.search_term
                HAVING purchases > 2
                ORDER BY total_revenue DESC
                LIMIT 20",
                $time_constraint
            );
            
            $top_revenue_terms = $wpdb->get_results($revenue_query);
            
            // Get keywords with low results but high search volume (content gap opportunities)
            $opportunity_terms = [];
            $opportunity_query = $wpdb->prepare(
                "SELECT 
                    search_term,
                    COUNT(*) as search_count,
                    AVG(results_count) as avg_results
                FROM {$wpdb->prefix}vortex_searches
                WHERE search_time >= %s
                GROUP BY search_term
                HAVING search_count > 10 AND avg_results < 5
                ORDER BY search_count DESC
                LIMIT 20",
                $time_constraint
            );
            
            $opportunity_terms = $wpdb->get_results($opportunity_query);
            
            return array(
                'top_search_terms' => $top_search_terms,
                'top_converting_terms' => $top_converting_terms,
                'top_revenue_terms' => $top_revenue_terms,
                'opportunity_terms' => $opportunity_terms
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get top performing keywords: ' . $e->getMessage());
            return array(
                'top_search_terms' => array(),
                'top_converting_terms' => array(),
                'top_revenue_terms' => array(),
                'opportunity_terms' => array()
            );
        }
    }

    /**
     * Get trending search terms
     * 
     * @param string $period Time period for analysis
     * @return array Trending search terms data
     */
    private function get_trending_search_terms($period = 'month') {
        try {
            global $wpdb;
            
            $current_period = $this->get_time_constraint($period);
            $previous_period = $this->get_time_constraint($period, true);
            
            // Get trending search terms (highest growth in searches)
            $trending_terms = [];
            $query = $wpdb->prepare(
                "SELECT 
                    search_term,
                    COUNT(CASE WHEN search_time >= %s THEN 1 ELSE NULL END) as current_period_searches,
                    COUNT(CASE WHEN search_time >= %s AND search_time < %s THEN 1 ELSE NULL END) as previous_period_searches
                FROM {$wpdb->prefix}vortex_searches
                WHERE search_time >= %s
                GROUP BY search_term
                HAVING COUNT(CASE WHEN search_time >= %s THEN 1 ELSE NULL END) > 5 
                AND COUNT(CASE WHEN search_time >= %s AND search_time < %s THEN 1 ELSE NULL END) > 0 
                ORDER BY 
                (COUNT(CASE WHEN search_time >= %s THEN 1 ELSE NULL END) - COUNT(CASE WHEN search_time >= %s AND search_time < %s THEN 1 ELSE NULL END)) DESC 
                LIMIT 30",
                $current_period,
                $previous_period,
                $current_period,
                $previous_period,
                $current_period,
                $previous_period,
                $current_period,
                $current_period,
                $previous_period,
                $current_period,
            );
            
            $trending_terms = $wpdb->get_results($query);
            
            // Calculate growth rates and add to results
            $processed_terms = array();
            foreach ($trending_terms as $term) {
                $growth_rate = 0;
                if ($term->previous_period_searches > 0) {
                    $growth_rate = round((($term->current_period_searches - $term->previous_period_searches) / $term->previous_period_searches) * 100, 2);
                }
                
                $processed_terms[] = array(
                    'term' => $term->search_term,
                    'current_searches' => $term->current_period_searches,
                    'previous_searches' => $term->previous_period_searches,
                    'growth_rate' => $growth_rate
                );
            }
            
            // Find new trending terms (not present in previous period)
            $new_trending_terms = [];
            $new_query = $wpdb->prepare(
                "SELECT 
                    s1.search_term,
                    COUNT(*) as search_count
                FROM {$wpdb->prefix}vortex_searches s1
                WHERE s1.search_time >= %s
                AND NOT EXISTS (
                    SELECT 1 FROM {$wpdb->prefix}vortex_searches s2
                    WHERE s2.search_term = s1.search_term
                    AND s2.search_time >= %s AND s2.search_time < %s
                )
                GROUP BY s1.search_term
                HAVING search_count > 3
                ORDER BY search_count DESC
                LIMIT 20",
                $current_period,
                $previous_period,
                $current_period
            );
            
            $new_trending_terms = $wpdb->get_results($new_query);
            
            // Get trending terms by category
            $category_trends = [];
            $category_query = $wpdb->prepare(
                "SELECT 
                    c.id,
                    c.category_name,
                    s.search_term,
                    COUNT(*) as search_count
                FROM {$wpdb->prefix}vortex_searches s
                JOIN {$wpdb->prefix}vortex_search_artwork_clicks sac ON s.id = sac.search_id
                JOIN {$wpdb->prefix}vortex_artworks a ON sac.artwork_id = a.artwork_id
                JOIN {$wpdb->prefix}vortex_categories c ON a.artwork_id = c.id
                WHERE s.search_time >= %s
                GROUP BY c.id, s.search_term
                ORDER BY c.category_name, search_count DESC",
                $current_period
            );
            
            $category_trends = $wpdb->get_results($category_query);
            
            // Process category-specific trending terms
            $trending_by_category = array();
            $current_category = null;
            $category_terms = array();
            
            foreach ($category_trends as $trend) {
                if ($current_category !== $trend->id) {
                    // Save previous category terms if they exist
                    if ($current_category !== null && !empty($category_terms)) {
                        $trending_by_category[] = array(
                            'category_id' => $current_category,
                            'category_name' => $category_name,
                            'terms' => $category_terms
                        );
                    }
                    
                    // Start new category
                    $current_category = $trend->id;
                    $category_name = $trend->category_name;
                    $category_terms = array();
                }
                
                // Add term to current category (limit to top 5 per category)
                if (count($category_terms) < 5) {
                    $category_terms[] = array(
                        'term' => $trend->search_term,
                        'search_count' => $trend->search_count
                    );
                }
            }
            
            // Add the last category if it exists
            if ($current_category !== null && !empty($category_terms)) {
                $trending_by_category[] = array(
                    'category_id' => $current_category,
                    'category_name' => $category_name,
                    'terms' => $category_terms
                );
            }
            
            return array(
                'trending_terms' => $processed_terms,
                'new_trending_terms' => $new_trending_terms,
                'trending_by_category' => $trending_by_category
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get trending search terms: ' . $e->getMessage());
            return array(
                'trending_terms' => array(),
                'new_trending_terms' => array(),
                'trending_by_category' => array()
            );
        }
    }

    /**
     * Generate optimal tags for artwork
     * 
     * @param int $artwork_id Optional artwork ID to generate tags for
     * @param string $period Time period for analysis
     * @return array Optimal tags data
     */
    private function generate_optimal_tags($artwork_id = null, $period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get artwork details if specified
            $artwork_data = null;
            if ($artwork_id) {
                $artwork_data = [];
                $artwork_query = $wpdb->prepare(
                    "SELECT 
                        a.title,
                        a.description,
                        s.style_name,
                        c.category_name,
                        GROUP_CONCAT(DISTINCT t.tag_name SEPARATOR ',') as existing_tags
                    FROM {$wpdb->prefix}vortex_artworks a
                    LEFT JOIN {$wpdb->prefix}vortex_art_styles s ON a.style_id = s.id
                    LEFT JOIN {$wpdb->prefix}vortex_categories c ON a.category_id = c.id
                    LEFT JOIN {$wpdb->prefix}vortex_artwork_tags at ON a.artwork_id = at.artwork_id
                    LEFT JOIN {$wpdb->prefix}vortex_tags t ON at.tag_id = t.tag_id
                    WHERE a.artwork_id = %d
                    GROUP BY a.artwork_id",
                    $artwork_id
                );
                
                $artwork_data = $wpdb->get_row($artwork_query);
            }
            
            // Get top converting search terms
            $converting_terms = [];
            $converting_terms_query = $wpdb->prepare(
                "SELECT 
                    search_term,
                    COUNT(*) as search_count,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                    (SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as conversion_rate
                FROM {$wpdb->prefix}vortex_searches
                WHERE search_time >= %s
                GROUP BY search_term
                HAVING search_count > 5 AND conversion_rate > 1
                ORDER BY conversion_rate DESC, conversions DESC
                LIMIT 100",
                $time_constraint
            );
            
            $converting_terms = $wpdb->get_results($converting_terms_query);
            
            // Get trending tags
            $trending_tags = [];
            $trending_tags_query = $wpdb->prepare(
                "SELECT 
                    t.tag_name,
                    COUNT(DISTINCT at.artwork_id) as artwork_count,
                    COUNT(DISTINCT v.view_id) as view_count,
                    COUNT(DISTINCT tr.transaction_id) as transaction_count
                FROM {$wpdb->prefix}vortex_tags t
                JOIN {$wpdb->prefix}vortex_artwork_tags at ON t.tag_id = at.tag_id
                LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                    at.artwork_id = v.artwork_id AND 
                    v.view_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_transactions tr ON 
                    at.artwork_id = tr.artwork_id AND 
                    tr.transaction_time >= %s AND
                    tr.status = 'completed'
                GROUP BY t.tag_id
                HAVING view_count > 10
                ORDER BY transaction_count DESC, view_count DESC
                LIMIT 50",
                $time_constraint,
                $time_constraint
            );
            
            $trending_tags = $wpdb->get_results($trending_tags_query);
            
            // Generate optimal tags
            $optimal_tags = array();
            
            // If we have specific artwork data, generate personalized tags
            if ($artwork_data) {
                // Extract keywords from artwork title and description
                $artwork_keywords = $this->extract_keywords($artwork_data->title . ' ' . $artwork_data->description);
                
                // Add style and category as tags
                $base_tags = array(
                    $artwork_data->style_name,
                    $artwork_data->category_name
                );
                
                // Add existing tags
                $existing_tags = $artwork_data->existing_tags ? explode(',', $artwork_data->existing_tags) : array();
                
                // Combine all potential tags
                $all_potential_tags = array_merge($base_tags, $artwork_keywords, $existing_tags);
                
                // Find matching converting search terms
                $matching_converting_terms = array();
                foreach ($converting_terms as $term) {
                    foreach ($all_potential_tags as $tag) {
                        if (stripos($term->search_term, $tag) !== false || stripos($tag, $term->search_term) !== false) {
                            $matching_converting_terms[] = $term->search_term;
                            break;
                        }
                    }
                }
                
                // Filter trending tags relevant to this artwork
                $relevant_trending_tags = array();
                foreach ($trending_tags as $tag) {
                    foreach ($all_potential_tags as $artwork_tag) {
                        if (stripos($tag->tag_name, $artwork_tag) !== false || stripos($artwork_tag, $tag->tag_name) !== false) {
                            $relevant_trending_tags[] = $tag->tag_name;
                            break;
                        }
                    }
                }
                
                // Combine and deduplicate tags
                $optimal_tags = array_unique(array_merge($base_tags, $matching_converting_terms, $relevant_trending_tags));
                
                // Limit to top 15 tags
                $optimal_tags = array_slice($optimal_tags, 0, 15);
                
                return array(
                    'artwork_id' => $artwork_id,
                    'existing_tags' => $existing_tags,
                    'optimal_tags' => $optimal_tags,
                    'base_tags' => $base_tags,
                    'converting_terms' => $matching_converting_terms,
                    'trending_tags' => $relevant_trending_tags
                );
            } else {
                // Return general tag recommendations
                $optimal_tags = array(
                    'top_converting_terms' => array_column($converting_terms, 'search_term'),
                    'trending_tags' => array_column($trending_tags, 'tag_name')
                );
                
                return $optimal_tags;
            }
        } catch (Exception $e) {
            $this->log_error('Failed to generate optimal tags: ' . $e->getMessage());
            return array(
                'artwork_id' => $artwork_id,
                'existing_tags' => array(),
                'optimal_tags' => array(),
                'error' => $e->getMessage()
            );
        }
    }

    /**
     * Get popular styles based on various metrics
     * 
     * @param string $period Time period for analysis
     * @return array Popular styles data with success/error status
     */
    private function get_popular_styles($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            // Get styles ranked by different metrics
            $styles = [];
            $query = $wpdb->prepare(
                "SELECT 
                    s.id,
                    s.style_name,
                    COUNT(DISTINCT a.artwork_id) as artwork_count,
                    COUNT(DISTINCT v.view_id) as view_count,
                    COUNT(DISTINCT l.id) as like_count,
                    COUNT(DISTINCT t.transaction_id) as purchase_count,
                    COALESCE(SUM(t.amount), 0) as sales_value,
                    CASE 
                        WHEN COUNT(DISTINCT v.view_id) > 0 
                        THEN COUNT(DISTINCT t.transaction_id) / COUNT(DISTINCT v.view_id)
                        ELSE 0
                    END as conversion_rate
                FROM {$wpdb->prefix}vortex_art_styles s
                JOIN {$wpdb->prefix}vortex_artworks a ON s.id = a.style_id
                LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON 
                    a.artwork_id = v.artwork_id AND 
                    v.view_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_artwork_likes l ON 
                    a.artwork_id = l.artwork_id AND 
                    l.like_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_transactions t ON 
                    a.artwork_id = t.artwork_id AND 
                    t.transaction_time >= %s AND
                    t.status = 'completed'
                GROUP BY s.id
                HAVING artwork_count > 0",
                $time_constraint,
                $time_constraint,
                $time_constraint
            );
            
            $styles = $wpdb->get_results($query);
            
            // Calculate engagement scores and organize by different metrics
            $processed_styles = array();
            $by_views = array();
            $by_likes = array();
            $by_purchases = array();
            $by_conversion = array();
            $by_revenue = array();
            
            foreach ($styles as $style) {
                // Calculate engagement score (weighted combination of metrics)
                $view_weight = 1;
                $like_weight = 5;
                $purchase_weight = 20;
                
                $engagement_score = 
                    ($style->view_count * $view_weight) + 
                    ($style->like_count * $like_weight) + 
                    ($style->purchase_count * $purchase_weight);
                
                $processed_style = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'artwork_count' => $style->artwork_count,
                    'view_count' => $style->view_count,
                    'like_count' => $style->like_count,
                    'purchase_count' => $style->purchase_count,
                    'sales_value' => round($style->sales_value, 2),
                    'conversion_rate' => round($style->conversion_rate * 100, 2),
                    'engagement_score' => $engagement_score
                );
                
                $processed_styles[] = $processed_style;
                
                // Add to specific metric arrays
                $by_views[] = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'value' => $style->view_count
                );
                
                $by_likes[] = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'value' => $style->like_count
                );
                
                $by_purchases[] = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'value' => $style->purchase_count
                );
                
                $by_conversion[] = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'value' => round($style->conversion_rate * 100, 2)
                );
                
                $by_revenue[] = array(
                    'style_id' => $style->id,
                    'style_name' => $style->style_name,
                    'value' => round($style->sales_value, 2)
                );
            }
            
            // Sort by engagement score
            usort($processed_styles, function($a, $b) {
                return $b['engagement_score'] <=> $a['engagement_score'];
            });
            
            // Sort by specific metrics
            usort($by_views, function($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            usort($by_likes, function($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            usort($by_purchases, function($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            usort($by_conversion, function($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            usort($by_revenue, function($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            // Limit each category to top 10
            $by_views = array_slice($by_views, 0, 10);
            $by_likes = array_slice($by_likes, 0, 10);
            $by_purchases = array_slice($by_purchases, 0, 10);
            $by_conversion = array_slice($by_conversion, 0, 10);
            $by_revenue = array_slice($by_revenue, 0, 10);
            
            return array(
                'status' => 'success',
                'data' => array(
                    'all_styles' => $processed_styles,
                    'by_views' => $by_views,
                    'by_likes' => $by_likes,
                    'by_purchases' => $by_purchases,
                    'by_conversion' => $by_conversion,
                    'by_revenue' => $by_revenue
                )
            );
        } catch (Exception $e) {
            $this->log_error('Failed to get popular styles: ' . $e->getMessage());
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Initialize marketing data
     */
    private function initialize_marketing_data() {
        $this->marketing_data = array(
            'seo' => array(
                'top_keywords' => $this->get_top_performing_keywords(),
                'trending_search_terms' => $this->get_trending_search_terms(),
                'optimal_tags' => $this->generate_optimal_tags()
            ),
            'content_strategy' => array(
                'popular_styles' => $this->get_popular_styles(),
                'emerging_themes' => $this->get_emerging_themes(),
                'content_gaps' => $this->identify_content_gaps()
            ),
            'user_acquisition' => array(
                'top_referral_sources' => $this->get_top_referral_sources(),
                'campaign_performance' => $this->get_campaign_performance(),
                'user_retention_rates' => $this->get_retention_rates()
            ),
            'social_impact' => array(
                'sharing_analytics' => $this->get_social_sharing_analytics(),
                'viral_content_patterns' => $this->analyze_viral_content(),
                'hashtag_effectiveness' => $this->analyze_hashtag_effectiveness()
            )
        );
    }
    
    /**
     * Initialize trend tracking
     */
    private function initialize_trend_tracking() {
        $this->update_trend_data();
    }
    
    /**
     * Update trend data
     */
    public function update_trend_data() {
        // Internal platform trends
        $platform_trends = $this->analyze_platform_trends();
        
        // External art market trends
        $external_trends = $this->fetch_external_art_trends();
        
        // Correlate internal and external trends
        $correlated_trends = $this->correlate_trends($platform_trends, $external_trends);
        
        $this->current_trends = array(
            'platform' => $platform_trends,
            'external' => $external_trends,
            'correlated' => $correlated_trends,
            'last_updated' => current_time('timestamp')
        );
        
        update_option('vortex_cloe_current_trends', $this->current_trends);
        
        return $this->current_trends;
    }
    
    /**
     * Get personalized greeting for user
     */
    public function get_personalized_greeting($user_id = 0) {
        // Get current user if not specified
        if ($user_id === 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        
        // Default greeting for non-logged in users
        if ($user_id === 0) {
            $time_category = $this->get_time_of_day_category();
            $greeting_templates = $this->greeting_templates['time_based'][$time_category];
            $greeting = $greeting_templates[array_rand($greeting_templates)];
            return sprintf($greeting, 'creative explorer');
        }
        
        // Get user data
        $user = get_userdata($user_id);
        $display_name = $user->display_name;
        
        // Check for user activity data
        $last_login = get_user_meta($user_id, 'vortex_last_login', true);
        $login_count = get_user_meta($user_id, 'vortex_login_count', true);
        $artwork_count = $this->get_user_artwork_count($user_id);
        $has_sales = $this->user_has_sales($user_id);
        $preferred_styles = $this->get_user_preferred_styles($user_id);
        $followed_trends = $this->get_user_followed_trends($user_id);
        
        // Determine greeting type
        $greeting_type = $this->determine_greeting_type($user_id, $last_login, $login_count, $artwork_count, $has_sales);
        
        // Generate appropriate greeting
        switch ($greeting_type) {
            case 'returning_short':
                $templates = $this->greeting_templates['returning_user']['short_absence'];
                $greeting = $templates[array_rand($templates)];
                return sprintf($greeting, $display_name);
                
            case 'returning_long':
                $templates = $this->greeting_templates['returning_user']['long_absence'];
                $greeting = $templates[array_rand($templates)];
                return sprintf($greeting, $display_name);
                
            case 'milestone':
                $templates = $this->greeting_templates['achievement_based']['new_milestone'];
                $greeting = $templates[array_rand($templates)];
                return sprintf($greeting, $display_name, $artwork_count);
                
            case 'first_sale':
                $templates = $this->greeting_templates['achievement_based']['first_sale'];
                $greeting = $templates[array_rand($templates)];
                return sprintf($greeting, $display_name);
                
            case 'trend_following':
                if (!empty($followed_trends)) {
                    $trend = $followed_trends[array_rand($followed_trends)];
                    $templates = $this->greeting_templates['trend_based']['following_trends'];
                    $greeting = $templates[array_rand($templates)];
                    return sprintf($greeting, $trend, $display_name);
                }
                // Fall through to default if no trends
                
            case 'style_consistent':
                if (!empty($preferred_styles)) {
                    $style = $preferred_styles[array_rand($preferred_styles)];
                    $templates = $this->greeting_templates['style_based']['consistent_style'];
                    $greeting = $templates[array_rand($templates)];
                    return sprintf($greeting, $style, $display_name);
                }
                // Fall through to default if no styles
                
            default:
                // Default to time-based greeting
                $time_category = $this->get_time_of_day_category();
                $greeting_templates = $this->greeting_templates['time_based'][$time_category];
                $greeting = $greeting_templates[array_rand($greeting_templates)];
                return sprintf($greeting, $display_name);
        }
    }
    
    /**
     * Determine which type of greeting to use
     */
    private function determine_greeting_type($user_id, $last_login, $login_count, $artwork_count, $has_sales) {
        // Just made first sale
        if ($this->is_recent_first_sale($user_id)) {
            return 'first_sale';
        }
        
        // Recent milestone achievement
        if ($this->is_recent_milestone($user_id, $artwork_count)) {
            return 'milestone';
        }
        
        // Long absence (more than 30 days)
        if ($last_login && (time() - $last_login) > 30 * DAY_IN_SECONDS) {
            return 'returning_long';
        }
        
        // Short absence (2-30 days)
        if ($last_login && (time() - $last_login) > 2 * DAY_IN_SECONDS) {
            return 'returning_short';
        }
        
        // User follows trends
        if ($this->user_follows_trends($user_id)) {
            return 'trend_following';
        }
        
        // User has consistent style
        if ($this->user_has_consistent_style($user_id)) {
            return 'style_consistent';
        }
        
        // Default time-based greeting
        return 'time_based';
    }
    
    /**
     * Get time of day category
     */
    private function get_time_of_day_category() {
        $hour = (int)current_time('G');
        
        if ($hour >= 5 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'afternoon';
        } elseif ($hour >= 17 && $hour < 22) {
            return 'evening';
        } else {
            return 'night';
        }
    }
    
    /**
     * Track user login
     */
    public function track_user_login($user_login, $user) {
        $user_id = $user->ID;
        
        // Get previous login time
        $last_login = get_user_meta($user_id, 'vortex_last_login', true);
        
        // Update login count
        $login_count = (int)get_user_meta($user_id, 'vortex_login_count', true);
        $login_count++;
        update_user_meta($user_id, 'vortex_login_count', $login_count);
        
        // Update last login time
        update_user_meta($user_id, 'vortex_last_login', time());
        
        // Record login for trend analysis
        $this->record_user_event($user_id, 'login', array(
            'timestamp' => time(),
            'previous_login' => $last_login
        ));
        
        // Prepare for AI learning
        do_action('vortex_ai_agent_learn', 'CLOE', 'user_login', array(
            'user_id' => $user_id,
            'login_count' => $login_count,
            'last_login' => $last_login,
            'timestamp' => time()
        ));
    }

    /**
     * Record user event
     *
     * @param int $user_id User ID
     * @param string $event_type Event type
     * @param array $event_data Event data
     * @return int|false The ID of the inserted record, or false on failure
     */
    public function record_user_event($user_id, $event_type, $event_data = array()) {
        // Check if VORTEX_User_Events class exists
        if (!class_exists('VORTEX_User_Events')) {
            require_once plugin_dir_path(__FILE__) . 'includes/class-vortex-user-events.php';
        }
        
        // Get user events instance
        $user_events = VORTEX_User_Events::get_instance();
        
        // Record event
        $event_id = $user_events->record_event($user_id, $event_type, $event_data);
        
        // Also send to AI learning system if event was recorded successfully
        if ($event_id) {
            do_action('vortex_ai_agent_learn', 'CLOE', $event_type, array(
                'user_id' => $user_id,
                'event_data' => $event_data,
                'timestamp' => time()
            ));
        }
        
        return $event_id;
    }
    
    /**
     * Get personalized recommendations for user
     */
    public function get_personalized_recommendations($user_id = 0, $type = 'artwork', $limit = 5) {
        // Get current user if not specified
        if ($user_id === 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
        }
        
        // For non-logged in users, return trending items
        if ($user_id === 0) {
            return $this->get_trending_items($type, $limit);
        }
        
        // Get user preferences
        $user_preferences = $this->get_user_preferences($user_id);
        
        // Get recommendations based on type
        switch ($type) {
            case 'artwork':
                $recommendations = $this->recommend_artwork($user_id, $user_preferences, $limit);
                break;
                
            case 'artist':
                $recommendations = $this->recommend_artists($user_id, $user_preferences, $limit);
                break;
                
            case 'style':
                $recommendations = $this->recommend_styles($user_id, $user_preferences, $limit);
                break;
                
            case 'feature':
                $recommendations = $this->recommend_features($user_id, $user_preferences, $limit);
                break;
                
            default:
                $recommendations = $this->recommend_artwork($user_id, $user_preferences, $limit);
        }
        
        // Track recommendation generation
        do_action('vortex_ai_agent_learn', 'CLOE', 'recommendations_generated', array(
            'user_id' => $user_id,
            'type' => $type,
            'recommendations' => $recommendations,
            'preferences_used' => $user_preferences,
            'timestamp' => time()
        ));
        
        return $recommendations;
    }
    
    /**
     * Generate SEO report with keywords and hashtags
     */
    public function generate_seo_report() {
        // Analyze platform content
        $content_analysis = $this->analyze_platform_content();
        
        // Get trending external keywords
        $external_keywords = $this->get_external_trending_keywords();
        
        // Generate optimal keywords
        $optimal_keywords = $this->generate_optimal_keywords($content_analysis, $external_keywords);
        
        // Generate hashtag recommendations
        $hashtag_recommendations = $this->generate_hashtag_recommendations($optimal_keywords);
        
        // Create the report
        $report = array(
            'timestamp' => time(),
            'content_analysis' => $content_analysis,
            'optimal_keywords' => $optimal_keywords,
            'hashtag_recommendations' => $hashtag_recommendations,
            'keyword_trends' => array(
                'rising' => $this->get_rising_keywords(),
                'falling' => $this->get_falling_keywords(),
                'stable' => $this->get_stable_keywords()
            ),
            'seo_recommendations' => $this->generate_seo_recommendations(),
            'keyword_difficulty' => $this->analyze_keyword_difficulty($optimal_keywords),
            'content_gaps' => $this->identify_content_gaps()
        );
        
        // Save the report
        update_option('vortex_cloe_latest_seo_report', $report);
        
        // Notify admin if enabled
        if (get_option('vortex_cloe_seo_notifications', false)) {
            $this->notify_admin_of_seo_report();
        }
        
        return $report;
    }
    
    /**
     * Generate monthly analytics report
     */
    public function generate_monthly_analytics() {
        // Collect and analyze data
        $time_period = array(
            'start' => strtotime('first day of last month midnight'),
            'end' => strtotime('last day of last month 23:59:59')
        );
        
        $user_metrics = $this->analyze_user_metrics($time_period);
        $content_metrics = $this->analyze_content_metrics($time_period);
        $financial_metrics = $this->analyze_financial_metrics($time_period);
        $engagement_metrics = $this->analyze_engagement_metrics($time_period);
        
        // Demographic breakdown
        $demographic_analysis = array(
            'age_groups' => $this->analyze_age_group_activity($time_period),
            'genders' => $this->analyze_gender_activity($time_period),
            'regions' => $this->analyze_regional_activity($time_period),
            'languages' => $this->analyze_language_activity($time_period)
        );
        
        // Time-based analysis
        $temporal_analysis = array(
            'hourly_activity' => $this->analyze_hourly_activity($time_period),
            'weekday_activity' => $this->analyze_weekday_activity($time_period),
            'monthly_trends' => $this->analyze_monthly_trends()
        );
        
        // Compile full report
        $report = array(
            'period' => $time_period,
            'generated_at' => time(),
            'user_metrics' => $user_metrics,
            'content_metrics' => $content_metrics,
            'financial_metrics' => $financial_metrics,
            'engagement_metrics' => $engagement_metrics,
            'demographic_analysis' => $demographic_analysis,
            'temporal_analysis' => $temporal_analysis,
            'recommendations' => $this->generate_data_based_recommendations($user_metrics, $content_metrics, $financial_metrics, $engagement_metrics)
        );
        
        // Save the report
        update_option('vortex_cloe_latest_monthly_report', $report);
        
        // Notify admin if enabled
        if (get_option('vortex_cloe_monthly_report_notifications', true)) {
            $this->notify_admin_of_monthly_report();
        }
        
        return $report;
    }
    
    /**
     * AJAX handler for getting personalized greeting
     */
    public function ajax_get_greeting() {
        check_ajax_referer('vortex_cloe_nonce', 'security');
        
        $user_id = get_current_user_id(); // 0 if not logged in
        
        $greeting = $this->get_personalized_greeting($user_id);
        
        wp_send_json_success(array(
            'greeting' => $greeting
        ));
    }
    
    /**
     * AJAX handler for getting recommendations
     */
    public function ajax_get_recommendations() {
        check_ajax_referer('vortex_cloe_nonce', 'security');
        
        $user_id = get_current_user_id(); // 0 if not logged in
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'artwork';
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 5;
        
        $recommendations = $this->get_personalized_recommendations($user_id, $type, $limit);
        
        wp_send_json_success(array(
            'recommendations' => $recommendations
        ));
    }
    
    /**
     * Add CLOE admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('CLOE Intelligence', 'vortex-marketplace'),
            __('CLOE Intelligence', 'vortex-marketplace'),
            'manage_options',
            'vortex-cloe-intelligence',
            array($this, 'display_admin_page')
        );
    }

    public function display_admin_page() {
        // Implementation of the display_admin_page method
    }

    /**
     * Check if the CLOE agent is active and functioning
     *
     * @since 1.0.0
     * @return bool Whether the agent is active
     */
    public function is_active() {
        // Check if learning models are initialized
        if (empty($this->learning_models)) {
            return false;
        }
        
        // Check if tracking categories are initialized
        if (empty($this->tracking_categories)) {
            return false;
        }
        
        // Perform a basic health check
        try {
            // Check if at least one model file exists
            $model_exists = false;
            foreach ($this->learning_models as $model) {
                if (file_exists($model['path'])) {
                    $model_exists = true;
                    break;
                }
            }
            
            if (!$model_exists) {
                return false;
            }
            
            // Check if we can write to model files (needed for learning)
            $test_model = reset($this->learning_models);
            $model_dir = dirname($test_model['path']);
            $is_writable = is_writable($model_dir);
            
            return $is_writable;
        } catch (Exception $e) {
            error_log('CLOE health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Correlates internal and external trends
     * 
     * @return array Correlated trend data
     */
    public function correlate_trends() {
        try {
            // Get internal marketplace trends
            $internal_styles = $this->get_popular_styles('month');
            $internal_styles = [];
            $internal_themes = $this->get_emerging_themes('month');
            
            // Get external trends
            $external_trends = $this->fetch_external_art_trends();
            
            // Build correlation data
            $correlations = array();
            
            if ((isset($internal_styles['status']) && isset($internal_styles['data'])) && $internal_styles['status'] === 'success' && $external_trends['status'] === 'success') {
                $internal_style_names = array_column($internal_styles['data'], 'style_name');
                
                // Extract external style names from various sources
                $external_style_names = array();
                foreach ($external_trends['data'] as $source => $data) {
                    if (isset($data['styles'])) {
                        foreach ($data['styles'] as $style) {
                            $external_style_names[] = $style['name'];
                        }
                    }
                }
                
                // Find matching styles
                $matching_styles = array_intersect($internal_style_names, $external_style_names);
                
                // Find trending styles in external sources that aren't popular internally
                $opportunity_styles = array_diff($external_style_names, $internal_style_names);
                
                $correlations['styles'] = array(
                    'matching' => $matching_styles,
                    'opportunities' => $opportunity_styles
                );
            }
            
            // Similar analysis for themes, mediums, price points, etc.
            // ...
            
            return array(
                'status' => 'success',
                'data' => $correlations
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Enables deep learning capabilities for CLOE
     * 
     * @param bool $status Enable or disable deep learning
     * @return bool Success status
     */
    public function enable_deep_learning($status = true) {
        try {
            $this->deep_learning_enabled = (bool) $status;
            update_option('vortex_cloe_deep_learning', $status);
            
            if ($status) {
                // Initialize deep learning components
                $this->initialize_deep_learning_model();
                
                // Set up scheduled tasks for model training
                if (!wp_next_scheduled('vortex_cloe_model_training')) {
                    wp_schedule_event(time(), 'daily', 'vortex_cloe_model_training');
                }
                
                add_action('vortex_cloe_model_training', array($this, 'train_deep_learning_model'));
            } else {
                // Clean up if disabling
                wp_clear_scheduled_hook('vortex_cloe_model_training');
            }
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Failed to enable deep learning: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trains the deep learning model
     */
    public function train_deep_learning_model() {
        try {
            // Get training data
            $training_data = $this->collect_training_data();
            
            // Train model
            $training_result = $this->perform_model_training($training_data);
            
            // Log training metrics
            $this->log_training_metrics($training_result);
            
            // Save updated model weights
            $this->save_model_weights();
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Model training failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Collects training data for the model
     * 
     * @return array Training data
     */
    private function collect_training_data() {
        global $wpdb;
        
        // Get recent analytics outputs with high confidence scores
        $query = "SELECT * FROM {$wpdb->prefix}vortex_cloe_analytics_outputs 
                  WHERE confidence_score >= 0.8
                  ORDER BY created_at DESC
                  LIMIT 1000";
                  
        $analytics_data = $wpdb->get_results($query);
        
        // Process raw data into training examples
        $training_examples = array();
        foreach ($analytics_data as $data) {
            // Transform data into input-output pairs
            $input_data = json_decode($data->input_parameters, true);
            $output_data = json_decode($data->output_data, true);
            
            if ($input_data && $output_data) {
                $training_examples[] = array(
                    'input' => $input_data,
                    'output' => $output_data,
                    'weight' => $data->confidence_score // Use confidence as weight
                );
            }
        }
        
        return $training_examples;
    }

    /**
     * Performs model training on collected data
     * 
     * @param array $training_data Training examples
     * @return array Training results and metrics
     */
    private function perform_model_training($training_data) {
        // This would typically call an external ML service or library
        // Simulated implementation for framework
        
        $training_metrics = array(
            'examples_processed' => count($training_data),
            'iterations' => 10,
            'initial_loss' => 0.38,
            'final_loss' => 0.29,
            'accuracy_improvement' => 0.09,
        );
        
        return $training_metrics;
    }

    /**
     * Logs training metrics
     * 
     * @param array $metrics Training metrics
     */
    private function log_training_metrics($metrics) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'vortex_ai_training_logs',
            array(
                'model_name' => 'cloe',
                'model_version' => $this->model_version,
                'examples_processed' => $metrics['examples_processed'],
                'iterations' => $metrics['iterations'],
                'initial_loss' => $metrics['initial_loss'],
                'final_loss' => $metrics['final_loss'],
                'accuracy_improvement' => $metrics['accuracy_improvement'],
                'training_time' => time()
            )
        );
    }

    /**
     * Saves updated model weights
     */
    private function save_model_weights() {
        // Implementation to save model weights
        $weights_dir = WP_CONTENT_DIR . '/uploads/vortex/models';
        
        // Ensure directory exists
        if (!file_exists($weights_dir)) {
            wp_mkdir_p($weights_dir);
        }
        
        $weights_path = $weights_dir . '/cloe_weights.dat';
        
        // Save weights logic
        // This would normally interact with ML framework
    }

    /**
     * Sets the learning rate for the AI model
     * 
     * @param float $rate The learning rate value (default: 0.001)
     * @return bool Success status
     */
    public function set_learning_rate($rate = 0.001) {
        try {
            // Validate input
            $rate = floatval($rate);
            if ($rate <= 0 || $rate > 1) {
                throw new Exception('Learning rate must be between 0 and 1');
            }
            
            $this->learning_rate = $rate;
            update_option('vortex_cloe_learning_rate', $rate);
            
            // Apply learning rate to model configuration
            $this->model_config['learning_rate'] = $rate;
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Failed to set learning rate: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enables continuous learning capability
     * 
     * @param bool $status Enable or disable continuous learning
     * @return bool Success status
     */
    public function enable_continuous_learning($status = true) {
        try {
            $this->continuous_learning = (bool) $status;
            update_option('vortex_cloe_continuous_learning', $status);
            
            if ($status) {
                // Initialize continuous learning components
                $this->setup_feedback_loop();
                $this->initialize_learning_pipeline();
            } else {
                // Clean up if disabling
                remove_action('vortex_user_feedback', array($this, 'process_feedback'));
                remove_action('vortex_cloe_analysis', array($this, 'evaluate_analysis'));
                wp_clear_scheduled_hook('vortex_cloe_learning_update');
            }
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Failed to set continuous learning: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sets the context window size for the AI model
     * 
     * @param int $window_size Size of the context window
     * @return bool Success status
     */
    public function set_context_window($window_size = 1000) {
        try {
            // Validate input
            $window_size = intval($window_size);
            if ($window_size < 100 || $window_size > 10000) {
                throw new Exception('Context window size must be between 100 and 10000');
            }
            
            $this->context_window = $window_size;
            update_option('vortex_cloe_context_window', $window_size);
            
            // Apply context window to model configuration
            $this->model_config['context_window'] = $window_size;
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Failed to set context window: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enables cross-learning between different AI agents
     * 
     * @param bool $status Enable or disable cross-learning
     * @return bool Success status
     */
    public function enable_cross_learning($status = true) {
        try {
            $this->cross_learning = (bool) $status;
            update_option('vortex_cloe_cross_learning', $status);
            
            if ($status) {
                // Register with knowledge hub for cross-agent learning
                $this->register_with_knowledge_hub();
                
                // Initialize cross-learning hooks
                add_action('vortex_ai_insight_generated', array($this, 'process_external_insight'), 10, 3);
            } else {
                remove_action('vortex_ai_insight_generated', array($this, 'process_external_insight'));
            }
            
            return true;
        } catch (Exception $e) {
            $this->log_error('Failed to set cross learning: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Private method to initialize deep learning model
     */
    private function initialize_deep_learning_model() {
        // Implementation details for model initialization
        // This would connect to a machine learning backend or service
        
        $this->model_config = array(
            'learning_rate' => $this->learning_rate,
            'context_window' => $this->context_window,
            'layers' => 4,
            'hidden_units' => 256,
            'activation' => 'relu'
        );
        
        // Register model with AI system
        do_action('vortex_register_ai_model', 'cloe', $this->model_config);
    }

    /**
     * Private method to set up the feedback loop for continuous learning
     */
    private function setup_feedback_loop() {
        // Implementation of feedback collection and processing
        add_action('vortex_user_feedback', array($this, 'process_feedback'), 10, 3);
        add_action('vortex_cloe_analysis', array($this, 'evaluate_analysis'), 10, 2);
    }

    /**
     * Private method to initialize the learning pipeline
     */
    private function initialize_learning_pipeline() {
        // Set up scheduled tasks for model updates
        if (!wp_next_scheduled('vortex_cloe_learning_update')) {
            wp_schedule_event(time(), 'daily', 'vortex_cloe_learning_update');
        }
        
        add_action('vortex_cloe_learning_update', array($this, 'update_model_weights'));
    }

    /**
     * Registers this agent with the central knowledge hub
     */
    private function register_with_knowledge_hub() {
        // Register capabilities and knowledge domains with central hub
        $capabilities = array(
            'market_analysis',
            'trend_detection',
            'user_behavior_analysis',
            'content_recommendations',
            'pricing_optimization'
        );
        
        $knowledge_domains = array(
            'art_market_trends',
            'user_preferences',
            'social_sharing_patterns',
            'purchase_behaviors',
            'content_engagement_metrics'
        );
        
        do_action('vortex_register_ai_agent', 'cloe', $capabilities, $knowledge_domains);
    }

    /**
     * Helper method to get time constraint for database queries
     * 
     * @param string $period Time period (day, week, month, year)
     * @param bool $previous Whether to get the previous period
     * @return string SQL-formatted datetime
     */
    private function get_time_constraint($period = 'month', $previous = false) {
        $now = time();
        
        switch($period) {
            case 'day':
                $interval = 1 * DAY_IN_SECONDS;
                break;
            case 'week':
                $interval = 7 * DAY_IN_SECONDS;
                break;
            case 'month':
                $interval = 30 * DAY_IN_SECONDS;
                break;
            case 'quarter':
                $interval = 90 * DAY_IN_SECONDS;
                break;
            case 'year':
                $interval = 365 * DAY_IN_SECONDS;
                break;
            default:
                $interval = 30 * DAY_IN_SECONDS;
        }
        
        if ($previous) {
            return date('Y-m-d H:i:s', $now - (2 * $interval));
        } else {
            return date('Y-m-d H:i:s', $now - $interval);
        }
    }

    /**
     * Helper method to calculate overall average from results
     * 
     * @param array $results Array of database results
     * @param string $field Field name to average
     * @return float Overall average
     */
    private function calculate_overall_average($results, $field) {
        if (empty($results)) {
            return 0;
        }
        
        $sum = 0;
        $count = 0;
        
        foreach ($results as $result) {
            if (isset($result->$field)) {
                $sum += $result->$field;
                $count++;
            }
        }
        
        return $count > 0 ? $sum / $count : 0;
    }

    /**
     * Gets emerging themes in artwork
     * 
     * @param string $period Time period for comparison
     * @return array Emerging themes data
     */
    public function get_emerging_themes($period = 'month') {
        try {
            global $wpdb;
            $results = [];
            $current_period = $this->get_time_constraint($period);
            $previous_period = $this->get_time_constraint($period, true);
            
            $query = $wpdb->prepare(
                "SELECT 
                    t.id,
                    t.theme_name,
                    COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s THEN tr.transaction_id ELSE NULL END) as current_purchases,
                    COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s AND tr.transaction_time < %s THEN tr.transaction_id ELSE NULL END) as previous_purchases
                FROM {$wpdb->prefix}vortex_artwork_themes t
                JOIN {$wpdb->prefix}vortex_artwork_theme_mapping tm ON t.id = tm.theme_id
                JOIN {$wpdb->prefix}vortex_artworks a ON tm.artwork_id = a.artwork_id
                LEFT JOIN {$wpdb->prefix}vortex_transactions tr ON a.artwork_id = tr.artwork_id AND tr.status = 'completed'
                GROUP BY t.id
                HAVING COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s THEN tr.transaction_id ELSE NULL END) > COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s AND tr.transaction_time < %s THEN tr.transaction_id ELSE NULL END)
                ORDER BY (COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s THEN tr.transaction_id ELSE NULL END) - COUNT(DISTINCT CASE WHEN tr.transaction_time >= %s AND tr.transaction_time < %s THEN tr.transaction_id ELSE NULL END)) DESC",
                $current_period,
                $previous_period,
                $current_period,
                $current_period,
                $previous_period,
                $current_period,
                $current_period,
                $previous_period,
                $current_period
            );
            
            $results = $wpdb->get_results($query);
            
            // Calculate growth percentage
            foreach ($results as &$theme) {
                $theme->growth_percentage = $theme->previous_purchases > 0 
                    ? (($theme->current_purchases - $theme->previous_purchases) / $theme->previous_purchases) * 100 
                    : ($theme->current_purchases > 0 ? 100 : 0);
            }
            
            return array(
                'status' => 'success',
                'data' => $results
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Identifies content gaps in the marketplace
     * 
     * @return array Content gap data
     */
    public function identify_content_gaps() {
        try {
            global $wpdb;
            $content_gaps = [];
            $price_gaps = [];
            // Find styles/themes with high search volume but low inventory
            $query = "SELECT 
                        'style' as type,
                        s.id as id,
                        s.style_name as name,
                        COUNT(DISTINCT sr.search_id) as search_count,
                        COUNT(DISTINCT a.artwork_id) as artwork_count,
                        (COUNT(DISTINCT sr.search_id) / GREATEST(COUNT(DISTINCT a.artwork_id), 1)) as demand_supply_ratio
                      FROM {$wpdb->prefix}vortex_art_styles s
                      LEFT JOIN {$wpdb->prefix}vortex_search_results sr ON s.id = sr.style_id
                      LEFT JOIN {$wpdb->prefix}vortex_artworks a ON s.id = a.style_id
                      GROUP BY s.id
                      
                      UNION
                      
                      SELECT 
                        'theme' as type,
                        t.id as id,
                        t.theme_name as name,
                        COUNT(DISTINCT sr.search_id) as search_count,
                        COUNT(DISTINCT tm.artwork_id) as artwork_count,
                        (COUNT(DISTINCT sr.search_id) / GREATEST(COUNT(DISTINCT tm.artwork_id), 1)) as demand_supply_ratio
                      FROM {$wpdb->prefix}vortex_artwork_themes t
                      LEFT JOIN {$wpdb->prefix}vortex_search_results sr ON t.id = sr.theme_id
                      LEFT JOIN {$wpdb->prefix}vortex_artwork_theme_mapping tm ON t.id = tm.theme_id
                      GROUP BY t.id
                      
                      ORDER BY demand_supply_ratio DESC
                      LIMIT 20";
            
            $content_gaps = $wpdb->get_results($query);
            
            // Find price range gaps
            $price_gap_query = "SELECT 
                                  c.id,
                                  c.category_name,
                                  MIN(a.price) as min_price,
                                  MAX(a.price) as max_price,
                                  AVG(a.price) as avg_price,
                                  COUNT(*) as artwork_count,
                                  MAX(a.price) - MIN(a.price) as price_range,
                                  STDDEV(a.price) as price_stddev
                                FROM {$wpdb->prefix}vortex_categories c
                                JOIN {$wpdb->prefix}vortex_artworks a ON c.id = a.category_id
                                GROUP BY c.id
                                HAVING artwork_count > 5 AND price_stddev > 0
                                ORDER BY price_stddev DESC";
            
            $price_gaps = $wpdb->get_results($price_gap_query);
            
            return array(
                'status' => 'success',
                'content_gaps' => $content_gaps,
                'price_gaps' => $price_gaps
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Gets top referral sources to the marketplace
     * 
     * @param string $period Time period for analysis
     * @return array Referral source data
     */
    public function get_top_referral_sources($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    referrer_domain,
                    COUNT(*) as visit_count,
                    COUNT(DISTINCT user_id) as unique_visitors,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions,
                    SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100 as conversion_rate
                FROM {$wpdb->prefix}vortex_referrers
                WHERE visit_time >= %s
                GROUP BY referrer_domain
                ORDER BY conversions DESC, visit_count DESC",
                $time_constraint
            );
            
            $results = $wpdb->get_results($query);
            
            return array(
                'status' => 'success',
                'data' => $results
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Gets campaign performance data
     * 
     * @param string $period Time period for analysis
     * @return array Campaign performance data
     */
    public function get_campaign_performance($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    c.campaign_id,
                    c.campaign_name,
                    c.campaign_type,
                    c.start_date,
                    c.end_date,
                    COUNT(r.visit_id) as total_clicks,
                    COUNT(DISTINCT r.user_id) as unique_visitors,
                    SUM(CASE WHEN r.converted = 1 THEN 1 ELSE 0 END) as conversions,
                    SUM(CASE WHEN r.converted = 1 THEN 1 ELSE 0 END) / COUNT(r.visit_id) * 100 as conversion_rate,
                    SUM(t.amount) as total_revenue,
                    SUM(t.amount) / SUM(CASE WHEN r.converted = 1 THEN 1 ELSE 0 END) as revenue_per_conversion,
                    c.campaign_cost,
                    CASE 
                        WHEN c.campaign_cost > 0 
                        THEN (SUM(t.amount) - c.campaign_cost) / c.campaign_cost * 100 
                        ELSE 0 
                    END as roi
                FROM {$wpdb->prefix}vortex_campaigns c
                LEFT JOIN {$wpdb->prefix}vortex_referrers r ON c.campaign_id = r.campaign_id AND r.visit_time >= %s
                LEFT JOIN {$wpdb->prefix}vortex_transactions t ON r.user_id = t.user_id AND t.status = 'completed' 
                -- AND t.transaction_time >= r.visit_time
                WHERE (c.end_date IS NULL OR c.end_date >= %s)
                GROUP BY c.campaign_id
                ORDER BY roi DESC",
                $time_constraint,
                $time_constraint
            );
            
            $results = $wpdb->get_results($query);
            
            return array(
                'status' => 'success',
                'data' => $results
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Gets user retention rates
     * 
     * @param string $period Time period for analysis
     * @return array Retention rate data
     */
    public function get_retention_rates($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            $new_users = [];
            // Get new users in the period
            $new_users_query = $wpdb->prepare(
                "SELECT 
                    user_id,
                    registration_date
                 FROM {$wpdb->prefix}vortex_users
                 WHERE registration_date >= %s",
                $time_constraint
            );
            
            $new_users = $wpdb->get_results($new_users_query);
            $total_new_users = count($new_users);
            
            if ($total_new_users === 0) {
                return array(
                    'status' => 'success',
                    'data' => array(
                        'total_new_users' => 0,
                        'retention_rates' => array()
                    )
                );
            }
            
            // Calculate retention at various intervals
            $intervals = array(1, 7, 14, 30, 60, 90);
            $retention_rates = array();
            
            foreach ($intervals as $days) {
                $retained_users = 0;
                
                foreach ($new_users as $user) {
                    $retention_date = date('Y-m-d H:i:s', strtotime($user->registration_date . ' + ' . $days . ' days'));
                    
                    if ($retention_date > date('Y-m-d H:i:s')) {
                        // Skip future dates
                        continue;
                    }
                    
                    $has_activity = 0;
                    $activity_check_query = $wpdb->prepare(
                        "SELECT COUNT(*) 
                         FROM {$wpdb->prefix}vortex_user_activity
                         WHERE user_id = %d
                         AND activity_time >= %s",
                        $user->user_id,
                        $retention_date
                    );
                    
                    $has_activity = $wpdb->get_var($activity_check_query);
                    
                    if ($has_activity > 0) {
                        $retained_users++;
                    }
                }
                
                $eligible_users = $total_new_users;
                $future_count = 0;
                
                // Don't count users that haven't reached this retention period
                foreach ($new_users as $user) {
                    $retention_date = date('Y-m-d H:i:s', strtotime($user->registration_date . ' + ' . $days . ' days'));
                    if ($retention_date > date('Y-m-d H:i:s')) {
                        $future_count++;
                    }
                }
                
                $eligible_users -= $future_count;
                
                $retention_rates[$days] = array(
                    'days' => $days,
                    'retained_users' => $retained_users,
                    'eligible_users' => $eligible_users,
                    'retention_rate' => $eligible_users > 0 ? ($retained_users / $eligible_users) * 100 : 0
                );
            }
            
            return array(
                'status' => 'success',
                'data' => array(
                    'total_new_users' => $total_new_users,
                    'retention_rates' => $retention_rates
                )
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Gets social sharing analytics
     * 
     * @param string $period Time period for analysis
     * @return array Social sharing data
     */
    public function get_social_sharing_analytics($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            $platform_stats = [];
            $artwork_stats = [];
            // Get sharing statistics by platform
            $platform_query = $wpdb->prepare(
                "SELECT 
                    platform,
                    COUNT(*) as share_count,
                    COUNT(DISTINCT artwork_id) as artwork_count,
                    COUNT(DISTINCT user_id) as user_count,
                    SUM(click_count) as click_count,
                    SUM(engagement_count) as engagement_count
                FROM {$wpdb->prefix}vortex_social_shares
                WHERE share_time >= %s
                GROUP BY platform
                ORDER BY share_count DESC",
                $time_constraint
            );
            
            $platform_stats = $wpdb->get_results($platform_query);
            
            // Get most shared artworks
            $artwork_query = $wpdb->prepare(
                "SELECT 
                    a.artwork_id,
                    a.title,
                    COUNT(s.id) as share_count,
                    COUNT(DISTINCT s.platform) as platform_count,
                    SUM(s.click_count) as click_count,
                    SUM(s.engagement_count) as engagement_count
                FROM {$wpdb->prefix}vortex_social_shares s
                JOIN {$wpdb->prefix}vortex_artworks a ON s.artwork_id = a.artwork_id
                WHERE s.share_time >= %s
                GROUP BY a.artwork_id
                ORDER BY share_count DESC
                LIMIT 10",
                $time_constraint
            );
            
            $artwork_stats = $wpdb->get_results($artwork_query);
            
            return array(
                'status' => 'success',
                'platform_stats' => $platform_stats,
                'top_shared_artworks' => $artwork_stats
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Analyzes viral content in the marketplace
     * 
     * @param string $period Time period for analysis
     * @return array Viral content analysis
     */
    public function analyze_viral_content($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            $viral_content = [];
            // Find artworks with viral potential (high sharing/engagement ratios)
            // $query = $wpdb->prepare(
            //     "SELECT 
            //         a.artwork_id,
            //         a.title,
            //         a.artist_id,
            //         (SELECT display_name FROM {$wpdb->prefix}vortex_users WHERE user_id = a.artist_id) as artist_name,
            //         COUNT(DISTINCT s.id) as share_count,
            //         SUM(s.click_count) as click_count,
            //         SUM(s.engagement_count) as engagement_count,
            //         COUNT(DISTINCT v.view_id) as view_count,
            //         (COUNT(DISTINCT s.id) / GREATEST(COUNT(DISTINCT v.view_id), 1)) * 100 as share_rate,
            //         (SUM(s.engagement_count) / GREATEST(SUM(s.click_count), 1)) * 100 as engagement_rate
            //     FROM {$wpdb->prefix}vortex_artworks a
            //     LEFT JOIN {$wpdb->prefix}vortex_social_shares s ON a.artwork_id = s.artwork_id AND s.share_time >= %s
            //     LEFT JOIN {$wpdb->prefix}vortex_artwork_views v ON a.artwork_id = v.artwork_id AND v.view_time >= %s
            //     GROUP BY a.artwork_id
            //     HAVING share_count > 0 AND view_count > 10
            //     ORDER BY (share_rate * engagement_rate) DESC
            //     LIMIT 20",
            //     $time_constraint,
            //     $time_constraint
            // );
            
            // $viral_content = $wpdb->get_results($query);
            
            // Calculate virality metrics
            foreach ($viral_content as &$content) {
                $content->virality_score = ($content->share_rate * $content->engagement_rate) / 100;
                $content->viral_coefficient = $content->share_count > 0 ? $content->click_count / $content->share_count : 0;
            }
            
            return array(
                'status' => 'success',
                'data' => $viral_content
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Analyzes hashtag effectiveness
     * 
     * @param string $period Time period for analysis
     * @return array Hashtag effectiveness data
     */
    public function analyze_hashtag_effectiveness($period = 'month') {
        try {
            global $wpdb;
            
            $time_constraint = $this->get_time_constraint($period);
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    h.hashtag,
                    COUNT(DISTINCT s.id) as usage_count,
                    SUM(s.click_count) as click_count,
                    SUM(s.engagement_count) as engagement_count,
                    SUM(s.click_count) / COUNT(DISTINCT s.id) as clicks_per_share,
                    SUM(s.engagement_count) / COUNT(DISTINCT s.id) as engagement_per_share
                FROM {$wpdb->prefix}vortex_social_hashtags h
                JOIN {$wpdb->prefix}vortex_hashtag_share_mapping m ON h.hashtag_id = m.hashtag_id
                JOIN {$wpdb->prefix}vortex_social_shares s ON m.share_id = s.id
                WHERE s.share_time >= %s
                GROUP BY h.hashtag
                HAVING usage_count > 5
                ORDER BY engagement_per_share DESC
                LIMIT 20",
                $time_constraint
            );
            
            $results = $wpdb->get_results($query);
            
            return array(
                'status' => 'success',
                'data' => $results
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Analyzes platform trends
     * 
     * @param string $period Time period for analysis
     * @return array Platform trend data
     */
    public function analyze_platform_trends($period = 'month') {
        try {
            global $wpdb;
            
            $current_period = $this->get_time_constraint($period);
            $previous_period = $this->get_time_constraint($period, true);
            $results = [];
            $query = $wpdb->prepare(
                "SELECT 
                    platform,
                    COUNT(DISTINCT CASE WHEN share_time >= %s THEN id ELSE NULL END) as current_shares,
                    COUNT(DISTINCT CASE WHEN share_time >= %s AND share_time < %s THEN id ELSE NULL END) as previous_shares,
                    SUM(CASE WHEN share_time >= %s THEN click_count ELSE 0 END) as current_clicks,
                    SUM(CASE WHEN share_time >= %s AND share_time < %s THEN click_count ELSE 0 END) as previous_clicks,
                    SUM(CASE WHEN share_time >= %s THEN engagement_count ELSE 0 END) as current_engagement,
                    SUM(CASE WHEN share_time >= %s AND share_time < %s THEN engagement_count ELSE 0 END) as previous_engagement
                FROM {$wpdb->prefix}vortex_social_shares
                WHERE share_time >= %s
                GROUP BY platform",
                $current_period,
                $previous_period,
                $current_period,
                $current_period,
                $previous_period,
                $current_period,
                $current_period,
                $previous_period,
                $current_period,
                $previous_period
            );
            
            $results = $wpdb->get_results($query);
            
            // Calculate growth percentages
            foreach ($results as &$platform) {
                $platform->share_growth = $platform->previous_shares > 0 
                    ? (($platform->current_shares - $platform->previous_shares) / $platform->previous_shares) * 100 
                    : ($platform->current_shares > 0 ? 100 : 0);
                    
                $platform->click_growth = $platform->previous_clicks > 0 
                    ? (($platform->current_clicks - $platform->previous_clicks) / $platform->previous_clicks) * 100 
                    : ($platform->current_clicks > 0 ? 100 : 0);
                    
                $platform->engagement_growth = $platform->previous_engagement > 0 
                    ? (($platform->current_engagement - $platform->previous_engagement) / $platform->previous_engagement) * 100 
                    : ($platform->current_engagement > 0 ? 100 : 0);
            }
            
            return array(
                'status' => 'success',
                'data' => $results
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Fetches external art trends from APIs
     * 
     * @return array External art trend data
     */
    public function fetch_external_art_trends() {
        try {
            $cached_trends = get_transient('vortex_external_art_trends');
            
            if ($cached_trends !== false) {
                return array(
                    'status' => 'success',
                    'source' => 'cache',
                    'data' => $cached_trends
                );
            }
            
            // Initialize results array
            $trends = array();
            
            // Fetch from external APIs - implemented as separate methods for modularity
            $artsy_trends = $this->fetch_artsy_trends();
            if ($artsy_trends['status'] === 'success') {
                $trends['artsy'] = $artsy_trends['data'];
            }
            
            $auction_trends = $this->fetch_auction_trends();
            if ($auction_trends['status'] === 'success') {
                $trends['auctions'] = $auction_trends['data'];
            }
            
            $gallery_trends = $this->fetch_gallery_trends();
            if ($gallery_trends['status'] === 'success') {
                $trends['galleries'] = $gallery_trends['data'];
            }
            
            $social_art_trends = $this->fetch_social_art_trends();
            if ($social_art_trends['status'] === 'success') {
                $trends['social'] = $social_art_trends['data'];
            }
            
            // Cache the results for 12 hours
            set_transient('vortex_external_art_trends', $trends, 12 * HOUR_IN_SECONDS);
            
            return array(
                'status' => 'success',
                'source' => 'api',
                'data' => $trends
            );
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Fetches trends from Artsy API
     * 
     * @return array Artsy trend data
     */
    private function fetch_artsy_trends() {
        // Implementation for fetching from Artsy API
        // This would use the WordPress HTTP API to make external requests
        
        // Simplified mock implementation
        return array(
            'status' => 'success',
            'data' => array(
                'styles' => array(
                    array('name' => 'Abstract Expressionism', 'trend_score' => 89),
                    array('name' => 'Contemporary', 'trend_score' => 95),
                    array('name' => 'Pop Art', 'trend_score' => 82),
                ),
                'mediums' => array(
                    array('name' => 'Digital Art', 'trend_score' => 93),
                    array('name' => 'Painting', 'trend_score' => 88),
                    array('name' => 'Photography', 'trend_score' => 85),
                ),
                'themes' => array(
                    array('name' => 'Nature', 'trend_score' => 91),
                    array('name' => 'Urban', 'trend_score' => 87),
                    array('name' => 'Political', 'trend_score' => 79),
                )
            )
        );
    }

    /**
     * Fetches trends from auction data
     * 
     * @return array Auction trend data
     */
    private function fetch_auction_trends() {
        // Implementation for fetching from auction data sources
        
        // Simplified mock implementation
        return array(
            'status' => 'success',
            'data' => array(
                'styles' => array(
                    array('name' => 'Impressionism', 'trend_score' => 86),
                    array('name' => 'Modern', 'trend_score' => 91),
                    array('name' => 'Minimalism', 'trend_score' => 84),
                ),
                'price_ranges' => array(
                    array('range' => 'Under $500', 'volume' => 35),
                    array('range' => '$500-$2000', 'volume' => 28),
                    array('range' => '$2000-$10000', 'volume' => 22),
                    array('range' => 'Over $10000', 'volume' => 15),
                )
            )
        );
    }

    /**
     * Fetches trends from gallery data
     * 
     * @return array Gallery trend data
     */
    private function fetch_gallery_trends() {
        // Implementation for fetching from gallery data sources
        
        // Simplified mock implementation
        return array(
            'status' => 'success',
            'data' => array(
                'styles' => array(
                    array('name' => 'Contemporary', 'trend_score' => 93),
                    array('name' => 'Surrealism', 'trend_score' => 81),
                    array('name' => 'Abstract', 'trend_score' => 88),
                ),
                'exhibition_themes' => array(
                    array('name' => 'Climate Change', 'trend_score' => 89),
                    array('name' => 'Identity', 'trend_score' => 92),
                    array('name' => 'Technology', 'trend_score' => 90),
                )
            )
        );
    }

    /**
     * Fetches trends from social media
     * 
     * @return array Social media art trend data
     */
    private function fetch_social_art_trends() {
        // Implementation for fetching from social media APIs
        
        // Simplified mock implementation
        return array(
            'status' => 'success',
            'data' => array(
                'hashtags' => array(
                    array('tag' => '#DigitalArt', 'frequency' => 12500),
                    array('tag' => '#NFTArt', 'frequency' => 8900),
                    array('tag' => '#ContemporaryArt', 'frequency' => 7600),
                ),
                'styles' => array(
                    array('name' => 'Digital', 'trend_score' => 95),
                    array('name' => 'Street Art', 'trend_score' => 92),
                    array('name' => 'Anime-Inspired', 'trend_score' => 88),
                )
            )
        );
    }

    /**
     * Get style name from style ID
     * 
     * @param int $style_id Style ID
     * @return string Style name
     */
    private function get_style_name($style_id) {
        global $wpdb;
        
        $style_name = '';
        $style_name = $wpdb->get_var($wpdb->prepare(
            "SELECT style_name FROM {$wpdb->prefix}vortex_art_styles WHERE id = %d",
            $style_id
        ));
        
        return $style_name ? $style_name : '';
    }

    /**
     * Get category name from category ID
     * 
     * @param int $category_id Category ID
     * @return string Category name
     */
    private function get_category_name($category_id) {
        global $wpdb;
        
        $category_name = '';
        $category_name = $wpdb->get_var($wpdb->prepare(
            "SELECT category_name FROM {$wpdb->prefix}vortex_categories WHERE id = %d",
            $category_id
        ));
        
        return $category_name ? $category_name : '';
    }

    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function log_error($message) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('VORTEX CLOE Error: ' . $message);
        }
    } 
} 