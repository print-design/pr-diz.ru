<div class="container-fluid header" style="padding-left: 20px;">
    <nav class="navbar navbar-expand-sm justify-content-end">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?=APPLICATION ?>/grafik/"><i class="fas fa-home"></i></a>
            </li>
            <?php
            if(LoggedIn()):
            $sql = "select id, name from machine order by position";
            $fetcher = new FetcherGrafik($sql);
            while ($row = $fetcher->Fetch()):
                $status = filter_input(INPUT_GET, 'id') == $row['id'] ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$status ?>" href="<?=APPLICATION ?>/grafik/machine.php<?= BuildQuery('id', $row['id']) ?>"><?=$row['name'] ?></a>
            </li>
            <?php
            endwhile;
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/personal/index.php' ? ' disabled' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$personal_status ?>" href="<?=APPLICATION ?>/grafik/personal/">Мои настройки</a>
            </li>
            <?php
            if(IsInRole('technologist', 'manager-senior')):
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/user/index.php' ? ' disabled' : '';
            $machine_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/machine/index.php' ? ' disabled' : '';
            $lamination_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/grafik/lamination/index.php' ? ' disabled' : '';
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    Администратор
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item<?=$user_status ?>" href="<?=APPLICATION ?>/grafik/user/">Пользователи</a>
                    <a class="dropdown-item<?=$machine_status ?>" href="<?=APPLICATION ?>/grafik/machine/">Машины</a>
                </div>
            </li>
            <?php
            endif;
            endif;
            ?>
        </ul>
        <div class="ml-auto"></div>
        <?php
        include 'header_right.php';
        ?>
    </nav>
</div>
<div id="topmost"></div>
<div class="modal fade" id="move_shifts_form">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Сдвиг тиражей</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" id="move_shifts_machine_id" name="move_shifts_machine_id" />
                    <input type="hidden" id="move_shifts_date_from" name="move_shifts_date_from" />
                    <input type="hidden" id="move_shifts_shift_from" name="move_shifts_shift_from" />
                    <div class="form-group">
                        <div id="move_shifts_title"></div>
                    </div>
                    <div class="form-group form-inline">
                        <label for="move_shifts_count">На сколько смен сдвинуть?&nbsp;</label>
                        <input type="number" id="move_shifts_count" name="move_shifts_count" min="1" max="99" class="form-control" value="1" required="required" />
                    </div>
                    <input type="hidden" id="scroll" name="scroll" />
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" id="move-shift-up-button" class="btn" onclick="javascript: MoveShiftsUp($(this))" data-dismiss="modal">Назад&nbsp;<i class="fas fa-arrow-up"></i></button>
                    <button type="button" id="move-shift-down-button" class="btn ml-1" onclick="javascript: MoveShiftsDown($(this))" data-dismiss="modal">Вперёд&nbsp;<i class="fas fa-arrow-down"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>