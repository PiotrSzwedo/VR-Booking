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

$dates = $vrService->getReservationDates(get_option('ta_vr_day_in_future', 3),get_option('ta_vr_day_in_future', 3));

?>

<form class="ta-vr-form" method="post">
    <input type="hidden" name="user_id" value='<?php echo get_current_user_id(); ?>'>

    <p style="margin: 0px">🟢 - <?php echo $elements["vr-form-devmode-info"]; ?></p>
    <label><?php echo $elements["form-vr"]; ?></label>
    <?php echo $vrRenderer->renderVrList(); ?>

    <label><?php echo $elements["form-description"]; ?></label>
    <textarea name="description" placeholder='<?php echo $elements["form-description-description"]?>' required></textarea>
    <div>
        <label><?php echo $elements["form-date"]; ?><span id="DateLabel"></span></label>
    </div>
    <div>
        <div>
            <button class="calButton" type="button" onclick="lastMonth()"><</button>
            <label id="calInfo"></label>
            <button class="calButton" type="button" onclick="newMonth()">></button>
        </div>
        <table id="calendar">
            <thead id="days"></thead>
            <thead id="dates"></thead>
        </table>
    </div> 

    <input style="display: none;" type="date" name="date" id="date">

    <div class="center-sub">
        <input type="submit" class="submit" name="ta_vr_book" value="<?php echo $elements["form-book"];?>"></input>
    </div>
</form>
<script>
function createCalendar(year, month) {
    const tableDays = document.getElementById("days");
    const tableDates = document.getElementById("dates");
    tableDates.innerHTML = '';
    tableDays.innerHTML = '';

    const days = ["pon", "wt", "śd", "czw", "pt", "sb", "ndz"];
    const monthNames = ["styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień"];

    document.getElementById("calInfo").textContent = `${year} ${monthNames[month]}`;

    days.forEach(day => {
        const td = document.createElement("td");
        td.textContent = day;
        tableDays.appendChild(td);
    });

    const firstDay = new Date(year, month, 1).getDay();
    const numbersOfDay = new Date(year, month + 1, 0).getDate();

    const adjustedFirstDay = (firstDay === 0) ? 6 : firstDay - 1; 

    let tr = document.createElement("tr");
    tableDates.appendChild(tr);

    for (let i = 0; i < adjustedFirstDay; i++) {
        const td = document.createElement("td");
        tr.appendChild(td);
    }

    for (let i = 1; i <= numbersOfDay; i++) {
        const td = document.createElement("td");
        const dateToCheck = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;

        td.innerHTML = isDateFree(dateToCheck) 
            ? `<button type='button' onclick='setDate(${year}, ${month + 1}, ${i})'>${i}</button>`
            : `<button type='button' disabled>${i}</button>`;

        tr.appendChild(td);

        if ((i + adjustedFirstDay) % 7 === 0) {
            tr = document.createElement("tr");
            tableDates.appendChild(tr);
        }
    }
}

let currentYear = <?php echo date("Y"); ?>;
let currentMonth = <?php echo date("m") - 1; ?>; 

createCalendar(currentYear, currentMonth);

function lastMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentYear--;
        currentMonth = 11;
    }
    createCalendar(currentYear, currentMonth);
}

function newMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentYear++;
        currentMonth = 0;
    }
    createCalendar(currentYear, currentMonth);
}

function setDate(year, month, day) {
    const dateLabel = document.getElementById("DateLabel");
    const formattedMonth = String(month).padStart(2, '0');
    const formattedDay = String(day).padStart(2, '0');

    dateLabel.textContent = `${day}.${month}.${year}`;
    document.getElementById("date").value = `${year}-${formattedMonth}-${formattedDay}`;
}

function isDateFree(date) {
    const dateResults = <?php echo json_encode($dates); ?>;
    const occupiedDates = new Set(); 
    const today = '<?php  
    $days_in_future = get_option('ta_vr_day_in_past', 3);
    $date = date("Y-m-d", strtotime("+{$days_in_future} days"));

    $containsSundays = 0;
    
    for ($i = 0; $i <= $days_in_future; $i++) {
        $checkDate = date("Y-m-d", strtotime("+$i days"));
        if (date("w", strtotime($checkDate)) == 0) {
            $containsSundays++;
        }
    }
    
    for ($i = 0; $i < $containsSundays; $i++) {
        $date = date("Y-m-d", strtotime($date . " +1 day"));
    }
    
    echo $date;
?>';

    dateResults.forEach(dates => {
        Object.values(dates).forEach(d => occupiedDates.add(d));
    });

    const checkDate = new Date(date);
    const checkDateStr = checkDate.toISOString().split('T')[0];
    const dayOfWeek = checkDate.getDay();

    return checkDateStr > today && !occupiedDates.has(checkDateStr) && dayOfWeek !== 0;
}
</script>