<?php
include_once 'define.php';

class Database {
    // Сообщение об ошибке
    public $error = '';
    
    // Указатель на первую строку полученных данных
    private $result;
    
    // Конструктор
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
    
    // Получение следующей строки данных
    function Fetch() {
        return mysqli_fetch_array($this->result, MYSQLI_ASSOC);
    }
    
    // Получение всех данных
    function FetchAll() {
        return mysqli_fetch_all($this->result, MYSQLI_ASSOC);
    }
}
?>