// This file is part of www.nand2tetris.org
// and the book "The Elements of Computing Systems"
// by Nisan and Schocken, MIT Press.
// File name: projects/04/Mult.asm

// Multiplies R0 and R1 and stores the result in R2.
// (R0, R1, R2 refer to RAM[0], RAM[1], and RAM[2], respectively.)
//
// This program only needs to handle arguments that satisfy
// R0 >= 0, R1 >= 0, and R0*R1 < 32768.

// Put your code here.
@R2
M=0

@R0 // 7
D=M // D=7
@END
D;JEQ // if R0=0 goto END

@R1
D=M
@END
D;JEQ // if R1=0 goto END

(LOOP)

	@R1 // 3
	M=M-1 // R1=R1-1
	D=M
	@END
	D;JLT // if R1=0 goto END

	@R0 // 7
	D=M // D=7

	@R2
	M=M+D // R2=R2+D

	@LOOP
	0;JMP // if R1>0 goto LOOP

(END)
	@END
	0;JMP // goto END