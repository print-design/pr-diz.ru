<?php
require_once '../include/constants.php';

// Готовые ролики
class CalculationRolls {
    public $volume = 0;
    
    public function __construct($id) {
        $sql = "select sum((ifnull(cts.radius, 0) * 2 + tm.spool) * cs.width / 1000000) as volume "
                . "from calculation c "
                . "inner join techmap tm on tm.calculation_id = c.id "
                . "inner join calculation_stream cs on cs.calculation_id = c.id "
                . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
                . "where c.id = ?";
        $fetcher = new Fetcher($sql, [$id]);
        if($row = $fetcher->Fetch()) {
            $this->volume = $row[0];
        }
    }

    public static function Create($id) {
        return new CalculationRolls($id);
    }
}
?>