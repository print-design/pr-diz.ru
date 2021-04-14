<?php
$dtDate = DateTime::createFromFormat('Y-m-d', $date);

if($position == 1) {
    if($shift == 'day') {
        echo "<td class='$top $shift' rowspan='$rowspan'>".$GLOBALS['weekdays'][$dtDate->format('w')].'</td>';
        echo "<td class='$top $shift' rowspan='$rowspan'>".$dtDate->format('d.m').".".$dtDate->format('Y')."</td>";
    }
    
    echo "<td class='$top $shift' rowspan='$my_rowspan'>".($shift == 'day' ? 'День' : 'Ночь')."</td>";
}

// Работник №1
            /*if($this->user1Name != '') {
                echo "<td class='$top $shift' rowspan='$my_rowspan' title='".$this->user1Name."'>";
                if(IsInRole('admin')) {
                    echo "<form method='post'>";
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo "<select id='user1_id' name='user1_id' style='width:100px;'>";
                    echo '<optgroup>';
                    echo '<option value="">...</option>';
                    foreach ($this->users1 as $value) {
                        $selected = '';
                        if(isset($row['u1_id']) && $row['u1_id'] == $value['id']) $selected = " selected = 'selected'";
                        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
                    }
                    echo '</optgroup>';
                    echo "<optgroup label='______________'>";
                    echo "<option value='+'>(добавить)</option>";
                    echo '</optgroup>';
                    echo '</select>';
                    echo '</form>';
                            
                    echo '<form method="post" class="d-none">';
                    echo '<input type="hidden" id="scroll" name="scroll" />';
                    if(isset($row['id'])) {
                        echo '<input type="hidden" id="id" name="id" value="'.$row['id'].'" />';
                    }
                    echo '<input type="hidden" id="date" name="date" value="'.$dateshift['date']->format('Y-m-d').'" />';
                    echo '<input type="hidden" id="shift" name="shift" value="'.$dateshift['shift'].'" />';
                    echo '<div class="input-group">';
                    echo '<input type="text" id="user1" name="user1" value="" class="editable" />';
                    echo '<div class="input-group-append"><button type="submit" class="btn btn-outline-dark"><i class="fas fa-save"></i></button></div>';
                    echo '</div>';
                    echo '</form>';
                }
                else {
                    echo (isset($row['u1_fio']) ? $row['u1_fio'] : '');
                }
                echo '</td>';
            }*/
?>
<td class="<?=$top ?> <?=$shift ?>">QWE</td>
<td class="<?=$top ?> <?=$shift ?>">RTY</td>