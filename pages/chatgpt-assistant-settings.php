<?php

/**
 * Displays the settings page for ChatGPT Assistant.
 */
function chatgpt_assistant_settings_page(): void
{
    // Check if the user has permission to access the settings page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Save the API key if the form is submitted
    if (isset($_POST['chatgpt_assistant_submit'])) {
        $api_key = sanitize_text_field($_POST['chatgpt_assistant_api_key']);
        $is_valid_api_key = chatgpt_assistant_validate_api_key($api_key);

        if ($is_valid_api_key) {
            update_option('chatgpt_assistant_api_key', $api_key);
            echo '<div class="alert alert-success" role="alert">Settings saved successfully.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Invalid API key. Please enter a valid key.</div>';
        }
    }

    // Check if the API key is already set
    $api_key = get_option('chatgpt_assistant_api_key');
    $is_api_key_set = !empty($api_key);

    // Display the settings form
    ?>
    <div class="container mx-width-600">
        <h1 class="page-header">ChatGPT Assistant Settings</h1>
        <?php if ($is_api_key_set) : ?>
            <div class="alert alert-info" role="alert">A <b>valid</b> API key has been set successfully.</div>
        <?php endif; ?>
        <form method="post" action="">
            <?php settings_fields('chatgpt_assistant_settings'); ?>
            <?php do_settings_sections('chatgpt_assistant_settings'); ?>
            <div class="form-group">
                <label for="chatgpt_assistant_api_key">OpenAI API Key</label>
                <input type="text" class="form-control" id="chatgpt_assistant_api_key" name="chatgpt_assistant_api_key" value="<?php echo esc_attr($api_key); ?>" <?php echo $is_api_key_set ? 'disabled' : ''; ?> />
                <?php if ($is_api_key_set) : ?>
                    <small class="form-text text-muted">API key is currently set. Click the "Edit" button to change it.</small>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" id="chatgpt_assistant_submit_button" name="chatgpt_assistant_submit" <?php echo $is_api_key_set ? 'style="display: none;"' : ''; ?>>Save Settings</button>
            <?php if ($is_api_key_set) : ?>
                <button type="button" class="btn btn-secondary" id="chatgpt_assistant_edit_button">Edit</button>
            <?php endif; ?>
        </form>
    </div>
    <?php
}

/**
 * Validates the provided API key for ChatGPT Assistant.
 *
 * @param string $api_key The API key to validate.
 * @return bool True if the API key is valid, false otherwise.
 */
function chatgpt_assistant_validate_api_key($api_key): bool {
    $response = wp_remote_get(
        'https://api.openai.com/v1/engines',
        array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
            ),
        )
    );

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        return true;
    }

    return false;
}