document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chatgpt-assistant-form');
    const responseElement = document.getElementById('chatgpt-assistant-response');

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const message = document.getElementById('chatgpt-assistant-message').value;
            responseElement.style.display = 'none';

            // Call the API to generate a response
            const data = new URLSearchParams();
            data.append('action', 'chatgpt_assistant_generate_response');
            data.append('message', message);

            console.log(data.toString());

            fetch(ajaxurl, {
                method: 'POST',
                body: data.toString(),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
                },
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP status ' + response.status + ', ' + response.statusText);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        responseElement.innerHTML = data.data.response;
                        responseElement.className = 'alert alert-success';
                    } else {
                        responseElement.textContent = data.error;
                        responseElement.className = 'alert alert-danger';
                    }
                    responseElement.style.display = 'block';
                })
                .catch(function (error) {
                    responseElement.textContent = 'An error occurred while retrieving the assistant\'s response: ' + error.message;
                    responseElement.className = 'alert alert-danger';
                    responseElement.style.display = 'block';
                });
        });
    }
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

function toggleResponse(link) {
    const responseRow = document.getElementById(link.getAttribute('href').replace('#', ''));
    if (responseRow.classList.contains('show')) {
        link.innerText = 'View';
    } else {
        link.innerText = 'Close';
    }
}

// Add event listeners to the "View Response" links
const viewResponseLinks = document.getElementsByClassName('btn-link');
for (let i = 0; i < viewResponseLinks.length; i++) {
    viewResponseLinks[i].addEventListener('click', function (event) {
        event.preventDefault();
        const link = this;
        toggleResponse(link);
    });
}