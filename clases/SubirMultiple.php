<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubirMultiple
 * @version 0.1
 * @author Javier Gallego
 * @license http://URL sin licencia
 * @copyright (c) 2014, Javier Gallego
 * 
 * Permite la subida de uno o varios ficheros simultaneamente.
 */
class SubirMultiple {
    
    private $inputname, $tamMax, $tamMaxTotal, $extensiones, $tipos, $accion, $destino, $crearCarpeta;
    private $error, $errorPHP;
    
    const NO_ERROR = 0;
    const OMITIR = 0, RENOMBRAR = 1, REEMPLAZAR = 2;
    
    function __construct($nombreinput) {
        $this->inputname = $nombreinput;
        $this->tamMax = 1024*1024*2;
        $this->tamMaxTotal = $this->tamMax*10;
        $this->extensiones = Array();
        $this->tipos = Array();
        $this->accion = SubirMultiple::OMITIR;
        $this->destino = "./";
        $this->crearCarpeta = false;
        $this->error = SubirMultiple::NO_ERROR;
        $this->errorPHP = UPLOAD_ERR_OK;
    }
    private function isExtension($extension){
        if (sizeof($this->extensiones) > 0 && !in_array($extension, $this->extensiones)) {
            //ERROR
            return false;
        }
        return true;
    }
    public function addExtension($ext){
        if(is_array($ext)){
            $this->extensiones = array_merge($this->extensiones, $ext);
        }else{
            $this->extensiones[] = $ext;
        }
    }
    public function setExtension($ext){
        if(is_array($var)){
            $this->extensiones = $ext;
        }else{
            unset($this->extensiones);
            $this->extensiones[] = $ext;
        }
    }
    public function addTipo($tipo) {
        if (is_array($tipo)) {
            $this->tipos = array_merge($this->tipos, $tipo);
        } else {
            $this->tipos[] = $tipo;
        }
    }
    /************************************** metodo isTipo ************************************/
    public function setAcccion($accion){
        if($accion==0){
            $this->accion = SubirMultiple::OMITIR;
        }elseif($accion==1){
            $this->accion = SubirMultiple::RENOMBRAR;
        }elseif($accion==2){
            $this->accion = SubirMultiple::REEMPLAZAR;
        }else{
            return false;
        }
        return true;
    }
    private function crearCarpeta(){ 
        if(mkdir($this->destino , Configuracion::PERMISOS, true)){
            return true;
        }else{
            return false;
        }
    }
    public function setDestino($var){
        $caracter = substr($var, -1);
        if ($caracter != "/")
            $var.="/";
        $this->destino = $var;
    }    
    public function setTamanio($tam){
        $this->tamMax = $tam;
    }
    public function setTamanioTotal($tam){
        $this->tamMaxTotal = $tam;
    }
    private function isTamanio($size){
        if($this->tamMax >= $size){
            return true;
        }else{
            return false;
        }
    }
    public function subir(){
        $archivos = $_FILES[$this->inputname];
        $i=-1;
        foreach($archivos['name'] as $archivo){
            $i++;
            $partes = pathinfo($archivos["name"][$i]);
            $extension = $partes['extension'];
            echo $extension;
            $nombre = $partes['filename'];
            $origen=$archivos['tmp_name'][$i];
            if(!$this->isExtension($extension)){
                continue;
            }
            echo "pasé por aqui en".$i;
            if($this->accion == SubirMultiple::REEMPLAZAR){
                move_uploaded_file($origen, $this->destino.$nombre.".".$extension);
            }elseif($this->accion == SubirMultiple::RENOMBRAR){
                $x=1;
                $destino = $this->destino . $nombre . $extension;
                while (file_exists($destino)) {
                    $destino = $this->destino . $nombre . "($x)." . $extension;
                    $x++;
                }
                move_uploaded_file($origen, $destino);
            }elseif($this->accion == SubirMultiple::OMITIR){
                $destino = $this->destino.$nombre.".".$extension;
                if (file_exists($destino)) {
                continue;
                }
                move_uploaded_file($origen, $destino);
            }
            
        }
    }
}
