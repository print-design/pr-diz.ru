<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'electrocarist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                padding-left: 0;
            }
            
            .container-fluid {
                padding-left: 15px;
            }
            
            @media (min-width: 768px) {
                body {
                    padding-left: 60px;
                }
            }
            
            td {
                height: 1.8rem;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= filter_input(INPUT_GET, 'link') ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div>Водитель погрузчика:</div>
            <?php
            $sql = "select last_name, first_name from user where id=". GetUserId();
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            ?>
            <div class="mb-5" style="font-size: x-large;"><?=$last_name.' '.$first_name ?></div>
            <?php
            endif;
            ?>
            <form method="post">
                <button type="submit" class="btn btn-outline-dark form-control" id="logout_submit" name="logout_submit">Выйти</button>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>