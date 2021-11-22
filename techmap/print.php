<?php
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Технологическая карта</title>
    </head>
    <body>
        <h1>Технологическая карта</h1>
    </body>
    <script>
        var css = '@page { size: landscape; margin: 8mm; }',
                head = document.head || document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            
        style.type = 'text/css';
        style.media = 'print';
            
        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
            
        head.appendChild(style);
        
        window.print();
    </script>
</html>