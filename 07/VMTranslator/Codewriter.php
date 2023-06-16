<?php 

class CodeWriter {
	public $outputFile;
	public $outputHandle;
	public $labelCount = 0;
	public $filename;
	public $destination = array(
		"local" => "LCL",
		"argument" => "ARG",
		"this" => "3",
		"that" => "4",
		"temp" => "5",
	);

	/**
	 * Constructor
	 * @param string $filename - the name of the file.
	 */
	function __construct($filename)
	{
		$this->filename = basename( $filename, '.vm' );
		$this->outputFile = str_replace( '.vm', '.asm', $filename );
		$this->outputHandle = fopen( $this->outputFile, 'w' );
		if (!$this->outputHandle) {
			echo 'Failed to open the output file.';
			exit;
		}
	}

	/**
	 * Handles routing the command to the appropriate method.
	 *
	 * @param string $line - the line of code being parsed.
	 * @param string $commandType - the type of command being parsed.
	 * @param string $command - the raw command being parsed.
	 * @param string $arg1 - the first argument of the command.
	 * @param string $arg2 - the second argument of the command.
	 */
	public function write( $line, $commandType, $command, $arg1, $arg2 ) {
		$this->writeLine( $line );
		switch ( $commandType ) {
			case 'C_ARITHMETIC':
				$this->writeArithmetic( $command );
				break;
			case 'C_PUSH':
			case 'C_POP':
				$this->writePushPop( $command, $arg1, $arg2 );
				break;
		}
	}

	/**
	 * Writes the assembly code that is the translation of the given arithmetic command.
	 *
	 * @param string $command - the raw command being parsed.
	 */
	private function writeArithmetic( $command ) {
		$contents = <<<EOD
				@SP
				AM=M-1
				D=M
				@SP
				AM=M-1 \n
				EOD;
		switch ( $command ) {
			case 'add':
				$contents .= <<<EOD
				M=M+D \n
				EOD;
				break;
			case 'sub':
				$contents .= <<<EOD
				M=M-D \n
				EOD;
				break;
			case 'eq':
				$contents .= $this->jumpCode( 'JEQ', $this->labelCount );
				$this->labelCount++;
				break;
			case 'lt':
				$contents .= $this->jumpCode( 'JLT', $this->labelCount );
				$this->labelCount++;
				break;
			case 'gt':
				$contents .= $this->jumpCode( 'JGT', $this->labelCount );
				$this->labelCount++;
				break;
			case 'and':
				$contents .= <<<EOD
				M=M&D \n
				EOD;
				break;
			case 'or':
				$contents .= <<<EOD
				M=M|D \n
				EOD;
				break;
			case 'not':
				$contents = '';
				$contents .= <<<EOD
				@SP
				AM=M-1
				M=!M \n
				EOD;
				break;
			case 'neg':
				$contents = '';
				$contents .= <<<EOD
				@SP
				AM=M-1
				M=-M \n
				EOD;
				break;
		}
		$contents .= <<<EOD
				@SP
				M=M+1 \n
				EOD;
		$contents = str_replace( "\t", '', $contents);
		fwrite($this->outputHandle, $contents . "\n");
	}

	/**
	 * Handles writing arithmetic comparisons where we need to jump.
	 *
	 * @param string $operator - the comparison operator.
	 * @param string $labelCount - the number of the label.
	 */
	private function jumpCode( $operator, $labelCount ) {
		return <<<EOD
				D=M-D
				@TRUE.{$labelCount}
				D;{$operator}
				@SP
				A=M
				M=0
				@END.{$labelCount}
				0;JMP
				(TRUE.{$labelCount})
				@SP
				A=M
				M=-1
				(END.{$labelCount}) \n
				EOD;
	}

	/**
	 * Writes the assembly code that is the translation of the given command, where command is either C_PUSH or C_POP.
	 *
	 * @param string $command - the raw command being parsed.
	 * @param string $arg1 - the first argument of the command.
	 * @param string $arg2 - the second argument of the command.
	 */
	private function writePushPop( $command, $arg1, $arg2 ) {
		$contents = '';
		switch ( $command ) {
			case 'push':
				$contents = $this->pushCommand( $arg1, $arg2 );
				break;
			case 'pop':
				$contents = $this->popCommand( $arg1, $arg2 );
				break;
		}
		fwrite($this->outputHandle, $contents . "\n");
	}

	/**
	 * Writes the given line to the output file as a comment.
	 */
	private function writeLine( $line ) {
		$line = "// $line";
		fwrite($this->outputHandle, $line . "\n");
	}

	/**
	 * Handles pushing to the stack.
	 *
	 * @param string $dest - the destination of the push command.
	 * @param string $num - the number associated with the push command.
	 */
	private function pushCommand( $dest, $num ) {
		$code = '';
		switch ( $dest ) {
			case 'constant':
				$code .= $this->pushConstant( $num );
				break;
			case 'pointer':
				$code .= $this->pushPointer( $dest, $num );
				break;
			case 'static':
				$code .= $this->pushStatic( $num );
				break;
			case 'temp':
				$dest = $this->destination[$dest] + $num;
				$code .= <<<EOD
				@{$dest}
				D=M \n
				EOD;
				break;
			default:
				$code .= <<<EOD
				@{$this->destination[$dest]}
				D=M
				@{$num}
				A=D+A
				D=M \n
				EOD;
		}
		$code .= <<<EOD
			@SP
			A=M
			M=D
			@SP
			M=M+1 \n
			EOD;
		return str_replace( "\t", '', $code);
	}

	/**
	 * Handle pushing a constant onto the stack
	 *
	 * @param string $num - the number associated with the push command.
	 */
	private function pushConstant( $num ) {
		return <<<EOD
		@{$num} 
		D=A \n
		EOD;
	}

	/**
	 * Handle pushing a pointer's value onto the stack.
	 *
	 * @param string $dest - the destination of the push command, this or that.
	 * @param string $num - the number associated with the pointer, 0 or 1.
	 */
	private function pushPointer( $dest, $num ) {
		$dest = ( $num === "0" ) ? "this" : "that";
		return <<<EOD
		@{$this->destination[$dest]}
		D=M \n
		EOD;
	}

	/**
	 * Handle pushing a static variable's value onto the stack.
	 *
	 * @param string $num - the number associated with the static variable.
	 */
	private function pushStatic( $num ) {
		return <<<EOD
		@{$this->filename}.{$num}
		D=M \n
		EOD;
	}

	/**
	 * Handles popping from the stack.
	 *
	 * @param string $dest - the destination of the pop command.
	 * @param string $num - the number associated with the pop command.
	 */
	private function popCommand( $dest, $num ) {
		$code = '';
		switch ( $dest ) {
			case 'pointer':
				$code .= $this->popPointer( $dest, $num );
				break;
			case 'static':
				$code .= $this->popStatic( $num );
				break;
			case 'temp':
				$code .= $this->popTemp( $dest, $num );
				break;
			default:
				$code .= str_replace(
					['{{dest}}', '{{num}}'],
					[$this->destination[$dest], $num],
					<<<EOD
					@{{dest}}
					D=M
					@{{num}}
					D=D+A
					@R13
					M=D
					@SP
					AM=M-1
					D=M
					@R13
					A=M
					M=D \n
					EOD
				);
		}
		return str_replace( "\t", '', $code);
	}

	/**
	 * Handle popping a pointer's value onto the stack.
	 *
	 * @param string $dest - the destination of the pop command, this or that.
	 * @param string $num - the number associated with the pointer, 0 or 1.
	 */
	private function popPointer( $dest, $num ) {
		$dest = ( $num === "0" ) ? "this" : "that";
		return <<<EOD
		@SP
		AM=M-1
		D=M
		@{$this->destination[$dest]}
		M=D \n
		EOD;
	}

	/**
	 * Handle popping a static variable's value onto the stack.
	 *
	 * @param string $num - the number associated with the static variable.
	 */
	private function popStatic( $num ) {
		return <<<EOD
		@SP
		AM=M-1
		D=M
		@{$this->filename}.{$num}
		M=D \n
		EOD;
	}

	/**
	 * Handle popping a temp variable's value onto the stack.
	 *
	 * @param string $dest - the destination of the pop command, temp.
	 * @param string $num - the offset associated with the temp variable.
	 */
	private function popTemp( $dest, $num ) {
		return <<<EOD
		@SP
		AM=M-1
		D=M
		@R13
		M=D
		@{$this->destination[$dest]}
		D=A
		@{$num}
		D=D+A
		@R14
		M=D
		@R13
		D=M
		@R14
		A=M
		M=D \n
		EOD;
	}

	public function infiniteLoop() {
		$contents = <<<EOD
		(END)
		@END
		0;JMP \n
		EOD;
		fwrite($this->outputHandle, $contents . "\n");
	}
}

?> 