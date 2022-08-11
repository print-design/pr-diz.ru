<html>
    <body>
        <a href="cut_old.php" title="Очистить">Очистить</a>
        <h1>Раскрой</h1>
        <form method="post">
            <table>
                <tr>
                    <td>
                        <label for="source_width">Ширина исходного ролика, мм</label><br />
                        <input type="number" min="1" name="source_width" value="<?= filter_input(INPUT_POST, 'source_width') ?>" required="required" /><br /><br />
                        <label for="cut_length">Длина одного съёма, м</label><br />
                        <input type="number" min="1" name="cut_length" value="<?= filter_input(INPUT_POST, 'cut_length') ?>" required="required" /><br /><br />
                    </td>
                    <td>
                        <?php
                        $post_roll_keys = array();
                        $post_roll_key = 0;
                        $key_exists = false;
                        do {
                            $post_roll_key++;
                            $key_exists = filter_input(INPUT_POST, "width_$post_roll_key") !== null && filter_input(INPUT_POST, "length_$post_roll_key") !== null;
                            if($key_exists) array_push ($post_roll_keys, $post_roll_key);
                        } while ($key_exists);
                        ?>
                        <table>
                            <tr>
                                <th>Ширина, мм</th>
                                <?php foreach($post_roll_keys as $item): ?>
                                <td><input type="number" min="1" name="width_<?=$item ?>" value="<?= filter_input(INPUT_POST, "width_$item") ?>" style="width: 70px;" /></td>
                                <?php endforeach; ?>
                                <?php if(null !== filter_input(INPUT_POST, 'add_submit') || count($post_roll_keys) == 0): ?>
                                <td><input type="number" min="1" name="width_<?=$post_roll_key ?>" style="width: 70px;" /></td>
                                <?php endif; ?>
                                <td><button type="submit" name="add_submit">Добавить</button></td>
                            </tr>
                            <tr>
                                <th>Длина, м</th>
                                <?php foreach($post_roll_keys as $item): ?>
                                <td><input type="number" min="1" name="length_<?=$item ?>" value="<?= filter_input(INPUT_POST, "length_$item") ?>" style="width: 70px;" /></td>
                                <?php endforeach; ?>
                                <?php if(null !== filter_input(INPUT_POST, 'add_submit') || count($post_roll_keys) == 0): ?>
                                <td><input type="number" min="1" name="length_<?=$post_roll_key ?>" style="width: 70px;" /></td>
                                <?php endif; ?>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <button type="submit" name="cut_sumbit">Рассчитать</button>
        </form>
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
                    
                if(array_key_exists($index + 1, $keys)) {
                    // Если ещё не дошли до последнего ролика, то перебираем все возможные количества ручьёв для следующего ролика.
                    Iterate($plan_rolls, $min_streams_counts, $variables, $new_streams_counts, $index + 1);
                }
                else {
                    // Если дошли до последнего ролика, то
                    // Определяем сумму ширин всех ручьёв.
                    $streams_widths_sum = 0;
                    
                    foreach($new_streams_counts as $key => $value) {
                        $streams_widths_sum += $value * (intval($plan_rolls[$key]['width']) / 1000);
                    }
                    
                    // Если сумма ручьёв меньше или равна ширины исходного ролика и больше максимальной суммы,
                    // то обозначаем эту сумму, как максимальную, 
                    // а это сочетание количеств ручьёв - как оптимальное.
                    if($streams_widths_sum <= $variables->source_width && $streams_widths_sum > $variables->max_streams_widths_sum) {
                        $variables->max_streams_widths_sum = $streams_widths_sum;
                        $variables->streams_counts = $new_streams_counts;
                    }
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
        
        if(null !== filter_input(INPUT_POST, 'cut_sumbit') && !empty(filter_input(INPUT_POST, 'source_width')) && !empty(filter_input(INPUT_POST, 'cut_length')) && !empty(filter_input(INPUT_POST, 'width_1')) && !empty(filter_input(INPUT_POST, 'length_1'))) {
            // Ширина исходного ролика, мм
            $source_width = filter_input(INPUT_POST, 'source_width');
            
            // Длина одного реза, м
            $cut_length = filter_input(INPUT_POST, 'cut_length');
            
            // Ширины конечных роликов, мм
            $plan_widths = array();

            // Длины конечных роликов, м
            $plan_lengths = array();
            
            $i = 1;
            
            while (!empty(filter_input(INPUT_POST, "width_$i")) && !empty(filter_input(INPUT_POST, "length_$i"))) {
                $plan_widths[$i] = filter_input(INPUT_POST, "width_$i");
                $plan_lengths[$i] = filter_input(INPUT_POST, "length_$i");
                $i++;
            }
        
            // Сортируем список ширин по значению
            asort($plan_widths);
        
            // Конечные ролики с ключами
            $plan_rolls = array();
        
            foreach($plan_widths as $key => $value) {
                $plan_rolls[$key] = array('width' => $value, 'length' => $plan_lengths[$key]);
            }
        
            echo "-------------------------------------------------------------------------------<br />";
            echo "Ширина исходного роля $source_width мм; один съём $cut_length метров.<br />";
            echo "-------------------------------------------------------------------------------<br />";
            echo "Задание на раскрой материала:<br />";
            foreach($plan_rolls as $key => $value) {
                echo "номер = $key; ширина = ".$value['width'].' мм; длина = '.$value['length'].' м;<br />';
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
            
                echo "------------------------------------------------------------<br />";
                echo "Рез №$cut; Остатки = ".($variables->source_width * 1000)." мм Х $cut_length м<br />";
        
                // Для каждого из ручьёв
                foreach($variables->streams_counts as $key => $value) {
                    $lengths_sum[$key] += $variables->streams_counts[$key] * $cut_length;
                    if($value > 0) {
                        echo "номер = $key; ширина = ".$plan_rolls[$key]['width']." мм; ручьёв = ".$variables->streams_counts[$key]."; длина = ".($variables->streams_counts[$key] * $cut_length)." м; сумма длин = ".$lengths_sum[$key]." м;<br />";
                    
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
        
            echo "=================================================================<br />";
            echo "<br />";
            echo "ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ: <br />";
        
            foreach($plan_rolls as $key => $value) {
                echo "номер = $key; ширина = ".$value['width']." мм; задано = ".$value['length']." м; получено = ".$lengths_sum[$key]." м; разн. = ".$fact_plan_diffs[$key]." м<br />";
            }
        
            echo "<br />";
            echo "========================================================<br />";
            echo "Кроим остатки шириной ".($variables->source_width * 1000)." мм; один съём $cut_length метров<br />";
            echo "--------------------------------------------------------<br />";
            echo "Добавляем в рез №$last_cut:<br /><br />";
        
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
                    echo "номер = $key; ширина = ".$plan_rolls[$key]['width']." м: ручьёв = ".$variables->streams_counts[$key]."; длина = ".($variables->streams_counts[$key] * $cut_length)." м<br />";
                    $min_streams_counts[$key] -= $variables->streams_counts[$key];
                    if($min_streams_counts[$key] < 0) $min_streams_counts[$key] = 0;
                }
            }
            echo "Получаем остатки = ".($variables->source_width * 1000)." мм X $cut_length м<br /><br />";
        
            $fact_plan_diffs = array();
            foreach($lengths_sum as $key => $value) {
                $fact_plan_diffs[$key] = $lengths_sum[$key] - $plan_rolls[$key]['length'];
            }
        
            echo "================================================================<br />";
            echo "<br />";
            echo "ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ=ПОЛУЧЕНО-ЗАДАНО: <br /><br />";
        
            foreach($plan_rolls as $key => $value) {
                if($value['length'] > 0) {
                    echo "номер = $key; ширина = ".$value['width']." мм; задано = ".$value['length']." м; получено = ".$lengths_sum[$key]." м; разн. = ".$fact_plan_diffs[$key]."<br />";
                }
            }
        }
        ?>
    </body>
</html>