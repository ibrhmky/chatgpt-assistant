<?php

/**
 * Display the form page
 */
function chatgpt_assistant_upgrade_page(): void
{
    // Check if the user has permission to access the form page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Display the form
    ?>

    <?php
}
