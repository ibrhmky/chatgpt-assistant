<?php



// Display the messages page
function chatgpt_assistant_messages_page(): void
{
    // Check if the user has permission to access the page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Retrieve previous messages and responses from the database
    $previous_messages = get_option('chatgpt_assistant_previous_messages', array());

    // Sort the messages by date in descending order
    usort($previous_messages, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // Display the messages and responses in a table
    echo '<h1>Previous Messages</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Message</th><th>Response</th><th>Date</th><th>Response Word Count</th><th>Actions</th></tr></thead>';
    echo '<tbody>';
    foreach ($previous_messages as $index => $message) {
        echo '<tr>';
        echo '<td>' . esc_html($message['message']) . '</td>';
        echo '<td>' . esc_html($message['response']) . '</td>';
        echo '<td>' . esc_html($message['date']) . '</td>';
        echo '<td>' . esc_html(str_word_count($message['response'])) . '</td>';
        echo '<td><button class="button button-secondary" onclick="deleteMessage(' . $index . ')">Delete</button></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

}

// Function to save a message and its response to the database
function chatgpt_assistant_save_message($message, $response): void
{
    $previous_messages = get_option('chatgpt_assistant_previous_messages', array());

    $previous_messages[] = array(
        'message' => $message,
        'response' => $response,
        'date' => current_time('Y-m-d H:i:s'), // Add the current date and time
    );

    update_option('chatgpt_assistant_previous_messages', $previous_messages);
}

function chatgpt_assistant_delete_message(): void
{
    // Check if the request is valid
    if (isset($_POST['index']) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'chatgpt_assistant_delete_message_nonce')) {
        $index = intval($_POST['index']);

        // Retrieve the previous messages from the database
        $previous_messages = get_option('chatgpt_assistant_previous_messages', array());

        // Check if the message index is within range
        if ($index >= 0 && $index < count($previous_messages)) {
            // Remove the message at the specified index
            array_splice($previous_messages, $index, 1);
            update_option('chatgpt_assistant_previous_messages', $previous_messages);

            // Send a success response
            echo 'success';
        }
    }
    exit;
}
add_action('wp_ajax_chatgpt_assistant_delete_message', 'chatgpt_assistant_delete_message');