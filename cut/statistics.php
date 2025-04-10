<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid">
            <div class="d-flex justify-content-between">
                <div>
                    <h1>Статистика по резчикам</h1>
                </div>
                <div>
                    <form class="form-inline mt-1" method="get">
                        <label for="from" style="font-size: larger;">от&nbsp;</label>
                        <input type="date" 
                               name="from" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'from') ?>" 
                               style="border: 0; width: 8.5rem;" />
                        <label for="to" style="font-size: larger;">до&nbsp;</label>
                        <input type="date" 
                               name="to" 
                               class="form-control mr-2" 
                               value="<?= filter_input(INPUT_GET, 'to') ?>" 
                               style="border: 0;" />
                        <button type="submit" class="btn btn-light ml-2""><i class="fas fa-list"></i>&nbsp;&nbsp;Сформировать</button>
                        <a href="statistics.php" class="btn btn-light ml-2"><i class="fas fa-times"></i>&nbsp;&nbsp;Очистить</a>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>