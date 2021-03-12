<?php
include '../include/topscripts.php';
include '../include/restrict_logged_in.php';
        
// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
// Обработка отправки формы
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['perspective_planning_edit_submit'])) {
    if($form_valid) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        if($conn->connect_error) {
            die('Ошибка соединения: '.$conn->connect_error);
        }
                
        $id = $_POST['id'];
        $organization_id = $_POST['organization_id'];
        $date = $_POST['date'] == '' ? 'NULL' : "'".$_POST['date']."'";
        $date_minus = $_POST['date_minus'] == '' ? 'NULL' : "'".$_POST['date_minus']."'";
        $date_plus = $_POST['date_plus'] == '' ? 'NULL' : "'".$_POST['date_plus']."'";
        $expenses = $_POST['expenses'] == '' ? 'NULL' : $_POST['expenses'];
        $film_id = $_POST['film_id'] == '' ? 'NULL' : $_POST['film_id'];
        $film_thickness = $_POST['film_thickness'] == '' ? 'NULL' : $_POST['film_thickness'];
        $film_width = $_POST['film_width'] == '' ? 'NULL' : $_POST['film_width'];
        $film_length = $_POST['film_length'] == '' ? 'NULL' : $_POST['film_length'];
        $film_weight = $_POST['film_weight'] == '' ? 'NULL' : $_POST['film_weight'];
        $film_price = $_POST['film_price'] == '' ? 'NULL' : $_POST['film_price'];
        $probability = $_POST['probability'] == '' ? 'NULL' : $_POST['probability'];
                
        $sql = "update perspective_planning set organization_id=$organization_id, date=$date, date_minus=$date_minus, date_plus=$date_plus, "
                . "expenses=$expenses, film_id=$film_id, film_thickness=$film_thickness, film_width=$film_width, film_length=$film_length, "
                . "film_weight=$film_weight, film_price=$film_price, probability=$probability "
                . "where id=$id";
                
        $conn->query('set names utf8');
        if ($conn->query($sql) === true) {
            header('Location: '.APPLICATION.'/organization/details.php?id='.$organization_id.'#perspective_planning');
        }
        else {
            $error_message = $conn->error;
        }
                
        $conn->close();
    }
}
        
// Если нет параметра organization_id, переход к списку предприятий
if(!isset($_GET['id'])) {
    header('Location: '.APPLICATION.'/organization/');
}
        
// Получение объекта
$id = $_GET['id'];
$organization_id = '';
$date = '';
$date_minus = '';
$date_plus = '';
$expenses = '';
$film_id = '';
$film_thickness = '';
$film_width = '';
$film_length = '';
$film_weight = '';
$film_price = '';
$probability = '';
        
$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
$sql = "select organization_id, date, date_minus, date_plus, expenses, film_id, film_thickness, film_width, film_length, film_weight, film_price, probability "
                    . "from perspective_planning where id=".$_GET['id'];
        
if($conn->connect_error) {
    die('Ошибка соединения: ' . $conn->connect_error);
}
        
$conn->query('set names utf8');
$result = $conn->query($sql);
if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
    $organization_id = $row['organization_id'];
    $date = $row['date'];
    $date_minus = $row['date_minus'];
    $date_plus = $row['date_plus'];
    $expenses = $row['expenses'];
    $film_id = $row['film_id'];
    $film_thickness = $row['film_thickness'];
    $film_width = $row['film_width'];
    $film_length = $row['film_length'];
    $film_weight = $row['film_weight'];
    $film_price = $row['film_price'];
    $probability = $row['probability'];
}
$conn->close();
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
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Новое планируемое действие</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/organization/details.php?id=<?=$organization_id ?>" class="btn btn-outline-dark"><span class="font-awesome">&#xf0e2;</span>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr />
                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?=$_GET['id'] ?>"/>
                        <input type="hidden" id="organization_id" name="organization_id" value="<?=$organization_id ?>"/>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Дата (&ndash;)</label>
                                    <input type="date" id="date_minus" name="date_minus" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date_minus']) ? $_POST['date_minus'] : $date_minus ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Дата</label>
                                    <input type="date" id="date" name="date" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date']) ? $_POST['date'] : $date ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Дата (+)</label>
                                    <input type="date" id="date_plus" name="date_plus" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date_plus']) ? $_POST['date_plus'] : $date_plus ?>" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Затраты</label>
                                    <input type="number" step="0.01" id="expenses" name="expenses" class="form-control float-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['expenses']) ? $_POST['expenses'] : $expenses ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Вероятность (%)</label>
                                    <input type="number" step="1" id="probability" name="probability" class="form-control int-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['probability']) ? $_POST['probability'] : $probability ?>" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Тип плёнки</label>
                                    <select class="form-control" id="film_id" name="film_id">
                                        <option value="">...</option>
                                        <?php
                                        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
                                        $sql = "select id, name from film order by name";
                                        
                                        if($conn->connect_error) {
                                            die('Ошибка соединения: ' . $conn->connect_error);
                                        }
                                        
                                        $conn->query('set names utf8');
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $selected = '';
                                                if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_id'])) {
                                                    $film_id = $_POST['film_id'];
                                                }
                                                if($film_id == $row['id']) {
                                                    $selected = " selected='selected'";
                                                }
                                                echo '<option value='.$row['id']."$selected>".htmlentities($row['name']).'</option>';
                                            }
                                        }
                                        $conn->close();
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Толщина плёнки</label>
                                    <input type="number" step="1" id="film_thickness" name="film_thickness" class="form-control int-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_thickness']) ? $_POST['film_thickness'] : $film_thickness ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Ширина плёнки</label>
                                    <input type="number" step="1" id="film_width" name="film_width" class="form-control int-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_width']) ? $_POST['film_width'] : $film_width ?>" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Длина плёнки</label>
                                    <input type="number" step="1" id="film_length" name="film_length" class="form-control int-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_length']) ? $_POST['film_length'] : $film_length ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Вес плёнки</label>
                                    <input type="number" step="1" id="film_weight" name="film_weight" class="form-control int-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_weight']) ? $_POST['film_weight'] : $film_weight ?>" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="name">Цена плёнки</label>
                                    <input type="number" step="0.01" id="film_price" name="film_price" class="form-control float-only" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['film_price']) ? $_POST['film_price'] : $film_price ?>" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="perspective_planning_edit_submit" name="perspective_planning_edit_submit">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>