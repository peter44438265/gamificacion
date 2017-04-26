<?php 
class Cit_Image_ImageResize extends Cit_Image_Image 
{ 
 
   private $src; 
   private $origWidth; 
   private $origHeight; 
   private $origType; 
   private $hasError = false; 
 
   public function __construct($src) 
   { 
      $this->setSrc($src); 
   } 
 
   private function setSrc($src) 
   { 
      if (is_file($src)) 
      { 
         // getimagesize retorna un arreglo si tuvo exito con la informacion de la imagen 
         // false en caso contrario 
         $info = getimagesize($src); 
         if ($info !== FALSE) 
         { 
            $this->src = $src; 
            $this->origWidth = $info[0]; // ancho de la imagen 
            $this->origHeight = $info[1]; // alto de la imagen 
            $this->origType = $info[2]; // constante de php que tiene el tipo de imagen, un entero 
 
            // por defecto usaremos las dimensiones de la imagen original 
            $this->resize($this->origHeight, $this->origHeight); 
         } 
         else 
         { 
            $this->throwError("$src is not an image file", E_USER_ERROR); 
         } 
      } 
      else 
      { 
         $this->throwError("$src is not file valid", E_USER_ERROR); 
      } 
   } 
 
   /** 
    * Asigna los valores a los que se redimensionara la imagen 
    * 
    * @param int $w ancho 
    * @param int $h alto 
    */ 
 
   public function resize($w, $h) 
   { 
      if ($w < 1)   $this->throwError("Ancho debe ser mayor que 0", E_USER_NOTICE); 
      if ($h < 1)   $this->throwError("Alto debe ser mayor que 0", E_USER_NOTICE); 
      $this->setWidth($w); 
      $this->setHeight($h); 
   } 
 
   /** 
    * Redimensiona la imagen con el ancho y alto asignado en resize 
    * y la guarda en el archivo de nombre $filename 
    * 
    * @param string $filename nombre del archivo 
    * @return bool TRUE en caso de exito, FALSE si algo salio mal 
    */ 
 
   public function save($filename) 
   { 
      $success= false; 
      // obtenemos la imagen en gd del archivo 
      $orig = $this->gdFromFile($this->src, $this->origType); 
      if ($orig !== FALSE) // si lo obtuvimos 
      { 
         // creamos una imagen vacia con ancho y alto, servira de contenedor 
         $base       = imagecreatetruecolor($this->width, $this->height); 
 
         // aqui redimensionamos la imagen 
         // la imagen redimensionada queda en $base, esta funcion retorna TRUE si tuvo exito, FALSE en caso contrario 
         $resized   = imagecopyresampled($base, $orig, 0, 0, 0, 0, $this->width, $this->height, $this->origWidth, $this->origHeight); 
         if ($resized) // pudimos redimensionar 
         { 
            // guardamos gd en el archivo $filename 
            if (!$this->gdToFile($base, $filename, $this->origType)) 
            { 
               $this->throwError("Archivo no generado", E_USER_WARNING); 
            } 
            else 
            { 
               // todo salio bien 
               $success = true; 
               // liberamos los recursos gd 
               imagedestroy($base); 
               imagedestroy($orig); 
            } 
         } 
      } 
      else 
      { 
         $this->throwError("Gd no fue generado.", E_USER_WARNING); 
      } 
      return $success; 
   } 
 
   private function throwError($msg, $level) 
   { 
      trigger_error($msg, $level); 
   } 
} 