<?php
class MyImage {
    public function __construct($_sourceFile) {
        $this->sourcefile = $_sourceFile;
        $this->filename = basename($this->sourcefile);
        
        $this->image_size = getimagesize($this->sourcefile);
        $this->width = $this->image_size[0];
        $this->height = $this->image_size[1];
        $this->extension = image_type_to_extension($this->image_size[2]);
    }
    
    private $sourcefile;
    private $image_size;
    public $errorMessage;
    public $filename;
    public $width;
    public $height;
    public $extension;
    
    public function Romanize($param) {
        $local_to_roman = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y',
            'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'e', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'y',
            'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f',
            'Х' => 'kh', 'Ц' => 'ts', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'shch', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
            'a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd', 'e' => 'e', 'f' => 'f', 'g' => 'g', 'h' => 'h', 'i' => 'i', 'j' => 'j', 'k' => 'k', 'l' => 'l', 'm' => 'm',
            'n' => 'n', 'o' => 'o', 'p' => 'p', 'q' => 'q', 'r' => 'r', 's' => 's', 't' => 't', 'u' => 'u', 'v' => 'v', 'w' => 'w', 'x' => 'x', 'y' => 'y', 'z' => 'z',
            'A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd', 'E' => 'e', 'F' => 'f', 'G' => 'g', 'H' => 'h', 'I' => 'i', 'J' => 'j', 'K' => 'k', 'L' => 'l', 'M' => 'm',
            'N' => 'n', 'O' => 'o', 'P' => 'p', 'Q' => 'q', 'R' => 'r', 'S' => 's', 'T' => 't', 'U' => 'u', 'V' => 'v', 'W' => 'w', 'X' => 'x', 'Y' => 'y', 'Z' => 'z',
            '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', ' ' => '_', '-' => '_', '.' => '.');
        
        $strlen = mb_strlen($param);
        $result = '';
    
        for($i=0; $i<$strlen; $i++) {
            $key = mb_substr($param, $i, 1);
            if(array_key_exists($key, $local_to_roman)) {
                $result .= $local_to_roman[$key];
            }
        }
        
        return $result;
    }

    public function ResizeAndSave($targetFolder, $max_width, $max_height) {
        $result = false;
        
        $romanized_name = $this->Romanize($_FILES['file']['name']);
        $this->filename = $romanized_name;
        
        while (file_exists($targetFolder.$this->filename)) {
            $this->filename = time().'_'.$romanized_name;
        }
        
        $dest_height = 0;
        $dest_width = 0;
        
        if((empty($max_height) || $max_height >= $this->height) && ($max_width == 0 || $max_width >= $this->width)) {
            $result = move_uploaded_file($this->sourcefile, $targetFolder.$this->filename);
        }
        else {
            if(empty($max_height) && !empty($max_width)) {
                $dest_width = $max_width;
                $dest_height = $this->height * $max_width / $this->width;
            }
                        
            if(!empty($max_height) && empty($max_width)) {
                $dest_height = $max_height;
                $dest_width = $this->width * $max_height / $this->height;
            }
                        
            if(!empty($max_height) && !empty($max_width)) {
                $dest_width = $max_width;
                $dest_height = $this->height * $max_width / $this->width;
                            
                if($dest_height > $max_height) {
                    $dest_height = $max_height;
                    $dest_width = $this->width * $max_height / $this->height;
                }
            }
                        
            $src_image = null;
            $dest_image = imagecreatetruecolor($dest_width, $dest_height);
                        
            switch ($this->image_size[2]) {
            case IMG_BMP:
                $src_image = imagecreatefrombmp($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagebmp($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_GIF:
                $src_image = imagecreatefromgif($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagegif($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_JPG:
                $src_image = imagecreatefromjpeg($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagejpeg($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_JPEG:
                $src_image = imagecreatefromjpeg($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagejpeg($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_PNG:
                $src_image = imagecreatefrompng($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagepng($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_WBMP:
                $src_image = imagecreatefromwbmp($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagewbmp($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_WEBP:
                $src_image = imagecreatefromwebp($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagewebp($dest_image, $targetFolder. $this->filename);
                break;
                        
            case IMG_XPM:
                $src_image = imagecreatefromxpm($this->sourcefile);
                imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $this->width, $this->height);
                $result = imagexbm($dest_image, $targetFolder. $this->filename);
                break;
            }
            
            $this->image_size = getimagesize($targetFolder. $this->filename);
            $this->width = $this->image_size[0];
            $this->height = $this->image_size[1];
        }
        
        return $result;
    }
}
?>