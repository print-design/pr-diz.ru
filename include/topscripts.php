<?php
include 'define.php';

global $weekdays;

$weekdays = array();
$weekdays[0] = 'Вс';
$weekdays[1] = 'Пн';
$weekdays[2] = 'Вт';
$weekdays[3] = 'Ср';
$weekdays[4] = 'Чт';
$weekdays[5] = 'Пт';
$weekdays[6] = 'Сб';

global $months_genitive;

$months_genitive = array();

$months_genitive[1] = "января";
$months_genitive[2] = "февраля";
$months_genitive[3] = "марта";
$months_genitive[4] = "апреля";
$months_genitive[5] = "мая";
$months_genitive[6] = "июня";
$months_genitive[7] = "июля";
$months_genitive[8] = "августа";
$months_genitive[9] = "сентабря";
$months_genitive[10] = "октября";
$months_genitive[11] = "ноября";
$months_genitive[12] = "декабря";

// Функции
function LoggedIn() {
    return !empty(filter_input(INPUT_COOKIE, USERNAME));
}

function GetUserId() {
    return filter_input(INPUT_COOKIE, USER_ID);
}

function IsInRole($role) {
    $cookie = filter_input(INPUT_COOKIE, ROLE);
    
    if(is_array($role)) {
        return in_array($cookie, $role);
    }
    else {
        return $cookie == $role;
    }
    
    return false;
}

function Initials() {
    $last_name = filter_input(INPUT_COOKIE, LAST_NAME);
    $first_name = filter_input(INPUT_COOKIE, FIRST_NAME);
    $result = '';
    
    if(mb_strlen($last_name) > 1) {
        $result .= mb_substr($last_name, 0, 1);
    }
    else {
        $result .= $last_name;
    }
    
    if(mb_strlen($first_name) > 1) {
        $result .= mb_substr($first_name, 0, 1);
    }
    else {
        $result .= $first_name;
    }
    
    return $result;
}

function BuildQuery($key, $value) {
    $result = '';
    $get_params = $_GET;
    $get_params[$key] = $value;
    $result = http_build_query($get_params);
    
    if(!empty($result)) {
        $result = "?$result";
    }
    
    return $result;
}

function BuildQueryRemove($key) {
    $result = '';
    $get_params = $_GET;
    unset($get_params[$key]);
    $result = http_build_query($get_params);

    if(!empty($result)) {
        $result = "?$result";
    }
    
    return $result;
}

function BuildQueryRemoveArray($array) {
    $result = '';
    $get_params = $_GET;
    foreach($array as $key) {
        unset($get_params[$key]);
    }
    $result = http_build_query($get_params);

    if(!empty($result)) {
        $result = "?$result";
    }
    
    return $result;
}

function BuildQueryAddRemove($key, $value, $remove) {
    $result = '';
    $get_params = $_GET;
    $get_params[$key] = $value;
    unset($get_params[$remove]);
    $result = http_build_query($get_params);
    
    if(!empty($result)) {
        $result = "?$result";
    }
    
    return $result;
}

function GetDateFromDateTo($getDateFrom, $getDateTo, &$dateFrom, &$dateTo) {
    $dateFrom = null;
    $dateTo = null;
    
    $diff7Days = new DateInterval('P7D');
    $diff14Days = new DateInterval('P14D');
    $diff30Days = new DateInterval('P30D');
    $diff1Day = new DateInterval('P1D');
    
    if($getDateFrom !== null && $getDateFrom !== '') {
        $dateFrom = DateTime::createFromFormat("Y-m-d", $getDateFrom);
    }
    
    if($getDateTo !== null && $getDateTo !== '') {
        $dateTo = DateTime::createFromFormat("Y-m-d", $getDateTo);
    }
    
    if($dateFrom !== null && $dateTo == null) {
        $dateTo = clone $dateFrom;
        $dateTo->add($diff30Days);
    }
    
    if($dateFrom == null && $dateTo !== null) {
        $dateFrom = clone $dateTo;
        $dateFrom->sub($diff30Days);
    }
    
    if($dateFrom !== null && $dateTo !== null && $dateFrom >= $dateTo) {
        $dateTo = clone $dateFrom;
    }
    
    if($dateFrom == null && $dateTo == null) {
        $dateFrom = new DateTime();
        $dateTo = clone $dateFrom;
        $dateTo->add($diff30Days);
    }
}

function DownloadSendHeaders($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function Array2Csv(array &$array, $titles) {
    if (count($array) == 0) {
            return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputs($df, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Это для правильной кодировки
    fputcsv($df, $titles, ';');
    foreach ($array as $row) {
        fputcsv($df, $row, ';');
    }
    fclose($df);
    return ob_get_clean();
}

// Классы
class Executer {
    public $error = '';
    public $insert_id = 0;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $conn->query($sql);
        $this->error = $conn->error;
        $this->insert_id = $conn->insert_id;
        
        $conn->close();
    }
}

class Grabber {
    public  $error = '';
    public $result = array();
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        
        if(is_bool($result)) {
            $this->error = $conn->error;
        }
        else {
            $this->result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        $conn->close();
    }
}

class Fetcher {
    public $error = '';
    private $result;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $this->result = $conn->query($sql);
        
        if(is_bool($this->result)) {
            $this->error = $conn->error;
        }
        
        $conn->close();
    }
    
    function Fetch() {
        return mysqli_fetch_array($this->result);
    }
}

// Валидация формы логина
define('LOGINISINVALID', ' is-invalid');
$login_form_valid = true;

$login_username_valid = '';
$login_password_valid = '';

// Обработка отправки формы логина
if(null !== filter_input(INPUT_POST, 'login_submit')) {
    $login_username = filter_input(INPUT_POST, 'login_username');
    if(empty($login_username)) {
        $login_username_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    $login_password = filter_input(INPUT_POST, 'login_password');
    if(empty($login_password)) {
        $login_password_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    if($login_form_valid) {
        $user_id = '';
        $username = '';
        $password = '';
        $password5 = ''; // Первые 5 символов зашифрованного пароля
        $last_name = '';
        $first_name = '';
        $role = '';
        $role_local = '';
        $twofactor = 0;
        
        $sql = "select u.id, u.username, u.password, u.last_name, u.first_name, u.email, r.name role, r.local_name role_local, r.twofactor "
                . "from user u "
                . "inner join role r on u.role_id=r.id "
                . "where u.username='$login_username' and u.password=password('$login_password') and u.active=true";
        
        $users_result = (new Grabber($sql))->result;
        
        foreach ($users_result as $row) {
            $user_id = $row['id'];
            $username = $row['username'];
            $password = $row['password'];
            if(strlen($password) > 5) {
                // Сохраняем первые 5 символов зашифрованного пароля (чтобы не хранить в куках весь пароль)
                $password5 = substr($password, 1, 5);
            }
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            $role = $row['role'];
            $role_local = $row['role_local'];
            $email = $row['email'];
            $twofactor = $row['twofactor'];
        }
        
        if(empty($user_id) || empty($username)) {
            $error_message = "Неправильный логин или пароль";
        }
        else {
            //*******************************
            // Двухфакторная аутентификация
            if($twofactor == 1) {
                $code_valid = '';
                include 'twofactor_email.php';
            }
            // ****************************
            
            setcookie(USER_ID, $user_id, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(USERNAME, $username, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(PASSWORD5, $password5, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(LAST_NAME, $last_name, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(FIRST_NAME, $first_name, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(ROLE, $role, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(ROLE_LOCAL, $role_local, time() + 60 * 60 * 24 * 100000, "/");
            setcookie(LOGIN_TIME, (new DateTime())->getTimestamp(), time() + 60 * 60 * 24 * 100000, "/");
            header("Refresh:0");
        }
    }
}

// Обработка формы отправки кода безопасности
if(null !== filter_input(INPUT_POST, 'security_code_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "select u.id, u.username, u.password, u.last_name, u.first_name, u.email, u.code, r.name role, r.local_name role_local "
            . "from user u inner join role r on u.role_id = r.id "
            . "where u.id=$id";
    $result = (new Grabber($sql))->result;
    
    foreach ($result as $row) {
        $user_id = $row['id'];
        $username = $row['username'];
        $password = $row['password'];
        if(strlen($password) > 5) {
            $password5 = substr($password, 1, 5);
        }
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $role = $row['role'];
        $role_local = $row['role_local'];
        $email = $row['email'];
        $code = $row['code'];
        
        if(filter_input(INPUT_POST, 'code') == $code) {
            $error_message = (new Executer("update user set code=NULL where id=$user_id"))->error;
            
            if($error_message == '') {
                setcookie(USER_ID, $user_id, time() + 60 * 60 * 24 * 100000, "/");
                setcookie(USERNAME, $username, time() + 60 * 60 * 24 * 100000, "/");
                setcookie(PASSWORD5, $password5, time() + 60 * 60 * 24 * 100000, "/");
                setcookie(LAST_NAME, $last_name, time() + 60 * 60 * 24 * 100000, "/");
                setcookie(FIRST_NAME, $first_name, time() + 60 * 60 * 24 * 100000, "/");
                setcookie(ROLE, $role, time() + 60 * 60 * 24 * 100000, '/');
                setcookie(ROLE_LOCAL, $role_local, time() + 60 * 60 * 24 * 100000, '/');
                setcookie(LOGIN_TIME, (new DateTime())->getTimestamp(), time() + 60 * 60 * 24 * 100000, "/");
                header("Refresh:0");
            }
        }
        else {
            define('ISINVALID', ' is-invalid');
            $code_valid = ISINVALID;
            include 'twofactor_email.php';
        }
    }
}

function Logout() {
    setcookie(USER_ID, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(USERNAME, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(PASSWORD5, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(LAST_NAME, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(FIRST_NAME, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(LOGIN_TIME, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(ROLE, '', time() + 60 * 60 * 24 * 100000, "/");
    setcookie(ROLE_LOCAL, '', time() + 60 * 60 * 24 * 100000, "/");
    header("Refresh:0");
    header('Location: '.APPLICATION.'/');
}

// Обработка кнопки выхода из системы
if(null !== filter_input(INPUT_POST, 'logout_submit')) {
    Logout();
}

// Выход из системы, если удалили пользователя или сменили пароль
if(LoggedIn()) {
    $username = filter_input(INPUT_COOKIE, USERNAME);
    $password5 = filter_input(INPUT_COOKIE, PASSWORD5);
    $sql = "select count(id) from user where username = '$username' and substring(password, 2, 5) = '$password5' and active=true";
    $row = (new Fetcher($sql))->Fetch();
    
    if($row[0] == 0) {
        Logout();
    }
    
    //---------------------------------------------------
    // ВРЕМЕННЫЙ КОД. ПОТОМ УДАЛИТЬ ЕГО.
    // Если в куках нет русского названия роли, берём его из базы и помещаем в куки
    $role = filter_input(INPUT_COOKIE, ROLE);
    $role_local = filter_input(INPUT_COOKIE, ROLE_LOCAL);
    
    if(empty($role_local) && !empty($role)) {
        $sql = "select local_name from role where name = '$role'";
        $fetcher = new Fetcher($sql);
        
        if($row = $fetcher->Fetch()) {
            setcookie(ROLE_LOCAL, $row[0], time() + 60 * 60 * 24 * 100000, '/');
        }
    }
    //---------------------------------------------------
}
?>