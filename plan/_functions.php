<?php
function GetFilmsString($_lamination, $_film_name, $_thickness, $_individual_film_name, $_individual_thickness, $_width_1, 
        $_lamination1_film_name, $_lamination1_thickness, $_lamination1_individual_film_name, $_lamination1_individual_thickness, $_width_2, 
        $_lamination2_film_name, $_lamination2_thickness, $_lamination2_individual_film_name, $_lamination2_individual_thickness, $_width_3) {
    $films_strings = array();
    
    if($_lamination == 1) {
        $film_name = $_film_name;
        $thickness = $_thickness;
        
        if(empty($film_name)) {
            $film_name = $_individual_film_name;
            $thickness = $_individual_thickness;
        }
        
        $lamination1_film_name = $_lamination1_film_name;
        $lamination1_thickness = $_lamination1_thickness;
        
        if(empty($lamination1_film_name)) {
            $lamination1_film_name = $_lamination1_individual_film_name;
            $lamination1_thickness = $_lamination1_individual_thickness;
        }
        
        $films_strings = array($film_name.' '.$thickness.' - '.intval($_width_1).' мм', '+', $lamination1_film_name.' '.$lamination1_thickness.' - '. intval($_width_2).' мм');
    }
    elseif($_lamination == 2) {
        $lamination2_film_name = $_lamination2_film_name;
        $lamination2_thickness = $_lamination2_thickness;
        
        if(empty($lamination2_film_name)) {
            $lamination2_film_name = $_lamination2_individual_film_name;
            $lamination2_thickness = $_lamination2_individual_thickness;
        }
        
        $films_strings = array('1 прогон', '+', $lamination2_film_name.' '.$lamination2_thickness.' - '. intval($_width_3).' мм');
    }
    
    return $films_strings;
}
?>