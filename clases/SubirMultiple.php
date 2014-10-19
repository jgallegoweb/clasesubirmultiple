<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubirMultiple
 * @version 0.4
 * @author Javier Gallego
 * @license http://URL sin licencia
 * @copyright (c) 2014, Javier Gallego
 * 
 * Permite la subida de uno o varios ficheros simultaneamente.
 */
class SubirMultiple {
    private $inputname, $tamMax, $tamMaxTotal, $extensiones, $tipos, $accion, $destino, $crearCarpeta;
    private $cantidadMax, $accionExcede;
    private $error, $errorPHP;
    
    const NO_ERROR = 0, ERROR_TAM_TOTAL = 1, ERROR_NUM_MAX = 2, ERROR_EXT = 3, 
            ERROR_TIPO = 4, ERROR_TAM = 5, ERROR_SUBIDA = 6, ERROR_CREAR_FALSE = 7, 
            ERROR_SIN_CARPETA = 8;
    const OMITIR = 0, RENOMBRAR = 1, REEMPLAZAR = 2;
    const OMITIR_TODO = 0, SUBIR_PARTE = 1;
    
    function __construct($nombreinput) {
        $this->inputname = $nombreinput;
        $this->tamMax = 512*1024;
        $this->tamMaxTotal = $this->tamMax*10;
        $this->extensiones = Array();
        $this->tipos = Array();
        $this->accion = SubirMultiple::OMITIR;
        $this->destino = "./";
        $this->crearCarpeta = false;
        $this->error = SubirMultiple::NO_ERROR;
        $this->errorPHP = UPLOAD_ERR_OK;
        $this->accionExcede = SubirMultiple::OMITIR_TODO;
        $this->cantidadMax = 10;
    }
    private function isExtension($extension){
        if (sizeof($this->extensiones) > 0 && !in_array($extension, $this->extensiones)) {
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
        if(is_array($ext)){ //alta probabilidad de error
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
    public function setTipo($tipo){
        if(is_array($tipo)){
            $this->tipos = $tipo;
        }else{
            unset($this->tipos);
            $this->extensiones[] = $tipo;
        }
    }
    private function isTipo($tipo){
        if (sizeof($this->tipos) > 0 && !in_array($tipo, $this->tipos)) {
            return false;
        }
        return true;
    }
    public function setAcccion($accion){
        if($accion==SubirMultiple::OMITIR){//usar las variables directamente?
            $this->accion = SubirMultiple::OMITIR;
        }elseif($accion==SubirMultiple::RENOMBRAR){
            $this->accion = SubirMultiple::RENOMBRAR;
        }elseif($accion==SubirMultiple::REEMPLAZAR){
            $this->accion = SubirMultiple::REEMPLAZAR;
        }else{
            return false;
        }
        return true;
    }
    private function crearCarpeta(){ 
        echo "llama al metodo";
        if($this->crearCarpeta){
            if(mkdir($this->destino , 0774, true)){ //pork no funcionan la clase configuracion??
                echo "creada";
                return true;
            }else{
                echo "no creada";
                return false;
            }
        }
        $this->error = SubirMultiple::ERROR_CREAR_FALSE;
        return false;
    }
    public function setCrearCarpeta($var){
        $this->crearCarpeta = $var;
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
    public function setCantidadMaxima($cantidad){
        $this->cantidadMax = $cantidad;
    }
    private function getCantidadMaxima(){ //metodo publico??
        return $this->cantidadMax;
    }
    private function isCantidad(){
        if($this->cantidadMax != NULL && $this->cantidadMax < $this->getNumeroArchivos()){
            return false;
        }
        return true;
    }
    public function setAccionExcede($accion){
        $this->accionExcede = $accion;
        return true;
    }
    private function getTamanioTotal(){
        $total = 0;
        $archivos = $_FILES[$this->inputname];
        $i=0;
        foreach($archivos['name'] as $archivo){
            $total += $archivos['size'][$i];
            $i++;
        }
        return $total;
    }
    private function getNumeroArchivos(){
        $archivos = $_FILES[$this->inputname];
        return sizeof($archivos['name']);
    }
    private function isTamanioTotal(){
        if($this->tamMaxTotal >= $this->getTamanioTotal()){
            return true;
        }
        return false;
    }
    public function getError(){
        return $this->error;
    }
    /*
     * set nombre comun a la subida???
     * crear un array con todos los errores de cada archivo?
     * 
     * 
     */
    public function subir(){
        $archivos = $_FILES[$this->inputname];
        if($this->accionExcede == SubirMultiple::OMITIR_TODO){
            if(!$this->isCantidad()){
                $this->error = SubirMultiple::ERROR_NUM_MAX;
                return false;
            }
            if(!$this->isTamanioTotal()){
                $this->error = SubirMultiple::ERROR_TAM_TOTAL;
                return false;
            }
            //comprobar todas la extensiones y tipos?
        }
        if(!file_exists($this->destino)){
            echo "no existe";
                if(!$this->crearCarpeta()){
                    echo "pero no la crea";
                    $this->error = SubirMultiple::ERROR_SIN_CARPETA;
                    return false;
                }
            }
        $i=-1;
        $totalsubida=0;
        foreach($archivos['name'] as $archivo){
            $i++;
            $totalsiguiente = $totalsubida + $archivos['size'][$i];
            echo $totalsiguiente;
            if($i>=$this->getCantidadMaxima()){
                echo "estos son muchos";
                return false;
            }
            if($totalsiguiente >= $this->tamMaxTotal){
                echo "este no cabe";
                continue;
            }
            $partes = pathinfo($archivos["name"][$i]);
            $extension = $partes['extension'];
            $nombre = $partes['filename'];
            $origen=$archivos['tmp_name'][$i];
            $lugardestino="";
            if(!$this->isExtension($extension)){
                $this->error = SubirMultiple::ERROR_EXT;
                continue;
            }
            if(!$this->isTipo($archivos['type'][$i])){
                $this->error = SubirMultiple::ERROR_TIPO;
                continue;
            }
            if(!$this->isTamanio($archivos['size'][$i])){
                $this->error = SubirMultiple::ERROR_TAM;
                continue;
            }
            if($this->accion == SubirMultiple::REEMPLAZAR){
                $lugardestino = $this->destino.$nombre.".".$extension;
            }elseif($this->accion == SubirMultiple::RENOMBRAR){
                $x=1;
                $lugardestino = $this->destino . $nombre .".". $extension;
                while (file_exists($lugardestino)) {
                    $lugardestino = $this->destino . $nombre . "($x)." . $extension;
                    $x++;
                }
            }elseif($this->accion == SubirMultiple::OMITIR){
                $lugardestino = $this->destino.$nombre.".".$extension;
                if (file_exists($lugardestino)) {
                    continue;
                }
            }
            if(!move_uploaded_file($origen, $lugardestino)){
                $this->error = SubirMultiple::ERROR_SUBIDA;
            }
            $totalsubida += $archivos['size'][$i];
        }
    }
}
