<select id='user2_id' name='user2_id' style='width:100px;' onchange='javascript: EditUser2($(this))' data-id='<?=(isset($this->shift_data['id']) ? $this->shift_data['id'] : '') ?>' data-date='<?=$this->date->format('Y-m-d') ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->timetable->machineId ?>' data-from='<?=$this->timetable->dateFrom->format('Y-m-d') ?>' data-to='<?=$this->timetable->dateTo->format('Y-m-d') ?>'>
    <option value="">...</option>
    <?php
    foreach ($this->timetable->users2 as $value) {
        $selected = '';
        if(isset($this->shift_data['u2_id']) && $this->shift_data['u2_id'] == $value['id']) $selected = " selected = 'selected'";
        echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
    }
    ?>
    <option disabled="disabled">______________</option>
    <option value='+'>(добавить)</option>
</select>
                            
<div class="input-group d-none">
    <input type="text" id="user2" name="user2" value="" class="editable" />
    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser2($(this));" data-id="<?=(isset($this->shift_data['id']) ? $this->shift_data['id'] : '') ?>" role_id="<?=$this->timetable->userRole ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-machine="<?=$this->timetable->machineId ?>" data-from="<?=$this->timetable->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->timetable->dateTo->format('Y-m-d') ?>"><i class="fas fa-save"></i></button></div>
    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user2="<?=(isset($this->shift_data['u2_id']) ? $this->shift_data['u2_id'] : '') ?>" onclick="javascript: CancelCreateUser2($(this));"><i class="fas fa-window-close"></i></button></div>
</div>