<?php 
class CompilationEngine {
	private $outputHandle = '';
	private $file = '';
	private $lineIndex = '';
	private $lines = array();
	private $currentRawToken = '';
	private $currentTokenType = '';
	private $currentToken = '';
	private $nextRawToken = '';
	private $nextTokenType = '';
	private $nextToken = '';
	private $prevRawToken = '';
	private $prevTokenType = '';
	private $prevToken = '';
	private $tabs = '';
	private $termSymbols = array(
		')',
		';',
		']',
		'+',
		'-',
		'*',
		'/',
		'&',
		'|',
		'<',
		'>',
		'=',
		',',
		'}',
		'&lt;',
		'&gt;',
		'&amp;'
	);

	/**
	 * Constructor
	 * @param string $outputFile The name of the file to write to.
	 */
	function __construct( $outputFile ) {
		$this->lineIndex = 0;
		$this->prepareFile( $outputFile );
		$this->outputHandle = fopen( $outputFile, 'w' );
	}

	/** Adds spaces */
	private function addSpaces() {
		$this->tabs .= '  ';
	}


	/** Removes spaces */
	private function removeSpaces() {
		$this->tabs = substr( $this->tabs, 0, -2 );
	}

	/**
	 * Prepare the file for tokenizing.
	 * @param string $fileName The name of the file to open.
	 */
	private function prepareFile( $fileName ) {
		$fileHandle = fopen( $fileName, 'r' );
		if ( $fileHandle ) {
			$this->file = fread( $fileHandle, filesize( $fileName ) );
			fclose( $fileHandle );
		} else {
			echo 'Failed to open the file.';
		}
	}

	/**
	 * Start compiling the file.
	 */
	public function startCompile() {
		// Split the file into lines.
		$this->lines = explode( "\n", $this->file );
		$this->fileSize = count( $this->lines );
		$currentLine = $this->lines[ $this->lineIndex ];
		$nextLine = $this->lines[ $this->lineIndex + 1 ];
		$this->currentRawToken = $this->getToken( $currentLine );
		$this->currentTokenType = $this->currentRawToken[0];
		$this->currentToken = $this->currentRawToken[1];

		$this->nextRawToken = $this->getToken( $nextLine );
		$this->nextTokenType = $this->nextRawToken[0];
		$this->nextToken = $this->nextRawToken[1];
		$this->compileClass();
	}

	/** Return the tokentype and current token
	 * @param string $line The line to tokenize.
	 */
	private function getToken( $line ){
		if (preg_match('/<([^>]+)>\s*([^<]+)/', $line, $matches)) {
			$tokenType = $matches[1];
			$token = trim($matches[2]);
			return array( $tokenType, $token );
		}
	}
	
	/** Advance the line index and set the current token and type */
	private function advance() {
		$this->prevRawToken = $this->currentRawToken;
		$this->prevTokenType = $this->currentTokenType;
		$this->prevToken = $this->currentToken;
		$this->lineIndex++;
		if ( isset( $this->lines[ $this->lineIndex ] ) ) {
			$currentLine = $this->lines[ $this->lineIndex ];
			$this->currentRawToken = $this->getToken( $currentLine );
			if ( is_array( $this->currentRawToken ) ) {
				$this->currentTokenType = $this->currentRawToken[0];
				$this->currentToken = $this->currentRawToken[1];
			} else {
				$this->currentTokenType = '';
				$this->currentToken = '';
			}
		} else {
			$this->currentRawToken = '';
			$this->currentTokenType = '';
			$this->currentToken = '';
		}
		
		if ( isset( $this->lines[ $this->lineIndex + 1 ] ) ) {
			$nextLine = $this->lines[ $this->lineIndex + 1 ];
			$this->nextRawToken = $this->getToken( $nextLine );
			if ( is_array( $this->nextRawToken ) ) {
				$this->nextTokenType = $this->nextRawToken[0];
				$this->nextToken = $this->nextRawToken[1];
			} else {
				$this->nextTokenType = '';
				$this->nextToken = '';
			}
		} else {
			$this->nextRawToken = '';
			$this->nextTokenType = '';
			$this->nextToken = '';
		}
	}

	/**
	 * Compile the class.
	 */
	private function compileClass() {
		$writingClass = true;
		$this->writeHeaderOpen( 'class' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingClass ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '{':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case 'static':
				case 'field':
					$this->compileClassVarDec();
					break;
				case 'constructor':
				case 'function':
				case 'method':
					$this->compileSubroutine();
					break;
				case '}':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case '':
					$writingClass = false;
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'class' );
	}

	/**
	 * Compile the class variable declaration.
	 */
	private function compileClassVarDec() {
		$writingClassVarDec = true;
		$this->writeHeaderOpen( 'classVarDec' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingClassVarDec ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ',':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case ';':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingClassVarDec = false;
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'classVarDec' );
	}

	/**
	 * Compile the subroutine.
	 */
	private function compileSubroutine() {
		$writingSubroutine = true;
		$this->writeHeaderOpen( 'subroutineDec' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingSubroutine ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '(':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->compileParameterList();
				case ')':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->compileSubroutineBody();
					$writingSubroutine = false;
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'subroutineDec' );
	}

	private function compileSubroutineBody() {
		$writingSubroutineBody = true;
		$this->writeHeaderOpen( 'subroutineBody' );
		$this->addSpaces();
		while ( $writingSubroutineBody ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '{':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case '}':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingSubroutineBody = false;
					break;
				case 'var':
					$this->compileVarDec();
					break;
				default:
					$this->compileStatements();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingSubroutineBody = false;
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'subroutineBody' );
	}

	/**
	 * Compile the parameter list.
	 */
	private function compileParameterList() {
		$writingParameterList = true;
		$this->writeHeaderOpen( 'parameterList' );
		$this->addSpaces();
		while ( $writingParameterList ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ')':
					$writingParameterList = false;
					break;
				case ',':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'parameterList' );
	}

	/**
	 * Compile the variable declaration.
	 */
	private function compileVarDec() {
		$writingVarDec = true;
		$this->writeHeaderOpen( 'varDec' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingVarDec ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ';':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingVarDec = false;
					break;
				case ',':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'varDec' );
	}

	/**
	 * Compile the statements.
	 */
	private function compileStatements() {
		$writingStatements = true;
		$this->writeHeaderOpen( 'statements' );
		$this->addSpaces();
		while ( $writingStatements ) {
			switch ( $this->currentToken ) {
				case '}':
					$writingStatements = false;
					break;
				case 'let':
					$this->compileLet();
					break;
				case 'if':
					$this->compileIf();
					break;
				case 'while':
					$this->compileWhile();
					break;
				case 'do':
					$this->compileDo();
					break;
				case 'return':
					$this->compileReturn();
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
			if ( $writingStatements ) {
				$this->advance();
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'statements' );
	}

	/**
	 * Compile the let statement.
	 */
	private function compileLet() {
		$writingToLet = true;
		$this->writeHeaderOpen( 'letStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingToLet ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '=':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingToLet = false;
					break;
				case ';':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingToLet = false;
					break;
				case '[':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'letStatement' );
	}

	/**
	 * Compile the if statement.
	 */
	private function compileIf() {
		$writingToIf = true;
		$this->writeHeaderOpen( 'ifStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingToIf ) {
			if ( $this->currentToken == '}' && $this->nextToken !== 'else' ) {
				$writingToIf = false;
				break;
			}
			$this->advance();
			switch( $this->currentToken ) {
				case '(':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case ')':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->compileStatements();
					break;
				case 'else':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileStatements();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case '{':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileStatements();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'ifStatement' );
	}

	/**
	 * Compile the while statement.
	 */
	private function compileWhile() {
		$writingToWhile = true;
		$this->writeHeaderOpen( 'whileStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingToWhile ) {
			$this->advance();
			switch( $this->currentToken ) {
				case '(':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case ')':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->compileStatements();
					break;
				case '{':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileStatements();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
			if ( $this->currentToken == '}' ) {
				$writingToWhile = false;
				break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'whileStatement' );
	}

	/**
	 * Compile the do statement.
	 */
	private function compileDo() {
		$writingToDo = true;
		$this->writeHeaderOpen( 'doStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingToDo ) {
			$this->advance();
			switch( $this->currentToken ) {
				case '(':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->compileExpressionList();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case ')':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case ';':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingToDo = false;
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'doStatement' );
	}

	/**
	 * Compile the return statement.
	 */
	private function compileReturn() {
		$writingToReturn = true;
		$this->writeHeaderOpen( 'returnStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		while ( $writingToReturn ) {
			$this->advance();
			switch( $this->currentToken ) {
				case ';':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingToReturn = false;
					break;
				default:
					$this->compileExpression();
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'returnStatement' );
	}

	/**
	 * Compile the expression.
	 */
	private function compileExpression() {
		$writingExpression = true;
		$this->writeHeaderOpen( 'expression' );
		$this->addSpaces();
		while ( $writingExpression ) {
			if ( ( $this->currentToken == '-' || $this->currentToken == '~' ) && $this->prevToken == '(' ) { // If the previous token was an opening paren, then this is a unary operator.
				$this->compileTerm();
				$writingExpression = false;
				break;
			}
			switch ( $this->currentToken ) {
				case ')':
				case ';':
				case ']':
					$writingExpression = false;
					break;
				case '+':
				case '-':
				case '*':
				case '/':
				case '&':
				case '|':
				case '<':
				case '>':
				case '=':
				case '&lt;':
				case '&gt;':
				case '&amp;':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case '(':
					$this->compileTerm();
					break;
				case '~':
					$this->compileTerm();
					break;
				default:
					$this->compileTerm();
					break;
			}
			if ( $this->nextToken == ')' || $this->nextToken == ';' || $this->nextToken == ']' || $this->nextToken == ',' ) {
				$writingExpression = false;
				break;
			}

			if ( $writingExpression ) {
				$this->advance();
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'expression' );
	}

	/**
	 * Compile the term.
	 */
	private function compileTerm() {
		$writingToTerm = true;
		$unary = false;
		$functionCall = false;
		$this->writeHeaderOpen( 'term' );
		$this->addSpaces();
		while ( $writingToTerm ) {
			if ( ( $this->currentToken == '-' || $this->currentToken == '~' ) && $this->prevToken == '(' ) { // If the previous token was an opening paren, then this is a unary operator.
				$this->writeToken( $this->currentTokenType, $this->currentToken );
				$this->advance();
				$this->compileTerm(); // enter here, we are j
				$writingToTerm = false;
				break;
			}
			if ( $this->currentToken == '.' ) {
				$functionCall = true;
			}

			switch ( $this->currentToken ) {
				case '(':
					if ( $functionCall ) {
						$functionCall = false;
						$this->writeToken( $this->currentTokenType, $this->currentToken );
						$this->compileExpressionList();
						$this->writeToken( $this->currentTokenType, $this->currentToken );
						$writingToTerm = false;
						break;
					}
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$writingToTerm = false;
					break;
				case '[':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					$this->advance();
					$this->compileExpression();
					$this->advance();
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				case '~':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
			}
			if (  in_array( $this->nextToken, $this->termSymbols ) ) {
				$writingToTerm = false;
				break;
			}
			if ( $writingToTerm ){
				$this->advance();
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'term' );
	}


	/**
	 * Compile the expression list.
	 */
	private function compileExpressionList() {
		$writingExpressionList = true;
		$this->writeHeaderOpen( 'expressionList' );
		$this->addSpaces();
		while ( $writingExpressionList ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ')':
					$writingExpressionList = false;
					break;
				case ',':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->compileExpression();
					break;
			}
		}
		$this->removeSpaces();
		$this->writeHeaderClose( 'expressionList' );
	}

	/**
	 * Write the token to the output file.
	 * @param string $tokenType The type of token.
	 * @param string $token The token.
	 */
	private function writeToken( $tokenType, $token ) {
		fwrite( $this->outputHandle, "$this->tabs" . "<$tokenType> $token </$tokenType>\n" );
	}

	/**
	 * Write the opening XML tag to the output file.
	 * @param string $header The header to write.
	 */
	private function writeHeaderOpen( $header ) {
		fwrite( $this->outputHandle, "$this->tabs" . "<$header>\n" );
	}

	/**
	 * Close out the XML tag to the output file.
	 * @param string $header The header to write.
	 */
	private function writeHeaderClose( $header ) {
		fwrite( $this->outputHandle, "$this->tabs" . "</$header>\n" );
	}
}

?>