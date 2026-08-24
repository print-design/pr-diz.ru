<?php
require_once '../include/constants.php';

// Готовые ролики
class CalculationRolls {
    // "Примерный" объём для отображения -- среднее между минимумом и максимумом
    public $volume = 0;
    
    // Минимум -- если ролики уложены плотно, "в шахматном порядке" (каждый следующий ряд вложен в промежутки предыдущего)
    public $volume_min = 0;
    
    // Максимум -- если ролики уложены просто рядами, каждый в своей отдельной "ячейке" (безопасная верхняя граница)
    public $volume_max = 0;
    
    public function __construct($id) {
        $sql = "select sum(power(ifnull(cts.radius, 0) * 2 + ifnull(tm.spool, 0), 2) * ifnull(cs.width, 0) / 1000000000) as volume "
                . "from calculation c "
                . "inner join techmap tm on tm.calculation_id = c.id "
                . "inner join calculation_stream cs on cs.calculation_id = c.id "
                . "inner join calculation_take_stream cts on cts.calculation_stream_id = cs.id "
                . "where c.id = ?";
        $fetcher = new Fetcher($sql, [$id]);
        if($row = $fetcher->Fetch()) {
            $this->volume_max = $row[0] ?? 0;
        }
        
        // Плотная ("шахматная") укладка теоретически компактнее укладки рядами в pi / (2 * sqrt(3)) раз (~1,1547),
        // то есть требует примерно в sqrt(3) / 2 (~0,866) раз меньше места при том же количестве роликов.
        // Точная экономия зависит от конкретной раскладки (сколько рядов, в какую сторону идёт вложение),
        // поэтому здесь используется теоретический предел как ориентировочная нижняя граница.
        $this->volume_min = $this->volume_max * (sqrt(3) / 2);
        
        $this->volume = ($this->volume_min + $this->volume_max) / 2;
    }

    public static function Create($id) {
        return new CalculationRolls($id);
    }
}
?>