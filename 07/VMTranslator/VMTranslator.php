<?php 
include_once('Parser.php');
$filename = $argv[1];

// Get the contents of the file and parse it.
$fileHandle = fopen($filename, 'r');
if ($fileHandle) {
    $file = fread($fileHandle, filesize($filename));
    fclose($fileHandle);
} else {
    echo 'Failed to open the file.';
}

$file = preg_replace('/\/\/.*/', '', $file);
$file = str_replace("\r", '', $file);
$file = trim($file);
$fileArray = explode("\n", $file);

// Parse the file.
$parser = new Parser( $fileArray, $filename );

?>