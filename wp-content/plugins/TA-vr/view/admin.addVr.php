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
?>

<form action="" class="ta-vr-form" method="post">
    <div>
        <label><?php echo $elements["form--add-vr-number"];?></label>
        <input type="text" name="number">
    </div>
    <div>
        <label><?php echo $elements["form--add-vr-developer-mode"];?></label>
        <input type="checkbox" name="developer_mode">
    </div>

    <div class="center-sub">
        <input type="submit" class="submit" name="ta_vr_add" value="<?php echo $elements["form--add-vr-add-vr"];?>"></input>
    </div>
</form>