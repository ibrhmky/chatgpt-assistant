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
require_once plugin_dir_path(__FILE__) . 'pages/chatgpt-assistant-history.php';

require_once plugin_dir_path(__FILE__) . 'shortcode/chatgpt-assistant-shortcode.php';

require_once plugin_dir_path(__FILE__) . 'chatgpt-assistant-menu.php';

// Enqueue Bootstrap assets for your plugin's pages
function chatgpt_assistant_enqueue_assets(): void
{
    $plugin_directory = plugin_dir_url( __FILE__ );

    // Get the current screen object
    $screen = get_current_screen();

    // Define an array of your plugin's page slugs
    $plugin_pages = array(
        'toplevel_page_chatgpt-assistant-settings',
        'chatgpt-assistant_page_chatgpt-assistant-form',
        'chatgpt-assistant_page_chatgpt-assistant-messages'
    );

    // Check if the current screen is in your plugin's pages
    if (in_array($screen->id, $plugin_pages)) {
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');

        // Enqueue Bootstrap JS
        wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '4.5.2');

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'twbs', 'https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js');

        wp_enqueue_style('chatgpt-dashicon', $plugin_directory . 'src/img/chatgpt-dashicon.svg');
        wp_enqueue_style('chatgp-assistant-admin-styles', $plugin_directory . 'src/css/style.css');

        wp_enqueue_script('chatgp-assistant-admin-script', $plugin_directory . 'src/js/script.js');
    }
}
add_action('admin_enqueue_scripts', 'chatgpt_assistant_enqueue_assets');

function chatgpt_assistant_create_table(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatgpt_message_history';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        post_id INT(11) NOT NULL,
        date DATETIME NOT NULL,
        word_count INT(11) NOT NULL,
        raw_response TEXT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'chatgpt_assistant_create_table');

// Function to retrieve the API key from plugin settings
function chatgpt_assistant_get_api_key() {
    return get_option('chatgpt_assistant_api_key', '');
}

// Function to generate a response from ChatGPT and publish it as a post
function chatgpt_assistant_generate_response(): void {
    // Retrieve the message from the request
    $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';

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
        wp_send_json_error(array('error' => $error_message));
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

    global $wpdb;

    // Create a new post with the assistant's reply and the chosen post title
    $table_name = $wpdb->prefix . 'chatgpt_message_history';
    // Get the post object
    $post = get_post($post_id);

    $data = array(
        'title' => $chosen_title,
        'message' => $message,
        'post_id' => $post_id,
        'date' => $post->post_date,
        'word_count' => str_word_count($assistant_reply),
        'raw_response' => serialize($response_data)
    );

    if ($post_id) {
        // Debug: return print_r($response_data);
        $wpdb->insert($table_name, $data);

        wp_send_json_success(array('success' => true, 'response' => '<span>Success: The assistant\'s reply has been published as a post with ID: <a href="'. get_admin_url() .'post.php?post='.$post_id.'&action=edit" target="_blank">'.$post_id.'</a></span>'));
    } else {
        wp_send_json_error(array('success' => false, 'error' => 'Error: An error occurred while publishing the assistant\'s reply.'));
    }
}

add_action('wp_ajax_chatgpt_assistant_generate_response', 'chatgpt_assistant_generate_response');
add_action('wp_ajax_nopriv_chatgpt_assistant_generate_response', 'chatgpt_assistant_generate_response');







