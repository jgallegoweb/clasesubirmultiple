<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require_once 'clases/SubirMultiple.php';
            
            $subir = new SubirMultiple("ficheros");
            $subir->addExtension("txt");
            $subir->addExtension("jpg"); //no funcionan los archivos .jpg
            $subir->addExtension("png");
            $subir->setAcccion(1);
            $subir->setAccionExcede(1);
            $subir->setCrearCarpeta(true);
            $subir->setDestino("carpetita");
            $subir->setCantidadMaxima(2);
            $subir->subir();
            echo "<br /> error_".$subir->getError();
            /*$archivos = $_FILES['ficheros'];
            $tope=  count($archivos['name']);
            echo $tope;
            $i=0;
            foreach($archivos['name'] as $clave => $archivo){
                
                if(move_uploaded_file($archivos['tmp_name'][$i], "./".$archivos['name'][$i])){
                    echo "true";
                }else{
                    echo "false";
                }
                echo "subido ".$archivos['name'][$i]."<br/>";
                $i++;
            }*/
        ?>
    </body>
</html>
