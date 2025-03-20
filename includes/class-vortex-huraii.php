<?php
/**
 * Vortex HURAII
 *
 * Handles the integration with the HURAII AI agent for the Vortex AI Marketplace.
 *
 * @package VortexAI
 * @subpackage Includes
 * @since 1.0.0
 */

namespace VortexAI\Includes;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * HURAII class.
 *
 * Manages integration with the Human Understanding Responsive Artificial Intelligence Interface (HURAII) system.
 */
class HURAII {
    /**
     * Instance of this class.
     *
     * @since 1.0.0
     * @var object
     */
    private static $instance = null;

    /**
     * API Key for HURAII services
     *
     * @since 1.0.0
     * @var string
     */
    private $api_key = '';

    /**
     * API endpoint
     *
     * @since 1.0.0
     * @var string
     */
    private $api_endpoint = 'https://api.vortex.ai/huraii/v1';

    /**
     * Current model version
     *
     * @since 1.0.0
     * @var string
     */
    private $model_version = '1.0.0';

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
     * The available AI models.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $models    The available AI models.
     */
    private $models;

    /**
     * The logger instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $logger    The logger instance.
     */
    private $logger;

    /**
     * Get a single instance of this class.
     *
     * @since 1.0.0
     * @return HURAII
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->api_key = get_option('vortex_huraii_api_key', '');
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        add_action('init', array($this, 'register_scripts'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_shortcode('vortex_huraii_chat', array($this, 'render_chat_interface'));
    }

    /**
     * Register necessary scripts and styles.
     *
     * @since 1.0.0
     */
    public function register_scripts() {
        wp_register_script(
            'vortex-huraii',
            plugins_url('assets/js/huraii.js', dirname(__FILE__)),
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_register_style(
            'vortex-huraii',
            plugins_url('assets/css/huraii.css', dirname(__FILE__)),
            array(),
            '1.0.0'
        );
    }

    /**
     * Register REST API routes.
     *
     * @since 1.0.0
     */
    public function register_rest_routes() {
        register_rest_route('vortex/v1', '/huraii/chat', array(
            'methods' => 'POST',
            'callback' => array($this, 'process_chat_request'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));
        
        register_rest_route('vortex/v1', '/huraii/analyze', array(
            'methods' => 'POST',
            'callback' => array($this, 'analyze_artwork'),
            'permission_callback' => function() {
                return current_user_can('read');
            }
        ));
    }

    /**
     * Render chat interface.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_chat_interface($atts) {
        $atts = shortcode_atts(
            array(
                'placeholder' => __('Ask HURAII about art...', 'vortex-ai-agents'),
                'button_text' => __('Send', 'vortex-ai-agents'),
                'theme' => 'light',
            ),
            $atts,
            'vortex_huraii_chat'
        );

        // Enqueue required scripts and styles
        wp_enqueue_script('vortex-huraii');
        wp_enqueue_style('vortex-huraii');
        
        wp_localize_script('vortex-huraii', 'vortexHURAII', array(
            'apiURL' => rest_url('vortex/v1/huraii/chat'),
            'nonce' => wp_create_nonce('wp_rest'),
            'loadingText' => __('HURAII is thinking...', 'vortex-ai-agents'),
        ));

        ob_start();
        ?>
        <div class="vortex-huraii-chat-container theme-<?php echo esc_attr($atts['theme']); ?>">
            <div class="vortex-huraii-messages"></div>
            <div class="vortex-huraii-input-container">
                <input type="text" class="vortex-huraii-input" placeholder="<?php echo esc_attr($atts['placeholder']); ?>">
                <button class="vortex-huraii-submit"><?php echo esc_html($atts['button_text']); ?></button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Process chat request.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request REST API request.
     * @return \WP_REST_Response Response object.
     */
    public function process_chat_request($request) {
        $params = $request->get_params();
        
        if (empty($params['message'])) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('No message provided', 'vortex-ai-agents'),
            ), 400);
        }
        
        // Sanitize input
        $message = sanitize_text_field($params['message']);
        $context = isset($params['context']) ? sanitize_text_field($params['context']) : '';
        
        // Call API
        $response = $this->call_api('chat', array(
            'message' => $message,
            'context' => $context,
            'user_id' => get_current_user_id(),
        ));
        
        if (is_wp_error($response)) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => $response->get_error_message(),
            ), 500);
        }
        
        return new \WP_REST_Response(array(
            'success' => true,
            'response' => $response,
        ));
    }
    
    /**
     * Analyze artwork.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request REST API request.
     * @return \WP_REST_Response Response object.
     */
    public function analyze_artwork($request) {
        $params = $request->get_params();
        
        if (empty($params['artwork_id'])) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('No artwork ID provided', 'vortex-ai-agents'),
            ), 400);
        }
        
        // Get artwork data
        $artwork_id = intval($params['artwork_id']);
        $artwork = get_post($artwork_id);
        
        if (!$artwork || 'artwork' !== $artwork->post_type) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('Invalid artwork', 'vortex-ai-agents'),
            ), 404);
        }
        
        // Get image URL
        $image_url = get_the_post_thumbnail_url($artwork_id, 'large');
        
        if (!$image_url) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => __('Artwork has no image', 'vortex-ai-agents'),
            ), 400);
        }
        
        // Call API
        $response = $this->call_api('analyze', array(
            'image_url' => $image_url,
            'title' => $artwork->post_title,
            'description' => $artwork->post_content,
            'artist_id' => get_post_meta($artwork_id, 'vortex_artist_id', true),
        ));
        
        if (is_wp_error($response)) {
            return new \WP_REST_Response(array(
                'success' => false,
                'message' => $response->get_error_message(),
            ), 500);
        }
        
        // Store analysis results
        update_post_meta($artwork_id, 'vortex_huraii_analysis', $response);
        update_post_meta($artwork_id, 'vortex_huraii_analysis_date', current_time('mysql'));
        
        return new \WP_REST_Response(array(
            'success' => true,
            'analysis' => $response,
        ));
    }
    
    /**
     * Call HURAII API.
     *
     * @since 1.0.0
     * @param string $endpoint API endpoint.
     * @param array $data Request data.
     * @return array|WP_Error Response data or error.
     */
    private function call_api($endpoint, $data) {
        if (empty($this->api_key)) {
            return new \WP_Error('no_api_key', __('HURAII API key not configured', 'vortex-ai-agents'));
        }
        
        $url = trailingslashit($this->api_endpoint) . $endpoint;
        
        $response = wp_remote_post($url, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'X-HURAII-Version' => $this->model_version,
            ),
            'body' => wp_json_encode($data),
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($code !== 200) {
            return new \WP_Error(
                'api_error',
                sprintf(__('HURAII API error: %s', 'vortex-ai-agents'), $body)
            );
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error(
                'invalid_response',
                __('Invalid response from HURAII API', 'vortex-ai-agents')
            );
        }
        
        return $data;
    }
    
    /**
     * Get artistic advice.
     *
     * @since 1.0.0
     * @param int $artist_id Artist ID.
     * @param string $topic Advice topic.
     * @return string|WP_Error Advice or error.
     */
    public function get_artistic_advice($artist_id, $topic = '') {
        $artist = get_post($artist_id);
        
        if (!$artist || 'artist' !== $artist->post_type) {
            return new \WP_Error('invalid_artist', __('Invalid artist', 'vortex-ai-agents'));
        }
        
        $response = $this->call_api('advice', array(
            'artist_id' => $artist_id,
            'artist_name' => $artist->post_title,
            'topic' => $topic,
            'portfolio' => $this->get_artist_portfolio_summary($artist_id),
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return isset($response['advice']) ? $response['advice'] : '';
    }
    
    /**
     * Get market insights.
     *
     * @since 1.0.0
     * @param string $category Artwork category.
     * @param string $timeframe Timeframe for analysis.
     * @return array|WP_Error Insights or error.
     */
    public function get_market_insights($category = '', $timeframe = '30days') {
        $response = $this->call_api('market', array(
            'category' => $category,
            'timeframe' => $timeframe,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return isset($response['insights']) ? $response['insights'] : array();
    }
    
    /**
     * Get artist portfolio summary.
     *
     * @since 1.0.0
     * @param int $artist_id Artist ID.
     * @return array Portfolio data.
     */
    private function get_artist_portfolio_summary($artist_id) {
        $artworks = get_posts(array(
            'post_type' => 'artwork',
            'posts_per_page' => 10,
            'meta_query' => array(
                array(
                    'key' => 'vortex_artist_id',
                    'value' => $artist_id,
                ),
            ),
        ));
        
        $portfolio = array();
        
        foreach ($artworks as $artwork) {
            $portfolio[] = array(
                'id' => $artwork->ID,
                'title' => $artwork->post_title,
                'description' => wp_trim_words($artwork->post_content, 50),
                'categories' => wp_get_post_terms($artwork->ID, 'artwork_category', array('fields' => 'names')),
                'image' => get_the_post_thumbnail_url($artwork->ID, 'medium'),
            );
        }
        
        return $portfolio;
    }
}

// Initialize the HURAII system
function vortex_huraii() {
    return HURAII::get_instance();
} 