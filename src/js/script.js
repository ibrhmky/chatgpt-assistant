// Wait for the DOM to be fully loaded
async function sendMessageAPI(location = null, message) {

    let systemMessage = 'Default system message.';
    let addToSettings = true;
    let addToFormData = false;

    const targetInputArea = document.querySelector(`#${location}`);
    message = targetInputArea.value;

    // show loading state
    showHideLoadingState(location, true);

    switch (location) {
        case 'companyInfoTextarea':

            systemMessage = 'You are an expert on creating company info. Use given message and look professional about this. Make it 2 sentences max.';

            if(!message) {
                message = 'Create a fiction company and make short info about the company. Make it 2 sentences max.'
            }
            break;
        case 'brandGuideTextarea':

            systemMessage = 'You are an expert on creating company brand guidelines. Use given message and look professional about this. Make it 2 sentences max.';

            const companyInfoValue = document.querySelector(`#companyInfoTextarea`).value;

            if(!message && !companyInfoValue) {
                message = 'Create a fiction company and make short brand guideline for the company. Make it 2 sentences max.'
            } else if (!message){
                message = 'Company info: ' + companyInfoValue + ' Create a brand guideline for the company. Make it 2 sentences max.'
            }
            break;
        case 'postTitleTextInput':

            addToSettings = false
            systemMessage = 'You are an expert marketer. Please generate a wordpress post title and consider wordpress guidelines.'
            break;

        case 'postDescriptionTextarea':

            const postTitleTextInput = document.querySelector('#postTitleTextInput')

            addToSettings = false;
            addToFormData = true;

            systemMessage = 'You are an expert on marketing and SEO. Please generate a wordpress post description with given title and consider wordpress guidelines. Post title: ' + postTitleTextInput.value
    }

    // Prepare the data to be sent
    const formData = new FormData();
    formData.append('action', 'chatgpt_assistant_generate_response');
    formData.append('message', message);
    formData.append('system_message', systemMessage);
    formData.append('location_data', location)

    // Send the data to the server
    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData,
        });

        showHideLoadingState(location, false)

        if (!response.ok) {
            throw new Error('HTTP status ' + response.status + ', ' + response.statusText);
        }

        const responseData = await response.json();

        // Handle the response data
        if (responseData.success) {
            const responseMessage = responseData.data.response;
            targetInputArea.value = responseMessage;
            sessionStorage.setItem(location, responseMessage);

            if(addToFormData) messageHistoryPrepare(JSON.stringify(responseData.data.response_data));

            if (addToSettings) {
                const settingsFormData = new FormData();
                settingsFormData.append('action', 'chatgpt_assistant_setting_action_callback');
                settingsFormData.append('setting_value', targetInputArea.value);
                settingsFormData.append('setting_key', targetInputArea.id);

                await settingsAjaxRequest(settingsFormData, true);
            }
        }

    } catch (error) {
        showHideLoadingState(location, false)
        throw error;
    }

}

function messageHistoryPrepare(responseData) {
    const responseDataInput = document.querySelector(`#responseDataRaw`);

    if (responseDataInput) {
        responseDataInput.value = responseData
        sessionStorage.setItem('responseDataRaw', responseData)
    }
}

function showHideLoadingState(location, showOrNot) {

    const targetInputAreaButton = document.querySelector(`#${location}_button`);
    const targetInputAreaLoad = document.querySelector(`#${location}_load`);

    if (showOrNot) {
        targetInputAreaButton.classList.add('d-none');
        targetInputAreaLoad.classList.remove('d-none');
    } else {
        targetInputAreaButton.classList.remove('d-none');
        targetInputAreaLoad.classList.add('d-none');
    }

}

let stepper
document.addEventListener('DOMContentLoaded', function () {

    const stepperDiv = document.querySelector('.bs-stepper');

    if(stepperDiv) {
        stepper = new Stepper(stepperDiv, {
            linear: false,
            animation: true
        })

        let refreshOrNot = false;

        if (window.performance && window.performance.navigation) {
            switch (performance.navigation.type) {
                case 0:
                    sessionStorage.setItem('activeStepIndex', '0');
                    refreshOrNot = false;
                    break;
                case 1:
                    refreshOrNot = true;
                    break;
            }
        }

        const stepperHeader = document.querySelector('.bs-stepper-header');
        const steps = stepperHeader.querySelectorAll('.step');

        // Get the active step from session storage or set it to 0 (the index of the first step)
        let activeStepIndex = sessionStorage.getItem('activeStepIndex');
        if (!activeStepIndex) {
            activeStepIndex = '0';
            sessionStorage.setItem('activeStepIndex', activeStepIndex);
        }

        // Add the 'active' class to the initial active step
        steps[parseInt(activeStepIndex)].classList.add('active');

        const buildingPostStep = document.querySelector('#building_the_post_step');
        const descPostStep = document.querySelector('#description_post_step');

        if (activeStepIndex === '0') {
            buildingPostStep.classList.remove('dstepper-none');
            buildingPostStep.classList.add('dstepper-block');
            buildingPostStep.classList.add('active');
        } else {
            descPostStep.classList.remove('dstepper-none');
            descPostStep.classList.add('dstepper-block');
            descPostStep.classList.add('active');

            steps[0].classList.remove('active');

            buildingPostStep.classList.remove('dstepper-block');
            buildingPostStep.classList.add('dstepper-none');
            buildingPostStep.classList.remove('active');
        }

        // Add click event listeners to the step buttons
        steps.forEach((step, index) => {
            step.addEventListener('click', () => {
                // Update the active step index in session storage
                sessionStorage.setItem('activeStepIndex', index);
            });
        });
    }

})

function nextStep() {
    stepper.next()
    document.querySelector('#chatpgt_assistant_post_title').value = document.querySelector('#postTitleTextInput').value

    // Set the initial content in the editor's underlying textarea
    tinyMCE.get('chatgpt_assistant_unique_editor').setContent(document.querySelector('#postDescriptionTextarea').value);

}

document.addEventListener('DOMContentLoaded', function() {

    // Get a list of all the input elements on the page
    const inputElementWrapper = document.querySelector('#chatgpt_assistant_new_post_wrapper');
    const inputElementWrapperDesc = document.querySelector('#description_post_step');


    if (inputElementWrapper) {
        getAllInputValues(inputElementWrapper);
    }

    if (inputElementWrapperDesc) {
        getAllInputValues(inputElementWrapperDesc);
    }

});

function getAllInputValues(inputElementWrapper) {

    if (!inputElementWrapper) {
        return;
    }

    const inputElements = inputElementWrapper.querySelectorAll('input, textarea');

    const inputValues = {};
    inputElements.forEach(inputElement => {
        const inputId = inputElement.id;
        inputValues[inputId] = inputElement.value;
        if (!inputElement.value) {
            inputElement.value = sessionStorage.getItem(inputId)
        } else {
          sessionStorage.setItem(inputId, inputElement.value)
        }

        if (!sessionStorage.getItem(inputId)) inputElement.value = '';
    });

    return inputValues;
}

document.addEventListener('DOMContentLoaded', function() {

    // Get a list of all the input elements on the page
    const inputElementWrapper = document.querySelector('#chatgpt_assistant_settings_wrapper');

    async function handleChildBlur(event) {

        const inputElement = event.target;

        // Get the value of the input field
        const inputValue = inputElement.value;

        if(!inputValue) return;

        sessionStorage.setItem(inputElement.id, inputValue)

        // Create a new FormData object to hold the data to be sent
        const formData = new FormData();
        formData.append('action', 'chatgpt_assistant_setting_action_callback');
        formData.append('setting_value', inputValue);
        formData.append('setting_key', inputElement.id);

        // save settings
        await settingsAjaxRequest(formData, true)

    }

    if (inputElementWrapper) {
        inputElementWrapper.addEventListener('focusout', handleChildBlur);
        getAllInputValues(inputElementWrapper);
    }

});

async function settingsAjaxRequest(formData, saveOrNot) {

    // Send the data to the server
    try {
        const response = await fetch(ajaxurl, {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('HTTP status ' + response.status + ', ' + response.statusText);
        }

        const responseData = await response.json();

        // Handle the response data
        if (responseData.success) {
            // Show a toast message to inform the user that the setting has been saved
            let toastElement = saveOrNot ? document.querySelector('.toast-delete') : document.querySelector('.toast-save');
            let toast = new bootstrap.Toast(toastElement);
            await toast.show();
        }
    } catch (error) {
        console.error(error);
    }
}

async function deleteSettingsAjax(location) {

    // Create a new FormData object to hold the data to be sent
    const formData = new FormData();
    formData.append('action', 'chatgpt_assistant_setting_remove_callback');
    formData.append('setting_key', location);

    sessionStorage.removeItem(location)

    document.querySelector(`#${location}`).value = '';

    // delete settings
    await settingsAjaxRequest(formData, false)

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

    // Get the submit button element
    const deleteButton = document.getElementById("chatgpt_assistant_delete_button");

    if (editButton) { // Add click event listener to the Edit button
        editButton.addEventListener("click", function () {
            // Enable the input field
            apiKeyInput.disabled = false;

            // Show the submit button
            submitButton.style.display = "block";

            // Hide the Edit button
            editButton.style.display = "none";

            // Hide the Delete button
            deleteButton.style.display = "none";
        });
    }

    if (deleteButton) {
        deleteButton.addEventListener("click", function() {
            // Create a new FormData object to hold the data to be sent
            const formData = new FormData();
            formData.append('action', 'chatgpt_assistant_remove_api_key');

            // Send an AJAX request to the server to remove the API key
            fetch(ajaxurl, {
                method: 'POST',
                body: new URLSearchParams({
                    action: 'chatgpt_assistant_remove_api_key',
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
                },
            })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('HTTP status ' + response.status + ', ' + response.statusText);
                    }
                    // Rest of the code...
                    // API key removal successful, update the UI accordingly
                    const apiKeyInput = document.getElementById("chatgpt_assistant_api_key");
                    apiKeyInput.value = '';
                    apiKeyInput.removeAttribute('disabled');
                    const editButton = document.getElementById("chatgpt_assistant_edit_button");
                    const submitButton = document.getElementById("chatgpt_assistant_submit_button");
                    editButton.style.display = "none";
                    submitButton.style.display = "block";
                    deleteButton.style.display = "none";

                    // Show the success message
                    const successMessage = document.getElementById('api-key-removed-message');
                    if (successMessage) {
                        successMessage.style.display = 'block';
                    }
                    // Show the success message
                    const invalidMessage = document.getElementById('api-key-invalid-message');
                    if (invalidMessage) {
                        invalidMessage.style.display = 'none';
                    }
                    // Show the success message
                    const savedMessage = document.getElementById('api-key-saved-message');
                    if (savedMessage) {
                        savedMessage.style.display = 'none';
                    }
                    // Show the success message
                    const infoMessage = document.getElementById('api-key-saved-info');
                    if (infoMessage) {
                        infoMessage.style.display = 'none';
                    }
                })
                .catch(function(error) {
                    // Handle any error during API key removal (if needed)
                    console.error('Error removing API key:', error);
                });
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

    const assistantForm = document.getElementById("chatgpt-assistant-form");

    if (assistantForm) { // Add event listener to the form submission
        assistantForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Show the loading state
            showLoadingState();

        });
    }

});

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById("chatgpt-assistant-form");
    const submitButton = document.getElementById("submit-chatgpt-message");
    const responseDiv = document.getElementById("chatgpt-assistant-response");
    const textarea = document.getElementById("chatgpt-assistant-message");

    if (form) {
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

            // Show the loader
            const loader = document.getElementById("submit-btn-loader");
            loader.classList.remove("d-none");

        });
    }

    function extractMessagesFromTextarea() {
        const inputValue = textarea.value.trim();
        return [inputValue];
    }

});

document.addEventListener('DOMContentLoaded', function() {

    if (sessionStorage.getItem('warningDismissed') === '')
        sessionStorage.setItem('warningDismissed', 'active');

    const warningClose = document.querySelector('#chatgpt_brand_warning_button');

    if (warningClose) {
        warningClose.addEventListener("click", function () {
            sessionStorage.setItem('warningDismissed', 'passive')
        });
    }

    const warningMessage = document.querySelector('#brand_warning_message');

    if (sessionStorage.getItem('warningDismissed') === 'passive' && warningMessage)
        warningMessage.style.display = 'none';

});

