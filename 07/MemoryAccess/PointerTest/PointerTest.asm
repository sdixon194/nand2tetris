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

