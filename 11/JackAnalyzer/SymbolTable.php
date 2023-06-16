<?php 

class SymbolTable {
	public $table;
	private $varCount;

	function __construct() {
		$this->reset();
	}

	public function reset() {
		$this->table = array();
		$this->varCount = array(
			'static' => 0,
			'field' => 0,
			'arg' => 0,
			'var' => 0,
		);
	}

	public function define( $name, $type, $kind ) {
		// Add the variable to the table
		$this->table[] = array(
			'name' => $name,
			'type' => $type,
			'kind' => $kind,
			'index' => $this->varCount( $kind ) 
		);
		$this->varCount[ $kind ]++;
	}

	/** Returns the number of variables of the given kind already defined in the current scope */
	public function varCount( $kind ) {
		return $this->varCount[ $kind ];
	}

	/** Returns the kind of the named variable */
	public function kindOf( $name ) {
		foreach( $this->table as $entry ) {
			if ( $entry['name'] === $name ) {
				return $entry['kind'];
			}
		}
	}

	/** Returns the type of the named variable */
	public function typeOf( $name ) {
		foreach( $this->table as $entry ) {
			if ( $entry['name'] === $name ) {
				return $entry['type'];
			}
		}
	}

	/** Returns the index assigned to the named variable */
	public function indexOf( $name ) {
		foreach( $this->table as $entry ) {
			if ( $entry['name'] === $name ) {
				return $entry['index'];
			}
		}
	}
}

?>