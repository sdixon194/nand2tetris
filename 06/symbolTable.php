<?php

class symbolTable {
	public $symbolTable = array();

	public function __construct() {
		$this->prepareSymbolTable();
	}

	/**
	 * Instantiate the known symbols in the table.
	 *
	 * @param $symbol The symbol to get the address of.
	 */
	private function prepareSymbolTable() {
		$this->symbolTable = array(
			'SP' => 0,
			'LCL' => 1,
			'ARG' => 2,
			'THIS' => 3,
			'THAT' => 4,
			'R0' => 0,
			'R1' => 1,
			'R2' => 2,
			'R3' => 3,
			'R4' => 4,
			'R5' => 5,
			'R6' => 6,
			'R7' => 7,
			'R8' => 8,
			'R9' => 9,
			'R10' => 10,
			'R11' => 11,
			'R12' => 12,
			'R13' => 13,
			'R14' => 14,
			'R15' => 15,
			'SCREEN' => 16384,
			'KBD' => 24576
		);
	}

	/**
	 * Add a new entry to the symbol table.
	 *
	 * @param $symbol The symbol to add.
	 * @param $address The address of the symbol.
	 */
	public function addEntry( $symbol, $address ) {
		$this->symbolTable[$symbol] = $address;
	}
}
?>