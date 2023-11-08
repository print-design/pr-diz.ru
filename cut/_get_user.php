<?php
require_once '../include/topscripts.php';

$current_time = new DateTime();
$current_time->setTimezone(new DateTimeZone('Europe/Moscow'));
echo "Ajax User...".$current_time->format("d.m.Y H:i:s").' --- '. GetUserId();
?>