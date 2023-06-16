// push constant 17
@17 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 17
@17 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// eq
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.0
D;JEQ
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

// push constant 17
@17 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 16
@16 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// eq
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.1
D;JEQ
@SP
A=M
M=0
@END.1
0;JMP
(TRUE.1)
@SP
A=M
M=-1
(END.1) 
@SP
M=M+1 

// push constant 16
@16 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 17
@17 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// eq
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.2
D;JEQ
@SP
A=M
M=0
@END.2
0;JMP
(TRUE.2)
@SP
A=M
M=-1
(END.2) 
@SP
M=M+1 

// push constant 892
@892 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 891
@891 
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
@TRUE.3
D;JLT
@SP
A=M
M=0
@END.3
0;JMP
(TRUE.3)
@SP
A=M
M=-1
(END.3) 
@SP
M=M+1 

// push constant 891
@891 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 892
@892 
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
@TRUE.4
D;JLT
@SP
A=M
M=0
@END.4
0;JMP
(TRUE.4)
@SP
A=M
M=-1
(END.4) 
@SP
M=M+1 

// push constant 891
@891 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 891
@891 
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
@TRUE.5
D;JLT
@SP
A=M
M=0
@END.5
0;JMP
(TRUE.5)
@SP
A=M
M=-1
(END.5) 
@SP
M=M+1 

// push constant 32767
@32767 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 32766
@32766 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// gt
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.6
D;JGT
@SP
A=M
M=0
@END.6
0;JMP
(TRUE.6)
@SP
A=M
M=-1
(END.6) 
@SP
M=M+1 

// push constant 32766
@32766 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 32767
@32767 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// gt
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.7
D;JGT
@SP
A=M
M=0
@END.7
0;JMP
(TRUE.7)
@SP
A=M
M=-1
(END.7) 
@SP
M=M+1 

// push constant 32766
@32766 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 32766
@32766 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// gt
@SP
AM=M-1
D=M
@SP
AM=M-1 
D=M-D
@TRUE.8
D;JGT
@SP
A=M
M=0
@END.8
0;JMP
(TRUE.8)
@SP
A=M
M=-1
(END.8) 
@SP
M=M+1 

// push constant 57
@57 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 31
@31 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// push constant 53
@53 
D=A 
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

// push constant 112
@112 
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

// neg
@SP
AM=M-1
M=-M 
@SP
M=M+1 

// and
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M&D 
@SP
M=M+1 

// push constant 82
@82 
D=A 
@SP
A=M
M=D
@SP
M=M+1 

// or
@SP
AM=M-1
D=M
@SP
AM=M-1 
M=M|D 
@SP
M=M+1 

// not
@SP
AM=M-1
M=!M 
@SP
M=M+1 

(END)
@END
0;JMP 

