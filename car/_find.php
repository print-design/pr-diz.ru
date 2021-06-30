<div class="row">
    <div class="col-12 col-md-6 col-lg-4">
        <form method="post" action="<?=APPLICATION ?>/car/">
            <div class="form-group">
                <label for="id">Введите ID</label>
                <div class="d-flex">
                    <input type="text"
                           id="id"
                           name="id"
                           class="form-control no-latin"
                           required="required" 
                           value="<?= filter_input(INPUT_POST, 'id') ?>" 
                           onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onmouseup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                           onkeydown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name');" 
                           onkeyup="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" 
                           onfocusout="javascript: $(this).attr('id', 'id'); $(this).attr('name', 'id');" />
                    <button type="submit" class="btn btn-info ml-1<?= empty(filter_input(INPUT_POST, 'id')) ? " d-none" : "" ?>" id="car-submit" name="car-submit">Найти</button>
                </div>
            </div>
            <p id="id-valid" class="text-danger d-none">Только цифры и русские буквы</p>
        </form>
    </div>
</div>