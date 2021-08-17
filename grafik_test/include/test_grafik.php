<?php
include_once 'database.php';
include_once 'printing.php';

class Test_Grafik {
    private ArrayObject $PrintingsArr;

    // .............................................................
    public function __construct() {
        $this->PrintingsArr = new ArrayObject();
        
        $sql = "select * from printing_table";
        $db = new Database($sql);
        
        while ($row = $db->Fetch()) {
            $printing = new Printing();
            $printing->Load($row);
            $this->PrintingsArr->append($printing);
        }
    }
    
    // .............................................................
    public function Show() {
?>
<table>
    <?php if($this->PrintingsArr->count() > 0): ?>
    <tr>
        <?php foreach($this->PrintingsArr->offsetGet(0)->Keys() as $key): ?>
        <th><?=$key ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    endif;
    foreach($this->PrintingsArr as $printing) {
        $printing->Show();
    }
    ?>
</table>
<?php
    }   
    // .............................................................
    public function ShowRange($begin, $end) {
        if($begin < $end && $this->PrintingsArr->offsetExists($begin)) {
?>
<table>
    <?php if($this->PrintingsArr->count() > 0): ?>
    <tr>
        <?php foreach($this->PrintingsArr->offsetGet(0)->Keys() as $key): ?>
        <th><?=$key ?></th>
        <?php endforeach; ?>
    </tr>
    <?php
    endif;
    for ($i=$begin; $i<=$end; $i++) {
        if($this->PrintingsArr->offsetExists($i)) {
            $this->PrintingsArr->offsetGet($i)->Show();
        }
    }
    ?>
</table>
<?php
        }
    }
}
?>