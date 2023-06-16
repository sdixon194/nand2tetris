<?php
include_once( 'JackTokenizer.php' );
include_once( 'CompilationEngine.php');

// Accept a Jack source file or directory, and tokenize it into a file or directory of XML files
$input = $argv[1];
$files = array();

// If the argument doesn't end in .jack, then it's a directory.
if ( substr( $input, -5 ) !== '.jack') {
	// Search the directory for files ending in .jack and add them to the files array.
	$files = glob( $input . '/*.jack' );
} else {
	$files[] = $input;
}

// Iterate through the files array and tokenize each file.
foreach ( $files as $file ) {
	$outputFile = basename( $file, '.jack' );
	$path = dirname( $file );
	$outputFile = $path . '/' . $outputFile . '.vm';

	$tokenizer = new JackTokenizer( $file, $outputFile );
	$tokenizer->tokenize();
	$tokenizer->closeFile();

	$compilationEngine = new CompilationEngine( $outputFile );
	$compilationEngine->startCompile();
}

?>