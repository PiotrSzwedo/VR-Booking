<?php

function ta_pm_handle_login()
{
    if (isset($_POST['ta-profile-manager-login'])) {
        $username = sanitize_text_field($_POST['identification']);
        $password = $_POST['password'];
        $remember = isset($_POST['is_remember_me']) ? true : false;

        $creds = [
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember,
        ];

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            wp_redirect(add_query_arg('login_error', urlencode($user->get_error_message())));
            exit;
        } else {
            $_POST = [];
            wp_redirect(home_url());
            exit;
        }
    }
}

add_action('init', 'ta_pm_handle_login');