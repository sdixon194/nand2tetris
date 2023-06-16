<?php 
include_once('SymbolTable.php');
include_once('VMWriter.php');
class CompilationEngine {
	private $argCount = 0;
	private $inIfExpression = false;
	private $inWhileExpression = false;
	private $labelIndex = 1;
	private $isNot = false;
	private $isNeg = false;
	private $isMethod = false; 
	private $isConstructor = false;
	private $currentFunction = '';
	private $argumentSize = 0;
	private $VMWriter = '';
	private $className = '';
	private $classTable = array();
	private $varTypes = array(
		'int',
		'char',
		'boolean',
		'void'
	);
	private $subroutineTable = array();
	private $file = '';
	private $lineIndex = '';
	private $lines = array();
	private $currentRawToken = '';
	private $currentTokenType = '';
	private $currentToken = '';
	private $nextRawToken = '';
	private $nextTokenType = '';
	private $nextToken = '';
	private $prevToken = '';

	/**
	 * Constructor
	 * @param string $outputFile The name of the file to write to.
	 */
	function __construct( $outputFile ) {
		$this->lineIndex = 0;
		$this->prepareFile( $outputFile );
		$this->VMWriter = new VMWriter( $outputFile );
		$this->classTable = new SymbolTable();
		$this->subroutineTable = new SymbolTable();
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
			$token = $matches[2];
			return array( $tokenType, $token );
		}
	}
	
	/** Advance the line index and set the current token and type */
	private function advance() {
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
					if ( $this->currentTokenType == 'identifier' ) {
						$this->className = $this->currentToken;
					}
					break;
			}
		}
	}

	/**
	 * Compile the class variable declaration.
	 */
	private function compileClassVarDec() {
		$writingClassVarDec = true;
		$classVarSet = array(
			'kind' => $this->currentToken,
			'type' => $this->nextToken,
			'name' => ''
		);
		while ( $writingClassVarDec ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ',':
					break;
				case ';':
					$writingClassVarDec = false;
					break;
				default:
					if ( $this->currentTokenType == 'identifier' ) {
						if ( in_array( $this->prevToken, $this->varTypes ) || $this->prevToken == ',' ) {
							$classVarSet['name'] = $this->currentToken;
							$this->classTable->define( $classVarSet['name'], $classVarSet['type'], $classVarSet['kind'] );
						} else {
							$this->varTypes[] = $this->currentToken;
						}
					}
					break;
			}
		}
	}

	/**
	 * Compile the subroutine.
	 */
	private function compileSubroutine() {
		$writingSubroutine = true;
		$this->isMethod = false;
		$this->isConstructor = false;
		$this->subroutineTable->reset();

		if ( $this->currentToken == 'method' ) {
			$this->subroutineTable->define( 'this', $this->className, 'arg' );
			$this->isMethod = true;
		}
		
		if ( $this->currentToken == 'constructor' ) {
			$this->isConstructor = true;
		}

		while ( $writingSubroutine ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '(':
					$this->compileParameterList();
				case ')':
					$this->compileSubroutineBody();
					$writingSubroutine = false;
					break;
				default:
					if ( $this->currentTokenType == 'identifier' && ! in_array($this->currentToken, $this->varTypes ) ) {
						$this->currentFunction = $this->currentToken;
					}
					break;
			}
		}
	}

	private function compileSubroutineBody() {
		$writingSubroutineBody = true;
		while ( $writingSubroutineBody ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '{':
					break;
				case '}':
					$writingSubroutineBody = false;
					break;
				case 'var':
					$this->compileVarDec();
					break;
				default:
					$this->VMWriter->writeFunction( $this->className . '.' . $this->currentFunction, $this->subroutineTable->varCount( 'var' ) );
					if ( $this->isMethod ) {
						$this->VMWriter->writePush( 'argument', 0 );
						$this->VMWriter->writePop( 'pointer', 0 );
					}
					if ( $this->isConstructor ) {
						// Get a count of all the fields in the class.
						$fieldCounter = 0;
						foreach( $this->classTable->table as $entry ) {
							if ( $entry['kind'] == 'field' ) {
								$fieldCounter++;
							}
						}
						$this->VMWriter->writePush( 'constant', $fieldCounter );
						$this->VMWriter->writeCall( 'Memory.alloc', 1 );
						$this->VMWriter->writePop( 'pointer', 0 );
					}
					$this->compileStatements();
					$writingSubroutineBody = false;
					break;
			}
		}
	}

	/**
	 * Compile the parameter list.
	 */
	private function compileParameterList() {
		$writingParameterList = true;
		while ( $writingParameterList ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ')':
					$writingParameterList = false;
					break;
				case ',':
					break;
				default:
					if ( $this->currentTokenType == 'identifier' && ! in_array($this->currentToken, $this->varTypes ) ) {
						$this->subroutineTable->define( $this->currentToken, $this->prevToken, 'arg' );
					}
					break;
			}
		}
	}

	/**
	 * Compile the variable declaration.
	 */
	private function compileVarDec() {
		$writingVarDec = true;
		$classVarSet = array(
			'kind' => $this->currentToken,
			'type' => $this->nextToken,
			'name' => ''
		);
		while ( $writingVarDec ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ';':
					$writingVarDec = false;
					break;
				case ',':
					break;
				default:
					if ( $this->currentTokenType == 'identifier' ) {
						if ( in_array( $this->prevToken, $this->varTypes ) || $this->prevToken == ',' ) {
							$classVarSet['name'] = $this->currentToken;
							$this->subroutineTable->define( $classVarSet['name'], $classVarSet['type'], $classVarSet['kind'] );
						} else {
							$this->varTypes[] = $this->currentToken;
						}
					}
					break;
			}
		}
	}

	/**
	 * Compile the statements.
	 */
	private function compileStatements() {
		$writingStatements = true;
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
					break;
			}
			if ( $writingStatements ) {
				$this->advance();
			}
		}
	}

	/**
	 * Compile the let statement.
	 */
	private function compileLet() {
		$writingToLet = true;
		$var = '';
		$arr = false;
		while ( $writingToLet ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case '=':
					$this->advance();
					$this->compileExpression();
					if ( $arr ) {
						$this->VMWriter->writePop( 'temp', 0 );
						$this->VMWriter->writePop( 'pointer', 1 );
						$this->VMWriter->writePush( 'temp', 0 );
						$this->VMWriter->writePop( 'that', 0 );
					} else {
						if ( $this->subroutineTable->kindOf( $var ) == 'var' || $this->subroutineTable->kindOf( $var ) == 'arg' ) {
							$this->VMWriter->writePop( $this->subroutineTable->kindOf( $var ), $this->subroutineTable->indexOf( $var ) );
						} elseif ( $this->classTable->kindOf( $var ) == 'field' ) {
							$this->VMWriter->writePop( 'this', $this->classTable->indexOf( $var ) );
						} elseif (  $this->classTable->kindOf( $var ) == 'static' ) {
							$this->VMWriter->writePop( 'static', $this->classTable->indexOf( $var ) );
						}
					}
					$writingToLet = false;
					break;
				case ';':
					$writingToLet = false;
					break;
				case '[':
					// Push array variable.
					if ( $this->subroutineTable->kindOf( $var ) == 'var' || $this->subroutineTable->kindOf( $var ) == 'arg' ) {
						$this->VMWriter->writePush( $this->subroutineTable->kindOf( $var ), $this->subroutineTable->indexOf( $var ) );
					} elseif ( $this->classTable->kindOf( $var ) == 'field' ) {
						$this->VMWriter->writePush( 'this', $this->classTable->indexOf( $var ) );
					} elseif (  $this->classTable->kindOf( $var ) == 'static' ) {
						$this->VMWriter->writePush( 'static', $this->classTable->indexOf( $var ) );
					}
					$this->handleArray();
					$this->advance();
					$this->compileExpression();
					$this->VMWriter->writePop( 'temp', 1 );
					$this->VMWriter->writePop( 'pointer', 1 );
					$this->VMWriter->writePush( 'temp', 1 );
					$this->VMWriter->writePop( 'that', 0 );
					$writingToLet = false;
					break;
				default:
					if ( $this->currentTokenType == 'identifier' ) {
							$var = $this->currentToken;
					}
					break;
			}
		}
	}

	/**
	 * Compile the if statement.
	 */
	private function compileIf() {
		$writingToIf = true;
		$L2 = 'label' . $this->labelIndex;
		$this->labelIndex++;
		$L1 = 'label' . $this->labelIndex;
		$this->labelIndex++;
		while ( $writingToIf ) {
			if ( $this->currentToken == '}' && $this->nextToken !== 'else' ) {
				$this->VMWriter->writeLabel( $L2 ); // L2
				$writingToIf = false;
				break;
			}
			$this->advance();
			if ( $this->currentToken == '{' && $this->prevToken == 'else' ) {
				$this->currentToken = 'else-cond';
			}
			if ( $this->currentToken == '{' && $this->prevToken == ')' ) {
				$this->currentToken = 'if-cond';
			}
			switch( $this->currentToken ) {
				case '(':
					$this->inIfExpression = true;
					$this->advance();
					$this->compileExpression(); 
					$this->VMWriter->writeArithmetic( 'not' );
					$this->VMWriter->writeIf( $L1 ); // if-goto L1
					$this->inIfExpression = false;
					break;
				// if statement condition.
				case 'if-cond':
					$this->advance();
					$this->compileStatements();
					$this->VMWriter->writeGoto( $L2 ); // goto L2
					$this->VMWriter->writeLabel( $L1 ); // L2
					break;
				case 'else-cond':
					$this->advance();
					$this->compileStatements();
					break;
				default:
					break;
			}
		}
		$this->inIfExpression = false;
	}

	/**
	 * Compile the while statement.
	 */
	private function compileWhile() {
		$writingToWhile = true;
		$this->writeHeaderOpen( 'whileStatement' );
		$this->addSpaces();
		$this->writeToken( $this->currentTokenType, $this->currentToken );
		$L1 = 'label' . $this->labelIndex;
		$this->labelIndex++;
		$L2 = 'label' . $this->labelIndex;
		$this->labelIndex++;
		while ( $writingToWhile ) {
			$this->advance();
			switch( $this->currentToken ) {
				case '(':
					$this->inWhileExpression = true;
					$this->VMWriter->writeLabel( $L1 );
					$this->advance();
					$this->compileExpression();
					$this->VMWriter->writeArithmetic( 'not' );
					$this->VMWriter->writeIf( $L2 );
					$this->inWhileExpression = false;
					break;
				case ')':
					$this->compileStatements();
					$this->VMWriter->writeGoto( $L1 );
					$this->VMWriter->writeLabel( $L2 );
					break;
				case '{':
					$this->advance();
					$this->compileStatements();
					$this->VMWriter->writeGoto( $L1 );
					$this->VMWriter->writeLabel( $L2 );
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
	}

	/**
	 * Compile the do statement.
	 */
	private function compileDo() {
		$writingToDo = true;
		$functionCall = '';
		while ( $writingToDo ) {
			$this->advance();
			switch( $this->currentToken ) {
				case '(':
					// If the function call doesn't include a . then it's a method call.
					if ( strpos( $functionCall, '.' ) === false ) {
						$functionCall = $this->className . '.' . $functionCall;
						$this->VMWriter->writePush( 'pointer', 0 );
						$this->argCount++;
					}
					$this->compileExpressionList();
					$this->VMWriter->writeCall( $functionCall, $this->argumentSize );
					$this->VMWriter->writePop( 'temp', 0 );
					$writingToDo = false;
					break;
				case ')':
					break;
				case ';':
					$writingToDo = false;
					break;
				default:
					$this->checkSubroutineTableFunction($this->currentToken);
					$this->checkClassTableFunction($this->currentToken);
					$functionCall .= $this->currentToken;
					break;
			}
		}
	}

	/**
	 * Compile the return statement.
	 */
	private function compileReturn() {
		$writingToReturn = true;
		while ( $writingToReturn ) {
			if ( $this->currentToken !== ';') {
				$this->advance();
			}
			switch( $this->currentToken ) {
				case 'this':
					$this->VMWriter->writePush( 'pointer', 0 );
					break;
				case ';':
					// This means that the return statement is empty and the function is returning void.
					if ( $this->prevToken == 'return' ) {
						$this->VMWriter->writePush( 'constant', 0 );
					}
					$this->VMWriter->writeReturn();
					$writingToReturn = false;
					break;
				default:
					$this->compileExpression();
					break;
			}
		}
	}

	/**
	 * Compile the expression.
	 */
	private function compileExpression() {
		$writingExpression = true;
		while ( $writingExpression ) {
			switch( $this->currentToken ) {
				case '-':
					if ( $this->prevToken == ',' || $this->prevToken == '(' || $this->prevToken == '=' || $this->prevToken == '[') {
						$this->isNeg = true;
						break;
					} else {
						$op = $this->currentToken;	
						$this->advance();
						$this->compileTerm();
						$this->VMWriter->writeArithmetic( $op );

						if ( $this->currentToken == ';' ) {
							$writingExpression = false;
							break;
						}
						break;
					}
				case '~':
					if ( $this->prevToken == ',' || $this->prevToken == '(' || $this->prevToken == '=' || $this->prevToken == '[') {
						$this->isNot = true;
						break;
					}
				case ')':
				case ';':
					if ( $this->nextToken == '/' ) {
						break;
					}
					$writingExpression = false;
					break;
				case '=':
					if ( $this->inIfExpression || $this->inWhileExpression ) {
						$this->advance();
						$this->compileExpression();
						$this->VMWriter->writeArithmetic( '=' );
						$writingExpression = false;
					}
					break;
				case '+':
				case '-':
				case '*':
				case '/':
				case '&':
				case '|':
				case '<':
				case '>':
				case '&lt;':
				case '&gt;':
				case '&amp;':
					$op = $this->currentToken;
					$this->advance();
					$this->compileTerm();
					$this->VMWriter->writeArithmetic( $op );
					if ($this->nextToken == '|' || $this->nextToken == '&amp;' || $this->nextToken == '/' ) { 
						break;
					}
					if ( $this->currentToken == ';' || $this->currentToken == ')' ) {
						$writingExpression = false;
						break;
					}
					break;
				case '[':
					$this->handleArray();
					$this->VMWriter->writePop( 'pointer', 1 );
					$this->VMWriter->writePush( 'that', 0 );
					break;
				case ',':
					$writingExpression = false;
					break;
				case ']':
					$writingExpression = false;
					break;
				default:
					$this->compileTerm();
					if ( $this->currentToken == ';' ) {
						$writingExpression = false;
					}
					break;
			}
			if ( $writingExpression ) {
				$this->advance();
			}
		}
	}

	private function handleArray() {
		$this->advance(); // a[ // advance inside bracket.
		$this->compileExpression();
		$this->VMWriter->writeArithmetic( 'add' );
	}
	/**
	 * Compile the term.
	 */
	private function compileTerm() {
		$writingTerm = true;
		while ( $writingTerm ) {
			// Handle unary operators and recursive expressions.
			if ( $this->currentToken == '(' ) {
					$localNeg = false;
					$localNot = false;
				if ( $this->prevToken == '-' ) {
					$localNeg = true;
				}
				if ( $this->prevToken == '~' ) {
					$localNot = true;
				}
				$this->advance();
				$this->compileExpression();
				if ( $localNeg ) {
					$this->VMWriter->writeArithmetic( 'neg' );
				}
				if ( $localNot ) {
					$this->VMWriter->writeArithmetic( 'not' );
				}
				$writingTerm = false;
				break;
			}

			// Handle function calls.
			if ( $this->nextToken == '.' ) {
				$this->checkSubroutineTableFunction($this->currentToken);
				$this->checkClassTableFunction($this->currentToken);
				$this->advance();
				$function = $this->prevToken . '.' . $this->nextToken;
				$this->advance();
				$this->advance();
				$this->compileExpressionList();
				$this->VMWriter->writeCall( $function, $this->argumentSize );
				$writingTerm = false;
				break;
			}

			if ( $this->currentToken == ';') {
				$writingTerm = false;
				break;
			}
			switch ( $this->currentTokenType ) {
				case 'integerConstant':
					$this->VMWriter->writePush( 'constant', $this->currentToken );
					if ( $this->isNeg && ( $this->prevToken == '-' ) ) {
						$this->VMWriter->writeArithmetic( 'neg' );
						$this->isNeg = false;
					}
					if ( $this->isNot && ( $this->prevToken == '~' ) ) {
						$this->VMWriter->writeArithmetic( 'not' );
						$this->isNot = false;
					}
					$writingTerm = false;
					break;
				case 'stringConstant':
					$this->VMWriter->writePush( 'constant', strlen( $this->currentToken ) );
					$this->VMWriter->writeCall( 'String.new', 1 );
					for ( $i = 0; $i < strlen( $this->currentToken ); $i++ ) {
						$this->VMWriter->writePush( 'constant', ord( $this->currentToken[ $i ] ) );
						$this->VMWriter->writeCall( 'String.appendChar', 2 );
					}
					$writingTerm = false;
					break;
				case 'keyword':
					switch ( $this->currentToken ) {
						case 'true':
							$this->VMWriter->writePush( 'constant', 1 );
							$this->VMWriter->writeArithmetic( 'neg' );
							$writingTerm = false;
							break;
						case 'false':
						case 'null':
							$this->VMWriter->writePush( 'constant', 0 );
							$writingTerm = false;
							break;
						case 'this':
							$this->VMWriter->writePush( 'pointer', 0 );
							$writingTerm = false;
							break;
						default:
							$writingTerm = false;
							break;
					}
					break;
				case 'identifier':
					$varFound = false;
					// Check if the current token is in the subroutine table array.
					foreach( $this->subroutineTable->table as $entry ) {
						if ( $entry['name'] == $this->currentToken ) {
							$varFound = true;
				
							$this->VMWriter->writePush( $entry['kind'], $entry['index'] );
							if ( $this->isNeg && ( $this->prevToken == '-' ) ) {
								$this->VMWriter->writeArithmetic( 'neg' );
								$this->isNeg = false;
							}
							if ( $this->isNot && ( $this->prevToken == '~' ) ) {
								$this->VMWriter->writeArithmetic( 'not' );
								$this->isNot = false;
							}
							
							if ( $this->nextToken == '[' ) {
								$this->advance();
								$this->handleArray();
								$this->VMWriter->writePop( 'pointer', 1 );
								$this->VMWriter->writePush( 'that', 0 );
							}
							$writingTerm = false;
							break;
						}
					}
					// Check if the current token is in the class table array if it's not in the function array.
					if ( ! $varFound ) {
						foreach( $this->classTable->table as $entry ){
							if ( $entry['name'] == $this->currentToken ) {
								if ( $entry['kind'] == 'field' ) {
									$this->VMWriter->writePush( 'this', $entry['index'] );
								} else {
									$this->VMWriter->writePush( $entry['kind'], $entry['index'] );
								}
								if ( $this->isNeg && ( $this->prevToken == '-' ) ) {
									$this->VMWriter->writeArithmetic( 'neg' );
									$this->isNeg = false;
								}
								if ( $this->isNot && ( $this->prevToken == '~' ) ) {
									$this->VMWriter->writeArithmetic( 'not' );
									$this->isNot = false;
								}

								if ( $this->nextToken == '[' ) {
									$this->advance();
									$this->handleArray();
									$this->VMWriter->writePop( 'pointer', 1 );
									$this->VMWriter->writePush( 'that', 0 );
								}

								$writingTerm = false;
								break;
							}
						}
					}
					break;
				default:
					break;
			}
			if ( $writingTerm ) {
				$this->advance();
			}
		}
	}

	private function checkSubroutineTableFunction ( $name ) {
		foreach( $this->subroutineTable->table as $entry ) {
			if ( $entry['name'] == $name ) {
				
				$this->VMWriter->writePush( $entry['kind'], $entry['index'] );
				$this->argCount++;
				$this->currentToken = $this->subroutineTable->typeOf( $name );
			}
		}
		return false;
	}

	private function checkClassTableFunction ( $name ) {
		foreach( $this->classTable->table as $entry ) {
			if ( $entry['name'] == $name ) {
				$this->VMWriter->writePush( 'this', $entry['index'] );
				$this->argCount++;
				$this->currentToken = $this->classTable->typeOf( $name );
			}
		}
		return false;
	}

	/**
	 * Compile the expression list.
	 */
	private function compileExpressionList() {
		$this->argumentSize = $this->argCount;
		$writingExpressionList = true;
		while ( $writingExpressionList ) {
			$this->advance();
			switch ( $this->currentToken ) {
				case ')':
				case ';':
					$writingExpressionList = false;
					break;
				case ',':
					$this->writeToken( $this->currentTokenType, $this->currentToken );
					break;
				default:
					$this->compileExpression();
					$this->argumentSize++;
					if ($this->currentToken == ')' ) {
						$writingExpressionList = false;
					}
					break;
			}
		}
		$this->argCount = 0;
	}

}

?>