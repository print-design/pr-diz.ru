<?php
// Перебор всех возможных количеств ручьёв в одном резе для каждого конечного ролика
function Iterate($plan_rolls, $min_streams_counts, $variables, $streams_counts, $index) {
    // Список ключей конечных роликов
    $keys = array_keys($plan_rolls);
            
    // Для каждого возможного количества ручьёв в одном резе
    for($i=0; $i<=$min_streams_counts[$keys[$index]]; $i++) {
        $new_streams_counts = $streams_counts;
                
        // К списку количеств ручьёв для предыдущих роликов добавляем количество ручьёв для данного ролика
        $new_streams_counts[$keys[$index]] = $i;
                    
        // Определяем сумму ширин всех ручьёв.
        // Если сумма ручьёв меньше ширины исходящего ролика и есть следующий уровень,
        // то идём на следующий уровень.
        // Иначе если сумма ручьёв меньше или равна ширине исходного ролика и больше максимальной суммы,
        // то обозначаем эту сумму, как максимальную,
        // а количество ручьёв, как оптимальное.
        $streams_widths_sum = 0;
        
        foreach($new_streams_counts as $key => $value) {
            $streams_widths_sum += $value * (intval($plan_rolls[$key]['width']) / 1000);
        }
        
        if($streams_widths_sum < $variables->source_width && array_key_exists($index + 1, $keys)) {
            Iterate($plan_rolls, $min_streams_counts, $variables, $new_streams_counts, $index + 1);
        }
        elseif($streams_widths_sum <= $variables->source_width && $streams_widths_sum > $variables->max_streams_widths_sum) {
            $variables->max_streams_widths_sum = $streams_widths_sum;
            $variables->streams_counts = $new_streams_counts;
        }
    }
}
        
class Variables {
    public function __construct($source_width) {
        $this->max_streams_widths_sum = 0;
        $this->source_width = $source_width;
        $this->streams_counts = array();
    }
            
    // Наибольшая сумма ширин ручьёв в одном резе
    public $max_streams_widths_sum;
            
    // Ширина исходного ролика
    public $source_width;
            
    // Количества ручьёв для каждого конечного ролика в одном резе
    public $streams_counts;
}
        
$result = array();
$text = "";

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

if(count($plan_widths) == 0 || count($plan_widths) == 0) {
    $result["error"] = "<p style='color: red;'>Не задано ни одного параметра конечного ролика.</p>";
    echo json_encode($result);
    exit();
}

// Сортируем список ширин по значению
asort($plan_widths);
        
// Конечные ролики с ключами
$plan_rolls = array();
        
foreach($plan_widths as $key => $value) {
    $plan_rolls[$key] = array('width' => $value, 'length' => $plan_lengths[$key]);
}

$text .= "-------------------------------------------------------------------------------<br />";
$text .= "Ширина исходного роля $source_width мм; один съём $cut_length метров.<br />";
$text .= "-------------------------------------------------------------------------------<br />";
$text .= "Задание на раскрой материала:<br />";

foreach($plan_rolls as $key => $value) {
    $text .=  "номер = $key; ширина = ".$value['width'].' мм; длина = '.$value['length'].' м;<br />';
}

// Минимальные количества ручьёв в одном резе для каждого конечного ролика
$min_streams_counts = array();
foreach($plan_rolls as $key => $value) {
    $min_streams_counts[$key] = floor($value['length'] / $cut_length);
}
        
// Суммы длин во всех резах для каждого конечного ролика
$lengths_sums = array();
foreach($plan_rolls as $key => $value) {
    $lengths_sum[$key] = 0;
}
        
// Номер реза
$cut = 1;
        
// Последний рез
$last_cut = 1;
        
// Делаем резы, пока не будут использованы все ручьи из возможных
while (count(array_filter($min_streams_counts, function($value) { return $value > 0; })) > 0) {
    $last_cut = $cut;
    $variables = new Variables($source_width / 1000);
            
    // Перебираем все возможные количества ручьёв для каждого конечного ролика
    Iterate($plan_rolls, $min_streams_counts, $variables, $variables->streams_counts, 0);
            
    // Остатки - часть ширины исходного ролика, не охваченная ручьями
    $variables->source_width = $variables->source_width - $variables->max_streams_widths_sum;
            
    $text .= "------------------------------------------------------------<br />";
    $text .= "Рез №$cut; Остатки = ".($variables->source_width * 1000)." мм Х $cut_length м<br />";
        
    // Для каждого из ручьёв
    foreach($variables->streams_counts as $key => $value) {
        $lengths_sum[$key] += $variables->streams_counts[$key] * $cut_length;
        if($value > 0) {
            $text .= "номер = $key; ширина = ".$plan_rolls[$key]['width']." мм; ручьёв = ".$variables->streams_counts[$key]."; длина = ".($variables->streams_counts[$key] * $cut_length)." м; сумма длин = ".$lengths_sum[$key]." м;<br />";
                    
            // От минимального количества ручьёв в одном резе для данного конечного ролика
            // отнимаем количество уже использованных ручьёв для данного конечного ролика.
            $min_streams_counts[$key] -= $variables->streams_counts[$key];
            if($min_streams_counts[$key] < 0) $min_streams_counts[$key] = 0;
        }
    }
        
    $cut++;
}
        
// Разница между суммой длин для каждого конечного ролика и плановой длиной этого ролика
$fact_plan_diffs = array();
foreach($lengths_sum as $key => $value) {
    $fact_plan_diffs[$key] = $lengths_sum[$key] - $plan_rolls[$key]['length'];
}
        
$text .= "=================================================================<br />";
$text .= "<br />";
$text .= "ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ: <br />";
        
foreach($plan_rolls as $key => $value) {
    $text .= "номер = $key; ширина = ".$value['width']." мм; задано = ".$value['length']." м; получено = ".$lengths_sum[$key]." м; разн. = ".$fact_plan_diffs[$key]." м<br />";
}
        
$text .= "<br />";
$text .= "========================================================<br />";
$text .= "Кроим остатки шириной ".($variables->source_width * 1000)." мм; один съём $cut_length метров<br />";
$text .= "--------------------------------------------------------<br />";
$text .= "Добавляем в рез №$last_cut:<br /><br />";
        
$min_streams_counts = array();
foreach($plan_rolls as $key => $value) { 
    $min_streams_counts[$key] = floor($value['length'] / $cut_length);
}
        
$variables = new Variables($variables->source_width);
            
// Перебираем все возможные количества резов для первого конечного ролика,
// затем для каждого из этих значений перебираем все возможные количества резов для следующего ролика,
// и так далее до последнего ролика.
Iterate($plan_rolls, $min_streams_counts, $variables, $variables->streams_counts, 0);
        
// Остатки - часть ширины исходного ролика, не охваченная ручьями
$variables->source_width = floatval($variables->source_width) - floatval($variables->max_streams_widths_sum);
            
foreach($variables->streams_counts as $key => $value) {
    $lengths_sum[$key] += $variables->streams_counts[$key] * $cut_length;
    if($value > 0) {
        $text .= "номер = $key; ширина = ".$plan_rolls[$key]['width']." м: ручьёв = ".$variables->streams_counts[$key]."; длина = ".($variables->streams_counts[$key] * $cut_length)." м<br />";
        $min_streams_counts[$key] -= $variables->streams_counts[$key];
        if($min_streams_counts[$key] < 0) $min_streams_counts[$key] = 0;
    }
}
$text .= "Получаем остатки = ".($variables->source_width * 1000)." мм X $cut_length м<br /><br />";
        
$fact_plan_diffs = array();
foreach($lengths_sum as $key => $value) {
    $fact_plan_diffs[$key] = $lengths_sum[$key] - $plan_rolls[$key]['length'];
}
        
$text .= "================================================================<br />";
$text .= "<br />";
$text .= "ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ=ПОЛУЧЕНО-ЗАДАНО: <br /><br />";

foreach($plan_rolls as $key => $value) {
    if($value['length'] > 0) {
        $text .= "номер = $key; ширина = ".$value['width']." мм; задано = ".$value['length']." м; получено = ".$lengths_sum[$key]." м; разн. = ".$fact_plan_diffs[$key]."<br />";
    }
}

$result["error"] = "";
$result["text"] = $text;
echo json_encode($result);
?>