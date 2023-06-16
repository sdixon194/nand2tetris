// function SimpleFunction.test 2
(SimpleFunction.test) 
@SP
A=M
M=0
@SP
M=M+1 
@SP
A=M
M=0
@SP
M=M+1 

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

// push local 1
@LCL
D=M
@1
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

// not
@SP
AM=M-1
M=!M 
@SP
M=M+1 

// push argument 0
@ARG
D=M
@0
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

// return
@LCL
D=M
@frame
M=D
@5
D=D-A // Frame - 5

A=D
D=M // D = *(Frame - 5)
@RET
M=D // retAddr = *(Frame - 5)

@SP
AM=M-1
D=M
@ARG
A=M
M=D // *ARG = pop()

@ARG
D=M+1
@SP
M=D // SP = ARG + 1

@frame
AM=M-1
D=M
@THAT
M=D // THAT = *(Frame - 1)

@frame
AM=M-1
D=M
@THIS
M=D // THIS = *(Frame - 2)

@frame
AM=M-1
D=M
@ARG
M=D // ARG = *(Frame - 3)

@frame
AM=M-1
D=M
@LCL
M=D // LCL = *(Frame - 4)

@RET
A=M
0;JMP 

(END)
@END
0;JMP 

