<?php

/**
 * Display the messages page.
 */
function chatgpt_assistant_messages_page(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'chatgpt_message_history';

    // Retrieve the total number of messages
    $total_messages = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

    // Set the number of messages to display per page
    $messages_per_page = 10;

    // Calculate the total number of pages
    $total_pages = ceil($total_messages / $messages_per_page);

    // Get the current page number
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

    // Calculate the offset for the messages query
    $offset = ($current_page - 1) * $messages_per_page;

    // Define the sorting parameters
    $orderby = isset($_GET['orderby']) ? sanitize_key($_GET['orderby']) : 'date';
    $order = isset($_GET['order']) ? sanitize_key($_GET['order']) : 'desc';

    // Retrieve the messages from the database with sorting and pagination
    $messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT $offset, $messages_per_page");

    ?>
    <div class="container-fluid table-responsive">
        <h3 class="page-header-sm">ChatGPT Assistant Previous Messages</h3>
        <table class="wp-list-table table table-auto table-hover previous-messages">
            <caption><?php echo $total_messages ?> Messages</caption>
            <colgroup>
                <col style="width: 3%">
                <col style="width: 28%">
                <col style="width: 28%">
                <col style="width: 9%">
                <col style="width: 15%">
                <col style="width: 10%">
                <col style="width: 7%">
            </colgroup>
            <thead class="thead-dark">
            <tr>
                <th scope='col'>No</th>
                <th scope='col'>
                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => 'message', 'order' => ((isset($_GET['orderby']) && $_GET['orderby'] == 'message') && (isset($_GET['order']) && $_GET['order'] == 'asc')) ? 'desc' : 'asc'))); ?>">
                        Message <?php echo ($_GET['orderby'] ?? '') == 'message' && ($_GET['order'] ?? '') == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>'; ?>
                    </a>
                </th>
                <th scope='col'>
                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => 'title', 'order' => ((isset($_GET['orderby']) && $_GET['orderby'] == 'title') && (isset($_GET['order']) && $_GET['order'] == 'asc')) ? 'desc' : 'asc'))); ?>">
                        Title <?php echo ($_GET['orderby'] ?? '') == 'title' && ($_GET['order'] ?? '') == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>'; ?>
                    </a>
                </th>
                <th scope='col'>
                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => 'post_id', 'order' => ((isset($_GET['orderby']) && $_GET['orderby'] == 'post_id') && (isset($_GET['order']) && $_GET['order'] == 'asc')) ? 'desc' : 'asc'))); ?>">
                        Post ID <?php echo ($_GET['orderby'] ?? '') == 'post_id' && ($_GET['order'] ?? '') == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>'; ?>
                    </a>
                </th>
                <th scope='col'>
                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => 'date', 'order' => ((isset($_GET['orderby']) && $_GET['orderby'] == 'date') && (isset($_GET['order']) && $_GET['order'] == 'asc')) ? 'desc' : 'asc'))); ?>">
                        Date <?php echo ($_GET['orderby'] ?? '') == 'date' && ($_GET['order'] ?? '') == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>'; ?>
                    </a>
                </th>
                <th scope='col'>
                    <a href="<?php echo esc_url(add_query_arg(array('orderby' => 'word_count', 'order' => ((isset($_GET['orderby']) && $_GET['orderby'] == 'word_count') && (isset($_GET['order']) && $_GET['order'] == 'asc')) ? 'desc' : 'asc'))); ?>">
                        Word Count <?php echo ($_GET['orderby'] ?? '') == 'word_count' && ($_GET['order'] ?? '') == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>'; ?>
                    </a>
                </th>
                <th scope='col'>Raw Response</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $row_number = ($current_page - 1) * $messages_per_page + 1; // Initialize row number
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
                echo '<td style="background: white" colspan="7"><pre style="white-space: pre-wrap; word-wrap: break-word;">' . esc_html(print_r(unserialize($message->raw_response), true)) . '</pre></td>';
                echo '</tr>';

                $row_number++; // Increment row number
            }
            ?>
            </tbody>
        </table>

        <?php
        // Display pagination links
        echo '<nav aria-label="Search results pages" class="pagination justify-content-center">';
        echo '<ul class="pagination">';
        $pages = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $total_pages,
            'current' => $current_page,
        ));

        // Turn paginate_links created look into Bootsrap look
        $page_1 = str_replace('class="page-numbers','class="page-link',$pages);
        $page_2 = str_replace('<a class="page-link"','<li class="page-item"><a class="page-link"', $page_1);
        $page_3 = str_replace('<a class="next','</li><li class="page-item"><a class="page-link next', $page_2);
        $page_4 = str_replace('<a class="prev','<li class="page-item"><a class="page-link prev', $page_3);
        $page_5 = str_replace('«</a>','«</a></li>', $page_4);
        $page_6 = str_replace('<span aria-current','<li class="page-item disabled"><span aria-current', $page_5);

        echo str_replace('</span>','</span></li>', $page_6);

        echo '</ul>';
        echo '</nav>';
        ?>
    </div>

    <?php

}