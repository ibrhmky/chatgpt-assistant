<?php

/**
 * Display the form page
 */
function chatgpt_assistant_form_page(): void
{
    // Check if the user has permission to access the form page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Display the form
    ?>
    <div class="container mx-width-600">
        <h3 class="page-header">ChatGPT Assistant Message</h3>
        <form id="chatgpt-assistant-form" class="mb-4">
            <div class="form-group">
                <textarea class="form-control" id="chatgpt-assistant-message" name="message" rows="5" placeholder="Type your message here..." required></textarea>
            </div>
            <small class="form-text text-muted">Your message will be turned into a post with a related title.</small>
            <div class="submit-wrapper mt-2">
                <button id="submit-chatgpt-message" type="submit" class="btn btn-primary">
                    <span id="submit-btn-text">Submit</span>
                    <span id="submit-btn-loader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                <span id="chatgpt-assistant-submit-info" class="chatgpt-assistant-submit-info">(Ctrl+Enter)</span>
            </div>
        </form>
        <div id="chatgpt-assistant-response" class="alert alert-info" style="display: none;"></div>
    </div>
    <?php
}
