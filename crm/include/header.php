<div class="container-fluid">
    <nav class="navbar navbar-expand-sm">
        <a class="navbar-brand" href="<?=HOST ?>/">
            <span class="font-awesome">&#xf015;</span>
        </a>
        <ul class="navbar-nav mr-auto">
        <?php
        $organization_status = $_SERVER['PHP_SELF'] == APPLICATION.'/organization/index.php' ? ' disabled' : '';
        $allorgs_status = $_SERVER['PHP_SELF'] == APPLICATION.'/organization/all.php' ? ' disabled' : '';
        $call_status = $_SERVER['PHP_SELF'] == APPLICATION.'/contact/index.php' ? ' disabled' : '';
        $planned_status = $_SERVER['PHP_SELF'] == APPLICATION.'/planned/index.php' ? ' disabled' : '';
        $order_status = $_SERVER['PHP_SELF'] == APPLICATION.'/order/index.php' ? ' disabled' : '';
        $personal_status = $_SERVER['PHP_SELF'] == APPLICATION.'/personal/index.php' ? ' disabled' : '';
        $manager_status = $_SERVER['PHP_SELF'] == APPLICATION.'/manager/index.php' ? ' disabled' : '';
        $film_status = $_SERVER['PHP_SELF'] == APPLICATION.'/film/index.php' ? ' disabled' : '';

        if(LoggedIn()) {
            // Количество запланированных контактов
            $planned_count = 0;
                
            $planned_conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
            $planned_sql = "select count(c.id) count "
                        . "from contact c "
                        . "inner join person p "
                        . "inner join organization o on p.organization_id = o.id "
                        . "on c.person_id = p.id "
                        . "where o.manager_id=".GetManagerId()." "
                        . "and c.next_date is not null "
                        . "and UNIX_TIMESTAMP(c.next_date) < UNIX_TIMESTAMP(DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)) "
                        . "and (select count(id) from contact where person_id = p.id and UNIX_TIMESTAMP(date) >= UNIX_TIMESTAMP(CURRENT_DATE())) = 0";
                    
                if($planned_conn->connect_error) {
                    die('Ошибка соединения: ' . $planned_conn->connect_error);
                }
                    
                $planned_result = $planned_conn->query($planned_sql);
                if ($planned_result->num_rows > 0 && $planned_row = $planned_result->fetch_assoc()) {
                    $planned_count = $planned_row['count'];
                }
                $planned_conn->close();
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$organization_status ?>" href="<?=APPLICATION ?>/organization/">Мои предприятия</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$allorgs_status ?>" href="<?=APPLICATION ?>/organization/all.php">Все предприятия</a>
            </li>
            <li class='nav-item'>
                <a class="nav-link<?=$call_status ?>" href='<?=APPLICATION ?>/contact/'>Перв. действ. контакты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$planned_status ?>" href="<?=APPLICATION ?>/planned/">Запланировано<?=$planned_count == 0 ? '' : ' ('.$planned_count.')' ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$order_status ?>" href="<?=APPLICATION ?>/order/">Заказы</a>
            </li>
            <?php
            }
            if(LoggedIn()) {
            ?>
            <li class="nav-item">
                <a class="nav-link<?=$personal_status ?>" href="<?=APPLICATION ?>/personal/">Мои настройки</a>
            </li>
            <?php
            }
            if(IsInRole('admin')) {
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    Администратор
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item<?=$manager_status ?>" href="<?=APPLICATION ?>/manager/">Менеджеры</a>
                    <a class="dropdown-item<?=$film_status ?>" href="<?=APPLICATION ?>/film/">Типы плёнки</a>
                </div>
            </li>
            <?php
            }
            ?>
        </ul>
        <?php
        if(isset($_COOKIE[USERNAME]) && $_COOKIE[USERNAME] != '') {
        ?>
        <form class="form-inline" method="post">
            <label>
                <?php
                $full_manager_name = '';
                if(isset($_COOKIE[LAST_NAME]) && $_COOKIE[LAST_NAME] != '') {
                    $full_manager_name .= $_COOKIE[LAST_NAME];
                }
                if(isset($_COOKIE[FIRST_NAME]) && $_COOKIE[FIRST_NAME] != '') {
                    if($full_manager_name != '') $full_manager_name .= ' ';
                    $full_manager_name .= $_COOKIE[FIRST_NAME];
                }
                if(isset($_COOKIE[MIDDLE_NAME]) && $_COOKIE[MIDDLE_NAME] != '') {
                    if($full_manager_name != '') $full_manager_name .= ' ';
                    $full_manager_name .= $_COOKIE[MIDDLE_NAME];
                }
                echo $full_manager_name;
                ?>
                &nbsp;
            </label>
            <button type="submit" class="btn btn-outline-dark" id="logout_submit" name="logout_submit">Выход</button>
        </form>
        <?php
        }
        else {
        ?>
        <form class="form-inline my-2 my-lg-0" method="post">
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_username_valid ?>" type="text" id="login_username" name="login_username" placeholder="Логин" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username']) ? $_POST['login_username'] : '' ?>" required="required" autocomplete="on" />
                <div class="invalid-feedback">*</div>
            </div>
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_password_valid ?>" type="password" id="login_password" name="login_password" placeholder="Пароль" required="required" />
                <div class="invalid-feedback">*</div>
            </div>
            <button type="submit" class="btn btn-outline-dark my-2 my-sm-2" id="login_submit" name="login_submit">Войти</button>
        </form>
        <?php
        }
        ?>
    </nav>
</div>
<hr />