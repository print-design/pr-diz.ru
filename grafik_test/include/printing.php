<?php
class Printing {
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
    function Show() {
?>
<tr>
    <?php foreach ($this->fields as $field): ?>
    <td style="padding-right: 20px;"><?= $field ?></td>
    <?php endforeach; ?>
</tr>
<?php
    }
}
?>