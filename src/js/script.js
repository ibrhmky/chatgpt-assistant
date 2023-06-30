// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // Get the form and response element
    const form = document.getElementById('chatgpt-assistant-form');
    const responseElement = document.getElementById('chatgpt-assistant-response');

    if (form) {
        // Add submit event listener to the form
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            // Get the message value
            const message = document.getElementById('chatgpt-assistant-message').value;
            responseElement.style.display = 'none';

            // Prepare the data to be sent
            const data = new URLSearchParams();
            data.append('action', 'chatgpt_assistant_generate_response');
            data.append('message', message);

            console.log(data.toString());

            // Send the data to the server
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
                    // Handle the response data
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

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // Get the edit button and API key input
    const editButton = document.getElementById('chatgpt_assistant_edit_button');
    const apiKeyInput = document.getElementById('chatgpt_assistant_api_key');

    if (editButton) {
        // Add click event listener to the edit button
        editButton.addEventListener('click', function() {
            // Enable the API key input field
            apiKeyInput.removeAttribute('disabled');
            apiKeyInput.focus();
        });
    }
});

// Function to toggle the response visibility
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

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Find the message input field
    const messageInput = document.getElementById('message');

    // Add an event listener for keydown events
    messageInput.addEventListener('keydown', function(event) {
        // Check if the event key is Enter and the event's CTRL (or Meta) key is pressed
        if ((event.key === 'Enter' || event.keyCode === 13) && (event.ctrlKey || event.metaKey)) {
            // Prevent the default behavior of the Enter key (usually creating a new line)
            event.preventDefault();

            // Submit the form
            document.getElementById('chat-form').submit();
        }
    });
});

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // Find the message textarea, submit button, and submit info
    const messageTextarea = document.getElementById('chatgpt-assistant-message');
    const submitButton = document.getElementById('submit-chatgpt-message');
    const submitInfo = document.getElementById('chatgpt-assistant-submit-info');

    // Function to check if the user pressed CTRL+ENTER (or CMD+ENTER on macOS)
    function isCtrlEnter(event) {
        return (event.ctrlKey || event.metaKey) && event.key === 'Enter';
    }

    // Function to handle form submission
    function handleFormSubmit(event) {
        if (isCtrlEnter(event)) {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Programmatically click the submit button
            submitButton.click();
        }
    }

    // Function to handle textarea focus
    function handleTextareaFocus() {
        submitInfo.style.display = 'inline'; // Show the submit info text
    }

    // Function to handle textarea blur
    function handleTextareaBlur() {
        submitInfo.style.display = 'none'; // Hide the submit info text
    }

    // Attach the event listeners
    messageTextarea.addEventListener('keydown', handleFormSubmit);
    messageTextarea.addEventListener('focus', handleTextareaFocus);
    messageTextarea.addEventListener('blur', handleTextareaBlur);
});
