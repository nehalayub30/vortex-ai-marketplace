<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://vortexartec.com
 * @since      1.0.0
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * enqueuing the public-facing stylesheet and JavaScript.
 * Also handles shortcodes, templates, and AJAX functionality.
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/public
 * @author     Marianne Nems <Marianne@VortexArtec.com>
 */
class Vortex_Public {

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
     * The marketplace instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      Vortex_Marketplace    $marketplace    The marketplace instance.
     */
    private $marketplace;

    /**
     * Theme compatibility handler.
     *
     * @since    1.0.0
     * @access   private
     * @var      Vortex_Theme_Compatibility    $theme_compatibility    Theme compatibility handler.
     */
    private $theme_compatibility;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string                    $plugin_name       The name of the plugin.
     * @param    string                    $version           The version of this plugin.
     * @param    Vortex_Marketplace        $marketplace       Optional. The marketplace instance.
     * @param    Vortex_Theme_Compatibility $theme_compatibility Optional. Theme compatibility handler.
     */
    public function __construct($plugin_name, $version, $marketplace = null, $theme_compatibility = null) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->marketplace = $marketplace;
        $this->theme_compatibility = $theme_compatibility;

        $this->load_dependencies();
        $this->register_shortcodes();
    }

    /**
     * Load dependencies needed for public functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load template functions
        // require_once plugin_dir_path(dirname(__FILE__)) . 'public/vortex-template-functions.php';
    }

    /**
     * Register all shortcodes.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_shortcodes() {
        add_shortcode('vortex_marketplace', array($this, 'marketplace_shortcode'));
        add_shortcode('vortex_artwork_grid', array($this, 'artwork_grid_shortcode'));
        add_shortcode('vortex_artist_grid', array($this, 'artist_grid_shortcode'));
        add_shortcode('vortex_artwork_search', array($this, 'artwork_search_shortcode'));
        add_shortcode('vortex_shopping_cart', array($this, 'shopping_cart_shortcode'));
        add_shortcode('vortex_checkout', array($this, 'checkout_shortcode'));
        add_shortcode('vortex_user_dashboard', array($this, 'user_dashboard_shortcode'));
        add_shortcode('vortex_artwork_generator', array($this, 'artwork_generator_shortcode'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Base stylesheet
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/vortex-public.css',
            array(),
            $this->version,
            'all'
        );

        // Grid layout stylesheet
        wp_enqueue_style(
            $this->plugin_name . '-grid',
            plugin_dir_url(__FILE__) . 'css/vortex-grid.css',
            array($this->plugin_name),
            $this->version,
            'all'
        );

        // Cart and checkout stylesheet
        if (is_cart() || is_checkout() || has_shortcode(get_post()->post_content, 'vortex_shopping_cart') || has_shortcode(get_post()->post_content, 'vortex_checkout')) {
            wp_enqueue_style(
                $this->plugin_name . '-cart',
                plugin_dir_url(__FILE__) . 'css/vortex-cart.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // Artwork details stylesheet
        if (is_singular('vortex_artwork')) {
            wp_enqueue_style(
                $this->plugin_name . '-artwork',
                plugin_dir_url(__FILE__) . 'css/vortex-artwork.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // Artist profile stylesheet
        if (is_singular('vortex_artist')) {
            wp_enqueue_style(
                $this->plugin_name . '-artist',
                plugin_dir_url(__FILE__) . 'css/vortex-artist.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // AI Generator stylesheet
        if (has_shortcode(get_post()->post_content, 'vortex_artwork_generator')) {
            wp_enqueue_style(
                $this->plugin_name . '-generator',
                plugin_dir_url(__FILE__) . 'css/vortex-generator.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // User dashboard stylesheet
        if (has_shortcode(get_post()->post_content, 'vortex_user_dashboard')) {
            wp_enqueue_style(
                $this->plugin_name . '-dashboard',
                plugin_dir_url(__FILE__) . 'css/vortex-user-dashboard.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // Theme compatibility stylesheet
        if ($this->theme_compatibility) {
            wp_enqueue_style(
                $this->plugin_name . '-theme-compatibility',
                plugin_dir_url(__FILE__) . 'css/vortex-theme-compatibility.css',
                array($this->plugin_name),
                $this->version,
                'all'
            );
        }

        // Custom CSS from settings
        $custom_css = get_option('vortex_advanced_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style($this->plugin_name, $custom_css);
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Main script with core functionality
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/vortex-public.js',
            array('jquery'),
            $this->version,
            true
        );

        // Localize main script
        wp_localize_script(
            $this->plugin_name,
            'vortexVars',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('vortex_public_nonce'),
                'isLoggedIn' => is_user_logged_in() ? 'true' : 'false',
                'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
                'checkoutUrl' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
                'currencySymbol' => get_option('vortex_marketplace_currency_symbol', '$'),
                'i18n' => array(
                    'addToCart' => __('Add to Cart', 'vortex-ai-marketplace'),
                    'added' => __('Added!', 'vortex-ai-marketplace'),
                    'removing' => __('Removing...', 'vortex-ai-marketplace'),
                    'removed' => __('Removed', 'vortex-ai-marketplace'),
                    'error' => __('Error', 'vortex-ai-marketplace'),
                    'loadMore' => __('Load More', 'vortex-ai-marketplace'),
                    'loading' => __('Loading...', 'vortex-ai-marketplace'),
                    'noResults' => __('No results found', 'vortex-ai-marketplace'),
                    'confirm' => __('Are you sure?', 'vortex-ai-marketplace'),
                    'pleaseWait' => __('Please wait...', 'vortex-ai-marketplace'),
                ),
            )
        );

        // Cart and checkout scripts
        if (is_cart() || is_checkout() || has_shortcode(get_post()->post_content, 'vortex_shopping_cart') || has_shortcode(get_post()->post_content, 'vortex_checkout')) {
            wp_enqueue_script(
                $this->plugin_name . '-cart',
                plugin_dir_url(__FILE__) . 'js/vortex-cart.js',
                array('jquery', $this->plugin_name),
                $this->version,
                true
            );
        }

        // Artwork details script
        if (is_singular('vortex_artwork')) {
            wp_enqueue_script(
                $this->plugin_name . '-artwork',
                plugin_dir_url(__FILE__) . 'js/vortex-artwork.js',
                array('jquery', $this->plugin_name),
                $this->version,
                true
            );

            // Track view count
            $artwork_id = get_the_ID();
            wp_localize_script(
                $this->plugin_name . '-artwork',
                'vortexArtworkVars',
                array(
                    'artworkId' => $artwork_id,
                )
            );
        }

        // Artist profile script
        if (is_singular('vortex_artist')) {
            wp_enqueue_script(
                $this->plugin_name . '-artist',
                plugin_dir_url(__FILE__) . 'js/vortex-artist.js',
                array('jquery', $this->plugin_name),
                $this->version,
                true
            );

            // Track view count
            $artist_id = get_the_ID();
            wp_localize_script(
                $this->plugin_name . '-artist',
                'vortexArtistVars',
                array(
                    'artistId' => $artist_id,
                )
            );
        }

        // AI Generator script
        if (has_shortcode(get_post()->post_content, 'vortex_artwork_generator')) {
            wp_enqueue_script(
                $this->plugin_name . '-generator',
                plugin_dir_url(__FILE__) . 'js/vortex-generator.js',
                array('jquery', $this->plugin_name),
                $this->version,
                true
            );

            // Get AI models from the API
            global $vortex_huraii;
            $ai_models = array();
            
            if ($vortex_huraii) {
                $ai_models = $vortex_huraii->get_available_models(false);
            }

            // Localize generator script
            wp_localize_script(
                $this->plugin_name . '-generator',
                'vortexGeneratorVars',
                array(
                    'models' => $ai_models,
                    'defaultModel' => get_option('vortex_ai_default_model', ''),
                    'maxTokens' => get_option('vortex_ai_max_tokens', 1000),
                    'publicGeneration' => get_option('vortex_ai_public_generation', false),
                    'loginUrl' => wp_login_url(get_permalink()),
                )
            );
        }

        // User dashboard script
        if (has_shortcode(get_post()->post_content, 'vortex_user_dashboard')) {
            wp_enqueue_script(
                $this->plugin_name . '-dashboard',
                plugin_dir_url(__FILE__) . 'js/vortex-user-dashboard.js',
                array('jquery', $this->plugin_name),
                $this->version,
                true
            );
        }

        // Blockchain integration
        if (get_option('vortex_blockchain_enabled', true)) {
            // Enqueue Web3.js
            wp_enqueue_script(
                'web3',
                'https://cdn.jsdelivr.net/npm/web3@1.8.0/dist/web3.min.js',
                array(),
                '1.8.0',
                true
            );

            wp_enqueue_script(
                $this->plugin_name . '-blockchain',
                plugin_dir_url(__FILE__) . 'js/vortex-blockchain.js',
                array('jquery', 'web3', $this->plugin_name),
                $this->version,
                true
            );

            // Localize blockchain script
            wp_localize_script(
                $this->plugin_name . '-blockchain',
                'vortexBlockchainVars',
                array(
                    'network' => get_option('vortex_blockchain_network', 'solana'),
                    'rpcUrl' => get_option('vortex_blockchain_rpc_url', ''),
                    'contractAddress' => get_option('vortex_tola_contract_address', ''),
                    'marketplaceWallet' => get_option('vortex_marketplace_wallet_address', ''),
                    'nftEnabled' => get_option('vortex_blockchain_nft_enabled', true),
                )
            );
        }

        // Custom JS from settings
        $custom_js = get_option('vortex_advanced_custom_js', '');
        if (!empty($custom_js)) {
            wp_add_inline_script($this->plugin_name, $custom_js);
        }
    }

    /**
     * Set up content wrappers for marketplace templates.
     *
     * @since    1.0.0
     */
    public function content_wrappers_start() {
        if ($this->theme_compatibility) {
            $this->theme_compatibility->content_wrapper_start();
        } else {
            // Default wrapper
            echo '<div id="vortex-marketplace-main" class="vortex-marketplace-content">';
        }
    }

    /**
     * Close content wrappers for marketplace templates.
     *
     * @since    1.0.0
     */
    public function content_wrappers_end() {
        if ($this->theme_compatibility) {
            $this->theme_compatibility->content_wrapper_end();
        } else {
            // Default wrapper
            echo '</div><!-- #vortex-marketplace-main -->';
        }
    }

    /**
     * Add body classes for marketplace pages.
     *
     * @since    1.0.0
     * @param    array    $classes    Body classes.
     * @return   array    Modified body classes.
     */
    public function body_classes($classes) {
        if (is_post_type_archive('vortex_artwork') || is_tax('vortex_artwork_category') || is_tax('vortex_artwork_tag')) {
            $classes[] = 'vortex-marketplace';
            $classes[] = 'vortex-artwork-archive';
        }

        if (is_post_type_archive('vortex_artist') || is_tax('vortex_artist_category')) {
            $classes[] = 'vortex-marketplace';
            $classes[] = 'vortex-artist-archive';
        }

        if (is_singular('vortex_artwork')) {
            $classes[] = 'vortex-marketplace';
            $classes[] = 'vortex-artwork-single';
            
            // Add class for AI generated artworks
            $is_ai_generated = get_post_meta(get_the_ID(), '_vortex_artwork_ai_generated', true);
            if ($is_ai_generated) {
                $classes[] = 'vortex-ai-artwork';
            }
        }

        if (is_singular('vortex_artist')) {
            $classes[] = 'vortex-marketplace';
            $classes[] = 'vortex-artist-single';
            
            // Add class for verified artists
            $is_verified = get_post_meta(get_the_ID(), '_vortex_artist_verified', true);
            if ($is_verified) {
                $classes[] = 'vortex-verified-artist';
            }
        }

        // Add class if marketplace shortcode is on the page
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'vortex_marketplace')) {
            $classes[] = 'vortex-marketplace-page';
        }

        return $classes;
    }

    /**
     * Add shopping cart fragments for AJAX cart updates.
     *
     * @since    1.0.0
     * @param    array    $fragments    Cart fragments.
     * @return   array    Modified cart fragments.
     */
    public function cart_fragments($fragments) {
        // If marketplace instance exists, get cart contents
        if ($this->marketplace) {
            ob_start();
            $cart_count = $this->marketplace->get_cart_count();
            ?>
            <span class="vortex-cart-count"><?php echo esc_html($cart_count); ?></span>
            <?php
            $fragments['.vortex-cart-count'] = ob_get_clean();

            ob_start();
            $cart_subtotal = $this->marketplace->get_cart_subtotal();
            ?>
            <span class="vortex-cart-subtotal"><?php echo esc_html($cart_subtotal); ?></span>
            <?php
            $fragments['.vortex-cart-subtotal'] = ob_get_clean();

            ob_start();
            $this->marketplace->get_mini_cart();
            $fragments['.vortex-mini-cart-contents'] = ob_get_clean();
        }

        return $fragments;
    }

    /**
     * AJAX handler for adding items to cart.
     *
     * @since    1.0.0
     */
    public function ajax_add_to_cart() {
        check_ajax_referer('vortex_public_nonce', 'nonce');

        $artwork_id = isset($_POST['artwork_id']) ? intval($_POST['artwork_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($artwork_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid artwork ID', 'vortex-ai-marketplace')));
        }

        // Add to cart using marketplace instance
        if ($this->marketplace) {
            $result = $this->marketplace->add_to_cart($artwork_id, $quantity);

            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            } else {
                wp_send_json_success(array(
                    'message' => __('Artwork added to cart', 'vortex-ai-marketplace'),
                    'cart_count' => $this->marketplace->get_cart_count(),
                    'cart_subtotal' => $this->marketplace->get_cart_subtotal(),
                ));
            }
        } else {
            wp_send_json_error(array('message' => __('Marketplace not available', 'vortex-ai-marketplace')));
        }
    }

    /**
     * AJAX handler for removing items from cart.
     *
     * @since    1.0.0
     */
    public function ajax_remove_from_cart() {
        check_ajax_referer('vortex_public_nonce', 'nonce');

        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';

        if (empty($cart_item_key)) {
            wp_send_json_error(array('message' => __('Invalid cart item', 'vortex-ai-marketplace')));
        }

        // Remove from cart using marketplace instance
        if ($this->marketplace) {
            $result = $this->marketplace->remove_from_cart($cart_item_key);

            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            } else {
                wp_send_json_success(array(
                    'message' => __('Item removed from cart', 'vortex-ai-marketplace'),
                    'cart_count' => $this->marketplace->get_cart_count(),
                    'cart_subtotal' => $this->marketplace->get_cart_subtotal(),
                ));
            }
        } else {
            wp_send_json_error(array('message' => __('Marketplace not available', 'vortex-ai-marketplace')));
        }
    }

    /**
     * AJAX handler for updating cart quantities.
     *
     * @since    1.0.0
     */
    public function ajax_update_cart() {
        check_ajax_referer('vortex_public_nonce', 'nonce');

        $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if (empty($cart_item_key)) {
            wp_send_json_error(array('message' => __('Invalid cart item', 'vortex-ai-marketplace')));
        }

        // Update cart using marketplace instance
        if ($this->marketplace) {
            $result = $this->marketplace->update_cart_item_quantity($cart_item_key, $quantity);

            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            } else {
                wp_send_json_success(array(
                    'message' => __('Cart updated', 'vortex-ai-marketplace'),
                    'cart_count' => $this->marketplace->get_cart_count(),
                    'cart_subtotal' => $this->marketplace->get_cart_subtotal(),
                    'cart_item_total' => $result['item_total'],
                ));
            }
        } else {
            wp_send_json_error(array('message' => __('Marketplace not available', 'vortex-ai-marketplace')));
        }
    }

    /**
     * AJAX handler for updating view counts.
     *
     * @since    1.0.0
     */
    public function ajax_update_view_count() {
        check_ajax_referer('vortex_public_nonce', 'nonce');

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $post_type = get_post_type($post_id);

        if ($post_id <= 0 || !in_array($post_type, array('vortex_artwork', 'vortex_artist'))) {
            wp_send_json_error(array('message' => __('Invalid post ID or type', 'vortex-ai-marketplace')));
        }

        // Check for bots
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            wp_send_json_error(array('message' => __('Bot detected', 'vortex-ai-marketplace')));
        }

        // Update view count
        $meta_key = '_vortex_' . $post_type . '_view_count';
        $current_views = get_post_meta($post_id, $meta_key, true);
        $new_views = (is_numeric($current_views) ? intval($current_views) : 0) + 1;
        update_post_meta($post_id, $meta_key, $new_views);

        // Update daily view count for analytics
        $today = date('Ymd');
        $daily_views_key = 'vortex_daily_' . $post_type . '_views_' . $today;
        $daily_views = get_option($daily_views_key, 0);
        update_option($daily_views_key, $daily_views + 1);

        wp_send_json_success(array('views' => $new_views));
    }

    /**
     * Main marketplace shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function marketplace_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('VORTEX AI Marketplace', 'vortex-ai-marketplace'),
            'description' => '',
            'featured' => 'yes',
            'categories' => 'yes',
            'latest' => 'yes',
            'artists' => 'yes',
            'search' => 'yes',
        ), $atts, 'vortex_marketplace');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/marketplace.php');
        return ob_get_clean();
    }

    /**
     * Artwork grid shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function artwork_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'count' => 12,
            'columns' => 3,
            'category' => '',
            'tag' => '',
            'artist' => '',
            'featured' => 'no',
            'ai_generated' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'show_filters' => 'no',
            'show_pagination' => 'yes',
            'show_price' => 'yes',
            'show_artist' => 'yes',
        ), $atts, 'vortex_artwork_grid');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artwork-grid.php');
        return ob_get_clean();
    }

    /**
     * Artist grid shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function artist_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'count' => 8,
            'columns' => 4,
            'category' => '',
            'featured' => 'no',
            'verified' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'show_filters' => 'no',
            'show_pagination' => 'yes',
            'show_bio' => 'yes',
        ), $atts, 'vortex_artist_grid');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artist-grid.php');
        return ob_get_clean();
    }

    /**
     * Artwork search shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function artwork_search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Search Artworks', 'vortex-ai-marketplace'),
            'placeholder' => __('Search for artworks...', 'vortex-ai-marketplace'),
            'show_categories' => 'yes',
            'show_tags' => 'yes',
            'show_artists' => 'yes',
            'show_price_filter' => 'yes',
            'show_ai_filter' => 'yes',
            'results_count' => 12,
            'results_columns' => 3,
        ), $atts, 'vortex_artwork_search');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artwork-search.php');
        return ob_get_clean();
    }

    /**
     * Shopping cart shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function shopping_cart_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Your Cart', 'vortex-ai-marketplace'),
            'show_thumbnails' => 'yes',
            'show_prices' => 'yes',
            'show_totals' => 'yes',
            'show_checkout_button' => 'yes',
        ), $atts, 'vortex_shopping_cart');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/cart.php');
        return ob_get_clean();
    }

    /**
     * Checkout shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function checkout_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Checkout', 'vortex-ai-marketplace'),
            'show_order_summary' => 'yes',
            'show_login_form' => 'yes',
        ), $atts, 'vortex_checkout');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/checkout.php');
        return ob_get_clean();
    }

    /**
     * User dashboard shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function user_dashboard_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('My Dashboard', 'vortex-ai-marketplace'),
            'show_profile' => 'yes',
            'show_purchases' => 'yes',
            'show_artworks' => 'yes',
            'show_wallet' => 'yes',
            'show_sales' => 'yes',
        ), $atts, 'vortex_user_dashboard');

        // Redirect to login page if not logged in
        if (!is_user_logged_in()) {
            $login_url = wp_login_url(get_permalink());
            wp_redirect($login_url);
            exit;
        }

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/user-dashboard.php');
        return ob_get_clean();
    }

    /**
     * Artwork generator shortcode.
     *
     * @since    1.0.0
     * @param    array     $atts    Shortcode attributes.
     * @return   string    Shortcode output.
     */
    public function artwork_generator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => __('AI Artwork Generator', 'vortex-ai-marketplace'),
            'description' => __('Create unique AI-generated artworks', 'vortex-ai-marketplace'),
            'prompt_placeholder' => __('Describe your artwork...', 'vortex-ai-marketplace'),
            'show_models' => 'yes',
            'show_options' => 'yes',
            'allow_public' => 'no',
            'show_gallery' => 'yes',
            'default_width' => 1024,
            'default_height' => 1024,
        ), $atts, 'vortex_artwork_generator');

        // Check if public generation is allowed
        $public_generation = get_option('vortex_ai_public_generation', false);
        $allow_public = $atts['allow_public'] === 'yes';

        // If not logged in and public generation not allowed, show login message
        if (!is_user_logged_in() && !($public_generation && $allow_public)) {
            ob_start();
            $login_url = wp_login_url(get_permalink());
            ?>
            <div class="vortex-login-required">
                <h3><?php esc_html_e('Login Required', 'vortex-ai-marketplace'); ?></h3>
                <p><?php esc_html_e('You need to log in to generate AI artworks.', 'vortex-ai-marketplace'); ?></p>
                <a href="<?php echo esc_url($login_url); ?>" class="vortex-button"><?php esc_html_e('Log In', 'vortex-ai-marketplace'); ?></a>
            </div>
            <?php
            return ob_get_clean();
        }

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artwork-generator.php');
        return ob_get_clean();
    }

    /**
     * Add to cart button template.
     *
     * @since    1.0.0
     * @param    int       $artwork_id    The artwork ID.
     * @param    bool      $echo          Whether to echo or return.
     * @return   string    The button HTML if $echo is false.
     */
    public function add_to_cart_button($artwork_id, $echo = true) {
        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/add-to-cart-button.php');
        $button = ob_get_clean();

        if ($echo) {
            echo $button;
        }

        return $button;
    }

    /**
     * Artwork price template.
     *
     * @since    1.0.0
     * @param    int       $artwork_id    The artwork ID.
     * @param    bool      $echo          Whether to echo or return.
     * @return   string    The price HTML if $echo is false.
     */
    public function get_artwork_price($artwork_id, $echo = true) {
        $price = get_post_meta($artwork_id, '_vortex_artwork_price', true);
        $currency_symbol = get_option('vortex_marketplace_currency_symbol', '$');

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artwork-price.php');
        $price_html = ob_get_clean();

        if ($echo) {
            echo $price_html;
        }

        return $price_html;
    }

    /**
     * Artist name template.
     *
     * @since    1.0.0
     * @param    int       $artist_id     The artist ID.
     * @param    bool      $show_badge    Whether to show verification badge.
     * @param    bool      $echo          Whether to echo or return.
     * @return   string    The artist name HTML if $echo is false.
     */
    public function get_artist_name($artist_id, $show_badge = true, $echo = true) {
        $artist_name = get_the_title($artist_id);
        $is_verified = get_post_meta($artist_id, '_vortex_artist_verified', true);

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artist-name.php');
        $artist_html = ob_get_clean();

        if ($echo) {
            echo $artist_html;
        }

        return $artist_html;
    }

    /**
     * Artwork rating template.
     *
     * @since    1.0.0
     * @param    int       $artwork_id    The artwork ID.
     * @param    bool      $echo          Whether to echo or return.
     * @return   string    The rating HTML if $echo is false.
     */
    public function get_artwork_rating($artwork_id, $echo = true) {
        $rating = get_post_meta($artwork_id, '_vortex_artwork_rating', true);
        $rating_count = get_post_meta($artwork_id, '_vortex_artwork_rating_count', true);

        ob_start();
        include(plugin_dir_path(__FILE__) . 'partials/artwork-rating.php');
        $rating_html = ob_get_clean();

        if ($echo) {
            echo $rating_html;
        }

        return $rating_html;
    }

    /**
     * Get the page ID for a specific marketplace page.
     *
     * @since    1.0.0
     * @param    string    $page    The page type (e.g., 'shop', 'cart', 'checkout').
     * @
</rewritten_file>
*/
}
