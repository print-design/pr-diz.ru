<?php
include_once 'database.php';
include_once 'user.php';

class Printing {
    private $fields;
    private ArrayObject $Users;

    // .....................................................................
    function __construct() {
        $this->fields = array();
        
        $this->Users = new ArrayObject();
        
        $sql = "select date, username, password, fio, quit from user";
        $db = new Database($sql);
        
        while ($row = $db->Fetch()) {
            $user = new User();
            $user->Load($row);
            $this->Users->append($user);
        }
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
    <td style="padding-right: 20px;">
        <select>
        <?php
        foreach ($this->Users as $user) {
            $user->ShowSelectOption();
        }
        ?>
        </select>
    </td>
    <td style="padding-right: 20px;"><?= $this->fields['name'] ?></td>
    <td style="padding-right: 20px;"><?= $this->fields['organization'] ?></td>
    <td style="padding-right: 20px;"><?= $this->fields['length'] ?></td>
    <td style="padding-right: 20px;"><?= $this->fields['width'] ?></td>
    <td style="padding-right: 20px;"><?= $this->fields['square'] ?></td>
</tr>
<?php
    }
}
?>