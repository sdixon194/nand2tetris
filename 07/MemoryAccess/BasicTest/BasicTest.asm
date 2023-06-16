// push constant 10
@10 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop local 0
@LCL
D=M
@0
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push constant 21
@21 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 22
@22 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop argument 2
@ARG
D=M
@2
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// pop argument 1
@ARG
D=M
@1
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push constant 36
@36 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop this 6
@3
D=M
@6
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push constant 42
@42 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 45
@45 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop that 5
@4
D=M
@5
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// pop that 2
@4
D=M
@2
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push constant 510
@510 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop temp 6
@SP
AM=M-1
D=M
@R13
M=D
@5
D=A
@6
D=D+A
@R14
M=D
@R13
D=M
@R14
A=M
M=D 

// push local 0
@LCL
D=M
@0
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// push that 5
@4
D=M
@5
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
@SP
M=M+1 

// push argument 1
@ARG
D=M
@1
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// sub
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M-D 
@SP
M=M+1 

// push this 6
@3
D=M
@6
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// push this 6
@3
D=M
@6
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
@SP
M=M+1 

// sub
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M-D 
@SP
M=M+1 

// push temp 6
@11
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
@SP
M=M+1 

// push constant 3030
@3030 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop pointer 0 
@SP
AM=M-1
D=M
@3
M=D 

// push constant 3040
@3040 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop pointer 1
@SP
AM=M-1
D=M
@4
M=D 

// push constant 32
@32 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop this 2
@3
D=M
@2
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push constant 46
@46 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// pop that 6
@4
D=M
@6
D=D+A
@R13
M=D
@SP
AM=M-1
D=M
@R13
A=M
M=D 

// push pointer 0
@3
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// push pointer 1
@4
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
@SP
M=M+1 

// push this 2
@3
D=M
@2
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// sub
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M-D 
@SP
M=M+1 

// push that 6
@4
D=M
@6
A=D+A
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
@SP
M=M+1 

(END)
@END
0;JMP 

