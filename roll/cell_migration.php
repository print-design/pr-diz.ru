<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
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
            <h1>Миграция ячеек (рулоны)</h1>
            <?php
            $total_count = 0;
            $sql = "select count(id) total from roll";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $total_count = $row['total'];
            }
            
            $ok_count = 0;
            $sql = "select count(id) ok_count from roll where id in(select roll_id from roll_cell_history)";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $ok_count = $row['ok_count'];
            }
            ?>
            <div id="progress" style="padding: 10px; border: solid gray 2px; font-size: 50px; width: 500px;"><?=$ok_count ?> из <?=$total_count ?></div>
            <button class="btn btn-dark mt-5" onclick="javascript: Start();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Start() {
            $.ajax({ url: 'cell_migration_ajax.php' })
                        .done(function(data) {
                            $('#progress').text(data + ' из <?=$total_count ?>');
                    
                            if(data > 0) {
                                Start();
                            }
                        })
                        .fail(function() {
                            $('#progress').text("Ошибка");
                        });
        }
    </script>
</html>