<?php
/**
 * AI Settings Template
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/admin/partials/settings
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Add nonce verification and settings save handler
if (isset($_POST['vortex_ai_save_settings']) && check_admin_referer('vortex_ai_settings_nonce')) {
    // Sanitize and save settings
    $ai_settings = array(
        // HURAII Configuration
        'huraii_enabled' => isset($_POST['vortex_ai_huraii_enabled']),
        'huraii_model' => sanitize_text_field(isset($_POST['vortex_ai_huraii_model']) ? $_POST['vortex_ai_huraii_model'] : 'balanced'),
        'huraii_api_key' => sanitize_text_field(isset($_POST['vortex_ai_huraii_api_key']) ? $_POST['vortex_ai_huraii_api_key'] : ''),
        'huraii_context_size' => intval(isset($_POST['vortex_ai_huraii_context']) ? $_POST['vortex_ai_huraii_context'] : 4096),
        
        // CLOE Configuration
        'cloe_enabled' => isset($_POST['vortex_ai_cloe_enabled']),
        'cloe_model' => sanitize_text_field(isset($_POST['vortex_ai_cloe_model']) ? $_POST['vortex_ai_cloe_model'] : 'market-balanced'),
        'cloe_api_key' => sanitize_text_field(isset($_POST['vortex_ai_cloe_api_key']) ? $_POST['vortex_ai_cloe_api_key'] : ''),
        'cloe_prediction_horizon' => intval(isset($_POST['vortex_ai_cloe_horizon']) ? $_POST['vortex_ai_cloe_horizon'] : 90),
        
        // Business Strategist AI
        'strategist_enabled' => isset($_POST['vortex_ai_strategist_enabled']),
        'strategist_model' => sanitize_text_field(isset($_POST['vortex_ai_strategist_model']) ? $_POST['vortex_ai_strategist_model'] : 'analyst-pro'),
        'strategist_api_key' => sanitize_text_field(isset($_POST['vortex_ai_strategist_api_key']) ? $_POST['vortex_ai_strategist_api_key'] : ''),
        
        // General AI Settings
        'max_tokens_per_request' => intval(isset($_POST['vortex_ai_max_tokens']) ? $_POST['vortex_ai_max_tokens'] : 2048),
        'cache_duration' => intval(isset($_POST['vortex_ai_cache_duration']) ? $_POST['vortex_ai_cache_duration'] : 3600),
        'request_timeout' => intval(isset($_POST['vortex_ai_request_timeout']) ? $_POST['vortex_ai_request_timeout'] : 30),
        'fallback_enabled' => isset($_POST['vortex_ai_fallback_enabled']),
        'log_requests' => isset($_POST['vortex_ai_log_requests']),
        
        // Content Moderation
        'moderation_enabled' => isset($_POST['vortex_ai_moderation_enabled']),
        'moderation_threshold' => floatval(isset($_POST['vortex_ai_moderation_threshold']) ? $_POST['vortex_ai_moderation_threshold'] : 0.8),
        
        // Fine-tuning Settings
        'fine_tuning_enabled' => isset($_POST['vortex_ai_fine_tuning_enabled']),
        'training_data_collection' => isset($_POST['vortex_ai_data_collection']),
        'auto_improvement' => isset($_POST['vortex_ai_auto_improvement'])
    );
    
    // Securely store the settings
    update_option('vortex_ai_settings', $ai_settings);
    
    // Display success message
    add_settings_error(
        'vortex_messages', 
        'vortex_ai_message', 
        esc_html__('AI Settings Saved Successfully', 'vortex-ai-marketplace'), 
        'updated'
    );
}

// Get current settings with default values
$ai_settings = get_option('vortex_ai_settings', array(
    // HURAII Configuration
    'huraii_enabled' => true,
    'huraii_model' => 'balanced',
    'huraii_api_key' => '',
    'huraii_context_size' => 4096,
    
    // CLOE Configuration
    'cloe_enabled' => true,
    'cloe_model' => 'market-balanced',
    'cloe_api_key' => '',
    'cloe_prediction_horizon' => 90,
    
    // Business Strategist AI
    'strategist_enabled' => true,
    'strategist_model' => 'analyst-pro',
    'strategist_api_key' => '',
    
    // General AI Settings
    'max_tokens_per_request' => 2048,
    'cache_duration' => 3600,
    'request_timeout' => 30,
    'fallback_enabled' => true,
    'log_requests' => true,
    
    // Content Moderation
    'moderation_enabled' => true,
    'moderation_threshold' => 0.8,
    
    // Fine-tuning Settings
    'fine_tuning_enabled' => false,
    'training_data_collection' => true,
    'auto_improvement' => false
));

// HURAII model options
$huraii_models = array(
    'balanced' => __('Balanced (Default)', 'vortex-ai-marketplace'),
    'creative' => __('Creative (Artistic Focus)', 'vortex-ai-marketplace'),
    'precise' => __('Precise (Technical Focus)', 'vortex-ai-marketplace'),
    'efficient' => __('Efficient (Fast Response)', 'vortex-ai-marketplace'),
    'custom' => __('Custom Fine-tuned Model', 'vortex-ai-marketplace')
);

// CLOE model options
$cloe_models = array(
    'market-balanced' => __('Market Balanced (Default)', 'vortex-ai-marketplace'),
    'trend-analyzer' => __('Trend Analyzer', 'vortex-ai-marketplace'),
    'price-optimizer' => __('Price Optimizer', 'vortex-ai-marketplace'),
    'market-predictor' => __('Market Predictor (Advanced)', 'vortex-ai-marketplace'),
    'custom' => __('Custom Fine-tuned Model', 'vortex-ai-marketplace')
);

// Business Strategist model options
$strategist_models = array(
    'analyst-pro' => __('Analyst Pro (Default)', 'vortex-ai-marketplace'),
    'growth-advisor' => __('Growth Advisor', 'vortex-ai-marketplace'),
    'risk-assessor' => __('Risk Assessor', 'vortex-ai-marketplace'),
    'market-explorer' => __('Market Explorer', 'vortex-ai-marketplace'),
    'custom' => __('Custom Fine-tuned Model', 'vortex-ai-marketplace')
);

?>

<div class="vortex-settings-content">
    <h2><?php echo esc_html__('AI System Settings', 'vortex-ai-marketplace'); ?></h2>
    <?php settings_errors('vortex_messages'); ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('vortex_ai_settings_nonce'); ?>

        <!-- HURAII Configuration Section -->
        <div class="vortex-section">
            <h3><?php esc_html_e('HURAII - Art Intelligence', 'vortex-ai-marketplace'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable HURAII', 'vortex-ai-marketplace'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="vortex_ai_huraii_enabled" 
                                   value="1" 
                                   <?php checked($ai_settings['huraii_enabled']); ?>>
                            <?php esc_html_e('Enable HURAII art intelligence system', 'vortex-ai-marketplace'); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e('HURAII provides intelligent art analysis, creation assistance, and marketplace optimization.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="huraii-setting">
                    <th scope="row">
                        <label for="vortex_ai_huraii_model">
                            <?php esc_html_e('HURAII Model', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="vortex_ai_huraii_model" name="vortex_ai_huraii_model">
                            <?php foreach ($huraii_models as $model_id => $model_name) : ?>
                                <option value="<?php echo esc_attr($model_id); ?>" <?php selected($ai_settings['huraii_model'], $model_id); ?>>
                                    <?php echo esc_html($model_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Select the HURAII model that best fits your marketplace needs.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="huraii-setting">
                    <th scope="row">
                        <label for="vortex_ai_huraii_api_key">
                            <?php esc_html_e('HURAII API Key', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" 
                               id="vortex_ai_huraii_api_key" 
                               name="vortex_ai_huraii_api_key" 
                               value="<?php echo esc_attr($ai_settings['huraii_api_key']); ?>" 
                               class="regular-text"
                               autocomplete="off">
                        <button type="button" class="button toggle-password" data-target="vortex_ai_huraii_api_key">
                            <?php esc_html_e('Show/Hide', 'vortex-ai-marketplace'); ?>
                        </button>
                        <p class="description">
                            <?php esc_html_e('Enter your HURAII API key. Leave empty to use the integrated service.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="huraii-setting">
                    <th scope="row">
                        <label for="vortex_ai_huraii_context">
                            <?php esc_html_e('Context Size', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="vortex_ai_huraii_context" 
                               name="vortex_ai_huraii_context" 
                               value="<?php echo esc_attr($ai_settings['huraii_context_size']); ?>" 
                               min="1024" 
                               max="16384" 
                               step="1024" 
                               class="medium-text">
                        <p class="description">
                            <?php esc_html_e('Maximum context size for HURAII processing (tokens).', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- CLOE Configuration Section -->
        <div class="vortex-section">
            <h3><?php esc_html_e('CLOE - Market Intelligence', 'vortex-ai-marketplace'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable CLOE', 'vortex-ai-marketplace'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="vortex_ai_cloe_enabled" 
                                   value="1" 
                                   <?php checked($ai_settings['cloe_enabled']); ?>>
                            <?php esc_html_e('Enable CLOE market intelligence system', 'vortex-ai-marketplace'); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e('CLOE analyzes market trends, optimizes pricing, and provides business insights.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="cloe-setting">
                    <th scope="row">
                        <label for="vortex_ai_cloe_model">
                            <?php esc_html_e('CLOE Model', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="vortex_ai_cloe_model" name="vortex_ai_cloe_model">
                            <?php foreach ($cloe_models as $model_id => $model_name) : ?>
                                <option value="<?php echo esc_attr($model_id); ?>" <?php selected($ai_settings['cloe_model'], $model_id); ?>>
                                    <?php echo esc_html($model_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Select the CLOE model that best fits your marketplace strategy.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="cloe-setting">
                    <th scope="row">
                        <label for="vortex_ai_cloe_api_key">
                            <?php esc_html_e('CLOE API Key', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" 
                               id="vortex_ai_cloe_api_key" 
                               name="vortex_ai_cloe_api_key" 
                               value="<?php echo esc_attr($ai_settings['cloe_api_key']); ?>" 
                               class="regular-text"
                               autocomplete="off">
                        <button type="button" class="button toggle-password" data-target="vortex_ai_cloe_api_key">
                            <?php esc_html_e('Show/Hide', 'vortex-ai-marketplace'); ?>
                        </button>
                        <p class="description">
                            <?php esc_html_e('Enter your CLOE API key. Leave empty to use the integrated service.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="cloe-setting">
                    <th scope="row">
                        <label for="vortex_ai_cloe_horizon">
                            <?php esc_html_e('Prediction Horizon', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="vortex_ai_cloe_horizon" 
                               name="vortex_ai_cloe_horizon" 
                               value="<?php echo esc_attr($ai_settings['cloe_prediction_horizon']); ?>" 
                               min="7" 
                               max="365" 
                               class="small-text">
                        <span><?php esc_html_e('days', 'vortex-ai-marketplace'); ?></span>
                        <p class="description">
                            <?php esc_html_e('Number of days for CLOE to forecast market trends.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Business Strategist Configuration Section -->
        <div class="vortex-section">
            <h3><?php esc_html_e('Business Strategist AI', 'vortex-ai-marketplace'); ?></h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Strategist AI', 'vortex-ai-marketplace'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="vortex_ai_strategist_enabled" 
                                   value="1" 
                                   <?php checked($ai_settings['strategist_enabled']); ?>>
                            <?php esc_html_e('Enable Strategist AI', 'vortex-ai-marketplace'); ?>
                        </label>
                        <p class="description">
                            <?php esc_html_e('Strategist AI provides business strategy analysis and recommendation.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="strategist-setting">
                    <th scope="row">
                        <label for="vortex_ai_strategist_model">
                            <?php esc_html_e('Strategist Model', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="vortex_ai_strategist_model" name="vortex_ai_strategist_model">
                            <?php foreach ($strategist_models as $model_id => $model_name) : ?>
                                <option value="<?php echo esc_attr($model_id); ?>" <?php selected($ai_settings['strategist_model'], $model_id); ?>>
                                    <?php echo esc_html($model_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php esc_html_e('Select the Strategist model that best fits your business strategy needs.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="strategist-setting">
                    <th scope="row">
                        <label for="vortex_ai_strategist_api_key">
                            <?php esc_html_e('Strategist API Key', 'vortex-ai-marketplace'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" 
                               id="vortex_ai_strategist_api_key" 
                               name="vortex_ai_strategist_api_key" 
                               value="<?php echo esc_attr($ai_settings['strategist_api_key']); ?>" 
                               class="regular-text"
                               autocomplete="off">
                        <button type="button" class="button toggle-password" data-target="vortex_ai_strategist_api_key">
                            <?php esc_html_e('Show/Hide', 'vortex-ai-marketplace'); ?>
                        </button>
                        <p class="description">
                            <?php esc_html_e('Enter your Strategist API key. Leave empty to use the integrated service.', 'vortex-ai-marketplace'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="vortex-submit-section">
            <input type="submit" 
                   name="vortex_ai_save_settings" 
                   class="button button-primary" 
                   value="<?php esc_attr_e('Save AI Settings', 'vortex-ai-marketplace'); ?>">
        </div>
    </form>
</div>

<style>
.vortex-section {
    margin: 20px 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.vortex-section h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.vortex-shortcode-list {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.vortex-shortcode-list th,
.vortex-shortcode-list td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

.vortex-shortcode-list th {
    background-color: #f8f9fa;
}

.vortex-submit-section {
    margin-top: 20px;
    padding: 20px 0;
    border-top: 1px solid #ddd;
}

.number-range {
    display: flex;
    align-items: center;
}

.number-range input[type="range"] {
    flex-grow: 1;
    margin-right: 10px;
}

.number-range output {
    min-width: 40px;
    text-align: center;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Toggle password visibility
    $('.toggle-password').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        var field = $('#' + target);
        var fieldType = field.attr('type');
        field.attr('type', fieldType === 'password' ? 'text' : 'password');
    });
    
    // Toggle HURAII settings visibility
    $('input[name="vortex_ai_huraii_enabled"]').on('change', function() {
        $('.huraii-setting').toggle($(this).is(':checked'));
    }).trigger('change');
    
    // Toggle CLOE settings visibility
    $('input[name="vortex_ai_cloe_enabled"]').on('change', function() {
        $('.cloe-setting').toggle($(this).is(':checked'));
    }).trigger('change');
    
    // Toggle Strategist settings visibility
    $('input[name="vortex_ai_strategist_enabled"]').on('change', function() {
        $('.strategist-setting').toggle($(this).is(':checked'));
    }).trigger('change');
    
    // Toggle fine-tuning settings visibility
    $('input[name="vortex_ai_fine_tuning_enabled"]').on('change', function() {
        $('.fine-tuning-setting').toggle($(this).is(':checked'));
    }).trigger('change');
    
    // Range sliders
    $('input[type="range"]').on('input', function() {
        $(this).next('output').val($(this).val());
    }).trigger('input');
    
    // Form change tracking
    var formChanged = false;
    
    $('form input, form select').on('change', function() {
        formChanged = true;
    });
    
    $('form').on('submit', function() {
        window.onbeforeunload = null;
        return true;
    });
    
    window.onbeforeunload = function() {
        if (formChanged) {
            return '<?php echo esc_js(__('You have unsaved changes. Are you sure you want to leave?', 'vortex-ai-marketplace')); ?>';
        }
    };
});
</script> 