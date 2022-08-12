<?php
include '../include/topscripts.php';
?>
<html>
    <body>
        <a href="cut1.php" title="Очистить">Очистить</a>
        <h1>Раскрой</h1>
        <form method="post">
            <table>
                <tr>
                    <?php if(false): ?>
                    <td>
                        <label for="source_width">Ширина исходного ролика, мм</label><br />
                        <input type="number" min="1" id="source_width" name="source_width" value="<?= filter_input(INPUT_POST, 'source_width') ?>" required="required" /><br /><br />
                        <label for="cut_length">Длина одного съёма, м</label><br />
                        <input type="number" min="1" id="cut_length" name="cut_length" value="<?= filter_input(INPUT_POST, 'cut_length') ?>" required="required" /><br /><br />
                    </td>
                    <?php endif; ?>
                    <td>
                        <label for="source_width">Ширина исходного ролика, мм</label><br />
                        <input type="number" min="1" id="source_width" name="source_width" value="1500" required="required" /><br /><br />
                        <label for="cut_length">Длина одного съёма, м</label><br />
                        <input type="number" min="1" id="cut_length" name="cut_length" value="2000" required="required" /><br /><br />
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
                                <td><input type="number" min="1" id="width_1" name="width_1" value="120" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="width_2" name="width_2" value="140" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="width_3" name="width_3" value="150" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="width_4" name="width_4" value="260" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="width_5" name="width_5" value="200" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="width_6" name="width_6" value="205" style="width: 70px;" /></td>
                            </tr>
                            <tr>
                                <th>Длина, м</th>
                                <td><input type="number" min="1" id="length_1" name="length_1" value="10000" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="length_2" name="length_2" value="2000" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="length_3" name="length_3" value="18000" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="length_4" name="length_4" value="4000" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="length_5" name="length_5" value="2000" style="width: 70px;" /></td>
                                <td><input type="number" min="1" id="length_6" name="length_6" value="4000" style="width: 70px;" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <button type="button" name="cut_sumbit" onclick="javascript: Start();">Рассчитать</button>
        </form>
        <div id="source"></div>
        <div id="cuts"></div>
        <div id="summary"></div>
        <div id="cut_ext"></div>
        <div id="result"></div>
        <div id="error" style="color: red; font-size: xx-large;"></div>
        <div id="waiting" style="position: absolute; left: 50px; top: 50px;"></div>
    </body>
    <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
    <script>
        function Start() {
            $('#source').html('');
            $('#cuts').html('');
            $('#summary').html('');
            $('#cut_ext').html('');
            $('#result').html('');
            $('#error').text('');
            
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
            
            // Сортировка по ширине
            var sort_by_width = {};
            
            var i = 1;
            
            while($('#width_' + i).val() !== '' && $('#width_' + i).val() !== undefined && $('#length_' + i).val() !== '' && $('#length_' + i).val() !== undefined) {
                plan_rolls[i] = {'width': $('#width_' + i).val(), 'length': $('#length_' + i).val()};
                sort_by_width[plan_rolls[i]['width']] = i;
                i++;
            }
            
            // Отправляем запрос к вычислению
            $('#waiting').html("<img src='../images/waiting2.gif' title='waiting' />");
            
            var get_params = '?source_width=' + source_width;
            get_params += '&cut_length=' + cut_length;
            
            for(var key in plan_rolls) {
                get_params += '&width_' + key + '=' + plan_rolls[key]['width'];
                get_params += '&length_' + key + '=' + plan_rolls[key]['length'];
            }
            
            $.ajax({ dataType: 'JSON', url: 'calculate1.php' + get_params })
                    .done(function(data) {
                        if(data.error !== '' && data.error !== undefined) {
                            $('#error').text(data.error);
                        }
                        else {
                            $('#result').html(data.text);
                            
                            var source = "-------------------------------------------------------------------------------<br />";
                            source += "Ширина исходного роля " + source_width + " мм; один съём " + cut_length + " метров.<br />"
                            source += "-------------------------------------------------------------------------------<br />";
                            source += "Задание на раскрой материала:<br />";
                            
                            for(var i in sort_by_width) {
                                var key = sort_by_width[i];
                                source += "номер = " + key + "; ширина = " + plan_rolls[key]['width'] + " мм; длина = " + plan_rolls[key]['length'] + " м;<br />";
                            }
                            
                            $('#source').html(source);
                            
                            var cuts = "";
                            var cut = 0;
                            
                            for(cut in data.cuts) {
                                cuts += "------------------------------------------------------------<br />";
                                cuts += "Рез №" + cut +"; Остатки = " + data.cuts[cut].remainder + " мм Х " + data.cuts[cut].length + " м<br />";
                                
                                for(var key in data.cuts[cut].streams_counts) {
                                    cuts += "номер = " + key + "; ширина = " + data.cuts[cut].streams_counts[key].width + " мм; ручьёв = " + data.cuts[cut].streams_counts[key].streams_count + "; длина = " + data.cuts[cut].streams_counts[key].length + " м; сумма длин = " + data.cuts[cut].streams_counts[key].lengths_sum + " м;<br />";
                                }
                            }
                            
                            $('#cuts').html(cuts);
                            
                            var summary = "=================================================================<br /><br />";
                            summary += "ИТОГО: ЗАДАНО/ПОЛУЧЕНО/РАЗНОСТЬ: <br />";
                            
                            for(var i in sort_by_width) {
                                key = sort_by_width[i];
                                summary += "номер = " + key + "; ширина = " + data.summary[key].width + " мм; задано = " + data.summary[key].length + " м; получено = " + data.summary[key].lengths_sum + " м; разн. = " + data.summary[key].fact_plan_diff + " м<br />";
                            }
                            
                            $('#summary').html(summary);
                            
                            var cut_ext = "<br />";
                            cut_ext += "========================================================<br />";
                            cut_ext += "Кроим остатки шириной " + data.remainder + " мм; один съём " + cut_length + " метров<br />";
                            cut_ext += "--------------------------------------------------------<br />";
                            cut_ext += "Добавляем в рез №" + cut + ":<br /><br />";
                            
                            for(var key in data.cut_ext) {
                                cut_ext += "номер = " + key + "; ширина = " + data.cut_ext[key].width + " м: ручьёв = " + data.cut_ext[key].streams_count + "; длина = " + data.cut_ext[key].length + " м<br />";
                            }
                            
                            cut_ext += "Получаем остатки = " + data.remainder_ext + " мм X " + cut_length + " м<br /><br />";
                            
                            $('#cut_ext').html(cut_ext);
                        }
                        
                        $('#waiting').html('');
                    })
                    .fail(function() {
                        $('#error').text("Ошибка при вычислении.");
                        $('#waiting').html('');
                    });
        }
    </script>
</html>