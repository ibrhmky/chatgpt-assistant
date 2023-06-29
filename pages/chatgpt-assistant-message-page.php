<?php
// Display the form page
function chatgpt_assistant_form_page(): void
{
    // Check if the user has permission to access the form page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Display the form
    ?>
    <div class="container">
        <h1 class="page-header">ChatGPT Assistant Message</h1>
        <form id="chatgpt-assistant-form" class="mb-4">
            <div class="form-group">
                <label for="chatgpt-assistant-message">Enter your message:</label>
                <textarea class="form-control" id="chatgpt-assistant-message" name="message" rows="5" required></textarea>
            </div>
            <small class="form-text text-muted">Your message will be turned into a post with a related title.</small>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>
        <div id="chatgpt-assistant-response" class="alert alert-info" style="display: none;"></div>
    </div>
    <?php
}