<?php
include_once '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'marker'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Текущий пользователь
$user_id = GetUserId();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include '_head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?= filter_input(INPUT_GET, 'link') ?>" class="nav-link"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
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
            
            $local_name = "";
            $last_name = "";
            $first_name = "";
            
            $sql = "select u.last_name, u.first_name, r.local_name from user u inner join role r on u.role_id = r.id where u.id=". GetUserId();
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()):
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            $local_name = $row['local_name'];
            ?>
            <p class="mt-4" style="font-size: 18px; line-height: 24px; font-weight: 600;"><?=$local_name ?>:</p>
            <p class="mt-2 mb-5" style="font-size: 24px; line-height: 32px; font-weight: 600;"><?=$last_name.' '.$first_name ?></p>
            <?php
            endif;
            ?>
            <form method="post" id="form_logout">
                <button type="submit" class="btn btn-outline-danger form-control" id="logout_submit" name="logout_submit">Выйти</button>
            </form>
        </div>
        <?php
        include '_footer.php';
        ?>
    </body>
</html>