<?php 

class JackTokenizer {
	private $file = '';
	private $outputHandle = '';
	private $keywords = array(
		'class',
		'constructor',
		'function',
		'method',
		'field',
		'static',
		'var',
		'int',
		'char',
		'boolean',
		'void',
		'true',
		'false',
		'null',
		'this',
		'let',
		'do',
		'if',
		'else',
		'while',
		'return'
	);
	private $symbols = array(
		'{',
		'}',
		'(',
		')',
		'[',
		']',
		'.',
		',',
		';',
		'+',
		'-',
		'*',
		'/',
		'&',
		'|',
		'<',
		'>',
		'=',
		'~'
	);

	function __construct( $file, $outputFile ) {
		$this->obtainFile( $file );
		$this->prepareFile();
		$this->outputHandle = fopen( $outputFile, 'w' );
	}

	/**
	 * Obtain the contents of the file.
	 * @param string $fileName The name of the file to open.
	 */
	private function obtainFile( $fileName ) {
		$fileHandle = fopen($fileName, 'r');
		if ( $fileHandle ) {
			$this->file = fread($fileHandle, filesize( $fileName ));
			fclose( $fileHandle );
		} else {
			echo 'Failed to open the file.';
		}
	}

	/**
	 * Prepare the file for tokenization.
	 */
	private function prepareFile() {
		$this->file = preg_replace('/\/\/.*/', '', $this->file);
		$this->file = preg_replace('/\/\*.*?\*\//s', '', $this->file);
		$this->file = str_replace("\r", '', $this->file);
		$this->file = trim($this->file);
	}

	/**
	 * Tokenize the file.
	 */
	public function tokenize() {
		// We need to iterate through the file character by character.
		// We will add the characters to a string and check that string against the token types.
		// If it matches a token type, that's our current token.
		// If it doesn't match a token type, we'll add the next character to the string and check again.
		$token = '';
		$stringToken = false;
		foreach( str_split( $this->file ) as $char ) {
			
			if ( $char == "\n" || $char == "\t" ) {
				continue;
			}

			// If the character is a double quote, we need to check if the token is a string constant.
			if ( $char == '"' ) {
				if ( ! $stringToken ) {
					$stringToken = true;
					$token .= $char;
					continue;
				} else {
					$stringToken = false;
					$token = ltrim( $token, '"' );
					$this->writeToken( 'stringConstant', $token );
					$token = '';
					continue;
				}
			}

			// If the character is a space, we need to check if the token is a keyword, symbol, integer constant, or string constant.
			if ( $char == ' ' && $stringToken == false ) {
				if ( $token == '' ) {
					continue;
				}

				if ( in_array( $token, $this->keywords ) ) {
					$this->writeToken( 'keyword', $token );
					$token = '';
					continue;
				} 
				
				if ( in_array( $token, $this->symbols ) ) {
					$this->writeToken( 'symbol', $token );
					$token = '';
					continue;
				}
				
				if ( ctype_digit( $token ) ) {
						$this->writeToken( 'integerConstant', $token );
						$token = '';
						continue;
				}
				
				if ( strpos( $token, '"' ) ) {
					$this->writeToken( 'stringConstant', $token );
					$token = '';
					continue;
				}

				// If the token is not a keyword or symbol, it's an identifier.
				$this->writeToken( 'identifier', $token );
				$token = '';
				continue;
			}

			// If the character isn't a space or double quote, append it to the token.
			$token .= $char;

			// Now we need to check if the token is a complete token.

			// Check if the token is a keyword.
			if ( in_array( $token, $this->keywords ) ) {
				$this->writeToken( 'keyword', $token );
				$token = '';
				continue;
			}

			// If the current character is a symbol, we need to check if the preceding string is a token.
			if ( in_array( $char, $this->symbols ) ) {
				if ( strlen( $token ) > 1 ) {
					$prevToken = substr( $token, 0, -1 );
					if ( ctype_digit( $prevToken ) ) {
						$this->writeToken( 'integerConstant',  $prevToken );
					} else {
						$this->writeToken( 'identifier', $prevToken );
					}
				}
				$char = $this->processSymbol( $char );
				$this->writeToken( 'symbol', $char );
				$token = '';
				continue;
			}
		}
	}

	private function writeToken( $tokenType, $token ) {
		fwrite( $this->outputHandle, "<$tokenType> $token </$tokenType>\n" );
	}

	private function processSymbol( $char ) {
		switch ( $char ) {
			case '<':
				return '&lt;';
				break;
			case '>':
				return '&gt;';
				break;
			case '&':
				return '&amp;';
				break;
			default:
				return $char;
				break;
		}
	}

	public function closeFile() {
		fclose( $this->outputHandle );
	}
}
?>
