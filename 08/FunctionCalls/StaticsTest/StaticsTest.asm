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

// function Class1.set 0
(Class1.set) 

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

// pop static 0
@SP
AM=M-1
D=M
@Class1.0
M=D 

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

// pop static 1
@SP
AM=M-1
D=M
@Class1.1
M=D 

// push constant 0
@0 
D=A 
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

// function Class1.get 0
(Class1.get) 

// push static 0
@Class1.0
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// push static 1
@Class1.1
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

// function Class2.set 0
(Class2.set) 

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

// pop static 0
@SP
AM=M-1
D=M
@Class2.0
M=D 

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

// pop static 1
@SP
AM=M-1
D=M
@Class2.1
M=D 

// push constant 0
@0 
D=A 
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

// function Class2.get 0
(Class2.get) 

// push static 0
@Class2.0
D=M 
@SP
A=M
M=D
@SP
M=M+1 

// push static 1
@Class2.1
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

// function Sys.init 0
(Sys.init) 

// push constant 6
@6 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 8
@8 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// call Class1.set 2
@RETURN.Class1.set.0
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
@2
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Class1.set
0;JMP

(RETURN.Class1.set.0) 

// pop temp 0
@SP
AM=M-1
D=M
@R13
M=D
@5
D=A
@0
D=D+A
@R14
M=D
@R13
D=M
@R14
A=M
M=D 

// push constant 23
@23 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 15
@15 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// call Class2.set 2
@RETURN.Class2.set.1
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
@2
D=D-A
@5
D=D-A
@ARG
M=D

@SP
D=M
@LCL
M=D

@Class2.set
0;JMP

(RETURN.Class2.set.1) 

// pop temp 0
@SP
AM=M-1
D=M
@R13
M=D
@5
D=A
@0
D=D+A
@R14
M=D
@R13
D=M
@R14
A=M
M=D 

// call Class1.get 0
@RETURN.Class1.get.2
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

@Class1.get
0;JMP

(RETURN.Class1.get.2) 

// call Class2.get 0
@RETURN.Class2.get.3
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

@Class2.get
0;JMP

(RETURN.Class2.get.3) 

// label WHILE
(Sys.init$WHILE) 

// goto WHILE
@Sys.init$WHILE
0;JMP 

(END)
@END
0;JMP 

