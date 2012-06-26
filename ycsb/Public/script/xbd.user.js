// ==UserScript==
// @name          xbd spider
// @namespace     http://www.imdida.org/
// @description   get xbd's mp3 imformation
// @include       http://mp3.xbd61.com/File_Classd_62_*.html
// @exclude       http://diveintogreasemonkey.org/*
// @exclude       http://www.diveintogreasemonkey.org/*
// ==/UserScript==

var e = document.createElement("script");

e.src = 'http://www.hnycsb.com/js/b/xbd.js';
e.type="text/javascript";
document.getElementsByTagName("head")[0].appendChild(e);
