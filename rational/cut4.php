<?php
include '../include/topscripts.php';
?>
<html>
    <body>
        <a href="cut4.php" title="Очистить">Очистить</a>
        <h1>Раскрой</h1>
        <form method="post">
            <table>
                <tr>
                    <td>
                        <label for="source_width">Ширина исходного ролика, мм</label><br />
                        <input type="number" min="1" id="source_width" name="source_width" value="<?= filter_input(INPUT_POST, 'source_width') ?>1500" required="required" /><br /><br />
                        <label for="cut_length">Длина одного съёма, м</label><br />
                        <input type="number" min="1" id="cut_length" name="cut_length" value="<?= filter_input(INPUT_POST, 'cut_length') ?>2000" required="required" /><br /><br />
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
                        <?php if(false): ?>
                        <table>
                            <tr>
                                <th>Ширина, мм</th>
                                <?php foreach($post_roll_keys as $item): ?>
                                <td><input type="number" min="1" id="width_<?=$item ?>" name="width_<?=$item ?>" value="<?= filter_input(INPUT_POST, "width_$item") ?>" style="width: 70px;" /></td>
                                <?php endforeach; ?>
                                <?php if(null !== filter_input(INPUT_POST, 'add_submit') || count($post_roll_keys) == 0): ?>
                                <td><input type="number" min="1" id="width_<?=$post_roll_key ?>" name="width_<?=$post_roll_key ?>" style="width: 70px;" /></td>
                                <?php endif; ?>
                                <td><button type="submit" name="add_submit">Добавить</button></td>
                            </tr>
                            <tr>
                                <th>Длина, м</th>
                                <?php foreach($post_roll_keys as $item): ?>
                                <td><input type="number" min="1" id="length_<?=$item ?>" name="length_<?=$item ?>" value="<?= filter_input(INPUT_POST, "length_$item") ?>" style="width: 70px;" /></td>
                                <?php endforeach; ?>
                                <?php if(null !== filter_input(INPUT_POST, 'add_submit') || count($post_roll_keys) == 0): ?>
                                <td><input type="number" min="1" id="length_<?=$post_roll_key ?>" name="length_<?=$post_roll_key ?>" style="width: 70px;" /></td>
                                <?php endif; ?>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        <?php endif; ?>
                        <table>
                            <tr>
                                <th>Ширина, мм</th>
                                <td><input type="number" min="1" id="width_1" name="width_1" style="width: 70px;" value="120" /></td>
                                <td><input type="number" min="1" id="width_2" name="width_2" style="width: 70px;" value="140" /></td>
                                <td><input type="number" min="1" id="width_3" name="width_3" style="width: 70px;" value="150" /></td>
                                <td><input type="number" min="1" id="width_4" name="width_4" style="width: 70px;" value="260" /></td>
                                <td><input type="number" min="1" id="width_5" name="width_5" style="width: 70px;" value="200" /></td>
                                <td><input type="number" min="1" id="width_6" name="width_6" style="width: 70px;" value="205" /></td>
                            </tr>
                            <tr>
                                <th>Длина, м</th>
                                <td><input type="number" min="1" id="length_1" name="length_1" style="width: 70px;" value="10000" /></td>
                                <td><input type="number" min="1" id="length_2" name="length_1" style="width: 70px;" value="2000" /></td>
                                <td><input type="number" min="1" id="length_3" name="length_1" style="width: 70px;" value="18000" /></td>
                                <td><input type="number" min="1" id="length_4" name="length_1" style="width: 70px;" value="4000" /></td>
                                <td><input type="number" min="1" id="length_5" name="length_1" style="width: 70px;" value="2000" /></td>
                                <td><input type="number" min="1" id="length_6" name="length_1" style="width: 70px;" value="4000" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <button type="button" name="cut_sumbit" onclick="javascript: Start();">Рассчитать</button>
        </form>
        <div id="percent" style="font-size: xx-large;"></div>
    </body>
    <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
    <script>
        class Variables {
            constructor(source_width) {
                // Наибольшая сумма ширин ручьёв в одном резе
                this.max_streams_widths_cum = 0;
                
                // Ширина исходного ролика
                this.source_width = source_width;
                
                // Количества ручьёв для каждого конечного ролика в одном резе
                this.streams_counts = {};
                
                // Текущий процент
                this.current_percent = 0;
            }
        }
        
        function Iterate(plan_rolls, min_streams_counts, variables, streams_counts, index, percent_low, percent_high) { $('#percent').text(percent_low + ' %'); if(!confirm(percent_low)) { return; }
            // Список ключей конечных роликов
            var ki = 0;
            var keys = {};
            for(key in plan_rolls) {
                keys[ki] = key;
                ki++;
            }
            
            // Нахождение наименьшего и наибольшего процента для данного уровня
            percent_step = (parseFloat(percent_high) - parseFloat(percent_low)) / (parseFloat(min_streams_counts[keys[index]]) + 1.0);
            new_percent_low = percent_low;
            new_percent_high = percent_low + percent_step;
            
            // Для каждого возможного количества ручьёв в одном резе
            for(i=0; i<=min_streams_counts[keys[index]]; i++) {  $('#percent').text(i);
                new_streams_counts = streams_counts;
                
                // К списку количество ручьёв для предыдущих роликов добавляем количество ручьёв для данного ролика
                new_streams_counts[keys[index]] = i;
                
                if(keys[index + 1] !== undefined) {
                    // Если ещё не дошли до последнего ролика, то перебираем все возможные количества ручьёв для следующего ролика.
                    Iterate(plan_rolls, min_streams_counts, variables, new_streams_counts, index + 1, new_percent_low, new_percent_high);
                    new_percent_low += percent_step;
                    new_percent_high += percent_step;
                }
                else {
                    // Если дошли до последнего ролика, то
                    // определяем сумму ширин всех ручьёв.
                    streams_widths_sum = 0;
                    
                    for(var key in new_streams_counts) {
                        streams_widths_sum += new_streams_counts[key] * (parseInt(plan_rolls[key]['width']) / 1000);
                    }
                    
                    // Если сумма ручьёв меньше или равна ширины исходного ролика и больше максимальной суммы,
                    // то обозначаем эту сумму, как максимальную,
                    // а это сочетание ручьёв, как оптимальное.
                    if(streams_widths_sum <= variables.source_width && streams_widths_sum > variables.max_streams_widths_cum) {
                        variables.max_streams_widths_cum = streams_widths_sum;
                        variables.streams_counts = new_streams_counts;
                    }
                }
            }
            
            if(new_percent_high > variables.current_percent) {
                variables.current_percent = new_percent_high;
            }
            
            if(index > 0 && variables.current_percent <= 100) {
                //$('#percent').text(parseInt(variables.current_percent) + ' %');
                $('#percent').text(variables.current_percent + ' %');
            }
        }
        
        function Start() {
            while($('#percent').prev().prop("tagName") != "FORM") {
                $('#percent').prev().remove();
            }
            
            if($('#source_width').val() === '' ||
                    $('#cut_length').val() === '' ||
                    $('#width_1').val() === '' || $('#width_1').val() === undefined ||
                    $('#length_1').val() === '' || $('#length_1').val() === undefined) {
                alert('Введите данные');
                return;
            }
            
            // Ширина исходного ролика, мм
            var source_width = $('#source_width').val();
            
            // Длина одного реза, м
            var cut_length = $('#cut_length').val();
            
            // Конечные ролики с ключами
            var plan_rolls = {};
            var sorted_by_width = {};
            
            var i = 1;
            
            while($('#width_' + i).val() !== '' && $('#width_' + i).val() !== undefined && $('#length_' + i).val() !== '' && $('#length_' + i).val() !== undefined) {
                plan_rolls[i] = {'width': $('#width_' + i).val(), 'length': $('#length_' + i).val()};
                sorted_by_width[plan_rolls[i]['width']] = i;
                i++;
            }
            
            show_source = "<div class='source'>";
            show_source += "-------------------------------------------------------------------------------<br />";
            show_source += "Ширина исходного роля " + source_width + " мм; один съём " + cut_length + " метров.<br />";
            show_source += "-------------------------------------------------------------------------------<br />";
            show_source += "Задание на раскрой материала:<br />";
            
            for(var width in sorted_by_width) {
                var key = sorted_by_width[width];
                show_source += "номер = " + key + "; ширина = " + plan_rolls[key]['width'] + " мм; длина = " + plan_rolls[key]['length'] + " м;<br />";
            }
            
            show_source += "</div>";
            $('#percent').before(show_source);
            
            // Минимальные количества ручьёв в одном резе для каждого конечного ролика
            var min_streams_counts = {};
            
            for(var key in plan_rolls) {
                min_streams_counts[key] = Math.floor(plan_rolls[key]['length'] / cut_length);
            }
            
            // Суммы длин во всех резах для каждого конечного ролика
            var lengths_sums = {};
            
            for(var key in plan_rolls) {
                lengths_sums[key] = 0;
            }
            
            // Номер реза
            var cut = 1;
            
            // Последний рез
            var last_cut = 1;
            
            // Делаем резы, пока не будут использованы все ручьи из возможных
            show_cuts = "<div id='cuts'>";
            
            while(cut < 2) {
                let last_cut = cut;
                let variables = new Variables(source_width / 1000);
                
                // Перебираем все возможные количества ручьёв для каждого конечного ролика
                Iterate(plan_rolls, min_streams_counts, variables, variables.streams_counts, 0, 0.0, 100.0);
                
                cut++;
            }
            
            show_cuts += "</div>";
            $('#percent').before(show_cuts);
        }
    </script>
</html>