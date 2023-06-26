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

// Add a shortcode to display the assistant form
function chatgpt_assistant_shortcode($atts): string
{
    $output = '';

    // Check if the form is submitted
    if (isset($_POST['chatgpt_assistant_submit'])) {
        $message = sanitize_text_field($_POST['chatgpt_assistant_message']);

        // Call the OpenAI API to generate a response
        $response = chatgpt_assistant_generate_response($message);

        // Display the response
        $output .= '<div class="chatgpt-assistant-response">' . $response . '</div>';
    }

    // Display the assistant form
    $output .= '
        <form method="post" action="">
            <label for="chatgpt_assistant_message">Enter your message:</label>
            <input type="text" name="chatgpt_assistant_message" id="chatgpt_assistant_message" required>
            <input type="submit" name="chatgpt_assistant_submit" value="Submit">
        </form>
    ';

    return $output;
}
add_shortcode('chatgpt-assistant', 'chatgpt_assistant_shortcode');

// Add a menu item for the plugin settings
function chatgpt_assistant_add_menu(): void
{
    add_menu_page(
        'ChatGPT Assistant',
        'ChatGPT Assistant',
        'manage_options',
        'chatgpt-assistant-settings',
        'chatgpt_assistant_settings_page',
        'dashicons-admin-generic'
    );
}
add_action('admin_menu', 'chatgpt_assistant_add_menu');

// Display the plugin settings page
function chatgpt_assistant_settings_page(): void
{
    // Check if the user has permission to access the settings page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Save the API key if the form is submitted
    if (isset($_POST['chatgpt_assistant_submit'])) {
        update_option('chatgpt_assistant_api_key', $_POST['chatgpt_assistant_api_key']);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Display the settings form
    ?>
    <div class="wrap">
        <h1>ChatGPT Assistant Settings</h1>
        <form method="post" action="">
            <?php settings_fields('chatgpt_assistant_settings'); ?>
            <?php do_settings_sections('chatgpt_assistant_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">OpenAI API Key</th>
                    <td><input type="text" name="chatgpt_assistant_api_key" value="<?php echo esc_attr(get_option('chatgpt_assistant_api_key')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'chatgpt_assistant_submit'); ?>
        </form>
    </div>
    <?php
}

// Register the plugin settings
function chatgpt_assistant_register_settings(): void
{
    register_setting('chatgpt_assistant_settings', 'chatgpt_assistant_api_key', 'sanitize_text_field');
}
add_action('admin_init', 'chatgpt_assistant_register_settings');

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

// Add a submenu item for the form
function chatgpt_assistant_add_submenu(): void
{
    add_submenu_page(
        'chatgpt-assistant-settings',
        'ChatGPT Assistant Form',
        'Message',
        'manage_options',
        'chatgpt-assistant-form',
        'chatgpt_assistant_form_page'
    );
}
add_action('admin_menu', 'chatgpt_assistant_add_submenu');

// Display the form page
function chatgpt_assistant_form_page(): void
{
    // Check if the user has permission to access the form page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Handle form submission
    if (isset($_POST['chatgpt_assistant_submit'])) {
        $message = sanitize_text_field($_POST['chatgpt_assistant_message']);

        // Generate response and publish as a post
        $response = chatgpt_assistant_generate_response($message);

        // Display success or error message
        if (str_starts_with($response, 'Error')) {
            $message_class = 'error';
        } else {
            $message_class = 'updated';
        }

        echo '<div class="' . $message_class . '"><p>' . $response . '</p></div>';
    }

    // Display the form
    ?>
    <div class="wrap">
        <h1>ChatGPT Assistant Form</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Message</th>
                    <td><textarea name="chatgpt_assistant_message" rows="5" cols="50"></textarea></td>
                </tr>
            </table>
            <?php submit_button('Submit', 'primary', 'chatgpt_assistant_submit'); ?>
        </form>
    </div>
    <?php
}



