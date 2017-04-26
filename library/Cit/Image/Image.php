<?php 
abstract class Cit_Image_Image
{ 
    protected $width; 
    protected $height; 
 
    public function setWidth($w) 
    { 
        // gd solo maneja enteros, ergo obligamos que ancho sea entero.  
        $w = (int) $w; 
        // ancho debe ser mayor que 0 
        if ($w > 0)    $this->width = $w; 
    } 
 
    public function getWidth() 
    { 
        return $this->width; 
    } 
 
    public function setHeight($h) 
    { 
        // gd solo maneja enteros, ergo obligamos que alto sea entero 
        $h = (int) $h; 
        // alto debe ser mayor que 0 
        if ($h > 0)    $this->height = $h; 
    } 
 
    public function getHeight() 
    { 
        return $this->height; 
    } 
 
    /** 
     * Genera una imagen gd del archivo con nombre $filename  
     * Retorna FALSE si ocurrior algun error, por ejemplo: el tipo no es soportado 
     * 
     * @param string $filename nombre del archivo 
     * @param int $type Tipo de imagen para saber que funcion usar 
     * @return resource Una imagen gd.  
     */ 
    protected function gdFromFile($filename, $type) 
    { 
        $gd = false; 
        switch ($type) 
        { 
            case IMAGETYPE_PNG: 
                $gd = imagecreatefrompng($filename); 
                break; 
            case IMAGETYPE_JPEG: 
                $gd = imagecreatefromjpeg($filename); 
                break; 
            case IMAGETYPE_GIF: 
                $gd = imagecreatefromgif($filename); 
                break; 
        } 
        return $gd; 
    } 
 
    /** 
     * Guarda una imagen gd en el archivo de nombre $filename 
     *  
     * @param resource $gd La imagen a guardar 
     * @param string $filename nombre del archivo 
     * @param int $type Tipo de imagen para saber que funcion usar 
     * @return bool TRUE en caso de exito, FALSE en caso contrario 
     *  
     */ 
    protected function gdToFile($gd, $filename, $type) 
    { 
        $success = false; 
        // si $filename es nulo las funciones posteriores imprimiran en la salida directamente 
        // aqui tratamos de evitar eso 
        $filename = (string) $filename; 
        if (trim($filename) != "") 
        { 
            // no tiene sentido verificar si el archivo existe, pues si no existe se creara 
            // las siguientes funciones retornan false si ocurrio algun error, true en caso de exito 
            switch ($type) 
            { 
                case IMAGETYPE_PNG: 
                    $success = imagepng($gd, $filename); 
                    break; 
                case IMAGETYPE_GIF: 
                    $success = imagegif($gd, $filename); 
                    break; 
                case IMAGETYPE_JPEG: 
                    $success = imagejpeg($gd, $filename); 
                    break; 
            } 
        } 
        return $success; 
    } 
 
    // Obligamos a que las clases que hereden esta clase implementen este método 
    /** 
     * La intencion de este metodo es que guarde la imagen creada en un archivo  
     * 
     * @param string $filename Nombre del archivo 
     * @return bool TRUE en caso de exito, FALSE en caso contrario 
     */ 
    abstract public function save($filename); 
}