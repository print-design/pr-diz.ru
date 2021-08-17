<?php
include_once 'database.php';
include_once 'printing.php';

class Test_Grafik {
    private $PrintingColumnNames = null;
    private ArrayObject $Printings;

    // .............................................................
    public function __construct() {
        $this->PrintingRows = array();
        $this->Printings = new ArrayObject();
        
        $sql = "select * from printing_table";
        $db = new Database($sql);
        $firstRow = true;
        
        while ($row = $db->Fetch()) {
            if($this->PrintingColumnNames == null) {
                $this->PrintingColumnNames = array_keys($row);
            }
            
            $printing = new Printing();
            $printing->Load(array_values($row));
            $this->Printings->append($printing);
        }
    }
    
    // .............................................................
    public function Show() {
?>
<table>
    <?php if($this->Printings->count() > 0): ?>
    <tr>
        <?php foreach($this->PrintingColumnNames as $columnName): ?>
        <th><?=$columnName ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    endif;
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
    <?php if($this->Printings->count() > 0): ?>
    <tr>
        <?php foreach($this->PrintingColumnNames as $columnName): ?>
        <th><?=$columnName ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    endif;
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