<?php
class Printing {
    private $fieldsArr;
    
    // .....................................................................
    function __construct() {
        $this->fieldsArr = array();
    }
    
    //.....................................................................
    function Load(array $array) {
        foreach (array_keys($array) as $key) {
            $this->fieldsArr[$key] = $array[$key];
        }
    }
    
    //.....................................................................
    function Append(string $key, string $value) {
        $this->fieldsArr[$key] = $value;
    }
    
    //.....................................................................
    function Keys() {
        return array_keys($this->fieldsArr);
    }
    
    //.....................................................................
    function Show() {
?>
<tr>
    <?php foreach (array_keys($this->fieldsArr) as $key): ?>
    <td style="padding-right: 20px;"><?= $this->fieldsArr[$key] ?></td>
    <?php endforeach; ?>
</tr>
<?php
    }
}
?>