<?php
// Печатные машины
const PRINTER_ZBS_1 = 1;
const PRINTER_ZBS_2 = 2;
const PRINTER_ZBS_3 = 3;
const PRINTER_COMIFLEX = 4;
const PRINTER_ATLAS = 5;

$printers = array(PRINTER_COMIFLEX, PRINTER_ZBS_1, PRINTER_ZBS_2, PRINTER_ZBS_3, PRINTER_ATLAS);
$printer_names = array(PRINTER_COMIFLEX => 'Comiflex', PRINTER_ZBS_1 => 'ZBS-1', PRINTER_ZBS_2 => 'ZBS-2', PRINTER_ZBS_3 => 'ZBS-3', PRINTER_ATLAS => 'Atlas');
$printer_shortnames = array(PRINTER_COMIFLEX => 'comiflex', PRINTER_ZBS_1 => 'zbs1', PRINTER_ZBS_2 => 'zbs2', PRINTER_ZBS_3 => 'zbs3', PRINTER_ATLAS => 'atlas');

// Ламинаторы
const LAMINATOR_SOLVENT = 1;
const LAMINATOR_SOLVENTLESS = 2;

$laminators = array(LAMINATOR_SOLVENT, LAMINATOR_SOLVENTLESS);
$laminator_names = array(LAMINATOR_SOLVENT => 'Ламинатор сольвент', LAMINATOR_SOLVENTLESS => 'Ламинатор бессольвент');

// Резки
const CUTTER_1 = 1;
const CUTTER_2 = 2;
const CUTTER_3 = 3;
const CUTTER_4 = 4;
const CUTTER_ATLAS = 101;
const CUTTER_SOMA = 102;

$cutters = array(CUTTER_1, CUTTER_2, CUTTER_3, CUTTER_4, CUTTER_SOMA, CUTTER_ATLAS);
$cutter_names = array(CUTTER_ATLAS => "Атлас", CUTTER_SOMA => "Сома");
$cutter_speeds = array(CUTTER_1 => 70, CUTTER_2 => 70, CUTTER_3 => 70, CUTTER_4 => 120, CUTTER_ATLAS => 70, CUTTER_SOMA => 120);

// Разделы плана
const WORK_PRINTING = 1;
const WORK_LAMINATION = 2;
const WORK_CUTTING = 3;

const WORKS = array(WORK_PRINTING, WORK_LAMINATION, WORK_CUTTING);
const WORK_NAMES = array(WORK_PRINTING => "Печать", WORK_LAMINATION => "Ламинация", WORK_CUTTING => "Резка");

// Роли плана
const PLAN_ROLE_PRINT = 1;
const PLAN_ROLE_ASSISTANT = 2;
const PLAN_ROLE_LAMINATE = 3;
const PLAN_ROLE_CUT = 4;

const PLAN_ROLES = array(PLAN_ROLE_PRINT, PLAN_ROLE_ASSISTANT, PLAN_ROLE_LAMINATE, PLAN_ROLE_CUT);
const PLAN_ROLE_NAMES = array(PLAN_ROLE_PRINT => "Печатник", PLAN_ROLE_ASSISTANT => "Помощник", PLAN_ROLE_LAMINATE => "Ламинаторщик", PLAN_ROLE_CUT => "Резчик");
const PLAN_ROLE_PLURALS = array(PLAN_ROLE_PRINT => "Печатники", PLAN_ROLE_ASSISTANT => "Помощники", PLAN_ROLE_LAMINATE => "Ламинаторщики", PLAN_ROLE_CUT => "Резчики");
const WORK_PLAN_ROLES = array(WORK_PRINTING => PLAN_ROLE_PRINT, WORK_LAMINATION => PLAN_ROLE_LAMINATE, WORK_CUTTING => PLAN_ROLE_CUT);

// Типы объектов плана
const PLAN_TYPE_EDITION = 1;
const PLAN_TYPE_EVENT = 2;
const PLAN_TYPE_CONTINUATION = 3;
const PLAN_TYPE_PART = 4;
const PLAN_TYPE_PART_CONTINUATION = 5;

// Типы наценки
const ET_NOPRINT = 1;
const ET_PRINT_NO_LAMINATION = 2;
const ET_PRINT_1_LAMINATION = 3;
const ET_PRINT_2_LAMINATIONS = 4;
const ET_SELF_ADHESIVE = 5;

$extracharge_types = array(ET_NOPRINT, ET_PRINT_NO_LAMINATION, ET_PRINT_1_LAMINATION, ET_PRINT_2_LAMINATIONS);
$extracharge_type_names = array(ET_NOPRINT => "Пленка без печати", ET_PRINT_NO_LAMINATION => "Пленка с печатью без ламинации", ET_PRINT_1_LAMINATION => "Пленка с печатью и ламинацией", ET_PRINT_2_LAMINATIONS => "Пленка с печатью и двумя ламинациями", ET_SELF_ADHESIVE => "Самоклеящиеся материалы");
?>