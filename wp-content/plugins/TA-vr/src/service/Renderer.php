<?php

class Renderer
{
    protected $vrService;

    protected $description;

    public function __construct(VrService $vrService, $description)
    {
        $this->vrService = $vrService;
        $this->description = $description;
    }

    public function renderVrList()
    {
        $vrList = $this->vrService->getAllVr();

        $html = '<div class="ta-vr-form-div-vr-list">';

        foreach ($vrList as $vr) {

            $devmode = "<span></span>";

            if ($vr["is_developer_mode"]) {
                $devmode = "<span class='dev-mode'>🟢</span>";
            }

            $html .= '
            <div class="ta-vr">
                <input class="ta-vr-input" type="checkbox" name="vr[]" value="' . htmlspecialchars($vr["id"]) . '">
                <div class="ta-vr-info">
                    <p>' . htmlspecialchars($vr["number"]) . '</p>
                    <img src="/wp-content/plugins/TA-vr/_inc/img/vr.webp" alt="vr">' .
                $devmode
                . '
                </div>
            </div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function renderAllBooked()
    {
        $reservations = [];
        $reservations = $this->vrService->getVrReservations();

        return $this->renderBooked($reservations);
    }

    public function renderMyBooked()
    {
        $reservations = $this->vrService->getMyVrReservations(get_current_user_id());

        return $this->renderBooked($reservations);
    }

    private function renderBooked($reservations)
    {
        ob_start();
        foreach ($reservations as $reservation) {
?>
            <div style="position: relative;">
                <div class="ta-vr-booked">
                    <div class="ta-vr-booked-div">
                        <h2><?php echo htmlspecialchars($this->description["booking-text"]) . $reservation["booking_number"] ?></h2>
                        <div>
                            <p><?php echo $reservation["booking_end_date"] ?></p>
                            <p><?php echo $reservation["user_name"] ?></p>
                        </div>
                    </div>
                    <div style="display: flex;flex-wrap: wrap;">
                        <button onclick="showBooked('booked<?php echo $reservation['booking_number'] ?>')"><?php echo $this->description["booking-text-more-info"] ?></button>
                        <form method="post">
                            <input type="hidden" name="booking_number" value="<?php echo $reservation["booking_number"]; ?>">
                            <input type="hidden" name="user_id" value='<?php echo get_current_user_id(); ?>'>
                            <input type="submit" name="ta_remove" value="<?php echo htmlspecialchars($this->description["booking-vr-delete"]); ?>">
                        </form>
                    </div>
                </div>
                <div class="ta-vr-booked-info" id="booked<?php echo $reservation['booking_number'] ?>">
                    <div class="ta-vr-booked-info-divs">
                        <div class="ta-vr-booked-div">
                            <h3><?php echo htmlspecialchars($this->description["booking-user-data"]) ?></h3>
                            <p><?php echo htmlspecialchars($this->description["booking-user-data-email"]) . htmlspecialchars($reservation["user_email"]) ?></p>
                            <p><?php echo htmlspecialchars($this->description["booking-user-data-name"]) . htmlspecialchars($reservation["user_name"]) ?></p>
                        </div>
                        <div class="ta-vr-booked-div">
                            <h3><?php echo htmlspecialchars($this->description["booking-vr-numbers"]) ?></h3>
                            <ul>
                                <?php
                                foreach ($reservation["vr_number"] as $program) {
                                ?><li><?php echo $program ?></li><?php
                                                                }
                                                                    ?>
                            </ul>
                        </div>
                    </div>
                    <div class="ta-vr-booked-div">
                        <h3><?php echo $this->description["booking-text-about-reservation"] ?></h3>
                        <p><?php echo $reservation["description"] ?></p>
                    </div>
                </div>
            </div>
        <?php
        }

        return ob_get_clean();
    }

    public function renderVrEditList()
    {
        $vrList = $this->vrService->getAllVr();

        $html = '<div class="ta-vr-form-div-vr-list">';

        foreach ($vrList as $vr) {

            $devmode = "<span></span>";

            if ($vr["is_developer_mode"]) {
                $devmode = "<span>🟢Developer mode</span>";
            }

            $html .= '
            <from class="ta-vr" method="get">
                <input class="ta-vr-input" type="submit" name="edit" value="' . htmlspecialchars($vr["id"]) . '">
                <div class="ta-vr-info">
                    <p>' . $vr["number"] . '</p>
                    <img src="/wp-content/plugins/TA-vr/_inc/img/vr.webp" alt="vr">' .
                $devmode
                . '
                </div>
            </from>';
        }

        $html .= '</div>';

        return $html;
    }

    public function renderVrEditPanel($id){
        $vr = $this->vrService->getVr($id);

        if (!key_exists("id", $vr) || !key_exists("is_developer_mode", $vr) || !key_exists("number", $vr)){
            return;
        }

        ob_start();
        ?> 
            <input type="hidden" name="id" value="<?php echo $vr["id"]; ?>">
            <div>
                <label><?php echo htmlspecialchars($this->description["form--add-vr-number"]);?></label>
                <input type="text" name="number" value="<?php echo $vr["number"]; ?>">
            </div>
            <div>
                <label><?php echo htmlspecialchars($this->description["form--add-vr-developer-mode"]);?></label>
                <input type="checkbox" name="developer_mode" <?php if ($vr["is_developer_mode"]) echo "checked"?>>
            </div>
        <?php
        return ob_get_clean();
    }

    public function renderSuccessBar($actionName){
        ob_start();
        ?> 
            <div id="ta-success-bar-id" style="
                        top: 0;
                        position: fixed;
                        z-index: 99;
                        background: #3ba93b;
                        color: var(--ta-vr-white);
                        padding: 5px 20px;
                        left: 0;
                        width: 100%;
                        height: min-content;
                        ">
            <div>
                <button type="button" onclick="document.getElementById('ta-success-bar-id').style.display = 'none'" style="
                background: none;
                padding: 0px;
            ">X</button>
            </div>
                <p>Akcja "<?php echo htmlspecialchars($actionName)?>" zakończona sukcesem</p>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('ta-success-bar-id').style.display = 'none';
                }, 10000);
            </script>
        <?php
        return ob_get_clean();
    }

    public function renderErrorBar($actionName){
        ob_start();
        ?> 
            <div id="ta-status-bar-id" style="
                        top: 0;
                        position: fixed;
                        z-index: 99;
                        background: #ac1919;
                        color: var(--ta-vr-white);
                        padding: 5px 20px;
                        left: 0;
                        width: 100%;
                        height: 10%">
            <div>
                <button type="button" onclick="document.getElementById('ta-status-bar-id').style.display = 'none'" style="
                background: none;
                padding: 0px;
            ">X</button>
            </div>
                <p style="margin: 0px 0px 10px;" >Akcja "<?php echo htmlspecialchars($actionName)?>" zakończona niepowodzeniem</p>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('ta-status-bar-id').style.display = 'none';
                }, 10000);
            </script>
        <?php
        return ob_get_clean();
    }
}
