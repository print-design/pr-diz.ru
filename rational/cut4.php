<html>
    <body>
        <a href="cut4.php" title="Очистить">Очистить</a>
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
            <button type="button" name="cut_sumbit">Рассчитать</button>
        </form>
    </body>
</html>