<?php
include 'define.php';
include 'constants.php';

session_start();

if(empty($_SESSION[CSRF_TOKEN])) {
    $_SESSION[CSRF_TOKEN] = bin2hex(random_bytes(32));
}

// Защита от CSRF: для каждого POST-запроса проверяем, что пришедший токен совпадает
// с тем, что хранится в сессии пользователя. Токен уже выводится во все формы проекта,
// здесь мы впервые начинаем его реально проверять.
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_csrf_token = filter_input(INPUT_POST, CSRF_TOKEN);
    
    if(empty($submitted_csrf_token) || empty($_SESSION[CSRF_TOKEN]) || !hash_equals($_SESSION[CSRF_TOKEN], $submitted_csrf_token)) {
        http_response_code(403);
        
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            die(json_encode(array('error' => 'Истекла сессия. Обновите страницу и попробуйте снова.')));
        }
        
        die('<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Истекла сессия</title></head><body>'
                . '<div style="font-family: sans-serif; max-width: 500px; margin: 100px auto; text-align: center;">'
                . '<h2>Истекла сессия</h2><p>Пожалуйста, обновите страницу и попробуйте снова.</p></div></body></html>');
    }
}

global $weekdays;

$weekdays = array();
$weekdays[0] = 'Вс';
$weekdays[1] = 'Пн';
$weekdays[2] = 'Вт';
$weekdays[3] = 'Ср';
$weekdays[4] = 'Чт';
$weekdays[5] = 'Пт';
$weekdays[6] = 'Сб';

global $weekday_names;

$weekday_names = array();
$weekday_names[0] = 'Воскресенье';
$weekday_names[1] = 'Понедельник';
$weekday_names[2] = 'Вторник';
$weekday_names[3] = 'Среда';
$weekday_names[4] = 'Четверг';
$weekday_names[5] = 'Пятница';
$weekday_names[6] = 'Суббота';

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
// ---------------------------------------------------------------------------
// Авторизация
//
// В куки хранятся только имя пользователя (USERNAME) и токен (PASSWORD5).
// Токен -- это HMAC от хеша пароля с секретом AUTH_SECRET (см. include/define.php),
// поэтому подделать его без знания секрета нельзя, а при смене пароля он перестаёт
// подходить (старые сессии завершаются). Роль, ФИО и id пользователя берутся из базы
// на сервере, а не из куки: куки браузера могут быть изменены пользователем.
// ---------------------------------------------------------------------------

if(!defined('AUTH_SECRET') || strlen(AUTH_SECRET) < 32) {
    http_response_code(500);
    die('Не задан AUTH_SECRET в include/define.php (строка не короче 32 символов).');
}

// Токен обычного пользователя
function MakeAuthToken($user_id, $password_hash) {
    return hash_hmac('sha256', 'user|'.$user_id.'|'.$password_hash, AUTH_SECRET);
}

// Токен пользователя резки (у таких пользователей нет записи в базе)
function MakeCutterAuthToken($cutter_username) {
    return hash_hmac('sha256', 'cutter|'.$cutter_username, AUTH_SECRET);
}

// Хеш графического ключа. Должен быть одинаковым при каждом вычислении (по нему идёт поиск
// пользователя), поэтому используется HMAC с секретом, а не password_hash().
function MakeGraphKeyHash($graph_key) {
    return hash_hmac('sha256', 'graph|'.$graph_key, AUTH_SECRET);
}

// Записывает куки авторизации
function SetAuthCookies($username, $token) {
    $options = array(
        'expires' => time() + 60 * 60 * 24 * 100000,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    );
    
    setcookie(USERNAME, $username, $options);
    setcookie(PASSWORD5, $token, $options);
    $GLOBALS['auth_cookies_just_set'] = true;
}

// Текущий авторизованный пользователь (проверенный по базе) или null.
// Результат запоминается на время запроса.
function AuthUser() {
    static $loaded = false;
    static $user = null;
    
    if($loaded) {
        return $user;
    }
    
    $loaded = true;
    $username = filter_input(INPUT_COOKIE, USERNAME);
    $token = filter_input(INPUT_COOKIE, PASSWORD5);
    
    if(empty($username) || empty($token)) {
        return null;
    }
    
    // Пользователи резки
    $lower_username = strtolower($username);
    if(in_array($lower_username, CUTTER_USERS, true)) {
        if(hash_equals(MakeCutterAuthToken($lower_username), $token)) {
            $user = array(
                'id' => CUTTER_USER_IDS[$lower_username],
                'username' => $lower_username,
                'last_name' => '',
                'first_name' => '',
                'role' => $lower_username,
                'role_local' => CUTTER_USER_NAMES[$lower_username]
            );
        }
        
        return $user;
    }
    
    // Обычные пользователи
    $row = (new Fetcher("select id, username, password, last_name, first_name, role_id from user where username = ? and active = true", [$username]))->Fetch();
    
    if($row && isset(ROLE_NAMES[$row['role_id']]) && hash_equals(MakeAuthToken($row['id'], $row['password']), $token)) {
        $user = array(
            'id' => $row['id'],
            'username' => $row['username'],
            'last_name' => $row['last_name'],
            'first_name' => $row['first_name'],
            'role' => ROLE_NAMES[$row['role_id']],
            'role_local' => ROLE_LOCAL_NAMES[$row['role_id']]
        );
    }
    
    return $user;
}

function AuthValue($key) {
    $user = AuthUser();
    return $user === null ? null : $user[$key];
}

function LoggedIn() {
    return AuthUser() !== null;
}

function GetUserId() {
    return AuthValue('id');
}

function GetUserRoleLocal() {
    return AuthValue('role_local');
}

// Пароли хранятся в формате password_hash(). Старый формат (функция MySQL password(),
// строка вида *ABC...40 hex) ещё распознаётся и при первом успешном входе пользователя
// автоматически заменяется на новый.
function IsLegacyPasswordHash($hash) {
    return preg_match('/^\*[0-9A-Fa-f]{40}$/', (string)$hash) === 1;
}

function HashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Проверяет пароль пользователя. Возвращает актуальный хеш из базы (после возможного
// перехода на новый формат) или null, если пароль неверный.
function CheckUserPassword($user_id, $stored_hash, $password) {
    $stored_hash = (string)$stored_hash;
    
    if($stored_hash === '' || $password === null || $password === '') {
        return null;
    }
    
    $legacy = IsLegacyPasswordHash($stored_hash);
    
    if($legacy) {
        $row = (new Fetcher("select password(?)", [$password]))->Fetch();
        $valid = $row && hash_equals(strtoupper($stored_hash), strtoupper((string)$row[0]));
    }
    else {
        $valid = password_verify($password, $stored_hash);
    }
    
    if(!$valid) {
        return null;
    }
    
    if($legacy || password_needs_rehash($stored_hash, PASSWORD_DEFAULT)) {
        $new_hash = HashPassword($password);
        $executer = new Executer("update user set password = ? where id = ?", [$new_hash, $user_id]);
        
        if(empty($executer->error)) {
            return $new_hash;
        }
    }
    
    return $stored_hash;
}

// Проверяет пароль пользователя по его id (для форм смены пароля)
function VerifyUserPasswordById($user_id, $password) {
    $row = (new Fetcher("select password from user where id = ?", [$user_id]))->Fetch();
    return $row && CheckUserPassword($user_id, $row['password'], $password) !== null;
}

// Резчик на резке "Сома". В ночную смену, когда нет мастера цеха, ему разрешено работать с планом
// (переназначать заказы с одной резки на другую). Мастер цеха подтвердил сохранение этого права (21.10.2026).
// Проверяем именно пользователя резки, а не GetUserId(): идентификаторы резок совпадают с идентификаторами
// обычных пользователей в таблице user.
function IsCutterSoma() {
    return AuthValue('role') === CUTTER_USER_SOMA;
}

function IsInRole($role) {
    // Роль 'manager-senior' имеет все права роли 'manager'
    if(is_array($role) && in_array(ROLE_NAMES[ROLE_MANAGER], $role)) {
        array_push($role, ROLE_NAMES[ROLE_MANAGER_SENIOR]);
    }
    elseif($role == ROLE_NAMES[ROLE_MANAGER]) {
        $role = array(ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]);
    }
    
    $cookie = AuthValue('role');
    
    if(is_array($role)) {
        return in_array($cookie, $role);
    }
    else {
        return $cookie == $role;
    }
    
    return false;
}

function DisplayNumber($value, $decimals) {
    if(is_float($value) || is_double($value) || is_int($value)) {
        return number_format($value ?? 0, $decimals ?? 0, ",", " ");
    }
    elseif(is_string($value)) {
        return str_replace(".", ",", $value);
    }
    else {
        return $value;
    }
}

// Возвращает нужную по правилам русского языка форму слова для числа $number.
// Пример: PluralForm(3, "паллет", "паллета", "паллетов") вернёт "паллета"
function PluralForm($number, $one, $few, $many) {
    $number = abs(intval($number));
    $last_two = $number % 100;
    $last_one = $number % 10;
    
    if($last_two >= 11 && $last_two <= 14) {
        return $many;
    }
    if($last_one == 1) {
        return $one;
    }
    if($last_one >= 2 && $last_one <= 4) {
        return $few;
    }
    return $many;
}

function Initials() {
    $last_name = (string)AuthValue('last_name');
    $first_name = (string)AuthValue('first_name');
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

function BuildQueryAddRemoveArray($key, $value, $array) {
    $result = '';
    $get_params = $_GET;
    $get_params[$key] = $value;
    foreach($array as $item_key) {
        unset($get_params[$item_key]);
    }
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
        $dateTo = clone $dateFrom;
        $dateTo->add($diff3Months);
    }
}

// Принудительная выгрузка (вместо открытия)
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

// Автоматически определяет строку типов для bind_param на основе значений параметров
trait BindParamsTrait {
    private static function BuildTypes($params) {
        $types = '';
        
        foreach($params as $param) {
            if(is_int($param)) {
                $types .= 'i';
            }
            elseif(is_float($param)) {
                $types .= 'd';
            }
            else {
                $types .= 's';
            }
        }
        
        return $types;
    }
}

// Классы
class Executer {
    use BindParamsTrait;
    
    public $error = '';
    public $insert_id = 0;
            
    function __construct($sql, $params = null) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        
        if($params !== null) {
            $stmt = $conn->prepare($sql);
            
            if($stmt === false) {
                $this->error = $conn->error;
                $conn->close();
                return;
            }
            
            if(!empty($params)) {
                $types = self::BuildTypes($params);
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $this->error = $stmt->error;
            $this->insert_id = $stmt->insert_id;
            $stmt->close();
        }
        else {
            $conn->query($sql);
            $this->error = $conn->error;
            $this->insert_id = $conn->insert_id;
        }
        
        $conn->close();
    }
}

class Grabber {
    use BindParamsTrait;
    
    public  $error = '';
    public $result = array();
            
    function __construct($sql, $params = null) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        
        if($params !== null) {
            $stmt = $conn->prepare($sql);
            
            if($stmt === false) {
                $this->error = $conn->error;
                $conn->close();
                return;
            }
            
            if(!empty($params)) {
                $types = self::BuildTypes($params);
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $mysqli_result = $stmt->get_result();
            
            if($mysqli_result === false) {
                $this->error = $stmt->error;
            }
            else {
                $this->result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
            }
            
            $stmt->close();
        }
        else {
            $result = $conn->query($sql);
            
            if(is_bool($result)) {
                $this->error = $conn->error;
            }
            else {
                $this->result = mysqli_fetch_all($result, MYSQLI_ASSOC);
            }
        }
        
        $conn->close();
    }
}

class Fetcher {
    use BindParamsTrait;
    
    public $error = '';
    private $result;
            
    function __construct($sql, $params = null) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        
        if($params !== null) {
            // Новый способ - подготовленное выражение
            $stmt = $conn->prepare($sql);
            
            if($stmt === false) {
                $this->error = $conn->error;
                $conn->close();
                return;
            }
            
            if(!empty($params)) {
                $types = self::BuildTypes($params);
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $this->result = $stmt->get_result();
            
            if($this->result === false) {
                $this->error = $stmt->error;
            }
            
            $stmt->close();
        }
        else {
            // Старый способ - для мест, которые ещё не мигрировали
            $this->result = $conn->query($sql);
            
            if(is_bool($this->result)) {
                $this->error = $conn->error;
            }
        }
        
        $conn->close();
    }
    
    function Fetch() {
        return mysqli_fetch_array($this->result);
    }
}

// Транзакция: держит одно соединение открытым на всю цепочку связанных запросов,
// в отличие от Executer/Fetcher/Grabber, которые открывают и сразу закрывают своё
// собственное соединение на каждый отдельный вызов (и поэтому не годятся для транзакций).
//
// Использование:
//   $transaction = new Transaction();
//   $transaction->Execute($sql1, $params1);
//   if(empty($transaction->error)) { $transaction->Execute($sql2, $params2); }
//   if(empty($transaction->error)) { $transaction->Commit(); } else { $transaction->Rollback(); }
class Transaction {
    use BindParamsTrait;
    
    private $conn;
    public $error = '';
    public $insert_id = 0;
    
    function __construct() {
        $this->conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($this->conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$this->conn->connect_error;
            return;
        }
        
        $this->conn->query('set names utf8');
        $this->conn->begin_transaction();
    }
    
    // insert/update/delete в рамках этой транзакции. Если в транзакции уже была ошибка на
    // предыдущем шаге, ничего не делает -- защита на случай, если вызывающий код забудет
    // проверить $transaction->error перед следующим шагом
    function Execute($sql, $params = array()) {
        if(!empty($this->error)) {
            return 0;
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if($stmt === false) {
            $this->error = $this->conn->error;
            return 0;
        }
        
        if(!empty($params)) {
            $types = self::BuildTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $this->error = $stmt->error;
        $this->insert_id = $stmt->insert_id;
        $stmt->close();
        
        return $this->insert_id;
    }
    
    // select в рамках этой транзакции -- возвращает массив всех найденных строк
    // (как Grabber; для одной строки берите $rows[0])
    function Fetch($sql, $params = array()) {
        if(!empty($this->error)) {
            return array();
        }
        
        $stmt = $this->conn->prepare($sql);
        
        if($stmt === false) {
            $this->error = $this->conn->error;
            return array();
        }
        
        if(!empty($params)) {
            $types = self::BuildTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result === false) {
            $this->error = $stmt->error;
            $stmt->close();
            return array();
        }
        
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $stmt->close();
        
        return $rows;
    }
    
    // Фиксирует изменения, если ошибок не было; иначе откатывает всё как есть
    function Commit() {
        if(empty($this->error)) {
            $this->conn->commit();
        }
        else {
            $this->conn->rollback();
        }
        
        $this->conn->close();
    }
    
    // Откатывает изменения явно (например, если вызывающий код решил отменить всё
    // по причине, не связанной с ошибкой SQL как таковой)
    function Rollback() {
        $this->conn->rollback();
        $this->conn->close();
    }
}

// Выгрузка картинки
if(null !== filter_input(INPUT_POST, 'download_image_submit')) {
    $object = filter_input(INPUT_POST, 'object');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $image = filter_input(INPUT_POST, 'image');
    
    if(!empty($object) && !empty($id) && !empty($image)) {
        $sql = "";
        
        if($object == PRINTING) {
            $sql = "select concat(c.name, cq.id) name, cq.image$image, cq.pdf$image from calculation_quantity cq inner join calculation c on cq.calculation_id = c.id where cq.id = ?";
        }
        elseif ($object == STREAM) {
            $sql = "select name, image$image, pdf$image from calculation_stream where id = ?";
        }
        
        if(!empty($sql)) {
            $targetname = "image";
            $fetcher = new Fetcher($sql, [$id]);
            
            if($row = $fetcher->Fetch()) {
                if(!empty($row['name'])) {
                    $targetname = $row['name'].'_'.$image;
                    $targetname = str_replace('.', '', $targetname);
                    $targetname = str_replace(',', '', $targetname);
                    $targetname = str_replace(';', '', $targetname);
                    $targetname = str_replace('"', "", $targetname);
                    $targetname = htmlspecialchars($targetname);
                }
                
                $filename = $row["image$image"];
                $filepath = "../content/$object/$filename";
                $extension = "";
                
                if(!empty($row["pdf$image"])) {
                    $filename = $row["pdf$image"];
                    $filepath = "../content/$object/pdf/$filename";
                    $extension = "pdf";
                }
                else {
                    $substrings = explode('.', $filename);
                    if(count($substrings) > 1) {
                        $extension = $substrings[count($substrings) - 1];
                    }
                }
                
                $targetname = $targetname.'.'.$extension; //echo $filepath; echo "<br />"; echo $targetname; exit();
                
                DownloadSendHeaders($targetname);
                readfile($filepath);
                exit();
            }
        }
    }
}

// Добавление статуса заказа
// $transaction -- необязательный параметр. Если не передан, функция сама открывает
// свою собственную транзакцию и сама же её фиксирует/откатывает (так вызывалась эта
// функция раньше, и так продолжают работать все существующие вызовы по всему проекту).
// Если передан -- это значит, что вызывающий код ведёт СВОЮ собственную, более крупную
// транзакцию, частью которой должна стать смена статуса; тогда commit/rollback остаётся
// на усмотрение вызывающего кода, а не этой функции.
function SetCalculationStatus($calculation_id, $status_id, $comment, $transaction = null) {
    $user_id = GetUserId();
    $old_status_id = 0;
    
    $own_transaction = false;
    
    if($transaction === null) {
        $transaction = new Transaction();
        $own_transaction = true;
    }
    
    $rows = $transaction->Fetch("select status_id from calculation_status_history where calculation_id = ? order by id desc limit 1", [$calculation_id]);
    if(!empty($rows)) {
        $old_status_id = $rows[0]['status_id'];
    }
    
    if($old_status_id != $status_id) {
        $transaction->Execute("insert into calculation_status_history (calculation_id, status_id, comment, user_id) values (?, ?, ?, ?)", [$calculation_id, $status_id, $comment, $user_id]);
        
        // Устанавливаем этот статус в дублирующееся поле.
        $transaction->Execute(
                "update calculation set duplicate_status_id = ?, duplicate_status_comment = ?, "
                . "duplicate_status_date = (select date from calculation_status_history where calculation_id = ? order by id desc limit 1) "
                . "where id = ?",
                [$status_id, $comment, $calculation_id, $calculation_id]);
    }
    
    $error_message = $transaction->error;
    
    if($own_transaction) {
        if(empty($error_message)) {
            $transaction->Commit();
        }
        else {
            $transaction->Rollback();
        }
    }
    
    return $error_message;
}

// Удаление последнего статуса заказа
function RemoveCalculationStatus($calculation_id, $status_id) {
    $error_message = '';
    
    // Удаляем из истории все записи с этим статусом для этого заказа (потому что один и тот же статус могут случайно поставить несколько раз).
    if(empty($error_message)) {
        $sql = "delete from calculation_status_history where status_id = ? and calculation_id = ?";
        $executer = new Executer($sql, [$status_id, $calculation_id]);
        $error_message = $executer->error;
    }
    
    // Удаляем из истории статус "В плане резки", если данного заказа нет в плане резки.
    if(empty($error_message)) {
        $sql = "delete from calculation_status_history where calculation_id = ? and status_id = ? "
                . "and (select count(id) from plan_edition where calculation_id = ? and work_id = ?) = 0";
        $executer = new Executer($sql, [$calculation_id, ORDER_STATUS_PLAN_CUT, $calculation_id, WORK_CUTTING]);
        $error_message = $executer->error;
    }
    
    // Удаляем из истории статус "В плане ламинации", если данного заказа нет в плане ламинации.
    if(empty($error_message)) {
        $sql = "delete from calculation_status_history where calculation_id = ? and status_id = ? "
                . "and (select count(id) from plan_edition where calculation_id = ? and work_id = ?) = 0";
        $executer = new Executer($sql, [$calculation_id, ORDER_STATUS_PLAN_LAMINATE, $calculation_id, WORK_LAMINATION]);
        $error_message = $executer->error;
    }
    
    // Удаляем из истории статус "В плане печати", если данного заказа нет в плане печати.
    if(empty($error_message)) {
        $sql = "delete from calculation_status_history where calculation_id = ? and status_id = ? "
                . "and (select count(id) from plan_edition where calculation_id = ? and work_id = ?) = 0";
        $executer = new Executer($sql, [$calculation_id, ORDER_STATUS_PLAN_PRINT, $calculation_id, WORK_PRINTING]);
        $error_message = $executer->error;
    }
    
    // Устанавливаем этот статус в дублирующее поле (дублирующее поле предназначено для ускорения загрузки).
    if(empty($error_message)) {
        $sql = "update calculation "
                . "set duplicate_status_id = (select status_id from calculation_status_history where calculation_id = ? order by id desc limit 1), "
                . "duplicate_status_comment = (select comment from calculation_status_history where calculation_id = ? order by id desc limit 1), "
                . "duplicate_status_date = (select date from calculation_status_history where calculation_id = ? order by id desc limit 1) "
                . "where id = ?";
        $executer = new Executer($sql, [$calculation_id, $calculation_id, $calculation_id, $calculation_id]);
        if(empty($error_message)) {
            $error_message = $executer->error;
        }
    }
    
    return $error_message;
}

// Ограничение числа неудачных попыток входа.
// Неудачные попытки записываются в таблицу login_attempt (см. login_attempt.sql).
// Если таблицы нет (SQL ещё не выполнен), ограничение не действует, а вход работает как раньше.
const LOGIN_MAX_ATTEMPTS = 5;        // неудачных попыток с одним логином с одного адреса
const LOGIN_IP_MAX_ATTEMPTS = 50;    // неудачных попыток с одного адреса при любых логинах
const LOGIN_WINDOW_MINUTES = 15;     // окно подсчёта и время блокировки

// Адрес посетителя. За обратным прокси (nginx хостинга) REMOTE_ADDR может оказаться внутренним
// адресом самого прокси, тогда берём адрес из заголовков прокси. Если же REMOTE_ADDR внешний,
// заголовкам не доверяем: их можно подделать.
function ClientIp() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    if($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        foreach(array('HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR') as $header) {
            if(!empty($_SERVER[$header])) {
                $candidate = trim(explode(',', $_SERVER[$header])[0]);
                
                if(filter_var($candidate, FILTER_VALIDATE_IP)) {
                    return $candidate;
                }
            }
        }
    }
    
    return $ip === '' ? '0.0.0.0' : $ip;
}

// Ключ для подсчёта попыток: логин без учёта регистра
function LoginAttemptKey($username) {
    return mb_substr(strtolower(trim((string)$username)), 0, 100);
}

// Сколько секунд осталось до конца блокировки (0, если вход не заблокирован)
function LoginLockSeconds($key) {
    try {
        $ip = ClientIp();
        $window = (int)LOGIN_WINDOW_MINUTES;
        $seconds = 0;
        
        $queries = array(
            array("select timestampdiff(second, now(), created_at + interval $window minute) from login_attempt "
                . "where username = ? and ip = ? and created_at > now() - interval $window minute "
                . "order by created_at desc limit 1 offset ".((int)LOGIN_MAX_ATTEMPTS - 1), array($key, $ip)),
            array("select timestampdiff(second, now(), created_at + interval $window minute) from login_attempt "
                . "where ip = ? and created_at > now() - interval $window minute "
                . "order by created_at desc limit 1 offset ".((int)LOGIN_IP_MAX_ATTEMPTS - 1), array($ip))
        );
        
        foreach($queries as $query) {
            $row = (new Fetcher($query[0], $query[1]))->Fetch();
            
            if($row) {
                $seconds = max($seconds, (int)$row[0]);
            }
        }
        
        return $seconds;
    }
    catch(Throwable $e) {
        return 0;
    }
}

function LoginLockMessage($seconds) {
    $minutes = max(1, (int)ceil($seconds / 60));
    return "Слишком много неудачных попыток входа. Повторите попытку через $minutes мин.";
}

// Записывает неудачную попытку входа
function RecordLoginFailure($key) {
    try {
        new Executer("insert into login_attempt (username, ip) values (?, ?)", array($key, ClientIp()));
        
        // Старые записи удаляем время от времени
        if(mt_rand(1, 50) == 1) {
            new Executer("delete from login_attempt where created_at < now() - interval 1 day");
        }
    }
    catch(Throwable $e) {
    }
}

// После успешного входа неудачные попытки этого логина с этого адреса не нужны
function ClearLoginFailures($key) {
    try {
        new Executer("delete from login_attempt where username = ? and ip = ?", array($key, ClientIp()));
    }
    catch(Throwable $e) {
    }
}

// Валидация формы логина
$login_form_valid = true;

$login_username_valid = '';
$login_password_valid = '';

// Обработка отправки формы логина
if(null !== filter_input(INPUT_POST, 'login_submit')) {
    $login_username = filter_input(INPUT_POST, 'login_username');
    if(empty($login_username)) {
        $login_username_valid = ISINVALID;
        $login_form_valid = false;
    }
    
    $login_password = filter_input(INPUT_POST, 'login_password');
    if(empty($login_password)) {
        $login_password_valid = ISINVALID;
        $login_form_valid = false;
    }
    
    // Если с этого адреса было слишком много неудачных попыток, пароль даже не проверяем
    $login_locked = false;
    $login_key = LoginAttemptKey($login_username);
    
    if($login_form_valid) {
        $lock_seconds = LoginLockSeconds($login_key);
        
        if($lock_seconds > 0) {
            $login_locked = true;
            $error_message = LoginLockMessage($lock_seconds);
        }
    }
    
    if($login_form_valid && !$login_locked) {
        //*****************************************************
        // Сначала пробуем залогниться, как пользователь резки.
        $lower_username = strtolower($login_username);
        $lower_password = strtolower($login_password);
        
        if(in_array($lower_username, CUTTER_USERS) && $lower_username == $lower_password) {
            ClearLoginFailures($login_key);
            SetAuthCookies($lower_username, MakeCutterAuthToken($lower_username));
            header('Refresh:0');
            exit();
        }
        //******************************************************
        
        // Если это не пользователь резки, то логинимся, как обычный пользователь.
        $user_id = '';
        $username = '';
        $last_name = '';
        $first_name = '';
        $twofactor = 0;
        $auth_token = '';
        
        $sql = "select id, username, password, last_name, first_name, email, role_id from user where username = ? and active = true";
        $users_result = (new Grabber($sql, [$login_username]))->result;
        
        foreach ($users_result as $row) {
            // Актуальный хеш пароля (старые хеши при успешной проверке заменяются на новые)
            $current_hash = CheckUserPassword($row['id'], $row['password'], $login_password);
            
            if($current_hash === null) {
                continue;
            }
            
            $user_id = $row['id'];
            $username = $row['username'];
            $auth_token = MakeAuthToken($user_id, $current_hash);
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            $email = $row['email'];
            $twofactor = ROLE_TWOFACTOR[$row['role_id']];
        }
        
        if(empty($user_id) || empty($username)) {
            $error_message = "Неправильный логин или пароль";
            RecordLoginFailure($login_key);
            
            // Эта попытка могла исчерпать лимит: сразу сообщаем о блокировке
            $lock_seconds = LoginLockSeconds($login_key);
            
            if($lock_seconds > 0) {
                $error_message = LoginLockMessage($lock_seconds);
            }
        }
        else {
            ClearLoginFailures($login_key);
            //*******************************
            // Двухфакторная аутентификация
            if($twofactor == 1) {
                // Запоминаем на сервере, для кого ожидается ввод кода: только этому пользователю
                // можно будет завершить вход через форму кода безопасности.
                $_SESSION['twofactor_user_id'] = (int)$user_id;
                $code_valid = '';
                include 'twofactor_email.php';
            }
            // ****************************
            
            SetAuthCookies($username, $auth_token);
            header("Refresh:0");
        }
    }
}

// Обработка формы отправки кода безопасности
if(null !== filter_input(INPUT_POST, 'security_code_submit')) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $pending_id = $_SESSION['twofactor_user_id'] ?? null;
    
    // Код принимается только для пользователя, который перед этим успешно ввёл логин и пароль
    if($pending_id !== null && $id !== null && $id !== false && (int)$pending_id === $id) {
        $sql = "select id, username, password, last_name, first_name, email, code, role_id from user where id = ? and active = true";
        $result = (new Grabber($sql, [$id]))->result;
        
        foreach ($result as $row) {
            $user_id = $row['id'];
            $username = $row['username'];
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            $email = $row['email'];
            $code = $row['code'];
            $posted_code = trim((string)filter_input(INPUT_POST, 'code'));
            
            if(!empty($code) && $posted_code !== '' && hash_equals((string)$code, $posted_code)) {
                $error_message = (new Executer("update user set code = NULL where id = ?", [$user_id]))->error;
                
                if($error_message == '') {
                    unset($_SESSION['twofactor_user_id']);
                    SetAuthCookies($username, MakeAuthToken($user_id, $row['password']));
                    header("Refresh:0");
                }
            }
            else {
                $code_valid = ISINVALID;
                include 'twofactor_email.php';
            }
        }
    }
}

function Logout() {
    // Удаляем и актуальные куки, и куки, которые использовались раньше
    $cookie_names = array(USER_ID, USERNAME, PASSWORD5, LAST_NAME, FIRST_NAME, LOGIN_TIME, ROLE, ROLE_LOCAL);
    
    foreach ($cookie_names as $cookie_name) {
        setcookie($cookie_name, '', time() - 3600, '/');
    }
    
    unset($_SESSION['twofactor_user_id']);
    header('Location: '.APPLICATION.'/');
    exit();
}

// Обработка кнопки выхода из системы
if(null !== filter_input(INPUT_POST, 'logout_submit')) {
    Logout();
}

// Выход из системы, если удалили (или заблокировали) пользователя, сменили пароль
// или в куки лежит неверный токен
if(empty($GLOBALS['auth_cookies_just_set']) && !empty(filter_input(INPUT_COOKIE, USERNAME)) && !LoggedIn()) {
    Logout();
}
?>