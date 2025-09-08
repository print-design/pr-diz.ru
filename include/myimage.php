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


    public function ResizeAndSave($targetFolder, $name, $max_width, $max_height) {
        $result = false;
        
        $this->filename = $name.$this->extension;
        
        while (file_exists($targetFolder.$this->filename)) {
            if(!unlink($targetFolder.$this->filename)) {
                return false;
            }
        }
        
        $dest_height = 0;
        $dest_width = 0;
        
        if((empty($max_height) || $max_height >= $this->height) && (empty($max_width) || $max_width >= $this->width)) {
            $dest_width = $this->width;
            $dest_height = $this->height;
        }
        
        if(empty($max_height) && !empty($max_width)) {
            $dest_width = $max_width;
            $dest_height = round($this->height * $max_width / $this->width);
        }
                        
        if(!empty($max_height) && empty($max_width)) {
            $dest_height = $max_height;
            $dest_width = round($this->width * $max_height / $this->height);
        }
                        
        if(!empty($max_height) && !empty($max_width)) {
            $dest_width = $max_width;
            $dest_height = round($this->height * $max_width / $this->width);
            
            if($dest_height > $max_height) {
                $dest_height = $max_height;
                $dest_width = round($this->width * $max_height / $this->height);
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
        
        return $result;
    }
}
?>