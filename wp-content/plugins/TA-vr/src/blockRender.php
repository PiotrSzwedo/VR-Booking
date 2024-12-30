
<?php
    $icon = "";
    $name = "";

    if (key_exists("name", $_GET)){
        $name = $_GET["name"];
    }

    if (key_exists("icon", $_GET)){
        $icon = $_GET["icon"];
    }

    if (key_exists("blok", $_GET)){
        $blok = $_GET["blok"];
    }
?>
if (!wp.blocks){
    const { registerBlockType } = wp.blocks;
    const { RichText } = wp.blockEditor;
}

    registerBlockType('ta-vr-reservation/<?php echo $blok; ?>', {
        title: '<?php echo $name; ?>',
        icon: '<?php echo $icon; ?>',
        category: 'common',

        attributes: {
            content: {
                type: 'string',
                source: 'html'
            },
        },

        save() {
            return null;
        },
    });
