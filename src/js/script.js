// Wait for the DOM to be fully loaded
async function sendMessageAPI(message = null, listItem = null) {

    // Get the form and response element
    const responseElement = document.getElementById('chatgpt-assistant-response');

    const bulkInputButton = document.getElementById("bulk-input-button");
    let bulkInputMode = false;
    let bulkMessage = '';

    let isDone = false;

    if (bulkInputButton) {
        bulkInputMode = bulkInputButton.classList.contains("btn-danger");
    }

    // Get the message value
    if (!message && bulkInputMode) {
        return;
    }

    if (bulkInputMode) {

        // Remove the previous status class
        listItem.classList.remove("list-group-item-secondary");

        // Update the class for the next message
        listItem.classList.add("list-group-item-warning");
    }

    // Get the message value
    if (!message && !bulkInputMode) {
        message = document.getElementById('chatgpt-assistant-message').value;
    }

    responseElement.style.display = 'none';

    const bulkInputText = bulkInputMode ? 'true' : 'false';

    // Prepare the data to be sent
    const data = new URLSearchParams();
    data.append('action', 'chatgpt_assistant_generate_response');
    data.append('message', message);
    data.append('bulk_input', bulkInputText);

    if (!message) {
        hideLoadingState();
        responseElement.innerHTML = 'An error occurred while retrieving the assistant\'s response: <b>Message is null</b>'
        responseElement.className = 'alert alert-danger';
        responseElement.style.display = 'block';
        return;
    }

    // Send the data to the server
    await fetch(ajaxurl, {
        method: 'POST',
        body: data.toString(),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
        },
    })
        .then(function (response) {

            if (!response.ok) {
                if (!bulkInputMode) {
                    hideLoadingState();
                    throw new Error('HTTP status ' + response.status + ', ' + response.statusText);
                } else {
                    bulkMessage = [response.statusText, false];
                }
            }
            isDone = true;
            return response.json();
        })
        .then(function (data) {

            // Handle the response data
            if (data.success) {
                if (!bulkInputMode) {
                    hideLoadingState();
                    responseElement.innerHTML = data.data.response;
                    responseElement.className = 'alert alert-success';
                } else {
                    bulkMessage = [data.data.response, true];
                }
            } else {
                if (!bulkInputMode) {
                    hideLoadingState();
                    responseElement.textContent = data.error;
                    responseElement.className = 'alert alert-danger';
                } else {
                    bulkMessage = [data.data.response, false];
                }
            }
            responseElement.style.display = 'block';
            isDone = true;
        })
        .catch(function (error) {

            if (!bulkInputMode) {
                hideLoadingState();
                responseElement.textContent = 'An error occurred while retrieving the assistant\'s response: ' + error.message;
                responseElement.className = 'alert alert-danger';
                responseElement.style.display = 'block';
            } else {
                bulkMessage = [error.message, false];
            }
            isDone = true;
        });

    return bulkMessage;
}

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

            // Unfocus the messageTextarea element
            messageTextarea.blur();

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

    if (messageTextarea) { // Attach the event listeners
        messageTextarea.addEventListener('keydown', handleFormSubmit);
        messageTextarea.addEventListener('focus', handleTextareaFocus);
        messageTextarea.addEventListener('blur', handleTextareaBlur);
    }
});

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // Get references to the button and loader elements
    const submitButton = document.getElementById("submit-chatgpt-message");
    const submitButtonText = document.getElementById("submit-btn-text");
    const submitButtonLoader = document.getElementById("submit-btn-loader");

    // Function to show the loading state
    function showLoadingState() {
        submitButton.disabled = true;
        submitButtonText.style.display = "none";
        submitButtonLoader.classList.remove("d-none");
    }

    // Function to hide the loading state
    window.hideLoadingState = function() {
        submitButton.disabled = false;
        submitButtonText.style.display = "inline-block";
        submitButtonLoader.classList.add("d-none");
    }

    // Add event listener to the form submission
    document.getElementById("chatgpt-assistant-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the form from submitting normally

        // Show the loading state
        showLoadingState();

    });

});

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById("chatgpt-assistant-form");
    const submitButton = document.getElementById("submit-chatgpt-message");
    const bulkInputButton = document.getElementById("bulk-input-button");
    const responseDiv = document.getElementById("chatgpt-assistant-response");
    const textarea = document.getElementById("chatgpt-assistant-message");
    const messageList = document.getElementById("message-list");

    let bulkInputMode = false;

    if (bulkInputButton) {

        bulkInputButton.addEventListener("click", function() {
            bulkInputMode = !bulkInputMode;

            messageList.innerHTML = '';

            if (bulkInputMode) {
                bulkInputButton.classList.add("btn-danger");
                bulkInputButton.innerHTML = "Cancel Bulk Input";
            } else {
                bulkInputButton.classList.remove("btn-danger");
                bulkInputButton.innerHTML = "Bulk Input";
            }

            const messageTextarea = document.getElementById("chatgpt-assistant-message");
            const responseDiv = document.getElementById("chatgpt-assistant-response");

            if (bulkInputMode) {
                messageTextarea.placeholder = "Type your messages here, each on a new line...";
                responseDiv.style.display = "inline-block";
                responseDiv.innerHTML = "Bulk input mode has been activated. Every line you entered will be a new message.";
            } else {
                messageTextarea.placeholder = "Type your message here...";
                responseDiv.style.display = "none";
            }

            if (form && bulkInputMode) {
                form.addEventListener("submit", function(event) {
                    event.preventDefault(); // Prevent form submission

                    const messages = extractMessagesFromTextarea();

                    if (messages.length === 0) {
                        responseDiv.innerHTML = "Please enter at least one message.";
                        responseDiv.style.display = "block";
                        return;
                    }

                    // Disable the submit button and bulk input button
                    submitButton.setAttribute("disabled", "true");
                    submitButton.classList.add("disabled");
                    bulkInputButton.setAttribute("disabled", "true");
                    bulkInputButton.classList.add("disabled");

                    // Show the loader
                    const loader = document.getElementById("submit-btn-loader");
                    loader.classList.remove("d-none");

                    // Append all messages to the list
                    messages.forEach((message, index) => {
                        const listItem = document.createElement("li");
                        listItem.classList.add("list-group-item", "list-group-item-secondary");
                        listItem.textContent = message;
                        listItem.id = 'assistant_message_list_' + index;
                        messageList.appendChild(listItem);

                    });

                    // Process messages
                    processMessages(messages);

                });
            }
        });
    }

    function extractMessagesFromTextarea() {
        const inputValue = textarea.value.trim();
        return bulkInputMode ? inputValue.split("\n").map(message => message.trim()) : [inputValue];
    }

    async function processMessages(messages) {

        const promises = [];

        for (const message of messages) {

            const index = messages.indexOf(message);

            const listItem = document.getElementById('assistant_message_list_' + index);

            const promise = sendMessageAPI(message, listItem).then((apiResponse) => {

                if (!apiResponse) return;

                const apiResponseText = apiResponse[0];
                const apiResponseErrorCheck = apiResponse[1];

                if (!apiResponseErrorCheck) {

                    // Remove the previous status class
                    listItem.classList.remove("list-group-item-warning");

                    // Update the class for the next message
                    listItem.classList.add("list-group-item-danger");

                    // Update list item text
                    listItem.textContent += ' - ' + apiResponseText;

                    return;
                }

                if (apiResponse) {

                    // Remove the previous status class
                    listItem.classList.remove("list-group-item-warning");

                    // Update the class for the next message
                    listItem.classList.add("list-group-item-success");

                    // Update list item text
                    listItem.innerHTML = listItem.textContent + ' - ' + apiResponseText;

                }

            });

            promises.push(promise);

        }

        await Promise.all(promises);

        // Enable the submit button and bulk input button
        submitButton.removeAttribute("disabled");
        submitButton.classList.remove("disabled");
        bulkInputButton.removeAttribute("disabled");
        bulkInputButton.classList.remove("disabled");

        // Hide the loader
        hideLoadingState();

        // Clear the textarea
        textarea.value = "";

        // Show the response message
        responseDiv.innerHTML = "Bulk input processing completed.";
        responseDiv.style.display = "block";

        textarea.addEventListener('focus', () => {
            messageList.innerHTML = ''
            responseDiv.innerHTML = "Bulk input mode has been activated. Every line you entered will be a new message.";
        });

    }
});

