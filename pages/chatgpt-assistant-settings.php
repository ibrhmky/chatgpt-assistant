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
    <div class="container mt-5">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-bottom: -3.5rem;">
            <button class="btn btn-dark" type="button">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
        <div class="wrap mx-width-600">
            <h3 class="page-header">AI Generator API Key</h3>
            <?php if ($is_api_key_set) : ?>
                <div class="alert alert-info" role="alert">A <b>valid</b> API key has been set successfully.</div>
            <?php endif; ?>
            <form method="post" action="">
                <?php settings_fields('chatgpt_assistant_settings'); ?>
                <?php do_settings_sections('chatgpt_assistant_settings'); ?>
                <div class="form-group">
                    <label for="chatgpt_assistant_api_key">OpenAI API Key</label>
                    <input type="text" class="form-control" id="chatgpt_assistant_api_key"
                           name="chatgpt_assistant_api_key"
                           value="<?php echo esc_attr($api_key); ?>" <?php echo $is_api_key_set ? 'disabled' : ''; ?> />
                    <?php if ($is_api_key_set) : ?>
                        <small class="form-text text-muted">API key is currently set. Click the "Edit" button to change
                            it.</small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" id="chatgpt_assistant_submit_button"
                        name="chatgpt_assistant_submit" <?php echo $is_api_key_set ? 'style="display: none;"' : ''; ?>>
                    Save Settings
                </button>
                <?php if ($is_api_key_set) : ?>
                    <button type="button" class="btn btn-secondary mt-3" id="chatgpt_assistant_edit_button">Edit</button>
                <?php endif; ?>
            </form>
        </div>
        <div class="wrap" style="max-width: 70%">
            <h3 class="page-header">Settings</h3>
            <form class="row g-2">
                <h6>Company or Personel Information:</h6>
                <label for="companyInfoTextarea" class="form-label mx-width-perc-60">Please share details about you or your business, like the industry you are in, the products or services
                    you o!er, your target audience, etc. The more specific the information, the more personalised and
                    relevant the generated content can be.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <textarea class="form-control" id="companyInfoTextarea" rows="2"></textarea>
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                </div>
            </form>
            <form class="row g-2">
                <h6>Brand Guidelines:</h6>
                <label for="brandGuideTextarea" class="form-label mx-width-perc-60">Please provide your brands tone of voice, style guide, key messages, etc. This will help the AI
                    maintain the brand's consistency across all generated content.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <textarea class="form-control" id="brandGuideTextarea" rows="2"></textarea>
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                </div>
            </form>
            <div class="wrap" style="pointer-events: none; border: solid 2px; padding: 40px 30px;">
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
        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-top: -3.5rem;">
            <button class="btn btn-dark" type="button">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
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