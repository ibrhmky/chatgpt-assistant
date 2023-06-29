<?php

// Display the messages page
function chatgpt_assistant_messages_page(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatgpt_message_history';

    // Retrieve the messages from the database
    $messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY date DESC");

    ?>
    <div class="container">
        <h1 class="page-header">ChatGPT Assistant Previous Messages</h1>
        <table class="wp-list-table table table-auto previous-messages">
            <thead>
            <tr>
                <th scope='col'>No</th>
                <th scope='col'>Message</th>
                <th scope='col'>Title</th>
                <th scope='col'>Post ID</th>
                <th scope='col'>Date</th>
                <th scope='col'>Word Count</th>
                <th scope='col'>Raw Response</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $row_number = 1; // Initialize row number
            foreach ($messages as $message) {
                // Toggle row class
                $row_class = ($row_number % 2 == 0) ? 'even' : 'odd';
                echo '<tr class="' . $row_class . '">';
                echo '<td>' . $row_number . '</td>'; // Display row number
                echo '<td>' . esc_html($message->message) . '</td>';
                echo '<td>' . esc_html($message->title) . '</td>';
                echo '<td><a href="'. get_admin_url() .'post.php?post=' . esc_html($message->post_id) . '&action=edit" target="_blank">' . esc_html($message->post_id) . '</a></td>';
                echo '<td>' . esc_html($message->date) . '</td>';
                echo '<td>' . esc_html($message->word_count) . '</td>';
                echo '<td class="view-response"><a class="btn btn-link" data-toggle="collapse" href="#response-'.$message->id.'" role="button" aria-expanded="false" aria-controls="response-'.$message->id.'" onclick="toggleResponse(this)">View</a></td>';
                echo '</tr>';
                echo '<tr class="collapse ' . $row_class . '" id="response-'.$message->id.'">';
                echo '<td style="background: white" colspan="7"><pre>' . esc_html(print_r(unserialize($message->raw_response), true)) . '</pre></td>';
                echo '</tr>';

                $row_number++; // Increment row number
            }
            ?>
            </tbody>
        </table>
    </div>

    <?php

}