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
            <h1>Миграция резчиков</h1>
            <?php
            $total_count = 0;
            $subscribed = 0;
            $sql = "select count(id) from calculation_take_stream where plan_employee_id is null and plan_employee_tested = false";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $total_count = $row[0];
            }
            $sql = "select count(id) from calculation_take_stream where plan_employee_tested = true";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $subscribed = $row[0];
            }
            ?>
            <p>Всего: <?=$total_count ?></p>
            <p>Подписанных: <?=$subscribed ?></p>
            <div id="progress" style="font-size: 22px;">0</div>
            <button id="Start" onclick="Start();">Старт</button>
            
            <br /><br />
            <?php
            $total_count_not = 0;
            $subscribed_not = 0;
            $sql = "select count(id) from calculation_not_take_stream where plan_employee_id is null and plan_employee_tested = false";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $total_count_not = $row[0];
            }
            $sql = "select count(id) from calculation_not_take_stream where plan_employee_tested = true";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $subscribed_not = $row[0];
            }
            ?>
            <p>Всего: <?=$total_count_not ?></p>
            <p>Подписанных: <?=$subscribed_not ?></p>
            <div id="progress_not" style="font-size: 22px;">0</div>
            <button id="StartNot" onclick="StartNot();">Старт</button>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        function Start() {
            $.ajax({ url: 'employee_migration_ajax.php' })
                    .done(function(data) {
                        $('#progress').text(data + ' из <?=$total_count ?>');
                
                        if(data <= <?=$total_count ?>) {
                            Start();
                        }
                    })
                    .fail(function() {
                        $('#progress').text("Ошибка");
                    });
        }
        
        function StartNot() {
            $.ajax({ url: 'employee_migration_ajax_not.php' })
                    .done(function(data) {
                        $('#progress_not').text(data + ' из <?=$total_count_not ?>');
                
                        if(data <= <?=$total_count ?>) {
                            StartNot();
                        }
                    })
                    .fail(function() {
                        $('#progress').text("Ошибка");
                    });
        }
        </script>
</html>