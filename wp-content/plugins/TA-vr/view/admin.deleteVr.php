<?php 
global $wpdb;

$sql = "SELECT * FROM ta_vr_description;";
$results = $wpdb->get_results($sql, ARRAY_A);
$elements = [];

foreach ($results as $field){
    $elements[$field["key"]] = $field["element"];
}

require_once __DIR__."/../src/service/VrService.php";
require_once __DIR__."/../src/service/Renderer.php";

$vrService = new VrService($wpdb);
$vrRenderer = new Renderer($vrService, $elements);

$url = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($url, PHP_URL_PATH);

// Warunek wyboru odpowiedniego formularza
if (!isset($_GET["edit"]) || $_GET["edit"] === "panel" || !is_numeric($_GET["edit"])){
?>

    <form id="Vrform" action="" method="get" class="ta-vr-form">
        <div class="ta-vr-form-div-vr-list">
            <?php echo $vrRenderer->renderVrEditList();?>
        </div>
    </form>
<?php
} else {
?>
    <form id="Vrform" method="post" class="ta-vr-form">
        <a href="<?php echo $parsedUrl; ?>"><</a>
        <?php echo $vrRenderer->renderVrEditPanel($_GET["edit"]); ?>
        <div class="center-sub">
            <input type="submit" name="ta_vr_edit" class="right-left-margin-5 submit" value="<?php echo $elements["vr-save-changes"];?>"></input>
            <input type="submit" name="ta_vr_delete" class="right-left-margin-5 submit" value="<?php echo $elements["vr-delete"];?>"></input>
        </div>
    </form>
<?php
}