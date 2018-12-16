Binary Exploitation



# Buffer Overflow x86/32 bit/i386/intel 80386

# Buffer Overflow x86_64/64 bit /amd64

# Stack Canaries attack

## Brute Forcing

**If canaries are 4-6 bytes long we can easily 1 by 1 brute force it**

## leaking canary

# `__x86_get_pc_thunk_ax`

This call is used in position-independent code on x86. It loads the position of the code into the %ebx register, which allows global objects (which have a fixed offset from the code) to be accessed as an offset from that register.

Position-independent code is code that can be loaded and executed, unmodified, at different addresses. It is important for code that will be linked into shared libraries, because these can be mapped at a different address in different processes.

Note that an equivalent call is not required on x86-64, because that architecture has IP-relative addressing modes (that is, it can directly address memory locations as an offset from the location of the current instruction).




# Format strings
