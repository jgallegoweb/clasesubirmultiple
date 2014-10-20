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
        <form action="subirmas.php" method="POST" enctype="multipart/form-data">
            <label>Seleccione los archivos a subir: </label><input type="file" name="ficheros[]" value="" multiple="" /> <input type="submit" value="Subir" />
        </form>
    </body>
</html>
