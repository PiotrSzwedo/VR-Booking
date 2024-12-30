<div class="wrap">
    <h1>Ustawienia</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('ta_vr_options_group');
        do_settings_sections('ta_vr_settings');
        ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Dni w przyszłość</th>
                <td>
                    <input type="number" name="ta_vr_day_in_past" value="<?php echo esc_attr(get_option('ta_vr_day_in_past', 3)); ?>" min="0" max="1000"/>
                    <p class="description">Liczba dni roboczych przed rezerwacją</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Dni pomiędzy rezerwacjami</th>
                <td>
                    <input type="number" name="ta_vr_day_in_future" value="<?php echo esc_attr(get_option('ta_vr_day_in_future', 3)); ?>" min="0" max="1000"/>
                    <p class="description">Liczba dni roboczych pomiędzy rezerwacjami</p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
</div>