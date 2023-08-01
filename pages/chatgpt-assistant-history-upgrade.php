<?php
/**
 * Displays the upgrade page for ChatGPT Assistant.
 */
function chatgpt_assistant_new_post_page(): void {
	// Check if the user has permission to access the settings page
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// Display Post Page
	?>



	<?php
}
