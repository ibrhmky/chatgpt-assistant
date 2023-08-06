<?php
/**
 * Displays the new post page for ChatGPT Assistant.
 */
function chatgpt_assistant_new_post_page(): void {
	// Check if the user has permission to access the settings page
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

    $buttons_disabled = '';

    if(!get_option('companyInfoTextarea') || !get_option('brandGuideTextarea')) {
        $buttons_disabled = 'disabled';
    }

	// Display Post Page
	?>

    <div class="bs-stepper">
        <div class="bs-stepper-header" role="tablist">
            <!-- your steps here -->
            <div class="step" data-target="#building_the_post_step">
                <button type="button" class="step-trigger" role="tab" aria-controls="building_the_post_step" id="building_the_post_step-trigger">
                    <span class="bs-stepper-circle">1</span>
                    <span class="bs-stepper-label">First</span>
                </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#description_post_step">
                <button type="button" class="step-trigger" role="tab" aria-controls="description_post_step" id="description_post_step-trigger">
                    <span class="bs-stepper-circle">2</span>
                    <span class="bs-stepper-label">Second</span>
                </button>
            </div>
        </div>
        <div class="bs-stepper-content">
            <!-- your steps content here -->
            <div id="building_the_post_step" class="content" role="tabpanel" aria-labelledby="building_the_post_step-trigger">
                <div class="container mt-1">
                    <?php if ($buttons_disabled): ?>
                        <div id="brand_warning_message" class="alert alert-danger alert-dismissible fade show"
                             role="alert">
                            <h5>Warning</h5>
                            <ul>
                                <li>&#9679; Adding website information on the "Settings" page can improve content
                                    generation
                                    experience.
                                </li>
                                <li>&#9679; It is required, and help AI to support you while adding descriptions.</li>
                                <li>&#9679; Please visit <a
                                            href="<?php echo esc_url(admin_url("admin.php?page=chatgpt-assistant-settings")); ?>">settings
                                        page and fill Company Informations and Brand Guidelines.</a>.
                                </li>
                            </ul>
                            <button type="button" id="chatgpt_brand_warning_button" class="btn-close"
                                    data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif ?>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-bottom: -3.5rem;">
                        <button class="btn btn-dark" type="button" onclick="nextStep()">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
                    </div>
                    <div class="description-header mt-3">
                        <h3>Add New Post</h3>
                    </div>
                    <div id="chatgpt_assistant_new_post_wrapper" class="wrap" style="max-width: 70%">
                        <div class="row g-2">
                            <h6>Title</h6>
                            <label for="postTitleTextInput" class="form-label mx-width-perc-60">This field is optional, but it's nice to
                                have.</label>
                            <div class="mb-3 col-auto mn-width-400">
                                <input type="text" class="form-control" id="postTitleTextInput" placeholder="Post Title">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary mb-3" onclick="sendMessageAPI('postTitleTextInput')" <?php echo $buttons_disabled; ?>>Generate title</button>
                                <button class="btn btn-danger mb-3" onclick="deleteSettingsAjax('postTitleTextInput')">Delete</button>
                            </div>
                        </div>
                        <div class="row g-2">
                            <h6>Description</h6>
                            <label for="postDescriptionTextarea" class="form-label mx-width-perc-60">This field is optional, but it's nice to
                                have.</label>
                            <div class="mb-3 col-auto mn-width-400">
                                <textarea class="form-control" id="postDescriptionTextarea" rows="3" placeholder="Post Description"></textarea>
                            </div>
                            <div class="mb-3 col-auto mn-width-400">
                                <button class="btn btn-primary mb-3" onclick="sendMessageAPI('postDescriptionTextarea')" <?php echo $buttons_disabled; ?>>Generate description</button>
                                <button class="btn btn-danger mb-3" onclick="deleteSettingsAjax('postDescriptionTextarea')">Delete</button>
                            </div>
                        </div>
                        <div class="wrap" style="pointer-events: none; opacity: 0.5;">
                            <form class="row g-2">
                                <h6>Tags (Available on the PRO version.)</h6>
                                <label for="tagsTextarea" class="form-label mx-width-perc-60">Please note that the ability to
                                    separate tags using
                                    commas.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="tagsTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Target Audience (Available on the PRO version.)</h6>
                                <label for="targetAudienceTextarea" class="form-label mx-width-perc-60">This will help the AI
                                    understand the tone, style,
                                    and language complexity it should aim for. For
                                    instance, the writing style for a tech-savvy audience will be different from that for a general
                                    audience.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="targetAudienceTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Content Length (Available on the PRO version.)</h6>
                                <label for="contentLengthTextarea" class="form-label mx-width-perc-60">Please specify the desired
                                    length of the content.
                                    This can be in terms of the number of words,
                                    characters, or paragraphs.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="contentLengthTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Tone of Voice (Available on the PRO version.)</h6>
                                <label for="tonVoiceTextarea" class="form-label mx-width-perc-60">Is the content supposed to be
                                    formal, informal,
                                    conversational, professional, etc.? Understanding the
                                    tone can help in creating the right content that matches the brand voice.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="tonVoiceTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Call to Action (Available on the PRO version.)</h6>
                                <label for="callActionTextarea" class="form-label mx-width-perc-60">Call to Action (Available on the
                                    PRO version.)
                                    Most marketing content includes a call to action (CTA). Ask users what action they want their
                                    readers
                                    to take after reading the content.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="callActionTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Specific Inclusions/Exclusions (Available on the PRO version.)</h6>
                                <label for="inclusionsExclusionsTextarea" class="form-label mx-width-perc-60">Allow users to specify
                                    any specific points
                                    or information that they want to include or exclude in the
                                    content.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="inclusionsExclusionsTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <h6>Content Format/Structure (Available on the PRO version.)</h6>
                                <label for="contentFormatTextarea" class="form-label mx-width-perc-60">This could include headings,
                                    subheadings, bullet points, numbered lists, etc.</label>
                                <div class="mb-3 col-auto mn-width-400">
                                    <textarea class="form-control" id="contentFormatTextarea" rows="2"></textarea>
                                </div>
                                <div class="mb-3 col-auto mn-width-400">
                                    <button type="submit" class="btn btn-primary mb-3">Generate</button>
                                </div>
                            </form>
                            <form class="row g-2">
                                <div class="mb-3 col-auto mn-width-400">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Generate a featured photo for the post. *only available on the PRO version.
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-top: -3.5rem;">
                        <button class="btn btn-dark" type="button" onclick="stepper.next()">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
                    </div>
                </div>
            </div>
            <div id="description_post_step" class="content" role="tabpanel" aria-labelledby="description_post_step-trigger">
                <div class="container mt-5">
                    <div class="description-header mt-3">
                        <h3>Post Description</h3>
                    </div>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="my_form_submission">
                        <?php
                            settings_fields('chatgpt_assistant_settings');
                            do_settings_sections('chatgpt_assistant_settings');
                            // Display the post title and WordPress post editor
                            $post_title = ''; // You can set the initial post title here if needed.
                            $editor_content = ''; // Your initial editor content here if needed.
                        ?>
                        <div class="mb-3 col-auto mx-width-600">
                            <label for="chatpgt_assistant_post_title" class="form-label mx-width-perc-60">Title</label>
                            <input type="text" class="form-control" id="chatpgt_assistant_post_title" name="chatpgt_assistant_post_title" value="<?php echo esc_attr($post_title); ?>">
                        </div>
                        <?php
                            $editor_id = 'chatgpt_assistant_unique_editor';
                            $editor_settings = array(
                                'wpautop' => true,
                                'media_buttons' => true,
                                'textarea_name' => 'chatpgt_assistant_editor',
                                'textarea_rows' => 20,
                                'tinymce' => true,
                                'quicktags' => true,
                                'teeny' => false,
                                'dfw' => false,
                                'media_buttons_context' => 'side',
                                'drag_drop_upload' => true,
                                'wp_lang_attr' => get_bloginfo('language'),
                            );
                            wp_editor($editor_content, $editor_id, $editor_settings);
                        ?>
                        <div class="d-grid gap-2 d-md-flex mt-3 justify-content-md-end"">
                            <button type="submit" class="btn btn-primary mb-3">Publish</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


	<?php

}

function my_plugin_save_settings() {
    if (isset($_POST['chatpgt_assistant_post_title']) && isset($_POST['chatpgt_assistant_editor'])) {
        // Sanitize and prepare the data
        $post_title = sanitize_text_field($_POST['chatpgt_assistant_post_title']);
        $editor_content = wp_kses_post($_POST['chatpgt_assistant_editor']);

        // Create the post array
        $new_post = array(
            'post_title'   => $post_title,
            'post_content' => $editor_content,
            'post_status'  => 'publish', // You can use 'draft' if you want to save it as a draft first.
            'post_type'    => 'post', // Change to your custom post type if needed.
        );

        // Insert the post into the database
        $post_id = wp_insert_post($new_post);

        // Optionally, you can redirect the user to the newly created post's edit page.
        if ($post_id) {
            $edit_post_link = get_edit_post_link($post_id);
            if ($edit_post_link) {
                wp_redirect($edit_post_link);
                exit;
            }
        }
    }
}
add_action('admin_init', 'my_plugin_save_settings');


// Define the function to handle the form submission and publish a post
function publish_post_from_form()
{
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Make sure the required fields are present (you can add more validation if needed)
        if (isset($_POST['chatpgt_assistant_post_title']) && isset($_POST['chatpgt_assistant_editor'])) {
            // Sanitize and retrieve the form data
            $post_title = sanitize_text_field($_POST['chatpgt_assistant_post_title']);
            $editor_content = $_POST['chatpgt_assistant_editor'];

            // Prepare the post data
            $post_data = array(
                'post_title' => $post_title,
                'post_content' => $editor_content,
                'post_status' => 'draft', // Change to 'draft' if you want to save as a draft instead
                'post_type' => 'post', // Change to 'page' if you want to publish a page instead
            );

            // Insert the post into the database
            $post_id = wp_insert_post($post_data);

            // Check if the post was successfully inserted
            if ($post_id) {
                // Redirect to the newly created post's URL
                $post_permalink = get_permalink($post_id);
                wp_redirect($post_permalink);
                exit;
            } else {
                // If there was an error, you can handle it here
                echo 'Error publishing the post.';
            }
        }
    }
}

// Hook the function to the 'admin_post' action hook for logged-in users
add_action('admin_post_my_form_submission', 'publish_post_from_form');

// Hook the function to the 'admin_post_nopriv' action hook for non-logged-in users
add_action('admin_post_nopriv_my_form_submission', 'publish_post_from_form');