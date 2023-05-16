<?php
require_once '../include/works.php';

const ROLE_PRINT = 1;
const ROLE_ASSISTANT = 2;
const ROLE_LAMINATE = 3;
const ROLE_CUT = 4;

$roles = array(ROLE_PRINT, ROLE_ASSISTANT, ROLE_LAMINATE, ROLE_CUT);
$role_names = array(ROLE_PRINT => "Печатник", ROLE_ASSISTANT => "Помощник", ROLE_LAMINATE => "Ламинаторщик", ROLE_CUT => "Резчик");
$role_plurals = array(ROLE_PRINT => "Печатники", ROLE_ASSISTANT => "Помощники", ROLE_LAMINATE => "Ламинаторщики", ROLE_CUT => "Резчики");
$work_roles = array(WORK_PRINTING => ROLE_PRINT, WORK_LAMINATION => ROLE_LAMINATE, WORK_CUTTING => ROLE_CUT);
?>