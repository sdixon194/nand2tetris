<?php 
include_once('Parser.php');
include_once('Codewriter.php');
$bootstrap = false;
$filename = $argv[1];
$files = array();
$outputFile = '';

// If the argument doesn't end in .vm, then it's a directory.
if ( substr( $filename, -3 ) !== '.vm') {
	// Search the directory for files ending in .vm and add them to the files array.
	$files = glob( $filename . '/*.vm' );
}

if ( empty( $files ) ) {
	$files[] = $filename;
	$outputFile = basename( $filename, '.vm' );
	$outputFile = str_replace( '.vm', '.asm', $filename );
} else {
	$outputFile = basename( $filename ) . '.asm';
	$outputFile = $filename . '/' . $outputFile;
}

// If files contains Sys.vm, then we need to set the bootstrap to true.
foreach ( $files as $file ) {
	if ( basename( $file ) == 'Sys.vm' ) {
		$bootstrap = true;
	}
}

// Get the contents of the file and parse it.
foreach ( $files as $filename ) {
	$currentFile = basename( $filename, '.vm' );
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
	$parser = new Parser( $fileArray, $outputFile, array_search( $filename, $files ), $currentFile, $bootstrap );
}
$codeWriter = new CodeWriter( $outputFile, 1 );
$codeWriter->infiniteLoop();
fclose( $codeWriter->outputHandle );

?>