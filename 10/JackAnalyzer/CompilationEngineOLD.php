<?php 
class CompilationEngine {
	private $outputHandle = '';
	private $file = '';
	private $fileSize = 0;
	private $currentLine = '';
	private $lineIndex = '';
	private $lines = array();
	private $currentToken = '';
	private $writingToClassVarDec = false;
	private $writingToParameter = false;
	private $writingToSubroutine = false;
	private $writingSubroutineBody = false;
	private $writingToVarDec = false;
	private $writingStatements = false;
	private $writingToLet = false;
	private $writingExpression = false;
	private $writeToTerm = false;
	private $writingToDo = false;
	private $writingtoExpressionList = false;
	private $writingToReturn = false;
	private $writingToIf = false;
	private $writingToWhile = false;
	private $prevToken = '';
	private $tabs = '';

	function __construct( $outputFile ) {
		$this->lineIndex = 0;
		$this->prepareFile( $outputFile );
		$this->outputHandle = fopen( $outputFile, 'w' );
	}

	private function addSpaces() {
		$this->tabs .= '  ';
	}

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

	public function startCompile() {
		// Split the file into lines.
		$this->lines = explode( "\n", $this->file );
		$this->fileSize = count( $this->lines );
		$this->currentLine = $this->lines[ $this->lineIndex ];
		$this->currentToken = $this->getToken();
		$this->compileClass();
	}

	private function getToken(){
		if (preg_match('/<([^>]+)>\s*([^<]+)/', $this->currentLine, $matches)) {
			$tokenType = $matches[1];
			$token = trim($matches[2]);
			return array( $tokenType, $token );
		}
	}
	
	private function advance() {
		$this->prevToken = $this->currentToken;
		$this->lineIndex++;
		if ( $this->lineIndex + 1 >= $this->fileSize ) {
			return;
		}
		$this->currentLine = $this->lines[ $this->lineIndex ];
		$this->currentToken = $this->getToken();
		switch( $this->currentToken[0] ) {
			case 'keyword':
				$this->compileKeyword();
				break;
			case 'symbol':
				$this->compileSymbol();
				break;
			case 'integerConstant':
				$this->compileIntegerConstant();
				break;
			case 'stringConstant':
				$this->compileStringConstant();
				break;
			case 'identifier':
				$this->compileIdentifier();
				break;
		}
	}

	private function compileKeyword() {
		$token = $this->currentToken[1];
		switch( $token ) {
			case 'class':
				$this->compileClass();
				break;
			case 'constructor':
			case 'function':
			case 'method':
			case 'void':
				$this->compileSubroutine();
				break;
			case 'static':
			case 'field':
				$this->compileClassVarDec();
				break;
			case 'int':
			case 'char':
			case 'boolean':
			case 'var':
				$this->compileVarDec();
				break;
			case 'let':
				$this->compileLet();
				break;
			case 'do':
				$this->compileDo();
				break;
			case 'if':
			case 'else':
				$this->compileIf();
				break;
			case 'while':
				$this->compileWhile();
				break;
			case 'return':
				$this->compileReturn();
				break;
		}		
	}
	private function compileSymbol() {
		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];

		if ( $this->writingToLet && $token == '=' ) {
			$this->writeToken( $tokenType, $token );
			$this->compileExpression();
			return;
		}

		if ( $this->writeToTerm && ( $token == ';' || $token == ')' ) ) {
			$this->writeToTerm = false;
			$this->writingExpression = false;
			return;
		}
		if ( ( $this->writingToVarDec || $this->writingToDo ) && $token == ';' ) {
			$this->writeToken( $tokenType, $token );
			$this->writingToVarDec = false;
			$this->writingToDo = false;
			return;
		}

		if ( ( $this->writingToParameter || $this->writingtoExpressionList ) && $token == ')' ) {
			$this->writingtoExpressionList = false;
			$this->writingToParameter = false;
			return;
		}

		if ( $this->writingToIf && $token == '{' ) {
			$this->writeToken( $tokenType, $token );
			$this->writingStatements = false;
			$this->compileStatements();
			$this->advance();
			return;
		}

		if ( $this->writingToSubroutine && $token == '{' ) {
			$this->compileSubroutineBody();
			return;
		}

		if ( $this->writingToSubroutine && $token == '(' ) {
			$this->writeToken( $tokenType, $token );
			if ( $this->writingToDo ) {
				$this->compileExpressionList();
				return;
			} elseif ( $this->writingToIf ) {
				$this->compileExpression();
				return;
			} else {
				$this->compileParameterList();
				return;
			}
		}

		if ( $this->writingSubroutineBody && $token == '{' ) {
			$this->writeToken( $tokenType, $token );
			$this->compileParameterList();
			return;
		}

		if ( ( $this->writingToClassVarDec || $this->writingToLet || $this->writingToReturn ) && $token === ';' ) {
			$this->writeToken( $tokenType, $token );
			$this->writingToClassVarDec = false;
			$this->writingToLet = false;
			$this->writingToReturn = false;
			return;
		}

		if ( $this->writingToIf && $token == '}' ) {
			$this->compileStatements();
			$this->writeToken( $tokenType, $token );
			$nextIndex = $this->lineIndex + 1;
			if ( $this->lines[ $nextIndex ] ) {
				$nextToken = $this->lines[ $nextIndex ];
				if ( ! strpos($nextToken, 'else' ) ) {
					$this->writingToIf = false;
				}
			}
			return;
		}

		if ( $this->writingSubroutineBody && $token == '}' ) {
			if ( $this->writingStatements ) {
				$this->compileStatements();
				$this->writingStatements = false;
			}
			$this->writeToken( $tokenType, $token );
			$this->writingSubroutineBody = false;
			return;
		}

		$this->writeToken( $tokenType, $token );
		$this->advance();
	}

	private function compileIdentifier() {
		// If we're returning, we want to create an expression.
		if ( $this->writingToReturn ) {
			$this->compileExpression();
			return;
		}
		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		$this->advance();
	}

	private function compileClass() {
		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeHeaderOpen( $token );
		$this->addSpaces();
		$this->writeToken( $tokenType, $token );
		$this->advance();
		$this->removeSpaces();
		$this->writeHeaderClose( $token );
	}

	private function compileClassVarDec() {
		if ( ! $this->writingToClassVarDec ) {
			$this->writeHeaderOpen( "classVarDec" );
			$this->addSpaces();
			$this->writingToClassVarDec = true;
		}

		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );

		while ( $this->writingToClassVarDec ) {
			$this->advance();
		}
		$this->removeSpaces();
		$this->writeHeaderClose( "classVarDec" );
		$this->advance();
	}

	private function compileSubroutine() {
		if ( ! $this->writingToSubroutine ) {
			$this->writeHeaderOpen( "subroutineDec" );
			$this->addSpaces();
			$this->writingToSubroutine = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}
		
		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );
		while ( $this->writingToSubroutine ) {
			$this->advance();
		}
		$this->removeSpaces();
		$this->writeHeaderClose( "subroutineDec" );
		$this->advance();
	}

	private function compileSubroutineBody() {
		if ( ! $this->writingSubroutineBody ) {
			$this->writeHeaderOpen( "subroutineBody" );
			$this->addSpaces();
			$this->writingSubroutineBody = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );
		while ( $this->writingSubroutineBody ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "subroutineBody" );
		if ( $this->writingToSubroutine ) {
			$this->writingToSubroutine = false;
			return;
		}
		$this->advance();
	}

	private function compileParameterList() {
		if ( ! $this->writingToParameter ) {
			$this->writeHeaderOpen( "parameterList" );
			$this->addSpaces();
			$this->writingToParameter = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}
		
		while ( $this->writingToParameter ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "parameterList" );
		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );
		$this->advance();
	}

	private function compileVarDec() {
		if ( $this->writingToClassVarDec || $this->writingToParameter || $this->writingtoExpressionList ) {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}
		if ( ! $this->writingToVarDec ) {
			$this->writeHeaderOpen( "varDec" );
			$this->addSpaces();
			$this->writingToVarDec = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );
		while ( $this->writingToVarDec ) {
			$this->advance();
		}
		$this->removeSpaces();
		$this->writeHeaderClose( "varDec" );
		$this->advance();
	}

	private function compileStatements() {
		if ( ! $this->writingStatements ) {
			$this->writeHeaderOpen( "statements" );
			$this->addSpaces();
			$this->writingStatements = true;
			return;
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "statements" );
		return;
	}

	private function compileLet() {
		if ( ! $this->writingStatements ) {
			$this->compileStatements();
		} 

		if ( ! $this->writingToLet ) {
			$this->writeHeaderOpen( "letStatement" );
			$this->addSpaces();
			$this->writingToLet = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		while ( $this->writingToLet ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "letStatement" );
		$this->advance();
	}

	private function compileIf() {
		if ( ! $this->writingStatements ) {
			$this->compileStatements();
		} 

		if ( ! $this->writingToIf ) {
			$this->writeHeaderOpen( "ifStatement" );
			$this->addSpaces();
			$this->writingToIf = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		while ( $this->writingToIf ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "ifStatement" );
		$this->advance();
	}

	private function compileWhile() {

	}

	private function compileDo() {
		if ( ! $this->writingStatements ) {
			$this->compileStatements();
		}

		if ( ! $this->writingToDo ) {
			$this->writeHeaderOpen( "doStatement" );
			$this->addSpaces();
			$this->writingToDo = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		while ( $this->writingToDo ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "doStatement" );
		$this->advance();
	}

	private function compileReturn() {
		if ( ! $this->writingStatements ) {
			$this->compileStatements();
		}

		if ( ! $this->writingToReturn ) {
			$this->writeHeaderOpen( "returnStatement" );
			$this->addSpaces();
			$this->writingToReturn = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		while ( $this->writingToReturn ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "returnStatement" );
		$this->advance();
	}

	private function compileExpression() {
		if ( ! $this->writingExpression ) {
			$this->writeHeaderOpen( "expression" );
			$this->addSpaces();
			$this->writingExpression = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}

		while ( $this->writingExpression ) {
			$this->compileTerm();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "expression" );
		$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
		if ( $this->writingToLet ) {
			$this->writingToLet = false;
			return;
		}
		$this->advance();
	}

	private function compileTerm() {
		if ( ! $this->writeToTerm ) {
			$this->writeHeaderOpen( "term" );
			$this->addSpaces();
			$this->writeToTerm = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}


		while ( $this->writeToTerm ) {
			if ( $this->writingToReturn ) {
				$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			}
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "term" );
	}

	private function compileExpressionList() {
		if ( ! $this->writingtoExpressionList ) {
			$this->writeHeaderOpen( "expressionList" );
			$this->addSpaces();
			$this->writingtoExpressionList = true;
		} else {
			$this->writeToken( $this->currentToken[0], $this->currentToken[1] );
			return;
		}
		
		while ( $this->writingtoExpressionList ) {
			$this->advance();
		}

		$this->removeSpaces();
		$this->writeHeaderClose( "expressionList" );
		$token = $this->currentToken[1];
		$tokenType = $this->currentToken[0];
		$this->writeToken( $tokenType, $token );
		$this->advance();
	}

	private function writeToken( $tokenType, $token ) {
		fwrite( $this->outputHandle, "$this->tabs" . "<$tokenType> $token </$tokenType>\n" );
	}

	private function writeHeaderOpen( $header ) {
		fwrite( $this->outputHandle, "$this->tabs" . "<$header>\n" );
	}

	private function writeHeaderClose( $header ) {
		fwrite( $this->outputHandle, "$this->tabs" . "</$header>\n" );
	}
}

?>