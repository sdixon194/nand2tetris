@256
D=A
@SP
M=D 

@RETURN.Sys.init.0
D=A
@SP
A=M
M=D
@SP
M=M+1

@LCL
D=M
@SP
A=M
M=D
@SP
M=M+1

@ARG
D=M
@SP
A=M
M=D
@SP
M=M+1

@THIS
D=M
@SP
A=M
M=D
@SP
M=M+1

@THAT
D=M
@SP
A=M
M=D
@SP
M=M+1

D=M
@0
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Sys.init
0;JMP

(RETURN.Sys.init.0) 

// function Main.fibonacci 0
(Main.fibonacci) 

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

// push constant 2
@2 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// lt
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.0
D;JLT
@SP
A=M
M=0
@END.0
0;JMP
(TRUE.0)
@SP
A=M
M=-1
(END.0) 
@SP
M=M+1 

// if-goto IF_TRUE
@SP
AM=M-1
D=M
@Main.fibonacci$IF_TRUE
D;JNE 

// goto IF_FALSE
@Main.fibonacci$IF_FALSE
0;JMP 

// label IF_TRUE
(Main.fibonacci$IF_TRUE) 

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

// label IF_FALSE
(Main.fibonacci$IF_FALSE) 

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

// push constant 2
@2 
D=A 
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

// call Main.fibonacci 1
@RETURN.Main.fibonacci.1
D=A
@SP
A=M
M=D
@SP
M=M+1

@LCL
D=M
@SP
A=M
M=D
@SP
M=M+1

@ARG
D=M
@SP
A=M
M=D
@SP
M=M+1

@THIS
D=M
@SP
A=M
M=D
@SP
M=M+1

@THAT
D=M
@SP
A=M
M=D
@SP
M=M+1

D=M
@1
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Main.fibonacci
0;JMP

(RETURN.Main.fibonacci.1) 

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

// push constant 1
@1 
D=A 
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

// call Main.fibonacci 1
@RETURN.Main.fibonacci.2
D=A
@SP
A=M
M=D
@SP
M=M+1

@LCL
D=M
@SP
A=M
M=D
@SP
M=M+1

@ARG
D=M
@SP
A=M
M=D
@SP
M=M+1

@THIS
D=M
@SP
A=M
M=D
@SP
M=M+1

@THAT
D=M
@SP
A=M
M=D
@SP
M=M+1

D=M
@1
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Main.fibonacci
0;JMP

(RETURN.Main.fibonacci.2) 

// add
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M+D 
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

// function Sys.init 0
(Sys.init) 

// push constant 4
@4 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// call Main.fibonacci 1
@RETURN.Main.fibonacci.0
D=A
@SP
A=M
M=D
@SP
M=M+1

@LCL
D=M
@SP
A=M
M=D
@SP
M=M+1

@ARG
D=M
@SP
A=M
M=D
@SP
M=M+1

@THIS
D=M
@SP
A=M
M=D
@SP
M=M+1

@THAT
D=M
@SP
A=M
M=D
@SP
M=M+1

D=M
@1
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Main.fibonacci
0;JMP

(RETURN.Main.fibonacci.0) 

// label WHILE
(Sys.init$WHILE) 

// goto WHILE
@Sys.init$WHILE
0;JMP 

(END)
@END
0;JMP 

