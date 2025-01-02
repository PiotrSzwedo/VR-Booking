<?php


include_once __DIR__ . "/src/service/VrService.php";
include_once __DIR__ . "/src/service/Renderer.php";
global $wpdb;

$vrService = new VrService($wpdb);
$renderer = new Renderer($vrService, []);

function handle_form_submission()
{
    global $vrService;
    global $renderer;

    if (isset($_POST['ta_vr_book'])) {

        if ($vrService->canManyReservation($_POST["user_id"], get_option('ta_vr_max_reservation', 3))){
            echo $renderer->renderErrorBar("Rezerwacja VR", "osiągnięto maksymalną ilość rezerwacji");
            $_POST = [];
            return;
        }

        if (
            key_exists("vr", $_POST) &&
            key_exists("user_id", $_POST) &&
            key_exists("date", $_POST) &&
            key_exists("description", $_POST)
        ) {
            if ($vrService->reserveVr($_POST["date"], $_POST["user_id"], $_POST["vr"], $_POST["description"], get_option('ta_vr_max_reservation', 3))) {
                echo $renderer->renderSuccessBar("Rezerwacja VR");
                $_POST = [];
                return;
            }
        }

        echo $renderer->renderErrorBar("Rezerwacja VR");
    }
}

function remove_reservation()
{
    global $vrService;
    global $renderer;

    if (isset($_POST['ta_remove'])) {
        if (isset($_POST["booking_number"]) && isset($_POST["user_id"]) && $vrService->deleteReservation($_POST["booking_number"], $_POST["user_id"])) {
            echo $renderer->renderSuccessBar("Usuwanie rezerwacji");
            $_POST = [];
            return;
        } else {
            echo $renderer->renderErrorBar("Usuwanie rezerwacji");
        }
    }
}

function remove_vr()
{
    global $vrService;
    global $renderer;

    if (isset($_POST['ta_vr_delete'])) {
        if (isset($_POST["id"]) && $vrService->deleteVr($_POST["id"])) {
            echo $renderer->renderSuccessBar("Usuwanie VR");
            $_POST = [];
            return;
        } else {
            echo $renderer->renderErrorBar("Usuwanie VR");
        }
    }
}

function add_vr()
{
    global $vrService;
    global $renderer;

    if (isset($_POST['ta_vr_add'])) {
        $isDeveloperMode = isset($_POST['developer_mode']) ? 1 : 0;

        if (isset($_POST["number"]) && $vrService->addVr($_POST["number"], $isDeveloperMode)) {
            echo $renderer->renderSuccessBar("Dodawanie VR");
            $_POST = [];
            return;
        } else {
            echo $renderer->renderErrorBar("Dodawanie VR");
        }
    }
}

function edit_vr()
{
    global $vrService;
    global $renderer;

    if (isset($_POST['ta_vr_edit'])) {
        if (key_exists('number', $_POST) && key_exists('id', $_POST)) {
            $isDeveloperMode = isset($_POST['developer_mode']) ? 1 : 0;
            if ($vrService->editVr($isDeveloperMode, $_POST["number"], $_POST["id"])) {
                echo $renderer->renderSuccessBar("Edytowanie VR");
                $_POST = [];
                return;
            }
        }
        echo $renderer->renderErrorBar("Edytowanie VR");
    }
}

add_action('wp', 'handle_form_submission');
add_action('wp', 'remove_reservation');
add_action('wp', 'remove_vr');
add_action('wp', 'add_vr');
add_action('wp', 'edit_vr');
