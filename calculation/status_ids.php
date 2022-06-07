<?php
// Статусы расчёта
const DRAFT = 1; // Черновик
const CALCULATION = 2; // Сделан расчёт
const TECHMAP = 3; // Составлена технологическая карта

$status_names = array(DRAFT => "Черновик", CALCULATION => "Сделан расчёт", TECHMAP => "Составлена тех. карта");
$status_colors = array(DRAFT => "gray", CALCULATION => "blue", TECHMAP => "green");
?>