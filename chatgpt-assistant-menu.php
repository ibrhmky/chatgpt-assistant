<?php

// Add a menu item for the plugin settings
function chatgpt_assistant_add_menu(): void
{
    add_menu_page(
        'ChatGPT Assistant',
        'ChatGPT Assistant',
        'manage_options',
        'chatgpt-assistant-settings',
        'chatgpt_assistant_settings_page',
        'dashicons-chatgpt-dashicon'
    );
}
add_action('admin_menu', 'chatgpt_assistant_add_menu');

// Add a submenu item for the form
function chatgpt_assistant_add_message_menu(): void
{
    add_submenu_page(
        'chatgpt-assistant-settings',
        'ChatGPT Assistant Form',
        'Message',
        'manage_options',
        'chatgpt-assistant-form',
        'chatgpt_assistant_form_page'
    );
}
add_action('admin_menu', 'chatgpt_assistant_add_message_menu');

// Add a submenu page under the ChatGPT Assistant menu
function chatgpt_assistant_add_history_menu(): void
{
    add_submenu_page(
        'chatgpt-assistant-settings',
        'Previous Messages',
        'Previous Messages',
        'manage_options',
        'chatgpt-assistant-messages',
        'chatgpt_assistant_messages_page'
    );
}
add_action('admin_menu', 'chatgpt_assistant_add_history_menu');

// Register the plugin settings
function chatgpt_assistant_register_settings(): void
{
    register_setting('chatgpt_assistant_settings', 'chatgpt_assistant_api_key', 'sanitize_text_field');
}
add_action('admin_init', 'chatgpt_assistant_register_settings');