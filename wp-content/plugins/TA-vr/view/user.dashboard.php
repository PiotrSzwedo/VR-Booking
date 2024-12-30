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
?>
<div class="wrap">
    <?php 
        echo $vrRenderer->renderMyBooked();
    ?>
</div>

<script>
    function showBooked(id){
        booked = document.getElementById(id);

        if (booked.classList.contains("open")){
            booked.classList.remove("open");
        }else{
            booked.classList.add("open");
        }
    }
</script>