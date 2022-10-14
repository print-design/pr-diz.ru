<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

if(empty($id)) {
    header('Location: ./');
}

const IMAGE_PNG = 'image/png';
const IMAGE_JPEG = 'image/jpeg';
const IMAGE_GIF = 'image/gif';
const IMAGE_BMP = 'image/bmp';

$sourcefile = null;

$sql = "select name from myimages where id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $sourcefile = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/temp/images/".$row['name'];
}
else {
    $error_message = "Ошибка при получении имени файла";
}

function hex2rgb($hex) {
  $color = str_replace('#','',$hex);
  $rgb = array('r' => hexdec(substr($color,0,2)),
               'g' => hexdec(substr($color,2,2)),
               'b' => hexdec(substr($color,4,2)));
  return $rgb;
}

function rgb2cmyk($var1,$g=0,$b=0) {
   if(is_array($var1)) {
      $r = $var1['r'];
      $g = $var1['g'];
      $b = $var1['b'];
   }
   else $r=$var1;
   $cyan    = 255 - $r;
   $magenta = 255 - $g;
   $yellow  = 255 - $b;
   $black   = min($cyan, $magenta, $yellow);
   $cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
   $magenta = @(($magenta - $black) / (255 - $black)) * 255;
   $yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
   return array('c' => $cyan / 255,
                'm' => $magenta / 255,
                'y' => $yellow / 255,
                'k' => $black / 255);
}
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a href="./" class="btn btn-outline-dark">К списку</a>
            <h1>Картинка</h1>
            <?php
            
            if(empty($error_message)):
            ?>
            <img src="../temp/images/<?=$row['name'] ?>" title="<?=$row['name'] ?>" class="img-fluid" />
            <br /><br />
            <?php
            $imagesize = getimagesize($sourcefile);
            print_r($imagesize);
            echo "<br /><br />";
            
            $gdimage = null;
            switch ($imagesize['mime']) {
            case IMAGE_PNG:
                $gdimage = imagecreatefrompng($sourcefile);
                break;
            
            case IMAGE_JPEG:
                $gdimage = imagecreatefromjpeg($sourcefile);
                break;
                        
            case IMAGE_GIF:
                $gdimage = imagecreatefromgif($sourcefile);
                break;
                        
            case IMAGE_BMP:
                $gdimage = imagecreatefrombmp($sourcefile);
                break;
            }
            
            $ccount = 0;
            $cvalue = 0.0;
            $mcount = 0;
            $mvalue = 0.0;
            $ycount = 0;
            $yvalue = 0.0;
            $kcount = 0;
            $kvalue = 0.0;
            
            if(!empty($gdimage)) {
                echo "<table>";
                for($y = 0; $y < $imagesize[1]; $y++) {
                    echo "<tr>";
                    for($x = 0; $x < $imagesize[0]; $x++) {
                        echo "<td class='text-nowrap pr-3'>";
                        $dec = imagecolorat($gdimage, $x, $y);
                        $hex = dechex($dec);
                        $rgb = hex2rgb($hex);
                        $cmyk = rgb2cmyk($rgb);
                        echo "C ". number_format($cmyk['c'] * 100, 2, ",", "")."% - M ". number_format($cmyk['m'] * 100, 2, ",", "")."% - Y ". number_format($cmyk['y'] * 100, 2, ",", "")."% - K ". number_format($cmyk['k'] * 100, 2, ",", "")."%";
                        echo "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            endif;
            ?>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_find.php';
        ?>
    </body>
</html>