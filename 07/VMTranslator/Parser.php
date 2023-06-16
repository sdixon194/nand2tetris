<?php 
include_once('Codewriter.php');

class Parser {

public $codeWriter;
public string $commandType;
public string $arg1;
public string $arg2;
public string $filename;

	/**
	 * Opens the input file/stream and gets ready to parse it.
	 *
	 * @param array $file - the file contents being parsed.
	 * @param string $filename - the name of the file being parsed.
	 */
	function __construct( $file, $filename ) {
		$this->filename = dirname( $filename );
		$this->codeWriter = new CodeWriter( $filename );
		$this->parse( $file );	
	}


	/**
	 * Parses the file we want to translate.
	 *
	 * @param array $file - the file contents being parsed.
	 */
	private function parse( $file ) {
		foreach( $file as $line ) {
			$command = explode(' ', $line);
			$rawCommand = $command[0];
			$arg1 = $command[1] ?? null;
			$arg2 = $command[2] ?? null;
			$commandType = $this->commandType( $rawCommand );
			if ( $commandType != 'C_RETURN' ) {
				$arg1 = $this->arg1( $commandType, $arg1 );
			}
			
			$this->codeWriter->write( $line, $commandType, $rawCommand, $arg1, $arg2 );
		}
		$this->codeWriter->infiniteLoop();
		fclose( $this->codeWriter->outputHandle );
	}

	/**
	 * Returns the type of the current VM command.
	 *
	 * @param string $command - the raw command being parsed.
	 * @return string $commandType - the type of command being parsed.
	 */
	private function commandType( $command ) {
		switch ( $command ) {
			case 'push':
				return 'C_PUSH';
				break;
			case 'pop':
				return 'C_POP';
				break;
			case 'add':
			case 'sub':
			case 'neg':
			case 'eq':
			case 'gt':
			case 'lt':
			case 'and':
			case 'or':
			case 'not':
				return 'C_ARITHMETIC';
				break;
			
		}
	}

	/**
	 * Returns the first argument of the current command.
	 *
	 * @param string $commandType - the type of command being parsed.
	 * @param string $arg1 - the first argument of the command.
	 * @return string $arg1 - the first argument of the command.
	 */
	private function arg1( $commandType, $arg1 ) {
		if ( $commandType === 'C_ARITHMETIC' ) {
			return $commandType;
		} else {
			return $arg1;
		}
	}
}

?> 