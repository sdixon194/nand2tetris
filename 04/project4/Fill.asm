// This file is part of www.nand2tetris.org
// and the book "The Elements of Computing Systems"
// by Nisan and Schocken, MIT Press.
// File name: projects/04/Fill.asm

// Runs an infinite loop that listens to the keyboard input.
// When a key is pressed (any key), the program blackens the screen,
// i.e. writes "black" in every pixel;
// the screen should remain fully black as long as the key is pressed. 
// When no key is pressed, the program clears the screen, i.e. writes
// "white" in every pixel;
// the screen should remain fully clear as long as no key is pressed.

// Put your code here.


@R0
M=0

(LOOP)
	@KBD
	D=M
	@WHITE 
	D;JEQ
	@BLACK
	0;JMP

(BLACK)
	@R0
	D=M
	@8192
	D=D-A 
	@LOOP
	D;JGE

	@R0
	D=M

	@SCREEN
	A=D+A
	M=-1

	@R0
	M=M+1
	@LOOP
	0;JMP
(WHITE)
	@R0
	D=M
	@LOOP
	D;JLT

	@R0
	D=M

	@SCREEN
	A=D+A
	M=0

	@R0
	M=M-1
	@LOOP
	0;JMP
	
(END)
@END
0;JMP