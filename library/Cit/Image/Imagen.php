<?php

class Cit_Image_Imagen {

    private $_width;
    private $_height;
    private $_rutaImagen;

    public function __construct($rutaImagen) {
        if (empty($rutaImagen)) {
            $this->throwError("Archivo no Ingresado", E_USER_ERROR);
        }

        $this->setrutaImagen($rutaImagen);
    }

    public function setrutaImagen($rutaImagen) {
        $this->_rutaImagen = $rutaImagen;
    }

    public function getrutaImagen() {
        return $this->_rutaImagen;
    }

    public function resize($width, $height) {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    public function getWidth() {
        return $this->_width;
    }

    public function getHeight() {
        return $this->_height;
    }

    private function setWidth($width) {
        $this->_width = $width;
    }

    private function setHeight($height) {
        $this->_height = $height;
    }

    public function save($fileSave) {
        $nWidth = $this->getWidth();
        $nHeight = $this->getHeight();
        $sImagen = $this->getrutaImagen();
        // Variables
        $sNombre = null;
        $sPath = null;
        $sExt = null;
        $aImage = null;
        $aThumb = null;
        $aImageMarco = null;
        $ImTransparente = null;
        $aSize = null;
        $nWidthMarco = false;
        $nWidthHeight = false;
        $nX = false;
        $nY = false;

        // Obtenemos el nombre de la imagen
        $sNombre = basename($sImagen);
        // Obtenemos la ruta especificada para buscar la imagen
        $sPath = dirname($sImagen) . '/';
        // Obtenemos la extension de la imagen

       // Obtenemos el tamaño de la imagen original
        $aSize = getImageSize($sImagen);
         if ($aSize !== FALSE)
         {
            $sExt = $aSize[2]; // constante de php que tiene el tipo de imagen, un entero
            // por defecto usaremos las dimensiones de la imagen original
         }

      switch ($sExt)
        {
            case IMAGETYPE_PNG:
                $aImage = imagecreatefrompng($sImagen);
                break;
            case IMAGETYPE_JPEG:
                $aImage = imagecreatefromjpeg($sImagen);
                break;
            case IMAGETYPE_GIF:
                $aImage = imagecreatefromgif($sImagen);
                break;
            default:
                return 'No se conoce el tipo de imagen enviado, por favor cambie el formato. Sólo se permiten imágenes *.jpg, *.gif, *.png ó *.bmp.';
                break;
        } 
        // Creamos la imagen a partir del tipo
       /* switch ($sExt) {
            // Imagen JPG
            case 'image/jpeg':
                $aImage = @imageCreateFromJpeg($sImagen);
                break;
            // Imagen GIF
            case 'image/gif':
                $aImage = @imageCreateFromGif($sImagen);
                break;
            // Imagen PNG
            case 'image/png':
                $aImage = @imageCreateFromPng($sImagen);
                break;
            // Imagen BMP
            case 'image/wbmp':
                $aImage = @imageCreateFromWbmp($sImagen);
                break;
            default:
                return 'No se conoce el tipo de imagen enviado, por favor cambie el formato. Sólo se permiten imágenes *.jpg, *.gif, *.png ó *.bmp.';
                break;
        }*/

        

        // Calculamos las proporciones de la imagen //
        // Obteniendo el alto (Recogiendo ancho y no alto)
        if ($nWidth !== false && $nHeight === false)
            $nHeight = round(( $aSize[1] * $nWidth ) / $aSize[0]);
        // Obteniendo el ancho (Recogiendo alto y no ancho)
        elseif ($nWidth === false && $nHeight !== false)
            $nWidth = round(( $aSize[0] * $nHeight ) / $aSize[1]);
        // Obteniendo proporciones (Recogiendo alto y ancho)
        elseif ($nWidth !== false && $nHeight !== false) {
            // Guardamos las dimensiones del marco
            $nWidthMarco = $nWidth;
            $nHeightMarco = $nHeight;

            // Si el ancho es mayor
            if ($nWidth < $nHeight) {
                $nHeight = round(( $aSize[1] * $nWidth ) / $aSize[0]);
                $nX = 0;
                $nY = round(( $nHeightMarco - $nHeight ) / 2);
            }
            // Si el alto es mayor
            elseif ($nHeight < $nWidth) {
                $nWidth = round(( $aSize[0] * $nHeight ) / $aSize[1]);
                $nX = round(( $nWidthMarco - $nWidth ) / 2);
                ;
                $nY = 0;
            }
        }
        // El ancho y el alto no se han enviado, informamos del error
        elseif ($nWidth === false && $nHeight === false)
            return 'No se ha especificado ningún valor para el ancho y el alto de la imágen.';

        // La nueva imagen reescalada
        $aThumb = imageCreateTrueColor($nWidth, $nHeight);

        // Reescalamos
        imageCopyResampled($aThumb, $aImage, 0, 0, 0, 0, $nWidth, $nHeight, $aSize[0], $aSize[1]);

        // Si tenemos que crear el marco
        /*if ($nWidthMarco !== false && $nHeightMarco !== false) {
            // El marco
            $aImageMarco = imageCreateTrueColor($nWidthMarco, $nHeightMarco);

            // Establecemos la imagen de fondo transparente
            imageAlphaBlending($aImageMarco, false);
            imageSaveAlpha($aImageMarco, true);

            // Establecemos el color transparente (negro)
            $ImTransparente = imageColorAllocateAlpha($aImageMarco, 0, 0, 0, 0xff / 2);

            // Ponemos el fondo transparente
            imageFilledRectangle($aImageMarco, 0, 0, $nWidthMarco, $nHeightMarco, $ImTransparente);

            // Combinamos las imagenes
            imageCopyResampled($aImageMarco, $aThumb, $nX, $nY, 0, 0, $nWidth, $nHeight, $nWidth, $nHeight);

            // Cambiamos la instancia
            $aThumb = $aImageMarco;
        }*/

        // Salvamos
        imagePng($aThumb, $fileSave);

        // Liberamos
        imageDestroy($aImage);
        imageDestroy($aThumb);

        return true;
    }

    private function throwError($msg, $level) {
        trigger_error($msg, $level);
    }

}

?>
