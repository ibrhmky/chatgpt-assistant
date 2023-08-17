<?php
/*
 * Plugin Name: WooDev AI Content Generator
 * Description: Integrates with OpenAI API to provide an AI-powered assistant.
 * Author: İbrahim KAYA
 * Author URI: https://woodev.net
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: chatgpt-assistant
 * Domain Path: /languages
 */

// Include the pages and other sections
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-settings.php';
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-upgrade-page.php';
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-history.php';
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-new-post-page.php';

require_once plugin_dir_path(__FILE__) . 'chatgpt-assistant-menu.php';

/**
 * Enqueue Bootstrap assets for your plugin's pages
 */
function chatgpt_assistant_enqueue_assets(): void
{
    $plugin_directory = plugin_dir_url(__FILE__);

    // Get the current screen object
    $screen = get_current_screen();

    // Define an array of your plugin's page slugs
    $plugin_pages = array(
        'toplevel_page_chatgpt-assistant-settings',
        'ai-content-generator_page_chatgpt-assistant-upgrade',
	    'ai-content-generator_page_chatgpt-assistant-new-post'
    );

    // Check if the current screen is in your plugin's pages
    if ($screen->id == 'ai-content-generator_page_chatgpt-assistant-messages') {
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');

        // Enqueue Bootstrap JS
        wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2');
    }

    // CSS and SVG must be shown everywhere
    wp_enqueue_style('chatgpt-dashicon', $plugin_directory . 'src/img/chatgpt-dashicon.svg');
    wp_enqueue_style('chatgp-assistant-admin-styles', $plugin_directory . 'src/css/style.css');

    // Check if the current screen is in your plugin's pages
    if (in_array($screen->id, $plugin_pages)) {
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css', array(), '5.3.1');
        wp_enqueue_style('bs-stepper', 'https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css');

        // Enqueue Bootstrap JS
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js', array(), '5.3.1');
        wp_enqueue_script('bs-stepper', 'https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js');

        wp_enqueue_script('jquery');
        wp_enqueue_script('twbs', 'https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js');

        wp_enqueue_script('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js');
        wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

        wp_enqueue_script('chatgp-assistant-admin-script', $plugin_directory . 'src/js/script.js');
    }
}
add_action('admin_enqueue_scripts', 'chatgpt_assistant_enqueue_assets');

/**
 * Creates the database table for storing chat message history.
 */
function chatgpt_assistant_create_table(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatgpt_message_history';

    $charset_collate = $wpdb->get_charset_collate();

    // Define the SQL query to create the table
    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        post_id INT(11) NOT NULL,
        date DATETIME NOT NULL,
        word_count INT(11) NOT NULL,
        raw_response TEXT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // Execute the SQL query to create the table
    dbDelta($sql);
}

// Register the table creation function to be executed on plugin activation
register_activation_hook(__FILE__, 'chatgpt_assistant_create_table');

/**
 * Function to retrieve the API key from plugin settings
 */
function chatgpt_assistant_get_api_key()
{
    return get_option('chatgpt_assistant_api_key', '');
}

/**
 * Function to generate a response from ChatGPT and publish it as a post
 */
function chatgpt_assistant_generate_response(): void {
    // Retrieve the message and assistant mode from the request
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $system_message = isset($_POST['system_message']) ? sanitize_textarea_field($_POST['system_message']) : '';
    $location_data = isset($_POST['location_data']) ? sanitize_textarea_field($_POST['location_data']) : '';

    $brand_guidelines = get_option('brandGuideTextarea');
    $company_info = get_option('companyInfoTextarea');
    $company_name = get_option('companyNameTextarea');

    $quotation_marks = false;

    switch ($location_data) {
        case 'postTitleTextInput':
            $message = 'You are a creative marketer for '.$company_info.' Your goal is to create a marketing post title that reflects our brand guidelines and company information. Our brand guidelines '.$brand_guidelines.' The title should establish trust, credibility for our products.';
            $quotation_marks = true;
            break;
        case 'postDescriptionTextarea':
            $message = 'Company Name: '.$company_name.'. You are a creative marketer for '.$company_info.' Your goal is to create a marketing post description for given title that reflects our brand guidelines and company information. Our brand guidelines '.$brand_guidelines.' The description should establish trust, credibility for our products.';
    }

    $api_key = chatgpt_assistant_get_api_key();
    $model_id = 'gpt-3.5-turbo';

    // Prepare the data for the API request
    $data = array(
        'messages' => array(
            array('role' => 'system', 'content' => $system_message), // Provide a default system message if $systemContent is empty
            array('role' => 'user', 'content' =>  $message)
        ),
        'model' => $model_id,
        'temperature' => 1.5,
    );

    // Send the API request
    $response = wp_remote_post(
        'https://api.openai.com/v1/chat/completions',
        array(
            'timeout' => 30,
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($data),
        )
    );

    // Check if the API request was successful
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        $error_message = 'Error: An error occurred while retrieving the assistant\'s response from OpenAI.';
        if (is_wp_error($response)) {
            $error_message .= ' ' . $response->get_error_message();
        } else {
            $response_data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($response_data['error']['message'])) {
                $error_message .= ' ' . $response_data['error']['message'];
            }
        }
        wp_send_json_error(array('error' => $error_message));
        
    }

    // Parse the API response
    $response_data = json_decode(wp_remote_retrieve_body($response), true);

    // Validate the API response data
    if (is_array($response_data) && isset($response_data['choices']) && is_array($response_data['choices']) && count($response_data['choices']) > 0) {
        // Extract the assistant's reply from the response
        $assistant_message = $response_data['choices'][0]['message']['content'];

        if($quotation_marks) {
            // Removing quotation marks
            $assistant_message = str_replace('"', '', $assistant_message);
        }

        if (!$assistant_message) {
            $error_message = 'Error: Body part of the message is empty. There is an error while separating title from post.';
            wp_send_json_error(array('error' => $error_message));
        } else {
            wp_send_json_success(array('success' => true, 'response' => $assistant_message, 'response_data' => $response_data));
        }
    } else {
        $error_message = 'Error: Invalid response data from OpenAI API.';
        wp_send_json_error(array('error' => $error_message));
    }

}

add_action('wp_ajax_chatgpt_assistant_generate_response', 'chatgpt_assistant_generate_response');
add_action('wp_ajax_nopriv_chatgpt_assistant_generate_response', 'chatgpt_assistant_generate_response');
