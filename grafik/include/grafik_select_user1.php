<select id='user1_id' name='user1_id' style='width:100px;' onchange='javascript: EditUser1($(this))' data-id='<?=(isset($this->shift_data['id']) ? $this->shift_data['id'] : '') ?>' data-date='<?=$this->date->format('Y-m-d') ?>' data-shift='<?=$this->shift ?>' data-machine='<?=$this->machine->machineId ?>' data-from='<?=$this->machine->dateFrom->format('Y-m-d') ?>' data-to='<?=$this->machine->dateTo->format('Y-m-d') ?>'>
    <optgroup>
        <option value="">...</option>
        <?php
        foreach ($this->timetable->users1 as $value) {
            $selected = '';
            if(isset($this->shift_data['u1_id']) && $this->shift_data['u1_id'] == $value['id']) $selected = " selected = 'selected'";
            echo "<option$selected value='".$value['id']."'>".$value['fio']."</option>";
        }
        ?>
    </optgroup>
    <optgroup label='______________'>
        <option value='+'>(добавить)</option>
    </optgroup>
</select>
        
<div class="input-group d-none">
    <input type="text" id="user1" name="user1" value="" class="editable" />
    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" onclick="javascript: CreateUser1($(this));" data-id="<?=(isset($this->shift_data['id']) ? $this->shift_data['id'] : '') ?>" role_id="<?=$this->machine->userRole ?>" data-date="<?=$this->date->format('Y-m-d') ?>" data-shift="<?=$this->shift ?>" data-machine="<?=$this->machine->machineId ?>" data-from="<?=$this->machine->dateFrom->format('Y-m-d') ?>" data-to="<?=$this->machine->dateTo->format('Y-m-d') ?>"><i class="fas fa-save"></i></button></div>
    <div class="input-group-append"><button type="button" class="btn btn-outline-dark" data-user1="<?=(isset($this->shift_data['u1_id']) ? $this->shift_data['u1_id'] : '') ?>" onclick="javascript: CancelCreateUser1($(this));"><i class="fas fa-window-close"></i></button></div>
</div>