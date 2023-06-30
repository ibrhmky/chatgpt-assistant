<?php

/**
 * Add a shortcode to display the assistant form
 */
function chatgpt_assistant_shortcode(): string
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