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
            
            $subir = new SubirMultiple("ficheros"); //creamos un objeto de la clase SubirMultiple
            
            $subir->addExtension("txt"); //Añadimos varias extensiones permitidas
            $subir->addExtension("jpg");
            $subir->addExtension("png");
            $subir->addTipo("text/plain"); //Añadimos los tipos MIME de las extensiones anteriores
            $subir->addTipo("image/jpeg");
            $subir->addTipo("image/png");
            $subir->setAcccion(1); //renombramos los archivos en caso de existir
            $subir->setAccionExcede(1); //subimos los archivos que si cumplan las condiciones
            $subir->setCrearCarpeta(true); //creamos el directorio de destino si no existe
            $subir->setDestino("carpetita"); //seleccionamos el destino
            $subir->subir(); //subimos los archivos
            $subir->setTamanio(1024*1024*5);
            $subir->setCantidadMaxima(4);
            $subir->subir();
            echo $subir->getErrores(); //imprime los errores.
            
        ?>
    </body>
</html>
