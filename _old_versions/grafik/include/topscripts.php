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

const TECHNOLOGIST = "technologist";

// Функции
function LoggedIn() {
    $username = filter_input(INPUT_COOKIE, USERNAME);
    return $username !== null;
}

function GetUserId() {
    return filter_input(INPUT_COOKIE, USER_ID);
}

function IsInRole($role) {
    $roles = filter_input(INPUT_COOKIE, ROLES);
    if($roles !== null) {
        $arr_roles = unserialize($roles);
        return in_array($role, $arr_roles);
    }
    
    return false;
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
    $diff3Months = new DateInterval('P3M');
    
    if($getDateFrom !== null && $getDateFrom !== '') {
        $dateFrom = DateTime::createFromFormat("Y-m-d", $getDateFrom);
    }
    
    if($getDateTo !== null && $getDateTo !== '') {
        $dateTo = DateTime::createFromFormat("Y-m-d", $getDateTo);
    }
    
    if($dateFrom !== null && $dateTo == null) {
        $dateTo = clone $dateFrom;
        $dateTo->add($diff3Months);
    }
    
    if($dateFrom == null && $dateTo !== null) {
        $dateFrom = clone $dateTo;
        $dateFrom->sub($diff3Months);
    }
    
    if($dateFrom !== null && $dateTo !== null && $dateFrom >= $dateTo) {
        $dateTo = clone $dateFrom;
    }
    
    if($dateFrom == null && $dateTo == null) {
        $dateFrom = new DateTime();
        // $dateFrom->sub($diff1Day); Раскомментировать эту строку, чтобы показывать даты не с сегодняшнего, а со вчерашнего
        $dateTo = clone $dateFrom;
        $dateTo->add($diff3Months);
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
        $result = $conn->query($sql);
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

class FetcherErp {
    public $error = '';
    private $result;
    
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME_ERP);
        
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
$login_submit = filter_input(INPUT_POST, 'login_submit');
if($login_submit !== null){
    $login_username = filter_input(INPUT_POST, 'login_username');
    if($login_username == '') {
        $login_username_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    $login_password = filter_input(INPUT_POST, 'login_password');
    if($login_password == '') {
        $login_password_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    if($login_form_valid) {
        $user_id = '';
        $username = '';
        $fio = '';
        
        $users_result = (new Grabber("select id, username, fio from user where username='$login_username' and password=password('$login_password') and quit = 0"))->result;
        
        foreach ($users_result as $row) {
            $user_id = $row['id'];
            setcookie(USER_ID, $user_id, time() + 60 * 60 * 24 * 100, "/");
            
            $username = $row['username'];
            setcookie(USERNAME, $username, time() + 60 * 60 * 24 * 100, "/");
            
            $fio = $row['fio'];
            setcookie(FIO, $fio, time() + 60 * 60 * 24 * 100, "/");
        }
        
        if($user_id == '' || $username == '') {
            $error_message = "Неправильный логин или пароль";
        }
        else {
            $roles = array();
            $role_i = 0;
            $roles_result = (new Grabber("select r.name from user_role ur inner join role r on ur.role_id = r.id where ur.user_id = $user_id"))->result;
            
            foreach ($roles_result as $role_row) {
                $roles[$role_i++] = $role_row['name'];
            }
            
            setcookie(ROLES, serialize($roles), time() + 60 * 60 * 24 * 100, '/');
            header("Refresh:0");
        }
    }
}

$logout_submit = filter_input(INPUT_POST, 'logout_submit');
if($logout_submit !== null) {
    setcookie(USER_ID, '', time() + 60 * 60 * 24 * 100, "/");
    setcookie(USERNAME, '', time() + 60 * 60 * 24 * 100, "/");
    setcookie(FIO, '', time() + 60 * 60 * 24 * 100, "/");
    setcookie(ROLES, '', time() + 60 * 60 * 24 * 100, "/");
    header("Refresh:0");
    header('Location: '.APPLICATION.'/');
}
?>