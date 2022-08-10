<?php
$result = "";

// Ширина исходного ролика, мм
$source_width = filter_input(INPUT_GET, 'source_width');

// Длина одного реза, м
$cut_length = filter_input(INPUT_GET, 'cut_length');

// Ширины конечных роликов, мм
$plan_widths = array();

// Длины конечных роликов, м
$plan_lengths = array();

$i = 1;

while(!empty(filter_input(INPUT_GET, "width_$i")) && !empty(filter_input(INPUT_GET, "length_$i"))) {
    $plan_widths[$i] = filter_input(INPUT_GET, "width_$i");
    $plan_lengths[$i] = filter_input(INPUT_GET, "length_$i");
    $i++;
}

// Сортируем список ширин по значению
asort($plan_widths);
        
// Конечные ролики с ключами
$plan_rolls = array();
        
foreach($plan_widths as $key => $value) {
    $plan_rolls[$key] = array('width' => $value, 'length' => $plan_lengths[$key]);
}

$result .= "-------------------------------------------------------------------------------<br />";
$result .= "Ширина исходного роля $source_width мм; один съём $cut_length метров.<br />";
$result .= "-------------------------------------------------------------------------------<br />";
$result .= "Задание на раскрой материала:<br />";

foreach($plan_rolls as $key => $value) {
    $result .=  "номер = $key; ширина = ".$value['width'].' мм; длина = '.$value['length'].' м;<br />';
}

echo $result;
?>