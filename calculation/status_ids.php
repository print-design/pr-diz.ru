<?php
// Статусы расчёта
const DRAFT = 1; // Черновик
const CALCULATION = 2; // Сделан расчёт
const WAITING = 3; // Ждём подтверждения
const CONFIRMED = 4; // Одобрено
const REJECTED = 5; // Отклонено
const TECHMAP = 6; // Составлена технологическая карта

$status_names = array(DRAFT => "Черновик", CALCULATION => "Сделан расчёт", WAITING => "Ждём подтверждения", CONFIRMED => "Одобрено", REJECTED => "Отклонено", TECHMAP => "Составлена тех. карта");
$status_colors = array(DRAFT => "gray", CALCULATION => "blue", WAITING => "goldenrod", CONFIRMED => "mediumseagreen", REJECTED => "indianred", TECHMAP => "saddlebrown");
$status_icons = array(DRAFT => "fas fa-edit", CALCULATION => "fas fa-check", WAITING => "fas fa-clock", CONFIRMED => "fas fa-check-double", REJECTED => "fas fa-times-circle", TECHMAP => "fas fa-file");
?>