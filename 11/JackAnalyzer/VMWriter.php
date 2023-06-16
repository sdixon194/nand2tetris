<?php

class VMWriter {
	private $outputFile;
	private $outputHandle;
	private $arithmetics = array(
		'+' => 'add',
		'add' => 'add',
		'-' => 'sub',
		'sub' => 'sub',
		'&' => 'and',
		'|' => 'or',
		'<' => 'lt',
		'>' => 'gt',
		'=' => 'eq',
		'*' => 'call Math.multiply 2',
		'/' => 'call Math.divide 2',
		'~' => 'not',
		'neg' => 'neg',
		'not' => 'not',
		'&amp;' => 'and',
		'&lt;' => 'lt',
		'&gt;' => 'gt',
	);

	function __construct( $outputFile ) {
		$this->outputFile = $outputFile;
		$this->outputHandle = fopen( $this->outputFile, 'w' );
	}

	public function writePush( $segment, $index ) {
		if ( $segment === 'var' ) {
			$segment = 'local';
		} elseif ( $segment === 'arg' ) {
			$segment = 'argument';
		}
		fwrite( $this->outputHandle, 'push ' . $segment . ' ' . $index . "\n" );
	}

	public function writePop( $segment, $index ) {
		if ( $segment === 'var' ) {
			$segment = 'local';
		} elseif ( $segment === 'arg' ) {
			$segment = 'argument';
		}
		fwrite( $this->outputHandle, 'pop ' . $segment . ' ' . $index . "\n" );
	}

	public function writeArithmetic( $command ) {
		fwrite( $this->outputHandle, $this->arithmetics[ $command ] . "\n" );
	}

	public function writeLabel( $label ) {
		fwrite( $this->outputHandle, 'label ' . $label . "\n" );
	}

	public function writeGoto( $label ) {
		fwrite( $this->outputHandle, 'goto ' . $label . "\n" );
	}

	public function writeIf( $label ) {
		fwrite( $this->outputHandle, 'if-goto ' . $label . "\n" );
	}

	public function writeCall( $name, $nArgs ) {
		fwrite( $this->outputHandle, 'call ' . $name . ' ' . $nArgs . "\n" );
	}

	public function writeFunction( $name, $nLocals ) {
		fwrite( $this->outputHandle, 'function ' . $name . ' ' . $nLocals . "\n" );
	}

	public function writeReturn() {
		fwrite( $this->outputHandle, 'return' . "\n" );
	}

	public function closeFile() {
		fclose( $this->outputHandle );
	}

	public function writeLog( $message ) {
		fwrite( $this->outputHandle, '// ' . $message . "\n" );
	}
}

?>