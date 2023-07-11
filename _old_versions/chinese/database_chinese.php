<?php
define('DATABASE_HOST_CHINESE', 'localhost');
define('DATABASE_USER_CHINESE', 'root');
define('DATABASE_PASSWORD_CHINESE', '');
define('DATABASE_NAME_CHINESE', 'CHINESE');

class ExecuterChinese {
    public $error = '';
    public $insert_id = 0;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST_CHINESE, DATABASE_USER_CHINESE, DATABASE_PASSWORD_CHINESE, DATABASE_NAME_CHINESE);

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

class GrabberChinese {
    public  $error = '';
    public $result = array();
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST_CHINESE, DATABASE_USER_CHINESE, DATABASE_PASSWORD_CHINESE, DATABASE_NAME_CHINESE);
        
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

class FetcherChinese {
    public $error = '';
    private $result;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST_CHINESE, DATABASE_USER_CHINESE, DATABASE_PASSWORD_CHINESE, DATABASE_NAME_CHINESE);
        
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
?>