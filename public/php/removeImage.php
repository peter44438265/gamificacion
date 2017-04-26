<?php

// includes --------------------------------------------------------------------

require_once('configuration.php');

// main script -----------------------------------------------------------------

$oldImageToDeleteName = $_POST['currentUploadedFilename'];

if ($oldImageToDeleteName) {
  $oldImageToDelete = Configuration::getPath('uploadsTempMedium') . $oldImageToDeleteName;
  if(file_exists($oldImageToDelete)) {
    unlink($oldImageToDelete);
  }
  $tmbOldImageToDelete = Configuration::getPath('uploadsTempThumb') . $oldImageToDeleteName;
  if(file_exists($tmbOldImageToDelete)) {
    unlink($tmbOldImageToDelete);
  }
}

/*
 * For files that contain only PHP code, the closing tag ("?>") is to be omitted.
 * It is not required by PHP, and omitting it prevents trailing whitespace from
 * being accidentally injected into the output.
 * ?>
 */
