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
            
            if(!empty($gdimage)) {
                echo "<table>";
                for($y = 0; $y < $imagesize[1]; $y++) {
                    echo "<tr>";
                    for($x = 0; $x < $imagesize[0]; $x++) {
                        echo "<td>";
                        echo imagecolorat($gdimage, $x, $y);
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