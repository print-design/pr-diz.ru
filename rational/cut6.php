<?php
include '../include/topscripts.php';
?>
<html>
    <body>
        <a href="cut6.php" title="Очистить">Очистить</a>
        <h1>Раскрой</h1>
        <form method="post">
            <table>
                <tr>
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
                                <td><input type="number" min="1" id="length_2" name="length_2" style="width: 70px;" value="2000" /></td>
                                <td><input type="number" min="1" id="length_3" name="length_3" style="width: 70px;" value="18000" /></td>
                                <td><input type="number" min="1" id="length_4" name="length_4" style="width: 70px;" value="4000" /></td>
                                <td><input type="number" min="1" id="length_5" name="length_5" style="width: 70px;" value="2000" /></td>
                                <td><input type="number" min="1" id="length_6" name="length_6" style="width: 70px;" value="4000" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <button type="button" name="cut_sumbit" onclick="javascript: Start();">Рассчитать</button>
        </form>
        <div id="result"></div>
        <div id="waiting" style="position: absolute; left: 50px; top: 50px;"></div>
    </body>
    <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
    <script>
        function Start() {
            $('#result').html('');
            
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
            
            var i = 1;
            
            while($('#width_' + i).val() !== '' && $('#width_' + i).val() !== undefined && $('#length_' + i).val() !== '' && $('#length_' + i).val() !== undefined) {
                plan_rolls[i] = {'width': $('#width_' + i).val(), 'length': $('#length_' + i).val()};
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
            
            $.ajax({ url: 'count.php' + get_params })
                    .done(function(data) {
                        $('#result').html(data);
                        $('#waiting').html('');
                    })
                    .fail(function() {
                        $('#result').html("<p style='color: red;'>Ошибка при вычислении.</p>");
                        $('#waiting').html('');
                    });
        }
    </script>
</html>