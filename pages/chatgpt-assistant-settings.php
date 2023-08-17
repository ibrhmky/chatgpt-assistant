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
            echo '<div class="alert alert-success" id="api-key-saved-message" role="alert">Settings saved successfully.</div>';
        } else {
            echo '<div class="alert alert-danger" id="api-key-invalid-message" role="alert">Invalid API key. Please enter a valid key.</div>';
        }
    }

    // Check if the API key is already set
    $api_key = get_option('chatgpt_assistant_api_key');
    $is_api_key_set = !empty($api_key);

    // Display the settings form
    ?>
    <div id="api-key-removed-message" class="alert alert-warning" role="alert" style="display: none;">API key removed successfully.</div>
    <div class="container mt-5">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-bottom: -5.5rem;">
            <button class="btn btn-dark" type="button" onclick="window.location.href='<?php echo esc_url(admin_url("admin.php?page=chatgpt-assistant-new-post")); ?>'">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
        <div class="wrap mx-width-600">
            <h3 class="page-header">AI Generator API Key</h3>
            <?php if ($is_api_key_set) : ?>
                <div id="api-key-saved-info" class="alert alert-info" role="alert">A <b>valid</b> API key has been set successfully.</div>
            <?php endif; ?>
            <form method="post" action="">
                <?php settings_fields('chatgpt_assistant_settings'); ?>
                <?php do_settings_sections('chatgpt_assistant_settings'); ?>
                <div class="form-group">
                    <label for="chatgpt_assistant_api_key">OpenAI API Key</label>
                    <input type="text" class="form-control mt-2" id="chatgpt_assistant_api_key"
                           name="chatgpt_assistant_api_key"
                           value="<?php echo esc_attr($api_key); ?>" <?php echo $is_api_key_set ? 'disabled' : ''; ?> />
                    <?php if ($is_api_key_set) : ?>
                        <small class="form-text text-muted">API key is currently set. Click the "Edit" button to change
                            it.</small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary mt-3" id="chatgpt_assistant_submit_button"
                        name="chatgpt_assistant_submit" <?php echo $is_api_key_set ? 'style="display: none;"' : ''; ?>>
                    Save Settings
                </button>
                <?php if ($is_api_key_set) : ?>
                    <button type="button" class="btn btn-secondary mt-3" id="chatgpt_assistant_edit_button">Edit</button>
                    <button type="button" class="btn btn-danger mt-3 ms-2" id="chatgpt_assistant_delete_button">Remove</button>
                <?php endif; ?>
            </form>
        </div>
        <div id="chatgpt_assistant_settings_wrapper" class="wrap" style="max-width: 70%">
            <h3 class="page-header">Settings</h3>
            <div class="row g-2">
                <h6>Company or Personal Information:</h6>
                <label for="companyNameTextarea" class="form-label mx-width-perc-60">Company Name</label>
                <div class="mb-3 col-auto mn-width-400">
                    <input type="text" class="form-control" id="companyNameTextarea" placeholder="Example Company" value="<?php echo get_option('companyNameTextarea'); ?>">
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button class="btn btn-danger mb-3" onclick="deleteSettingsAjax('companyNameTextarea')">Delete</button>
                </div>
                <label for="companyInfoTextarea" class="form-label mx-width-perc-60">Please share details about you or your business, like the industry you are in, the products or services
                    you offer, your target audience, etc. The more specific the information, the more personalised and
                    relevant the generated content can be.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <textarea class="form-control" id="companyInfoTextarea" rows="4"><?php echo get_option('companyInfoTextarea'); ?></textarea>
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button id="companyInfoTextarea_button" class="btn btn-primary mb-3" onclick="sendMessageAPI('companyInfoTextarea')" >Generate</button>
                    <button id="companyInfoTextarea_load" class="btn btn-primary mb-3 d-none" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        <span role="status">Loading...</span>
                    </button>
                    <button class="btn btn-danger mb-3" onclick="deleteSettingsAjax('companyInfoTextarea')">Delete</button>
                </div>
            </div>
            <div class="row g-2">
                <h6>Brand Guidelines:</h6>
                <label for="brandGuideTextarea" class="form-label mx-width-perc-60">Please provide your brands tone of voice, style guide, key messages, etc. This will help the AI
                    maintain the brand's consistency across all generated content.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <textarea class="form-control" id="brandGuideTextarea" rows="4"><?php echo get_option('brandGuideTextarea'); ?></textarea>
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button id="brandGuideTextarea_button" class="btn btn-primary mb-3" onclick="sendMessageAPI('brandGuideTextarea')">Generate</button>
                    <button id="brandGuideTextarea_load" class="btn btn-primary mb-3 d-none" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        <span role="status">Loading...</span>
                    </button>
                    <button class="btn btn-danger mb-3" onclick="deleteSettingsAjax('brandGuideTextarea')">Delete</button>
                </div>
            </div>
            <div class="wrap" style="pointer-events: none; border: solid 2px; padding: 40px 30px; border-radius: 4px">
                <div class="wrap mb-5">
                    <h5>Default Settings:</h5>
                    <p style="font-size: 16px">You should have the option to set default preferences for content type,
                        length, tone, etc., so you don't
                        have to enter these details every time for generate content. By the way you could also set
                        di!erent
                        values for all content by manually.</p>
                </div>
                <div class="wrap" style="opacity: 0.5;">
                    <form class="row g-2">
                        <h6>Target Audience (Available on the PRO version.)</h6>
                        <label for="targetAudienceTextarea" class="form-label">This will help the AI
                            understand the tone, style,
                            and language complexity it should aim for. For
                            instance, the writing style for a tech-savvy audience will be different from that for a
                            general
                            audience.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="targetAudienceTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                    <form class="row g-2">
                        <h6>Content Length (Available on the PRO version.)</h6>
                        <label for="contentLengthTextarea" class="form-label">Please specify the
                            desired
                            length of the content.
                            This can be in terms of the number of words,
                            characters, or paragraphs.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="contentLengthTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                    <form class="row g-2">
                        <h6>Tone of Voice (Available on the PRO version.)</h6>
                        <label for="tonVoiceTextarea" class="form-label">Is the content supposed to be
                            formal, informal,
                            conversational, professional, etc.? Understanding the
                            tone can help in creating the right content that matches the brand voice.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="tonVoiceTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                    <form class="row g-2">
                        <h6>Call to Action (Available on the PRO version.)</h6>
                        <label for="callActionTextarea" class="form-label">Call to Action (Available on
                            the
                            PRO version.)
                            Most marketing content includes a call to action (CTA). Ask users what action they want
                            their
                            readers
                            to take after reading the content.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="callActionTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                    <form class="row g-2">
                        <h6>Specific Inclusions/Exclusions (Available on the PRO version.)</h6>
                        <label for="inclusionsExclusionsTextarea" class="form-label">Allow users to
                            specify
                            any specific points
                            or information that they want to include or exclude in the
                            content.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="inclusionsExclusionsTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                    <form class="row g-2">
                        <h6>Content Format/Structure (Available on the PRO version.)</h6>
                        <label for="contentFormatTextarea" class="form-label">This could include
                            headings,
                            subheadings, bullet points, numbered lists, etc.</label>
                        <div class="mb-3 col-auto mn-width-400">
                            <textarea class="form-control" id="contentFormatTextarea" rows="2"></textarea>
                        </div>
                        <div class="mb-3 col-auto mn-width-400">
                            <button type="submit" class="btn btn-primary mb-3">Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-top: -3rem;">
            <button class="btn btn-dark" type="button" onclick="window.location.href='<?php echo esc_url(admin_url("admin.php?page=chatgpt-assistant-new-post")); ?>'">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
    </div>

    <!-- Create a toast container element -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <!-- Create a toast element -->
        <div class="toast toast-delete align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Setting saved!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <!-- Create a toast element -->
        <div class="toast toast-save align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Setting deleted!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>


    <script>

    </script>

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

// Define the callback function to handle API key removal
function chatgpt_assistant_remove_api_key() {
    // Check if the request is a POST request
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Check for user capabilities and perform necessary checks before removing the key
        if (current_user_can('manage_options')) {
            // Remove the API key option from the database
            delete_option('chatgpt_assistant_api_key');
            echo "success"; // Sending a response back to the client to indicate success
        } else {
            // User doesn't have sufficient permissions to remove the API key
            echo "error"; // Sending a response back to the client to indicate error
        }
    }
    // Always use die() or exit() after processing AJAX requests
    die();
}
add_action('wp_ajax_chatgpt_assistant_remove_api_key', 'chatgpt_assistant_remove_api_key');
add_action('wp_ajax_nopriv_chatgpt_assistant_remove_api_key', 'chatgpt_assistant_remove_api_key');

// Define the callback function for the my_update_setting_action AJAX action
function chatgpt_assistant_setting_action_callback() {

    // Check if the setting_value parameter is set
    if (isset($_POST['setting_value'])) {
        // Get the value of the setting from the request
        $setting_value = sanitize_text_field($_POST['setting_value']);

        // Update the setting in the database
        update_option($_POST['setting_key'], $setting_value);

        // Send a success response back to the client
        wp_send_json_success();
    } else {
        // Send an error response back to the client
        wp_send_json_error();
    }
    
}
// Register the my_update_setting_action AJAX action
add_action('wp_ajax_chatgpt_assistant_setting_action_callback', 'chatgpt_assistant_setting_action_callback');
add_action('wp_ajax_nopriv_chatgpt_assistant_setting_action_callback', 'chatgpt_assistant_setting_action_callback');

// Define the callback function for the my_update_setting_action AJAX action
function chatgpt_assistant_setting_remove_callback() {

    // Check if the setting_value parameter is set
    if (isset($_POST['setting_key'])) {

        // Update the setting in the database
        delete_option($_POST['setting_key']);

        // Send a success response back to the client
        wp_send_json_success();
    } else {
        // Send an error response back to the client
        wp_send_json_error();
    }
    
}
// Register the my_update_setting_action AJAX action
add_action('wp_ajax_chatgpt_assistant_setting_remove_callback', 'chatgpt_assistant_setting_remove_callback');
add_action('wp_ajax_nopriv_chatgpt_assistant_setting_remove_callback', 'chatgpt_assistant_setting_remove_callback');