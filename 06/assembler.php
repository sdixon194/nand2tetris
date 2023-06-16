<?php
/**
Initialize the assembler and take in a file that we want to parse.
*/
include_once('parser.php');
$filename = $argv[1];
$file = file_get_contents(
    $filename,
    $use_include_path = false,
    $context = null,
    $offset = 0,
    $length = null
);

$parser = new Parser( $file );
$parser->firstParse( $parser->fileArray );
$parser->secondParse( $parser->fileArray );

// Write the binary to a file.
$binary = implode("\n", $parser->binaryArray);
$binary = trim($binary);
$binaryFilename = str_replace('.asm', '.hack', $filename );
file_put_contents($binaryFilename, $binary);
?>