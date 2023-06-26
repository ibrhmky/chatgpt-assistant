<?php
/*
 * Plugin Name: ChatGPT Assistant
 * Description: Integrates with OpenAI API to provide an AI-powered assistant.
 * Author: İbrahim KAYA
 * Author URI: https://ibrhmky.com
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chatgpt-assistant
 * Domain Path: /languages
 */

// Include the pages and other sections
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-settings.php';
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-message-page.php';
require_once plugin_dir_path(__FILE__) . 'shortcode/chatgpt-assistant-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'chatgpt-assistant-menu.php';

// Enqueue Bootstrap CSS and JavaScript files
function chatgpt_assistant_enqueue_scripts(): void
{
    $plugin_directory = plugin_dir_url( __FILE__ );

    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', array('jquery'), '4.5.0', true);
    wp_enqueue_style('chatgpt-dashicon', $plugin_directory . 'src/img/chatgpt-dashicon.svg');
    wp_enqueue_style('chatgp-assistant-admin-styles', $plugin_directory . 'src/css/style.css');
    wp_enqueue_script('chatgp-assistant-admin-script', $plugin_directory . 'src/js/script.js');
}
add_action('admin_enqueue_scripts', 'chatgpt_assistant_enqueue_scripts');

// Function to retrieve the API key from plugin settings
function chatgpt_assistant_get_api_key() {
    return get_option('chatgpt_assistant_api_key', '');
}

// Function to generate a response from ChatGPT and publish it as a post
function chatgpt_assistant_generate_response($message): string {
    $api_key = chatgpt_assistant_get_api_key();
    $model_id = 'gpt-3.5-turbo';

    // Prepare the data for the API request
    $data = array(
        'messages' => array(
            array('role' => 'system', 'content' => 'You are a helpful assistant. Before your message done please create a title for it and use "|" as separator and separate title from your message.'),
            array('role' => 'user', 'content' => $message)
        ),
        'model' => $model_id // Add the model parameter
    );

    // Send the API request
    $response = wp_remote_post(
        'https://api.openai.com/v1/chat/completions',
        array(
            'timeout' => 30, // Increase the timeout value (in seconds)
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($data)
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
        return $error_message;
    }

    // Parse the API response
    $response_data = json_decode(wp_remote_retrieve_body($response), true);

    // Extract the assistant's reply and the chosen post title from the response
    $assistant_messages = $response_data['choices'][0]['message']['content'];

    // Separate the title from the assistant's response using the "|" separator
    $title_separator = '|';
    $messages = explode($title_separator, $assistant_messages);
    $chosen_title = trim($messages[0]); // Extract the first part as the chosen title
    $assistant_reply = trim($messages[1]);

    // Create a new post with the assistant's reply and the chosen post title
    $post_data = array(
        'post_title' => $chosen_title,
        'post_content' => $assistant_reply,
        'post_status' => 'publish',
        'post_author' => 1, // Replace with the desired author ID
        'post_type' => 'post',
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        // Debug: return print_r($response_data);
        return 'Success: The assistant\'s reply has been published as a post with ID: ' . $post_id;
    } else {
        return 'Error: An error occurred while publishing the assistant\'s reply.';
    }
}








