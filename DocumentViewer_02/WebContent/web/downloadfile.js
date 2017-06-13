var req = new XMLHttpRequest (); 
req.open ('GET', url, false); 
/ / XHR binary charset opt by Marcus Granado 2006 [http://mgran.blogspot.com] 
req.overrideMimeType ('text / plain; charset = x-user-defined'); 
            
try { 
Req.send (null); 
the if (req.status! = 200) return; 
/ / * Bugfix * by Marcus Granado 2006 [http://mgran.blogspot.com] adapted by Thomas Belot 
var out = "; 
for (i = 0; i <req.responseText.length; i + +) { 
out + = String.fromCharCode (req.responseText.charCodeAt (i) & 0xff); 
/ / Out + = req.responseText.charCodeAt (i) & 0xff; 
} 
return OUT; 
} Catch (e) { 
this.DEBUG ('loadBinaryResource: failednException:' + e); 
}