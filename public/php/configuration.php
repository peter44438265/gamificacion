<?php

class Configuration {

    public static $pathnames = array(
        'documentRoot' => '../',
        'urlRoot' => 'http://local.valeplaza.com/',
        'removeImage' => 'removeImage.php',
        'uploadsTempMedium' => 'uploads/temp/medium/',
        'uploadsTempThumb' => 'uploads/temp/thumb/',
        'uploadsMedium' => 'uploads/medium/',
        'uploadsThumb' => 'uploads/thumb/',
    );

    public static $data = array(
        'fileFieldName' => 'imageToUpload',
        // "image/x-png" and "image/pjpeg" are the png and jpg types for Internet Explorer
        // File types supported by the script: png, jpg, gif.
        'allowedImageTypes' => array(
            'image/png',
            'image/x-png',
            'image/jpg',
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
        ),
        'maxImageSize' => 3000000, // in bytes. The maximum supported is 3000000.
        'maxImageDimension' => 2500, // in pixels. The maximum supported is 2500.
        'mediumImageDimension' => 500, // in pixels
        'thumbImageDimension' => 125, // in pixels
    );

    public static $messages = array(

    'unknown' => '
        <p>An unknown error has been produced.</p>',
    'upload' => '
        <p>Error while loading the file.</p>
        <p>Error code: %s</p>',
    'type' => '
        <p>The file is not an allowed type.</p>
        <p>Select an image of type PNG, JPG or GIF.</p>',
    'size' => '
        <p>The file is bigger than the maximum allowed.</p>
        <p>File size: %s KB</p>
        <p>Maximum allowed: %s KB</p>',
    'dimension' => '
        <p>The file is larger than the maximum allowed.</p>
        <p>File dimmensions: %s x %s px</p>
        <p>Maximum allowed: %s x %s px</p>',
    'unknownUploadError' => '
        <p>An error has been produced while uploading.</p>
        <p>Please, try it again.</p>',
    );

    // functions ---------------------------------------------------------------

    public static function getPath($pathKey){
        return self::$pathnames['documentRoot'] . self::$pathnames[$pathKey];
    }

    public static function getUrlPath($pathKey){
        return self::$pathnames['urlRoot'] . self::$pathnames[$pathKey];
    }

} // end of class Configuration

/*
 * For files that contain only PHP code, the closing tag ("?>") is to be omitted.
 * It is not required by PHP, and omitting it prevents trailing whitespace from
 * being accidentally injected into the output.
 * ?>
 */
