<?php
// Статусы расчёта
const DRAFT = 1; // Черновик
const CALCULATION = 2; // Сделан расчёт
const WAITING = 3; // Ждём подтверждения
const CONFIRMED = 4; // Ждём постановки в план
const REJECTED = 5; // Отклонено
const TECHMAP = 6; // Составлена технологическая карта
const TRASH = 7; // В корзине
const PLAN = 8; // В плане

$status_names = array(DRAFT => "Черновик", CALCULATION => "Сделан расчёт", WAITING => "Ждём подтверждения", CONFIRMED => "Ждём постановки в план", REJECTED => "Отклонено", TECHMAP => "Составлена тех. карта", TRASH => "В корзине", PLAN => "В плане");
$status_colors = array(DRAFT => "gray", CALCULATION => "steelblue", WAITING => "goldenrod", CONFIRMED => "mediumseagreen", REJECTED => "crimson", TECHMAP => "saddlebrown", TRASH => "black", PLAN => "#9933ff");
$status_icons = array(DRAFT => "fas fa-edit", CALCULATION => "fas fa-check", WAITING => "fas fa-clock", CONFIRMED => "fas fa-check-double", REJECTED => "fas fa-times-circle", TECHMAP => "fas fa-file", TRASH => "fas fa-trash-alt", PLAN => "fas fa-list-ol");
?>