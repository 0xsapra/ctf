#!/usr/bin/env python
import cgi;
import sys
from html import escape
flag="done123"

source = input()
value1 = input()
value2 =input()
op = input()



def get_value(val):
    val = str(val)[:64]
    if str(val).isdigit(): return int(val)
    blacklist = ['(',')','[',']','\'','"'] # I don't like tuple, list and dict.
    if val == '' or [c for c in blacklist if c in val] != []:
        print('<center>Invalid value</center>')
        sys.exit(0)
    return val

def get_op(val):
    val = str(val)[:2]
    list_ops = ['+','-','/','*','=','!']
    if val == '' or val[0] not in list_ops:
        print('<center>Invalid op</center>')
        sys.exit(0)
    return val

op = get_op(get_value(op))
value1 = get_value(value1)
value2 = get_value(value2)

# solution 'Tru'+f'{101 if flag>source else 99:c}'
# value 1 = Tru
# op = +f
# value 2 = {101 if flag>source else 99:c}
# source is brute forced from a-----Z

if str(value1).isdigit() ^ str(value2).isdigit(): # check if both r same type
    print('<center>Types of the values don\'t match</center>')
    sys.exit(0)

calc_eval = str(repr(value1)) + str(op) + str(repr(value2))
print(calc_eval)



try:
    result = str(eval(calc_eval))
    if result.isdigit() or result == 'True' or result == 'False':
        print(result)
    else:
        print (result)
        print("Invalid") # Sorry we don't support output as a string due to security issue.
except:
    print("Invalid")
