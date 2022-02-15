<?php
class GrafikEditionReadonly {
    public function __construct(DateTime $date, $shift, GrafikReadonly $grafik, $edition_key, $edition, $date_editions_count) {
        $this->date = $date;
        $this->shift = $shift;
        $this->grafik = $grafik;
        $this->edition_key = $edition_key;
        $this->edition = $edition;
        $this->date_editions_count = $date_editions_count;
    }
    
    private DateTime $date;
    private $shift;
    private GrafikReadonly $grafik;
    private $edition_key;
    private $edition;
    private $date_editions_count;
            
    function Show() {
        $top = 'nottop';
        if($this->shift == 'day') {
            $top = 'top';
        }
        
        include 'grafik_edition_readonly.php';
    }
}
?>