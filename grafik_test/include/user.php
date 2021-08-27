<?php
class User {
    private $fields;

    // .....................................................................
    function __construct() {
        $this->fields = array();
    }
    
    //.....................................................................
    function Load(array $array) {
        $this->fields = $array;
    }
    
    //.....................................................................
    function Append(string $value) {
        array_push($this->fields, $value);
    }
        
    //.....................................................................
    function ShowSelectOption() {
?>
<option><?= $this->fields['fio'] ?></option>
<?php
    }
}
?>
