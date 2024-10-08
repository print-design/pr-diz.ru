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

// Конечная выдача
$result = array();

// Ширина исходного ролика, мм
$source_width = filter_input(INPUT_GET, 'source_width');

// Длина одного реза, м
$cut_length = filter_input(INPUT_GET, 'cut_length');

// Конечные ролики с ключами
$plan_rolls = array();

$i = 1;

while(!empty(filter_input(INPUT_GET, "width_$i")) && !empty(filter_input(INPUT_GET, "length_$i"))) {
    $plan_rolls[$i] = array('width' => filter_input(INPUT_GET, "width_$i"), 'length' => filter_input(INPUT_GET, "length_$i"));
    $i++;
}

if(count($plan_rolls) == 0) {
    $result["error"] = "Не задано ни одного параметра конечного ролика.";
    echo json_encode($result);
    exit();
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
$cuts = array();

while (count(array_filter($min_streams_counts, function($value) { return $value > 0; })) > 0) {
    $last_cut = $cut;
    $variables = new Variables($source_width / 1000);
            
    // Перебираем все возможные количества ручьёв для каждого конечного ролика
    Iterate($plan_rolls, $min_streams_counts, $variables, $variables->streams_counts, 0);
            
    // Остатки - часть ширины исходного ролика, не охваченная ручьями
    $variables->source_width = $variables->source_width - $variables->max_streams_widths_sum;
            
    $current_cut = array();
    $current_cut['remainder'] = intval($variables->source_width * 1000);
    $current_cut['length'] = $cut_length;
    $current_streams_counts = array();
    
    // Для каждого из количеств ручьёв
    foreach($variables->streams_counts as $key => $value) {
        $lengths_sum[$key] += $variables->streams_counts[$key] * $cut_length;
        if($value > 0) {
            $current_streams_counts[$key] = array('width' => $plan_rolls[$key]['width'], 'streams_count' => $variables->streams_counts[$key], 'length' => $variables->streams_counts[$key] * $cut_length, 'lengths_sum' => $lengths_sum[$key]);
                    
            // От минимального количества ручьёв в одном резе для данного конечного ролика
            // отнимаем количество уже использованных ручьёв для данного конечного ролика.
            $min_streams_counts[$key] -= $variables->streams_counts[$key];
            if($min_streams_counts[$key] < 0) $min_streams_counts[$key] = 0;
        }
    }
    
    $current_cut['streams_counts'] = $current_streams_counts;
    $cuts[$cut] = $current_cut;
        
    $cut++;
}

$result['cuts'] = $cuts;
        
// Разница между суммой длин для каждого конечного ролика и плановой длиной этого ролика
$fact_plan_diffs = array();
foreach($lengths_sum as $key => $value) {
    $fact_plan_diffs[$key] = $lengths_sum[$key] - $plan_rolls[$key]['length'];
}

// ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ
$summary = array();
        
foreach($plan_rolls as $key => $value) {
    $summary[$key] = array('width' => $value['width'], 'length' => $value['length'], 'lengths_sum' => $lengths_sum[$key], 'fact_plan_diff' => $fact_plan_diffs[$key]);
}

$result['summary'] = $summary;
$result['remainder'] = intval($variables->source_width * 1000);
        
$min_streams_counts = array();
foreach($plan_rolls as $key => $value) { 
    $min_streams_counts[$key] = floor($value['length'] / $cut_length);
}
        
$variables = new Variables($variables->source_width);
            
// Перебираем все возможные количества резов для первого конечного ролика,
// затем для каждого из этих значений перебираем все возможные количества резов для следующего ролика,
// и так далее до последнего ролика.
Iterate($plan_rolls, $min_streams_counts, $variables, $variables->streams_counts, 0);

$cut_ext = array();

// Остатки - часть ширины исходного ролика, не охваченная ручьями
$variables->source_width = floatval($variables->source_width) - floatval($variables->max_streams_widths_sum);
            
foreach($variables->streams_counts as $key => $value) {
    $lengths_sum[$key] += $variables->streams_counts[$key] * $cut_length;
    if($value > 0) {
        $cut_ext[$key] = array('width' => $plan_rolls[$key]['width'], 'streams_count' => $variables->streams_counts[$key], 'length' => $variables->streams_counts[$key] * $cut_length);
        $min_streams_counts[$key] -= $variables->streams_counts[$key];
        if($min_streams_counts[$key] < 0) $min_streams_counts[$key] = 0;
    }
}

$result['cut_ext'] = $cut_ext;
$result['remainder_ext'] = intval($variables->source_width * 1000);
        
$fact_plan_diffs = array();
foreach($lengths_sum as $key => $value) {
    $fact_plan_diffs[$key] = $lengths_sum[$key] - $plan_rolls[$key]['length'];
}
        
$summary_ext = array();

foreach($plan_rolls as $key => $value) {
    if($value['length'] > 0) {
        $summary_ext[$key] = array('width' => $value['width'], 'length' => $value['length'], 'lengths_sum' => $lengths_sum[$key], 'fact_plan_diff' => $fact_plan_diffs[$key]);
    }
}

$result['summary_ext'] = $summary_ext;
$result["error"] = "";
echo json_encode($result);
?>