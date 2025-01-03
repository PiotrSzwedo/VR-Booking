<?php

global $language = require_once __DIR__."/../_inc/lang/pl-PL.php";

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

function ta_pm_handle_register() {
    if (isset($_POST['ta-profile-manager-register'])) {
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];
        $name = sanitize_text_field($_POST['name']);
        $forename = sanitize_text_field($_POST['forename']);

        if (empty($username) || empty($password)) {
            wp_die(__('All fields are required.', 'text-domain'));
            wp_redirect(add_query_arg('register_error_username', $language["register_error_no_username"]));
            wp_redirect(add_query_arg('register_error_password', $language["register_error_password_no_password"]));
        }

        if (username_exists($username)) {
            wp_die(__('Username already exists. Please choose another.', 'text-domain'));
            wp_redirect(add_query_arg('register_error_username', $language["register_error_username_exist"]));
        }

        if (!validate_username($username)) {
            wp_die(__('Invalid username.', 'text-domain'));
            wp_redirect(add_query_arg('register_error_username', $language["register_error_invalid_username"]));
        }

        if (strlen($password) < 6) {
            wp_die(__('Password must be at least 6 characters long.', 'text-domain'));
            wp_redirect(add_query_arg('register_error_password', $language["register_error_password_to_short"]));
        }

        $user_data = [
            'user_login' => $username,
            'user_pass' => $password,
            'first_name' => $name,
            'last_name' => $forename,
        ];

        $user_id = wp_insert_user($user_data);

        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
            $_POST['ta-profile-manager-register'] === null;
        } else {
            wp_redirect(add_query_arg("register_success", true));
            exit;
        }
    }
}


add_action('wp', 'ta_pm_handle_login');
add_action('wp', 'ta_pm_handle_register');