document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chatgpt-assistant-form');
    const responseElement = document.getElementById('chatgpt-assistant-response');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const message = document.getElementById('chatgpt-assistant-message').value;
        responseElement.style.display = 'none';

        // Call the API to generate a response
        const data = new FormData();
        data.append('action', 'chatgpt_assistant_generate_response');
        data.append('message', message);

        fetch(chatgptAssistantData.ajaxurl, {
            method: 'POST',
            body: data
        })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    responseElement.textContent = data.response;
                    responseElement.className = 'alert alert-success';
                } else {
                    responseElement.textContent = data.error;
                    responseElement.className = 'alert alert-danger';
                }
                responseElement.style.display = 'block';
            })
            .catch(function(error) {
                responseElement.textContent = 'An error occurred while retrieving the assistant\'s response.';
                responseElement.className = 'alert alert-danger';
                responseElement.style.display = 'block';
            });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('chatgpt_assistant_edit_button');
    const apiKeyInput = document.getElementById('chatgpt_assistant_api_key');

    if (editButton) {
        editButton.addEventListener('click', function() {
            apiKeyInput.removeAttribute('disabled');
            apiKeyInput.focus();
        });
    }
});

// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    // Get the Edit button element
    const editButton = document.getElementById("chatgpt_assistant_edit_button");

    // Get the API key input element
    const apiKeyInput = document.getElementById("chatgpt_assistant_api_key");

    // Get the submit button element
    const submitButton = document.getElementById("chatgpt_assistant_submit_button");

    if (editButton) { // Add click event listener to the Edit button
        editButton.addEventListener("click", function () {
            // Enable the input field
            apiKeyInput.disabled = false;

            // Show the submit button
            submitButton.style.display = "block";

            // Hide the Edit button
            editButton.style.display = "none";
        });
    }
});

function deleteMessage(index) {
    if (confirm('Are you sure you want to delete this message?')) {
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Prepare the request
        xhr.open('POST', ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Set up the data to send
        const data = 'action=chatgpt_assistant_delete_message' +
            '&index=' + index +
            '&nonce=<?php echo wp_create_nonce("chatgpt_assistant_delete_message_nonce"); ?>';

        // Set up the callback function
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Refresh the page on successful deletion
                if (xhr.responseText === 'success') {
                    location.reload();
                }
            }
        };

        // Send the request
        xhr.send(data);
    }
}