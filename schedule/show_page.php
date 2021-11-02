<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table tbody tr td.top {
                border-top: solid 2px darkgray;
            }
            
            table tbody tr td.night {
                background-color: #F2F2F2;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1 text-nowrap">
                    <h1 style="font-size: 32px; font-weight: 600;" class="d-inline">Расписание</h1>
                </div>
                <div class="p-1 text-nowrap">
                    <form method="get" class="form-inline">
                        <div class="form-group">
                            <label for="from" class="mr-2" style="font-size: large;">От</label>
                            <input type="date" name="from" value="<?= filter_input(INPUT_GET, 'from') ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="to" class="ml-2 mr-2" style="font-size: large;">до</label>
                            <input type="date" name="to" value="<?= filter_input(INPUT_GET, 'to') ?>" class="form-control" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark ml-2">OK</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-bordered">
                <tbody>
                    <?php foreach ($dateshifts as $dateshift): ?>
                    <tr>
                        <?php if($dateshift['shift'] == 'day'): ?>
                        <td class='<?=$dateshift['top'] ?> <?=$dateshift['shift'] ?>' rowspan='<?=$dateshift['rowspan'] ?>'><?=$GLOBALS['weekdays'] [$dateshift['date']->format('w')] ?></td>
                        <td class='<?=$dateshift['top'] ?> <?=$dateshift['shift'] ?>' rowspan='<?=$dateshift['rowspan'] ?>'><?=$dateshift['date']->format('d.m').".".$dateshift['date']->format('Y') ?></td>
                        <?php endif; ?>
                        <td class='<?=$dateshift['top'] ?> <?=$dateshift['shift'] ?>' rowspan='<?=$dateshift['my_rowspan'] ?>'><?=($dateshift['shift'] == 'day' ? 'День' : 'Ночь') ?></td>
                    
                        <?php
                        $techmap = null;
                        if(count($dateshift['techmaps']) == 0):
                        ?>
                        <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                        <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                        <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                        <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                        <td class='<?=$dateshift['top']." ".$dateshift['shift'] ?>'></td>
                        <?php
                        else:
                        $techmap = array_shift($dateshift['techmaps']);
                        ShowTechmap($techmap, $dateshift['top'], $dateshift);
                        endif;
                        ?>
                    </tr>
                
                    <!-- Дополнительные тех. карты -->
                    <?php
                    $techmap = array_shift($dateshift['techmaps']);
                    while ($techmap != null):
                    ?>
                    <tr><?php ShowTechmap($techmap, 'nottop', $dateshift); ?></tr>
                    <?php
                    $techmap = array_shift($dateshift['techmaps']);
                    endwhile;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>