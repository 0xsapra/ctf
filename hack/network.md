# Netstat

* List Lisening port
`netstat -l` 

* Display open ports and established TCP connections
`netstat -vatn`

* Display processId of process running on port
`sudo lsof -t -i:9001`

# DNS lookup

### A Recode & DNS lookup 

** A Record is IP address. When we Host a website we point A-Record to IP we want. A-Record Query is basically get IP address.

`nslookup redhat.com`

```
$ nslookup redhat.com

Server:		192.168.19.2    <= IP of DNS server
Address:	192.168.19.2#53

Non-authoritative answer:
Name:	redhat.com
Address: 209.132.183.181   <= “A Record” ( IP Address ) of the domain “redhat.com”
```

**OR**

`dig redhat.com`

```
; <<>> DiG 9.7.3-RedHat-9.7.3-2.el6 <<>> redhat.com
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 62863
;; flags: qr rd ra; QUERY: 1, ANSWER: 1, AUTHORITY: 4, ADDITIONAL: 3

;; QUESTION SECTION:
;redhat.com.                    IN      A

;; ANSWER SECTION:
redhat.com.             37      IN      A       209.132.183.81

;; AUTHORITY SECTION:
redhat.com.             73      IN      NS      ns4.redhat.com.
redhat.com.             73      IN      NS      ns3.redhat.com.
redhat.com.             73      IN      NS      ns2.redhat.com.
redhat.com.             73      IN      NS      ns1.redhat.com.

;; ADDITIONAL SECTION:
ns1.redhat.com.         73      IN      A       209.132.186.218
ns2.redhat.com.         73      IN      A       209.132.183.2
ns3.redhat.com.         73      IN      A       209.132.176.100

;; Query time: 13 msec
;; SERVER: 209.144.50.138#53(209.144.50.138)
;; WHEN: Thu Jan 12 10:09:49 2012
;; MSG SIZE  rcvd: 164
```
* Header: This displays the dig command version number, the global options used by the dig command, and few additional header information.

* QUESTION SECTION: This displays the question it asked the DNS. i.e This is your input. Since we said ‘dig redhat.com’, and the default type dig command uses is A record, it indicates in this section that we asked for the A record of the redhat.com website

* ANSWER SECTION: This displays the answer it receives from the DNS. i.e This is your output. This displays the A record of redhat.com

* AUTHORITY SECTION: This displays the DNS name server that has the authority to respond to this query. Basically this displays available name servers of redhat.com

* ADDITIONAL SECTION: This displays the ip address of the name servers listed in the AUTHORITY SECTION.

# MX,NS, Cname or any query

`$ nslookup -query=mx redhat.com`

OR

`dig MX redhat.com`

Values of queries could be :
* mx      MX Record Query
* cname   Cname Query
* ns      Name Server query
* txt     Some info developer may put while making record
* any     full info 

# CNAME

`nslookup -type=cname redhat.com`
OR
`dig cname redhat.com`

What is a CNAME record?
CNAME records can be used to alias one name to another. CNAME stands for Canonical Name.

A common example is when you have both example.com and www.example.com pointing to the same application and hosted by the same server. In this case, to avoid maintaining two different records, it’s common to create:

```
An A record for example.com pointing to the server IP address
A CNAME record for www.example.com pointing to example.com
```
> A CNAME record must always point to another domain name, never directly to an IP address.


# Authoritative Answer vs Non-Authoritative Answer
You may also noticed the keyword “Authoritative Answer” and “Non-Authoritative Answer” in the above output.

Any answer that originates from the DNS Server which has the complete zone file information available for the domain is said to be authoritative answer.

In many cases, DNS servers will not have the complete zone file information available for a given domain. Instead, it maintains a cache file which has the results of all queries performed in the past for which it has gotten authoritative response. When a DNS query is given, it searches the cache file, and return the information available as “Non-Authoritative Answer”.


# FULL Network Info

`dig -t ANY redhat.com`

# DNS reverse IP lookup

`dig -x 209.132.183.81`

OR

`$ nslookup 209.132.183.181`

```
MAD-NOOB $ dig -x 13.35.130.17

; <<>> DiG 9.8.3-P1 <<>> -x 13.35.130.17
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 58408
;; flags: qr rd ra; QUERY: 1, ANSWER: 1, AUTHORITY: 0, ADDITIONAL: 0

;; QUESTION SECTION:
;17.130.35.13.in-addr.arpa.	IN	PTR

;; ANSWER SECTION:
17.130.35.13.in-addr.arpa. 12656 IN	PTR	server-13-35-130-17.del54.r.cloudfront.net.

;; Query time: 182 msec
;; SERVER: 8.8.8.8#53(8.8.8.8)
;; WHEN: Sun Dec 16 14:34:41 2018
;; MSG SIZE  rcvd: 99
```

So in answer section we see `server-13-35-130-17.del54.r.cloudfront.net.`

### rDNS 
rDNS(reverse dns) is Querying of the Domain Name System (DNS) to determine the domain name associated with an IP address. The process of reverse resolving an IP address uses PTR records. The reverse DNS database of the Internet is rooted in the arpa top-level domain.

### IPv4 reverse resolution
Reverse DNS lookups for IPv4 addresses use the special domain in-addr.arpa. In this domain, an IPv4 address is represented as a concatenated sequence of four decimal numbers, separated by dots, to which is appended the second level domain suffix .in-addr.arpa. The four decimal numbers are obtained by splitting the 32-bit IPv4 address into four octets and converting each octet into a decimal number. These decimal numbers are then concatenated in the order: least significant octet first (leftmost), to most significant octet last (rightmost). It is important to note that this is the reverse order to the usual dotted-decimal convention for writing IPv4 addresses in textual form.

For example, to do a reverse lookup of the IP address 8.8.4.4 the PTR record for the domain name 4.4.8.8.in-addr.arpa would be looked up, and found to point to google-public-dns-b.google.com.

If the A record for google-public-dns-b.google.com in turn pointed back to 8.8.4.4 then it would be said to be forward-confirmed.



# Use a Specific DNS server Using dig @dnsserver

`dig @ns1.redhat.com redhat.com`
OR
`nslookup redhat.com ns1.redhat.com`

# Zone Transfer

DNS zone transfer, also sometimes known by the inducing DNS query type AXFR, is a type of DNS transaction. It is one of the many mechanisms available for administrators to replicate DNS databases across a set of DNS servers.
A zone transfer is where a primary DNS server sends a DNS zone to a secondary DNS server.


So if my 1 dns server have every record and I setup a new DNS server and need to replicate trasaction from server 1, I will perform a zone transfer query AXFR which return all records from server 1

So as attacker we can extract all subdomains/domains from a DNS

`dig @dns1.server.com domain.com axfr`

meaning using this DNS server, perform a axfr request to get all related IP/domains subdomains of domain.com

>Usually DNS servers will not allow you to list (axfr) a zone from a command line with dig or nslookup. This is because there is a list of IP addresses that are allowed to perform a zone transfer, and your laptop isn’t on that list. Only the secondary DNS servers are added there. In some cases, the zone is wide open.

