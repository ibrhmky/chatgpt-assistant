document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('chatgpt-assistant-form');
    var responseElement = document.getElementById('chatgpt-assistant-response');

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var message = document.getElementById('chatgpt-assistant-message').value;
        responseElement.style.display = 'none';

        // Call the API to generate a response
        var data = new FormData();
        data.append('action', 'chatgpt_assistant_generate_response');
        data.append('message', message);

        fetch(ajaxurl, {
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
    var editButton = document.getElementById('chatgpt_assistant_edit_button');
    var apiKeyInput = document.getElementById('chatgpt_assistant_api_key');

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
    var editButton = document.getElementById("chatgpt_assistant_edit_button");

    // Get the API key input element
    var apiKeyInput = document.getElementById("chatgpt_assistant_api_key");

    // Get the submit button element
    var submitButton = document.getElementById("chatgpt_assistant_submit_button");

    // Add click event listener to the Edit button
    editButton.addEventListener("click", function() {
        // Enable the input field
        apiKeyInput.disabled = false;

        // Show the submit button
        submitButton.style.display = "block";

        // Hide the Edit button
        editButton.style.display = "none";
    });
});