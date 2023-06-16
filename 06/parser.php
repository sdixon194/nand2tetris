<?php
 include_once('code.php');
 include_once('symbolTable.php');

/**
 * Parse the file.
 */
 class Parser {
	public $fileArray = array();
	public $binaryArray = array();
	public $RAM = 16;
	public $symbolTable;
	public $code;

	public function __construct( $file ) {
		$this->prepareFile( $file );
		$this->symbolTable = new symbolTable();
		$this->code = new Code();
	}

	/**
	 * Remove whitespace, comments, and carriage returns from the file and return it as an array.
	 *
	 * @param $file The file to parse.
	 */
	private function prepareFile( $file ) {
		$file = preg_replace('/\/\/.*/', '', $file);
		$file = str_replace("\r", '', $file);
		$file = trim($file);
		$this->fileArray = explode("\n", $file);
	}

	/**
	 * In the first parse of the file, we just look for label declarations and add them to the symbol table and increment line count.
	 *
	 * @param $file The file to parse.
	 */
	public function firstParse( $file ) {
		$lineCount = 0;
		foreach( $file as $line ) {

			// If the line doesn't start with a (, it's not a label.
			if ( ! preg_match('/^\(/', $line) ) {
				$lineCount++;
				continue;
			}

			// If the line starts with ( and ends with ), it's a label.
			if ( preg_match('/^\(.+\)$/', $line) ) {
				$line = str_replace('(', '', $line);
				$line = str_replace(')', '', $line);
				$this->symbolTable->addEntry( $line, $lineCount );
			}
		}
	}

	/**
	 * In the second parse of the file, we look for A and C instructions and convert them to binary.
	 *
	 * @param $file The file to parse.
	 */
	public function secondParse( $file ) {
		foreach( $file as $key => $line ) {
			$line = trim($line);
			// If the line starts with @, it's an A instruction.
			if ( preg_match('/^@/', $line) ) {
				$this->symbol( $key, $line );
			}

			// If the line starts with a letter or a zero, it's a C instruction.
			if ( preg_match('/^[a-zA-Z0]/', $line) ) {
				$this->binaryArray[] = $this->code->Cinstruction( $line );
			}
		}
	}


	/**
	 * If the line starts with @ and a number, convert it to binary.
	 * If the line starts with @ and a letter, it's a symbol that we need to add to the symbol table before converting.
	 *
	 * @param $line The line to parse.
	 */
	private function symbol( $line ) {
		// If the line starts with @ and a number, return the number.
		if ( preg_match('/^@\d/', $line) ) {
			$this->binaryArray[] = $this->code->Ainstruction( $line );
		}

		// If the line starts with @ and a letter, it's a symbol.
		if ( preg_match('/^@[a-zA-Z]/', $line) ) {
			$symbol = str_replace('@', '', $line);
			if ( ! array_key_exists( $symbol, $this->symbolTable->symbolTable ) ) {
				$this->symbolTable->addEntry( $symbol, $this->RAM );
				$this->RAM++;
			}

			$symbol = $this->symbolTable->symbolTable[$symbol];
			$this->binaryArray[] = $this->code->Ainstruction( $symbol );
		}
	}
 }
?>