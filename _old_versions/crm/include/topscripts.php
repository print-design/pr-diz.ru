<?php
include 'define.php';

// Функции
function LoggedIn() {
    if(isset($_COOKIE[USERNAME]) && $_COOKIE[USERNAME] != '') {
        return true;
    }
    else {
        return false;   
    }
}

function GetManagerId() {
    return $_COOKIE[MANAGER_ID];
}

function IsInRole($role) {
    if(isset($_COOKIE[ROLES])) {
        $roles = unserialize($_COOKIE[ROLES]);
        if(in_array($role, $roles))
                return true;
    }
    
    return false;
}

// Валидация формы логина
define('LOGINISINVALID', ' is-invalid');
$login_form_valid = true;

$login_username_valid = '';
$login_password_valid = '';

// Обработка отправки формы логина
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_submit'])){
    if($_POST['login_username'] == '') {
        $login_username_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    if($_POST['login_password'] == '') {
        $login_password_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    if($login_form_valid) {
        $login_manager_id = '';
        $login_username = '';
        $login_first_name = '';
        $login_middle_name = '';
        $login_last_name = '';
        $login_roles = '';

        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        $sql = "select id, username, first_name, middle_name, last_name from manager where username='".$_POST['login_username']."' and password=password('".$_POST['login_password']."')";
        
        if($conn->connect_error) {
            die('Ошибка соединения: ' . $conn->connect_error);
        }
        
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
            $login_manager_id = $row['id'];
            setcookie(MANAGER_ID, $row['id'], 0, "/");
            
            $login_username = $row['username'];
            setcookie(USERNAME, $row['username'], 0, "/");
            
            $login_first_name = $row['first_name'];
            setcookie(FIRST_NAME, $row['first_name'], 0, "/");
            
            $login_middle_name = $row['middle_name'];
            setcookie(MIDDLE_NAME, $row['middle_name'], 0, "/");
            
            $login_last_name = $row['last_name'];
            setcookie(LAST_NAME, $row['last_name'], 0, "/");
        }
        else {
            $error_message = "Неправильный логин или пароль.";
        }
        
        if($login_manager_id != '') {
            $role_sql = "select r.name from manager_role ur inner join role r on ur.role_id = r.id where ur.manager_id = ".$login_manager_id;
            $conn->query('set names utf8');
            $role_result = $conn->query($role_sql);
            if($role_result->num_rows > 0) {
                $roles = array();
                $role_i = 0;
                while ($role_row = $role_result->fetch_assoc()) {
                    $roles[$role_i++] = $role_row['name'];
                }
                
                setcookie(ROLES, serialize($roles), 0, '/');
            }
        }
        
        $conn->close();
        
        if($login_username != '') {
            header("Refresh:0");
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout_submit'])) {
    setcookie(MANAGER_ID, '', 0, "/");
    setcookie(USERNAME, '', 0, "/");
    setcookie(FIRST_NAME, '', 0, "/");
    setcookie(MIDDLE_NAME, '', 0, "/");
    setcookie(LAST_NAME, '', 0, "/");
    setcookie(ROLES, '', 0, "/");
    header("Refresh:0");
    header('Location: '.APPLICATION.'/');
}
?>