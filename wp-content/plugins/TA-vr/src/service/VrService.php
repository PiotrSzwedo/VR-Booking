<?php 

class VrService{

    private $wpdb;

    public function __construct($wpdb){
        $this->wpdb = $wpdb;
    }
    public function getAllVr(){
        $sql = "
            SELECT 
                ta_vr.number AS 'number',
                ta_vr.id AS 'id',
                ta_vr.developer_mode AS 'is_developer_mode'
            FROM 
                ta_vr
            where ta_vr.active = 1
        ";

        $results = $this->wpdb->get_results($sql, ARRAY_A);

        return $this->praseVr($results);
    }

    public function getVr($id){
        if (!is_numeric($id)){
            return [];
        }

        $sql = "
        SELECT 
            ta_vr.number AS 'number',
            ta_vr.id AS 'id',
            ta_vr.developer_mode AS 'is_developer_mode'
        FROM 
            ta_vr
        where ta_vr.active = 1 and id = %d
        ";

        $sql = $this->wpdb->prepare($sql, $id);
        $results = $this->wpdb->get_results($sql, ARRAY_A);

        if ($results){
            return $results[0];
        }else{
            return [];
        }
    }

    public function getVrReservations(){
        $sql = "
            SELECT
                reservation.id as 'booking_number',
                reservation.end as 'booking_end_date',
                reservation.booking_date as 'booking_date',
                vr.number as 'vr_number',
                users.display_name as 'name',
                users.user_email as 'email',
                reservation.description as 'description'
            FROM 
                ta_vr_reservation_vr AS reservation_vr
            JOIN 
                ta_vr_reservation AS reservation 
                ON reservation_vr.reservation_id = reservation.id
            JOIN 
                ta_vr AS vr 
                ON vr.id = reservation_vr.vr_id
            JOIN
                wp_users AS users on users.ID = reservation.user_id
                where reservation.active = 1;
            ";


            $results = $this->wpdb->get_results($sql, ARRAY_A);

        return $this->praseReservations($results);
    }

    public function getMyVrReservations($myId){
            $sql = "
            SELECT
                reservation.id as 'booking_number',
                reservation.end as 'booking_end_date',
                reservation.booking_date as 'booking_date',
                vr.number as 'vr_number',
                users.display_name as 'name',
                users.user_email as 'email',
                reservation.description as 'description'
            FROM 
                ta_vr_reservation_vr AS reservation_vr
            JOIN 
                ta_vr_reservation AS reservation 
                ON reservation_vr.reservation_id = reservation.id
            JOIN 
                ta_vr AS vr 
                ON vr.id = reservation_vr.vr_id
            JOIN
                wp_users AS users on users.ID = reservation.user_id
                where users.ID = %d
                and reservation.active = 1;
        ";
        
        if (!intval($myId)){
            return false;
        }

        $prepared_sql = $this->wpdb->prepare($sql, $myId);
        $results = $this->wpdb->get_results($prepared_sql, ARRAY_A);

        return $this->praseReservations($results);
    }

    public function reserveVr($date, $userId, $vrList, $description){
        if ($userId != get_current_user_id()){
            return false;
        }

        if (!$vrList || !is_array($vrList)){
            return false;
        }

        $dateList = $this->getReservationDates(get_option('ta_vr_day_in_future', 3), get_option('ta_vr_day_in_future', 3));

        if (!$this->isDateFree($date, $dateList)){
            return false;
        }

        $date = htmlspecialchars($date);

        $this->wpdb->insert(
            'ta_vr_reservation',
            array(
                'end'          => htmlspecialchars($date),
                'user_id'      => htmlspecialchars($userId),
                'description' =>  htmlspecialchars($description)
            ),
            array(
                '%s', 
                '%d', 
                '%s',
                '%s'
            )
        );
        
        $inserted_id = $this->wpdb->insert_id;

        foreach ($vrList as $vr){
            $this->wpdb->insert(
                'ta_vr_reservation_vr', 
                array(
                    'vr_id' => $vr,
                    'reservation_id' => $inserted_id
                ),
                array(
                    '%d', 
                    '%d'  
                )
            );
        }

        return true;
    }

    public function deleteReservation($bookedId, $userId){

        if (!intval($userId)){
            return false;
        }

        if ($userId != get_current_user_id()) {
            return false;
        }
        
        if (current_user_can('administrator')) {
            $sql = "UPDATE  `ta_vr_reservation` SET `active` = '0' WHERE `id` = %d";
            $prepared_sql = $this->wpdb->prepare($sql, $bookedId);
        } else {
            $sql = "UPDATE `ta_vr_reservation` SET `active` = '0' WHERE `id` = %d AND `user_id` = %d"; 
            $prepared_sql = $this->wpdb->prepare($sql, $bookedId, $userId);
        }
        
        $result = $this->wpdb->get_results($prepared_sql);

        if ($result === false) {
            return false;
        }
        
        return true;
    }

    public function addVr($number, $isDeveloperMode = 0){
        if (!(current_user_can('technik') || current_user_can('administrator'))) {
            return false;
        }

        if (!is_string($number)) {
            return false;
        }
        
        $isDeveloperMode = $isDeveloperMode ? 1 : 0;
        
        $number = trim($number);
        $number = htmlspecialchars($number);

        
        $result = $this->wpdb->insert(
            'ta_vr',
            array(
                'number' => $number,
                'developer_mode' => $isDeveloperMode
            ),
            array(
                '%s', 
                '%d'  
            )
        );
        
        if ($result === false) {
            return false; 
        }
        
        return true;
    }

    public function deleteVr($vrId){
        if (!(current_user_can('technik') || current_user_can('administrator'))){
            return false;
        }
        
        $sql = "UPDATE ta_vr SET `active` = 0 WHERE id = %d";
        $prepared_sql = $this->wpdb->prepare($sql, $vrId);
        
        $this->wpdb->query($prepared_sql);
        
        return true;
    }

    public function editVr($isDeveloperMode, $number, $id){
        if (!(current_user_can('technik') || current_user_can('administrator'))){
            return false;
        }

        $number = trim($number);
        $number = htmlspecialchars($number);

        if (!(is_numeric($id) && is_string($number) && is_numeric($isDeveloperMode)))
            return false;

        $sql = "UPDATE ta_vr SET `developer_mode` = %d, `number` = %s WHERE `id` = %d;";

        $prepared_sql = $this->wpdb->prepare($sql, $isDeveloperMode, $number, $id);
        
        $this->wpdb->query($prepared_sql);
        
        return true;
    }

    private function praseReservations($results){
        $grouped_results = [];

        if (is_array($results)){
            foreach ($results as $row) {
                if (key_exists('booking_number', $row)){
                    $number = $row['booking_number'];
                    $vr_number = $row['vr_number'];
        
                    if (!isset($grouped_results[$number])) {
                        $grouped_results[$number] = [
                            'booking_number' => $row["booking_number"],
                            'booking_end_date' => $row["booking_end_date"],
                            'booking_date' => $row["booking_date"],
                            'user_name' => $row["name"],
                            "user_email" => $row["email"],
                            "description" => $row["description"],
                            'vr_number' => []
                        ];
                    }
        
                    $grouped_results[$number]['vr_number'][] = $vr_number;
                }
            }
        }

        return array_values($grouped_results);
    }

    public function getReservationDates($dayInFuture, $dayInPast){
            $sql = "select end FROM ta_vr_reservation where active = 1;";
            $results = $this->wpdb->get_results($sql, ARRAY_A);
            $dates = [];

            foreach ($results as $data){
                $dates[] = $this->addBusinessDays($data["end"], $dayInFuture, $dayInFuture);
            }

            return $dates ?: [];
    }
    private function isDateFree($dateToCheck, $datesList){
        $dateTime = new DateTime($dateToCheck);

        if (date("w", strtotime($dateToCheck)) == 0){
            return false;
        }

        foreach($datesList as $dates){
            foreach($dates as $date){
                if ($date == $dateToCheck) return false;
            }
        }

        return true;
    }

    private function addBusinessDays($date, $daysToAdd, $daysToRemove) {
        $dateTime = new DateTime($date);
        $datesList = []; 
        
        $datesList[] = $date;
        
        $addedDays = 0;
        while ($addedDays < $daysToAdd) {
            $dateTime->modify('+1 day');
            if ($dateTime->format('N') <= 6) {
                $datesList[] = $dateTime->format('Y-m-d');
                $addedDays++;
            }
        }
        
        $dateTime = new DateTime($date);
        $removedDays = 0;
        while ($removedDays < $daysToRemove) {
            $dateTime->modify('-1 day');
            if ($dateTime->format('N') <= 6) {
                $datesList[] = $dateTime->format('Y-m-d');
                $removedDays++;
            }
        }
        
        return $datesList;
    }

    private function praseVr($results){
        $grouped_results = [];

        if (is_array($results)){
            foreach ($results as $row) {
                $number = $row['number'];
                $id = $row["id"];
                $isDeveloperMode = $row['is_developer_mode'];

                if (!isset($grouped_results[$id])) {
                    $grouped_results[$id] = [
                        'number' => $number,
                        'id' => $id,
                        'is_developer_mode' => $isDeveloperMode,
                    ];
                }
            }
        }

        return array_values($grouped_results);
    }
}