<?php
const PRINTER_ZBS_1 = 1;
const PRINTER_ZBS_2 = 2;
const PRINTER_ZBS_3 = 3;
const PRINTER_COMIFLEX = 4;
const PRINTER_ATLAS = 5;

$printers = array(PRINTER_COMIFLEX, PRINTER_ZBS_1, PRINTER_ZBS_2, PRINTER_ZBS_3, PRINTER_ATLAS);
$printer_names = array(PRINTER_COMIFLEX => 'Comiflex', PRINTER_ZBS_1 => 'ZBS-1', PRINTER_ZBS_2 => 'ZBS-2', PRINTER_ZBS_3 => 'ZBS-3', PRINTER_ATLAS => 'Atlas');
$printer_shortnames = array(PRINTER_COMIFLEX => 'comiflex', PRINTER_ZBS_1 => 'zbs1', PRINTER_ZBS_2 => 'zbs2', PRINTER_ZBS_3 => 'zbs3', PRINTER_ATLAS => 'atlas');

const LAMINATOR_SOLVENT = 1;
const LAMINATOR_SOLVENTLESS = 2;

$laminators = array(LAMINATOR_SOLVENT, LAMINATOR_SOLVENTLESS);
$laminator_names = array(LAMINATOR_SOLVENT => 'Ламинатор сольвент', LAMINATOR_SOLVENTLESS => 'Ламинатор бессольвент');

const CUTTER_ATLAS = 101;
const CUTTER_SOMA = 102;
const CUTTER_1 = 1;
const CUTTER_2 = 2;
const CUTTER_3 = 3;
const CUTTER_4 = 4;

$cutters = array(CUTTER_ATLAS, CUTTER_SOMA, CUTTER_1, CUTTER_2, CUTTER_3, CUTTER_4);
$cutter_names = array(CUTTER_ATLAS => "Атлас", CUTTER_SOMA => "Сома");
?>