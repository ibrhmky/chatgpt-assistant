<?php

/**
 * Display the form page
 */
function chatgpt_assistant_form_page(): void
{
    // Check if the user has permission to access the form page
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Display the form
    ?>
    <div class="container mx-width-600 mt-5">
        <!-- Tab navigation -->
        <ul class="nav nav-pills justify-content-center" id="unordered-message-tab" role="tablist">
            <li class="nav-item me-4" role="presentation">
                <button class="nav-link" id="basic-message-tab-link" data-bs-toggle="tab" data-bs-target="#basic-message-tab-content" type="button" role="tab" aria-controls="basic-message-tab-content" aria-selected="true">Basic</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="advanced-message-tab-link" data-bs-toggle="tab" data-bs-target="#advanced-message-tab-content" type="button" role="tab" aria-controls="advanced-message-tab-content" aria-selected="false">Advanced</button>
            </li>
        </ul>
        <!-- Tab content -->
        <div class="tab-content mt-4">
            <div class="tab-pane fade" id="basic-message-tab-content" role="tabpanel" aria-labelledby="basic-message-tab-link">
                <h4 class="page-header">Basic Message</h4>
                <form id="chatgpt-assistant-form" method="post" action="" class="mb-4">
                    <div class="form-group">
                        <div class="input-group mb-2">
                        <textarea class="form-control" id="chatgpt-assistant-message" name="message" rows="6"
                                  placeholder="Type your message here..." required></textarea>
                        </div>
                        <div class="row">
                            <small class="form-text text-muted mb-1">Please select an expertise if you want assistant to
                                act
                                like one.</small>
                        </div>
                        <div class="input-group mb-2" data-bs-theme="light">
                            <label class="input-group-text" for="inputGroupSelect01">Expertise</label>
                            <select class="form-select border-light-grey" aria-label="Expertise" id="assistant_mode"
                                    name="assistantMode">
                                <option selected value="0">Choose an expertise...</option>
                                <option value="Technology">Technology</option>
                                <option value="Art">Art</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <small class="form-text text-muted">Your message will be turned into a post with a related
                            title.</small>
                    </div>
                    <div class="submit-wrapper mt-1">
                        <button id="submit-chatgpt-message" type="submit" onclick="sendMessageAPI()"
                                class="btn btn-primary">
                            <span id="submit-btn-text">Submit</span>
                            <span id="submit-btn-loader" class="spinner-border spinner-border-sm d-none" role="status"
                                  aria-hidden="true"></span>
                        </button>
                        <button id="bulk-input-button" type="button" class="btn btn-secondary">Bulk Input</button>
                        <span id="chatgpt-assistant-submit-info"
                              class="chatgpt-assistant-submit-info">(Ctrl+Enter)</span>
                    </div>
                </form>
                <div id="chatgpt-assistant-response" class="alert alert-info" style="display: none;"></div>
                <ul id="message-list" class="list-group mt-4"></ul>
            </div>
            <div class="tab-pane fade" id="advanced-message-tab-content" role="tabpanel" aria-labelledby="advanced-message-tab-link">
                <h4 class="page-header">Advanced Message</h4>
                <form id="chatgpt-assistant-form" method="post" action="" class="mb-4">
                    <div class="form-group">
                        <div class="input-group mb-2">
                        <textarea class="form-control" id="chatgpt-assistant-message" name="message" rows="6"
                                  placeholder="Type your message here..." required></textarea>
                        </div>
                        <div class="row">
                            <small class="form-text text-muted mb-1">Please select an expertise if you want assistant to
                                act
                                like one.</small>
                        </div>
                        <div class="input-group mb-2" data-bs-theme="light">
                            <label class="input-group-text" for="inputGroupSelect01">Expertise</label>
                            <select class="form-select border-light-grey" aria-label="Expertise" id="assistant_mode"
                                    name="assistantMode">
                                <option selected value="0">Choose an expertise...</option>
                                <option value="Technology">Technology</option>
                                <option value="Art">Art</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <small class="form-text text-muted">Your message will be turned into a post with a related
                            title.</small>
                    </div>
                    <div class="submit-wrapper mt-1">
                        <button id="submit-chatgpt-message" type="submit" onclick="sendMessageAPI()"
                                class="btn btn-primary">
                            <span id="submit-btn-text">Submit</span>
                            <span id="submit-btn-loader" class="spinner-border spinner-border-sm d-none" role="status"
                                  aria-hidden="true"></span>
                        </button>
                        <button id="bulk-input-button" type="button" class="btn btn-secondary">Bulk Input</button>
                        <span id="chatgpt-assistant-submit-info"
                              class="chatgpt-assistant-submit-info">(Ctrl+Enter)</span>
                    </div>
                </form>
                <div id="chatgpt-assistant-response" class="alert alert-info" style="display: none;"></div>
                <ul id="message-list" class="list-group mt-4"></ul>
            </div>
        </div>
    </div>
    <script>
        // JavaScript code using Bootstrap to handle the tab navigation
        const triggerTabList = document.querySelectorAll('#unordered-message-tab button');
        const activeTabKey = 'activeTab';

        // Function to store the active tab ID in LocalStorage
        function storeActiveTab(tabId) {
            sessionStorage.setItem(activeTabKey, tabId);
        }

        // Function to retrieve the active tab ID from LocalStorage
        function getActiveTab() {
            return sessionStorage.getItem(activeTabKey);
        }

        triggerTabList.forEach(triggerEl => {
            const tabTrigger = new bootstrap.Tab(triggerEl);

            triggerEl.addEventListener('click', event => {
                event.preventDefault();
                tabTrigger.show();
                storeActiveTab(triggerEl.getAttribute('id'));
            });
        });

        // On page load, set the active tab based on the value stored in LocalStorage
        document.addEventListener('DOMContentLoaded', () => {
            const activeTabId = getActiveTab();
            if (activeTabId) {
                const activeTabEl = document.querySelector(`#${activeTabId}`);
                if (activeTabEl) {
                    const tabTrigger = new bootstrap.Tab(activeTabEl);
                    tabTrigger.show();
                }
            } else {
                const activeTabEl = document.querySelector(`#basic-message-tab-link`);
                const tabTrigger = new bootstrap.Tab(activeTabEl);
                tabTrigger.show();
            }
        });
    </script>

    <?php
}
