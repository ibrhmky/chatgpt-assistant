<?php
/**
 * Displays the new post page for ChatGPT Assistant.
 */
function chatgpt_assistant_new_post_page(): void {
	// Check if the user has permission to access the settings page
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// Display Post Page
	?>

	<div class="container mt-5">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5>Warning</h5>
            <ul>
                <li>&#9679; Adding website information on the "Settings" page can improve content generation
                    experience.</li>
                <li>&#9679; It is required, and help AI to support you while adding descriptions.</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end" style="margin-bottom: -3.5rem;">
            <button class="btn btn-dark" type="button">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
        <div class="description-header mt-3">
            <h3>Add New Post Description</h3>
            <button type="button" class="btn btn-outline-secondary mb-2" style="pointer-events: none">Step 1</button>
        </div>
        <div class="wrap" style="max-width: 70%">
            <form class="row g-2">
                <h6>Title</h6>
                <label for="postTitleTextInput" class="form-label mx-width-perc-60">This field is optional, but it's nice to
                    have.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <input type="text" class="form-control" id="postTitleTextInput" placeholder="Post Title">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3">Generate title</button>
                </div>
            </form>
            <form class="row g-2">
                <h6>Description</h6>
                <label for="postDescriptionTextarea" class="form-label mx-width-perc-60">This field is optional, but it's nice to
                    have.</label>
                <div class="mb-3 col-auto mn-width-400">
                    <textarea class="form-control" id="postDescriptionTextarea" rows="3"
                              placeholder="Post Description"></textarea>
                </div>
                <div class="mb-3 col-auto mn-width-400">
                    <button type="submit" class="btn btn-primary mb-3">Generate description</button>
                </div>
            </form>
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
            <button class="btn btn-dark" type="button">Next<i class="fa-solid fa-arrow-right-long ms-2"></i></button>
        </div>
	</div>

	<?php

}