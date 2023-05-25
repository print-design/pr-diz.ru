<?php
const ET_NOPRINT = 1;
const ET_PRINT_NO_LAMINATION = 2;
const ET_PRINT_1_LAMINATION = 3;
const ET_PRINT_2_LAMINATIONS = 4;
const ET_SELF_ADHESIVE = 5;

$extracharge_types = array(ET_NOPRINT, ET_PRINT_NO_LAMINATION, ET_PRINT_1_LAMINATION, ET_PRINT_2_LAMINATIONS);
$extracharge_type_names = array(ET_NOPRINT => "Пленка без печати", ET_PRINT_NO_LAMINATION => "Пленка с печатью без ламинации", ET_PRINT_1_LAMINATION => "Пленка с печатью и ламинацией", ET_PRINT_2_LAMINATIONS => "Пленка с печатью и двумя ламинациями", ET_SELF_ADHESIVE => "Самоклеящиеся материалы");
?>