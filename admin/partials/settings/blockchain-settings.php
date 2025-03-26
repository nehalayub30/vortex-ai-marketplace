<?php
/**
 * Blockchain Settings template.
 *
 * @link       https://vortexartec.com
 * @since      1.0.0
 *
 * @package    Vortex_AI_Marketplace
 * @subpackage Vortex_AI_Marketplace/admin/partials/settings
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get settings from the database
$blockchain_network = get_option('vortex_blockchain_network', 'solana');
$tola_contract_address = get_option('vortex_tola_contract_address', '4ToLaDzTx6NdskNhiReRpaXGYVVVjpG5fAHC9kajrEv4');
$tola_nft_contract_address = get_option('vortex_tola_nft_contract_address', '8UNMsq7LhfxSMkHkVD8CWBKAeURBdPAVgwufnuqedfBz');
$tola_default_royalty = get_option('vortex_tola_default_royalty', 10);
$tola_token_decimals = get_option('vortex_tola_token_decimals', 9);
$enable_wallet_connection = get_option('vortex_enable_wallet_connection', 1);
$wallet_connection_providers = get_option('vortex_wallet_connection_providers', array('phantom', 'solflare'));
$blockchain_enabled = get_option('vortex_blockchain_enabled', 1);
$nft_minting_enabled = get_option('vortex_nft_minting_enabled', 1);
$rpc_endpoint = get_option('vortex_blockchain_rpc_endpoint', '');
$require_wallet_verification = get_option('vortex_require_wallet_verification', 1);

// Available blockchain networks
$networks = array(
    'solana' => 'Solana',
    'ethereum' => 'Ethereum',
    'polygon' => 'Polygon',
    'binance' => 'Binance Smart Chain',
    'near' => 'NEAR Protocol'
);

// Available wallet providers
$wallet_providers = array(
    'phantom' => 'Phantom',
    'solflare' => 'Solflare',
    'slope' => 'Slope',
    'sollet' => 'Sollet',
    'metamask' => 'MetaMask',
    'walletconnect' => 'WalletConnect',
    'coinbase' => 'Coinbase Wallet'
);
?>

<div class="vortex-settings-section" id="blockchain-general-settings">
    <h2><?php esc_html_e('Blockchain Network Settings', 'vortex-ai-marketplace'); ?></h2>
    <p class="description"><?php esc_html_e('Configure the blockchain network settings for your marketplace.', 'vortex-ai-marketplace'); ?></p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="vortex_blockchain_enabled"><?php esc_html_e('Enable Blockchain Features', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <label for="vortex_blockchain_enabled">
                    <input type="checkbox" id="vortex_blockchain_enabled" name="vortex_blockchain_enabled" 
                           value="1" <?php checked($blockchain_enabled, 1); ?> />
                    <?php esc_html_e('Enable blockchain features in the marketplace', 'vortex-ai-marketplace'); ?>
                </label>
                <p class="description"><?php esc_html_e('When disabled, all blockchain-related features will be hidden from users.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_blockchain_network"><?php esc_html_e('Blockchain Network', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <select id="vortex_blockchain_network" name="vortex_blockchain_network">
                    <?php foreach ($networks as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($blockchain_network, $value); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e('The blockchain network that will be used for tokens and NFTs.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_blockchain_rpc_endpoint"><?php esc_html_e('RPC Endpoint URL', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <input type="text" id="vortex_blockchain_rpc_endpoint" name="vortex_blockchain_rpc_endpoint" 
                       value="<?php echo esc_attr($rpc_endpoint); ?>" class="regular-text" />
                <p class="description"><?php esc_html_e('RPC endpoint URL for blockchain operations. Leave empty to use default public endpoints.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
    </table>
</div>

<div class="vortex-settings-section" id="tola-token-settings">
    <h2><?php esc_html_e('TOLA Token Settings', 'vortex-ai-marketplace'); ?></h2>
    <p class="description"><?php esc_html_e('Configure the TOLA token settings for transactions in the marketplace.', 'vortex-ai-marketplace'); ?></p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="vortex_tola_contract_address"><?php esc_html_e('TOLA Token Contract Address', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <input type="text" id="vortex_tola_contract_address" name="vortex_tola_contract_address" 
                       value="<?php echo esc_attr($tola_contract_address); ?>" class="regular-text" />
                <p class="description"><?php esc_html_e('The contract address of the TOLA token on the selected blockchain.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_tola_token_decimals"><?php esc_html_e('Token Decimals', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <input type="number" id="vortex_tola_token_decimals" name="vortex_tola_token_decimals" 
                       value="<?php echo esc_attr($tola_token_decimals); ?>" min="0" max="18" class="small-text" />
                <p class="description"><?php esc_html_e('Number of decimal places for the TOLA token (usually 9 for Solana, 18 for Ethereum).', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
    </table>
</div>

<div class="vortex-settings-section" id="nft-settings">
    <h2><?php esc_html_e('NFT Settings', 'vortex-ai-marketplace'); ?></h2>
    <p class="description"><?php esc_html_e('Configure NFT minting and marketplace settings.', 'vortex-ai-marketplace'); ?></p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="vortex_nft_minting_enabled"><?php esc_html_e('Enable NFT Minting', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <label for="vortex_nft_minting_enabled">
                    <input type="checkbox" id="vortex_nft_minting_enabled" name="vortex_nft_minting_enabled" 
                           value="1" <?php checked($nft_minting_enabled, 1); ?> />
                    <?php esc_html_e('Allow users to mint their artwork as NFTs', 'vortex-ai-marketplace'); ?>
                </label>
                <p class="description"><?php esc_html_e('When enabled, users can mint their artworks as NFTs on the blockchain.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_tola_nft_contract_address"><?php esc_html_e('NFT Contract Address', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <input type="text" id="vortex_tola_nft_contract_address" name="vortex_tola_nft_contract_address" 
                       value="<?php echo esc_attr($tola_nft_contract_address); ?>" class="regular-text" />
                <p class="description"><?php esc_html_e('The contract address for minting NFTs on the selected blockchain.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_tola_default_royalty"><?php esc_html_e('Default Royalty Percentage', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <input type="number" id="vortex_tola_default_royalty" name="vortex_tola_default_royalty" 
                       value="<?php echo esc_attr($tola_default_royalty); ?>" min="0" max="50" step="0.1" class="small-text" />
                <p class="description"><?php esc_html_e('Default royalty percentage for secondary sales of NFTs.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
    </table>
</div>

<div class="vortex-settings-section" id="wallet-connection-settings">
    <h2><?php esc_html_e('Wallet Connection Settings', 'vortex-ai-marketplace'); ?></h2>
    <p class="description"><?php esc_html_e('Configure wallet connection options for users.', 'vortex-ai-marketplace'); ?></p>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="vortex_enable_wallet_connection"><?php esc_html_e('Enable Wallet Connection', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <label for="vortex_enable_wallet_connection">
                    <input type="checkbox" id="vortex_enable_wallet_connection" name="vortex_enable_wallet_connection" 
                           value="1" <?php checked($enable_wallet_connection, 1); ?> />
                    <?php esc_html_e('Allow users to connect their blockchain wallets', 'vortex-ai-marketplace'); ?>
                </label>
                <p class="description"><?php esc_html_e('When enabled, users can connect their blockchain wallets to interact with the marketplace.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label><?php esc_html_e('Wallet Connection Providers', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <?php foreach ($wallet_providers as $value => $label) : ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="vortex_wallet_connection_providers[]" 
                               value="<?php echo esc_attr($value); ?>" 
                               <?php checked(in_array($value, (array)$wallet_connection_providers), true); ?> />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
                <p class="description"><?php esc_html_e('Select which wallet providers to enable for users.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="vortex_require_wallet_verification"><?php esc_html_e('Require Wallet Verification', 'vortex-ai-marketplace'); ?></label>
            </th>
            <td>
                <label for="vortex_require_wallet_verification">
                    <input type="checkbox" id="vortex_require_wallet_verification" name="vortex_require_wallet_verification" 
                           value="1" <?php checked($require_wallet_verification, 1); ?> />
                    <?php esc_html_e('Require users to verify wallet ownership', 'vortex-ai-marketplace'); ?>
                </label>
                <p class="description"><?php esc_html_e('When enabled, users must verify ownership of their wallet by signing a message.', 'vortex-ai-marketplace'); ?></p>
            </td>
        </tr>
    </table>
</div> 