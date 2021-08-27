<?php
include_once 'database.php';
include_once 'printing.php';

class Test_Grafik {
    private ArrayObject $Printings;

    // .............................................................
    public function __construct() {
        $this->Printings = new ArrayObject();
        
        $sql = "select name, organization, length, width, square from printing_table";
        $db = new Database($sql);
        
        while ($row = $db->Fetch()) {
            $printing = new Printing();
            $printing->Load($row);
            $this->Printings->append($printing);
        }
    }
    
    // .............................................................
    public function Show() {
?>
<table>
    <tr>
        <th>Работники</th>
        <th>Тираж</th>
        <th>Заказчик</th>
        <th>Длина</th>
        <th>Ширина</th>
        <th>Площадь</th>
    </tr>
    <?php
    foreach($this->Printings as $printing) {
        $printing->Show();
    }
    ?>
</table>
<?php
    }
    // .............................................................
    public function ShowRange($begin, $end) {
        if($begin < $end && $this->Printings->offsetExists($begin)) {
?>
<table>
    <tr>
        <th>Работники</th>
        <th>Тираж</th>
        <th>Заказчик</th>
        <th>Длина</th>
        <th>Ширина</th>
        <th>Площадь</th>
    </tr>
    <?php
    for ($i=$begin; $i<=$end; $i++) {
        if($this->Printings->offsetExists($i)) {
            $this->Printings->offsetGet($i)->Show();
        }
    }
    ?>
</table>
<?php
        }
    }
}
?>