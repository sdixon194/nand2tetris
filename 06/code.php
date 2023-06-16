<?php 
/** This class will accept the A or C instruction and translate it into binary code */

class Code {

	/**
	 * The comp table is used to translate the comp portion of the C instruction to binary.
	 */
	private $compTable = array(
		'0' => '101010',
		'1' => '111111',
		'-1' => '111010',
		'D' => '001100',
		'A' => '110000',
		'M' => '110000',
		'!D' => '001101',
		'!A' => '110001',
		'!M' => '110001',
		'-D' => '001111',
		'-A' => '110011',
		'-M' => '110011',
		'D+1' => '011111',
		'A+1' => '110111',
		'M+1' => '110111',
		'D-1' => '001110',
		'A-1' => '110010',
		'M-1' => '110010',
		'D+A' => '000010',
		'D+M' => '000010',
		'D-A' => '010011',
		'D-M' => '010011',
		'A-D' => '000111',
		'M-D' => '000111',
		'D&A' => '000000',
		'D&M' => '000000',
		'D|A' => '010101',
		'D|M' => '010101'
	);

	/**
	 * The jump table is used to translate the jump portion of the C instruction to binary.
	 */
	private $jumpTable = array(
		'JGT' => '001',
		'JEQ' => '010',
		'JGE' => '011',
		'JLT' => '100',
		'JNE' => '101',
		'JLE' => '110',
		'JMP' => '111'
	);

	/**
	 * Convert A instructions to binary.
	 *
	 * @param $line The line to convert.
	 */
	public function Ainstruction( $line ) {
		$line = str_replace('@', '', $line);
		$line = decbin($line);
		$line = str_pad($line, 16, '0', STR_PAD_LEFT);
		return $line;
	}

	/**
	 * Convert C instructions to binary.
	 *
	 * @param $line The line to convert.
	 */
	public function Cinstruction( $line ) {
		$dest = null;
		$jump = null;
		$a = 0;

		// If $line contains =, it's a destination.
		if ( preg_match('/=/', $line) ) {
			$destLine = explode('=', $line);
			$dest = $destLine[0];
			$line = $destLine[1];
		}

		// If $line contains ;, it's a jump.
		if ( preg_match('/;/', $line) ) {
			$jumpLine = explode(';', $line);
			$jump = $jumpLine[1];
			$line = $jumpLine[0];
		}

		// Now we have the comp.
		if ( preg_match('/M/', $line) ) {
			$a = '1';
		}

		$comp = $this->compBinary( $line );
		$dest = $this->destBinary( $dest );
		$jump = $this->jumpBinary( $jump );

	
		$line = '111' . $a . $comp . $dest . $jump;
		return $line;
	}

	/**
	 * Convert the comp portion of the C instruction to binary.
	 *
	 * @param $comp The comp portion of the C instruction.
	 */
	private function compBinary( $comp ) {
		$binary = $this->compTable[$comp];
		return $binary;
	}

	/**
	 * Convert the dest portion of the C instruction to binary.
	 *
	 * @param $dest The dest portion of the C instruction.
	 */
	private function destBinary( $dest ) {
		$binary = null;
		if ( $dest == null ) {
			$binary = '000';
		} else {
			if ( preg_match('/A/', $dest) ) {
				$binary .= '1';
			} else {
				$binary .= '0';
			}
			if ( preg_match('/D/', $dest) ) {
				$binary .= '1';
			} else {
				$binary .= '0';
			}
			if ( preg_match('/M/', $dest) ) {
				$binary .= '1';
			} else {
				$binary .= '0';
			}
		}
		return $binary;
	}

	/**
	 * Convert the jump portion of the C instruction to binary.
	 *
	 * @param $jump The jump portion of the C instruction.
	 */
	private function jumpBinary( $jump ) {
		$binary = null;
		if ( $jump == null ) {
			$binary = '000';
		} else {
			$binary = $this->jumpTable[$jump];
		}
		return $binary;
	}
}


?>