<?php
include 'include/topscripts.php';
include 'include/grafik.php';

$export_submit = filter_input(INPUT_POST, 'export_submit');
if($export_submit !== null) {
    $titles = array("id","date","shift",
        "user1_id","user1","user2_id","user2",
        "machine_id","machine",
        "id","name","organization",
        "length",
        "lamination_id","lamination",
        "coloring",
        "roller_id","roller",
        "manager_id","manager",
        "comment");
    
    $date_from = filter_input(INPUT_POST, 'from');
    $date_to = filter_input(INPUT_POST, 'to');
    $machine = filter_input(INPUT_POST, 'machine');
    
    $sql = "select ws.id ws_id, ws.date, ws.shift, "
            . "ws.user1_id, u1.fio user1, ws.user2_id, u2.fio user2, "
            . "ws.machine_id, m.name machine, "
            . "e.id e_id, e.name, e.organization, "
            . "e.length, e.lamination_id, l.name lamination, "
            . "e.coloring, "
            . "e.roller_id, r.name roller, e.manager_id, u3.fio manager, "
            . "e.comment "
            . "from workshift ws inner join edition e "
            . "on e.workshift_id = ws.id "
            . "inner join user u1 on ws.user1_id = u1.id "
            . "inner join user u2 on ws.user2_id = u2.id "
            . "inner join machine m on ws.machine_id = m.id "
            . "left join lamination l on e.lamination_id = l.id "
            . "left join roller r on e.roller_id = r.id "
            . "left join user u3 on e.manager_id = u3.id "
            . "where ws.date between '$date_from' and '$date_to' and ws.machine_id = $machine "
            . "order by ws.date, ws.shift";
    
    $data = array();
    
    $fetcher = new Fetcher($sql);
    while ($row = $fetcher->Fetch()) {
        array_push($data, array($row['ws_id'], $row['date'], $row['shift'],
            $row['user1_id'], $row['user1'], $row['user2_id'], $row['user2'],
            $row['machine_id'], $row['machine'], $row['e_id'], $row['name'], $row['organization'], 
            $row['length'], $row['lamination_id'], $row['lamination'], $row['coloring'], 
            $row['roller_id'], $row['roller'], $row['manager_id'], $row['manager'], $row['comment']));
    }
    
    $file_name = "grafik-ot-$date_from-do-$date_to.csv";
    
    DownloadSendHeaders($file_name);
    echo Array2Csv($data, $titles);
    die();
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать в CSV надо наэати на кнопку "Экспорт" в верхней правой части страницы.</h1>
    </body>
</html>