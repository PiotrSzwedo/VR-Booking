<?php

function ta_pm_handle_login()
{
    if (isset($_POST['ta-profile-manager-login'])) {
        // Pobierz dane z formularza
        $username = sanitize_text_field($_POST['identification']);
        $password = $_POST['password'];
        $remember = isset($_POST['is_remember_me']) ? true : false;

        // Zbuduj tablicę z danymi do logowania
        $creds = [
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember,
        ];

        $user = wp_signon($creds, is_ssl());

        if (is_wp_error($user)) {
            // Obsługa błędów logowania
            wp_redirect(add_query_arg('login_error', urlencode($user->get_error_message()), wp_get_referer()));
            exit;
        } else {
            wp_redirect(home_url()); // Lub inna strona
            exit;
        }
    }
}

add_action('init', 'ta_pm_handle_login');