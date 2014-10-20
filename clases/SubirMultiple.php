<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubirMultiple
 * @version 0.6
 * @author Javier Gallego
 * @license http://URL sin licencia
 * @copyright (c) 2014, Javier Gallego
 * 
 * Permite la subida de uno o varios ficheros simultaneamente.
 */
class SubirMultiple {
    private $inputname, $tamMax, $tamMaxTotal, $extensiones, $tipos, $accion, $destino, $crearCarpeta, $nuevoNombre;
    private $cantidadMax, $accionExcede;
    private $error, $errorPHP, $listaErrores;
    
    const NO_ERROR = 0, ERROR_TAM_TOTAL = 1, ERROR_NUM_MAX = 2, ERROR_EXT = 3, 
            ERROR_TIPO = 4, ERROR_TAM = 5, ERROR_SUBIDA = 6, ERROR_CREAR_FALSE = 7, 
            ERROR_SIN_CARPETA = 8, ERROR_OMISION = 9;
    const OMITIR = 0, RENOMBRAR = 1, REEMPLAZAR = 2;
    const OMITIR_TODO = 0, SUBIR_PARTE = 1;
    
    function __construct($nombreinput) {
        $this->inputname = $nombreinput;
        $this->tamMax = 1024*1024*2; //2 MB por archivo por defecto
        $this->tamMaxTotal = $this->tamMax*10; //20MB de subida total
        $this->extensiones = Array();
        $this->tipos = Array();
        $this->accion = SubirMultiple::OMITIR; //omitimos las subidas repetidas
        $this->destino = "./"; //carpeta actual por defecto
        $this->crearCarpeta = false; //no podemos crear carpetas
        $this->error = SubirMultiple::NO_ERROR;
        $this->errorPHP = UPLOAD_ERR_OK;
        $this->accionExcede = SubirMultiple::OMITIR_TODO; //si algún archivo sobrepasa tamaño o numero no se sube ninguno
        $this->cantidadMax = 10; //numero archivos permitidos 10
        $this->listaErrores = Array();
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
        if(is_array($ext)){
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
        if($this->crearCarpeta){
            if(mkdir($this->destino , 0774, true)){
                return true;
            }else{
                return false;
            }
        }
        $this->error = SubirMultiple::ERROR_CREAR_FALSE;
        $this->listaErrores['carpeta'] = SubirMultiple::ERROR_CREAR_FALSE;
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
    private function getCantidadMaxima(){ //método util??
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
    public function getErrores(){
        $cadenaerror = "Resultado de la subida: <br />";
        foreach ($this->listaErrores as $key => $value) {
            $cadenaerror .= $key." --- ".$this->getMensajeError($value);
            $cadenaerror .= "<br />";
        }
        return $cadenaerror;
    }
    public function setNuevoNombre($nom){
        $this->nuevoNombre = $nom;
        return true;
    }
    /**
     * Devuelve un string con el significado de un código de error pasado
     * por parámetro.
     * @access private
     * @param int $codigoerror entero con el número del error
     * @return string Devuelve una cadena con el significado del error
     */
    private function getMensajeError($codigoerror){
        switch($codigoerror){
            case SubirMultiple::NO_ERROR:
                return "No se han detectado errores";
            case SubirMultiple::ERROR_TAM_TOTAL:
                return "Se ha sobrepasado el tamaño total permitido: ".$this->tamMaxTotal;
            case SubirMultiple::ERROR_NUM_MAX:
                return "Se ha sobrepasado el número máximo de archivos: ".$this->cantidadMax;
            case SubirMultiple::ERROR_EXT:
                return "Extensión no permitida";
            case SubirMultiple::ERROR_TIPO:
                return "Tipo MIME no permitido";
            case SubirMultiple::ERROR_TAM:
                return "Archivo demasiado grande";
            case SubirMultiple::ERROR_SUBIDA:
                return "Ha fallado la subida del archivo";
            case SubirMultiple::ERROR_CREAR_FALSE:
                return "No se ha creado el directorio";
            case SubirMultiple::ERROR_SIN_CARPETA:
                return "No se ha encontrado la carpeta de destino";
            case SubirMultiple::ERROR_OMISION:
                return "El archivo ya existe en el directorio y se ha omitido su subida";
            default:
                return "Error desconocido";
        }
         
    }
    /**
     * Sube el archivo o archivos enviados en el formulario según la
     * configuración especificada.
     * @access public
     */
    public function subir(){
        $archivos = $_FILES[$this->inputname];
        if($this->accionExcede == SubirMultiple::OMITIR_TODO){
            if(!$this->isCantidad()){
                $this->error = SubirMultiple::ERROR_NUM_MAX;
                $this->listaErrores['subida'] = SubirMultiple::ERROR_NUM_MAX;
                return false;
            }
            if(!$this->isTamanioTotal()){
                $this->error = SubirMultiple::ERROR_TAM_TOTAL;
                $this->listaErrores['subida'] = SubirMultiple::ERROR_TAM_TOTAL;
                return false;
            }
        }
        if(!file_exists($this->destino)){
            if(!$this->crearCarpeta()){
                $this->error = SubirMultiple::ERROR_SIN_CARPETA;
                $this->listaErrores['subida'] = SubirMultiple::ERROR_SIN_CARPETA;
                return false;
            }
        }
        $i=-1;
        $totalsubida=0;
        $contador=0;
        foreach($archivos['name'] as $archivo){
            $i++;
            $totalsiguiente = $totalsubida + $archivos['size'][$i];
            if($contador>=$this->getCantidadMaxima()){
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_NUM_MAX;
                return false;
            }
            if($totalsiguiente >= $this->tamMaxTotal){
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_TAM_TOTAL;
                continue;
            }
            $partes = pathinfo($archivos["name"][$i]);
            $extension = $partes['extension'];
            $nombre="";
            if($this->nuevoNombre=="" || $this->nuevoNombre==NULL){
                $nombre = $partes['filename'];
            }else{
                $nombre = $this->nuevoNombre;
            }
            $origen=$archivos['tmp_name'][$i];
            $lugardestino="";
            if(!$this->isExtension($extension)){
                $this->error = SubirMultiple::ERROR_EXT;
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_EXT;
                continue;
            }
            if(!$this->isTipo($archivos['type'][$i])){
                $this->error = SubirMultiple::ERROR_TIPO;
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_TIPO;
                continue;
            }
            if(!$this->isTamanio($archivos['size'][$i])){
                $this->error = SubirMultiple::ERROR_TAM;
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_TAM;
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
                    $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_OMISION;
                    continue;
                }
            }
            if(!move_uploaded_file($origen, $lugardestino)){
                $this->error = SubirMultiple::ERROR_SUBIDA;
                $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::ERROR_SUBIDA;
            }
            $totalsubida += $archivos['size'][$i];
            $contador++;
            $this->listaErrores[$archivos['name'][$i]] = SubirMultiple::NO_ERROR;
        }
    }
}
