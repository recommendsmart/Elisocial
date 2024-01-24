/*! modernizr 3.5.0 (Custom Build) | MIT *
 * https://modernizr.com/download/?-cssanimations-inputtypes-touchevents-addtest-prefixed-prefixes-setclasses-teststyles !*/
!function(e,t,n){function r(e,t){return typeof e===t}function i(){var e,t,n,i,o,s,a;for(var l in w)if(w.hasOwnProperty(l)){if(e=[],t=w[l],t.name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(n=0;n<t.options.aliases.length;n++)e.push(t.options.aliases[n].toLowerCase());for(i=r(t.fn,"function")?t.fn():t.fn,o=0;o<e.length;o++)s=e[o],a=s.split("."),1===a.length?Modernizr[a[0]]=i:(!Modernizr[a[0]]||Modernizr[a[0]]instanceof Boolean||(Modernizr[a[0]]=new Boolean(Modernizr[a[0]])),Modernizr[a[0]][a[1]]=i),_.push((i?"":"no-")+a.join("-"))}}function o(e){var t=T.className,n=Modernizr._config.classPrefix||"";if(P&&(t=t.baseVal),Modernizr._config.enableJSClass){var r=new RegExp("(^|\\s)"+n+"no-js(\\s|$)");t=t.replace(r,"$1"+n+"js$2")}Modernizr._config.enableClasses&&(t+=" "+n+e.join(" "+n),P?T.className.baseVal=t:T.className=t)}function s(e,t){if("object"==typeof e)for(var n in e)b(e,n)&&s(n,e[n]);else{e=e.toLowerCase();var r=e.split("."),i=Modernizr[r[0]];if(2==r.length&&(i=i[r[1]]),"undefined"!=typeof i)return Modernizr;t="function"==typeof t?t():t,1==r.length?Modernizr[r[0]]=t:(!Modernizr[r[0]]||Modernizr[r[0]]instanceof Boolean||(Modernizr[r[0]]=new Boolean(Modernizr[r[0]])),Modernizr[r[0]][r[1]]=t),o([(t&&0!=t?"":"no-")+r.join("-")]),Modernizr._trigger(e,t)}return Modernizr}function a(e){return e.replace(/([a-z])-([a-z])/g,function(e,t,n){return t+n.toUpperCase()}).replace(/^-/,"")}function l(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):P?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function u(){var e=t.body;return e||(e=l(P?"svg":"body"),e.fake=!0),e}function f(e,n,r,i){var o,s,a,f,c="modernizr",p=l("div"),d=u();if(parseInt(r,10))for(;r--;)a=l("div"),a.id=i?i[r]:c+(r+1),p.appendChild(a);return o=l("style"),o.type="text/css",o.id="s"+c,(d.fake?d:p).appendChild(o),d.appendChild(p),o.styleSheet?o.styleSheet.cssText=e:o.appendChild(t.createTextNode(e)),p.id=c,d.fake&&(d.style.background="",d.style.overflow="hidden",f=T.style.overflow,T.style.overflow="hidden",T.appendChild(d)),s=n(p,e),d.fake?(d.parentNode.removeChild(d),T.style.overflow=f,T.offsetHeight):p.parentNode.removeChild(p),!!s}function c(e,t){return!!~(""+e).indexOf(t)}function p(e,t){return function(){return e.apply(t,arguments)}}function d(e,t,n){var i;for(var o in e)if(e[o]in t)return n===!1?e[o]:(i=t[e[o]],r(i,"function")?p(i,n||t):i);return!1}function m(e){return e.replace(/([A-Z])/g,function(e,t){return"-"+t.toLowerCase()}).replace(/^ms-/,"-ms-")}function h(t,n,r){var i;if("getComputedStyle"in e){i=getComputedStyle.call(e,t,n);var o=e.console;if(null!==i)r&&(i=i.getPropertyValue(r));else if(o){var s=o.error?"error":"log";o[s].call(o,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else i=!n&&t.currentStyle&&t.currentStyle[r];return i}function v(t,r){var i=t.length;if("CSS"in e&&"supports"in e.CSS){for(;i--;)if(e.CSS.supports(m(t[i]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var o=[];i--;)o.push("("+m(t[i])+":"+r+")");return o=o.join(" or "),f("@supports ("+o+") { #modernizr { position: absolute; } }",function(e){return"absolute"==h(e,null,"position")})}return n}function y(e,t,i,o){function s(){f&&(delete q.style,delete q.modElem)}if(o=r(o,"undefined")?!1:o,!r(i,"undefined")){var u=v(e,i);if(!r(u,"undefined"))return u}for(var f,p,d,m,h,y=["modernizr","tspan","samp"];!q.style&&y.length;)f=!0,q.modElem=l(y.shift()),q.style=q.modElem.style;for(d=e.length,p=0;d>p;p++)if(m=e[p],h=q.style[m],c(m,"-")&&(m=a(m)),q.style[m]!==n){if(o||r(i,"undefined"))return s(),"pfx"==t?m:!0;try{q.style[m]=i}catch(g){}if(q.style[m]!=h)return s(),"pfx"==t?m:!0}return s(),!1}function g(e,t,n,i,o){var s=e.charAt(0).toUpperCase()+e.slice(1),a=(e+" "+A.join(s+" ")+s).split(" ");return r(t,"string")||r(t,"undefined")?y(a,t,i,o):(a=(e+" "+O.join(s+" ")+s).split(" "),d(a,t,n))}function C(e,t,r){return g(e,n,n,t,r)}var _=[],w=[],S={_version:"3.5.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout(function(){t(n[e])},0)},addTest:function(e,t,n){w.push({name:e,fn:t,options:n})},addAsyncTest:function(e){w.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=S,Modernizr=new Modernizr;var x=S._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];S._prefixes=x;var b,T=t.documentElement,P="svg"===T.nodeName.toLowerCase();!function(){var e={}.hasOwnProperty;b=r(e,"undefined")||r(e.call,"undefined")?function(e,t){return t in e&&r(e.constructor.prototype[t],"undefined")}:function(t,n){return e.call(t,n)}}(),S._l={},S.on=function(e,t){this._l[e]||(this._l[e]=[]),this._l[e].push(t),Modernizr.hasOwnProperty(e)&&setTimeout(function(){Modernizr._trigger(e,Modernizr[e])},0)},S._trigger=function(e,t){if(this._l[e]){var n=this._l[e];setTimeout(function(){var e,r;for(e=0;e<n.length;e++)(r=n[e])(t)},0),delete this._l[e]}},Modernizr._q.push(function(){S.addTest=s});var z=l("input"),k="search tel url email datetime date month week time datetime-local number range color".split(" "),j={};Modernizr.inputtypes=function(e){for(var r,i,o,s=e.length,a="1)",l=0;s>l;l++)z.setAttribute("type",r=e[l]),o="text"!==z.type&&"style"in z,o&&(z.value=a,z.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(r)&&z.style.WebkitAppearance!==n?(T.appendChild(z),i=t.defaultView,o=i.getComputedStyle&&"textfield"!==i.getComputedStyle(z,null).WebkitAppearance&&0!==z.offsetHeight,T.removeChild(z)):/^(search|tel)$/.test(r)||(o=/^(url|email)$/.test(r)?z.checkValidity&&z.checkValidity()===!1:z.value!=a)),j[e[l]]=!!o;return j}(k);var E=S.testStyles=f;Modernizr.addTest("touchevents",function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var r=["@media (",x.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");E(r,function(e){n=9===e.offsetTop})}return n});var N="Moz O ms Webkit",A=S._config.usePrefixes?N.split(" "):[];S._cssomPrefixes=A;var L=function(t){var r,i=x.length,o=e.CSSRule;if("undefined"==typeof o)return n;if(!t)return!1;if(t=t.replace(/^@/,""),r=t.replace(/-/g,"_").toUpperCase()+"_RULE",r in o)return"@"+t;for(var s=0;i>s;s++){var a=x[s],l=a.toUpperCase()+"_"+r;if(l in o)return"@-"+a.toLowerCase()+"-"+t}return!1};S.atRule=L;var O=S._config.usePrefixes?N.toLowerCase().split(" "):[];S._domPrefixes=O;var V={elem:l("modernizr")};Modernizr._q.push(function(){delete V.elem});var q={style:V.elem.style};Modernizr._q.unshift(function(){delete q.style}),S.testAllProps=g;S.prefixed=function(e,t,n){return 0===e.indexOf("@")?L(e):(-1!=e.indexOf("-")&&(e=a(e)),t?g(e,t,n):g(e,"pfx"))};S.testAllProps=C,Modernizr.addTest("cssanimations",C("animationName","a",!0)),i(),o(_),delete S.addTest,delete S.addAsyncTest;for(var R=0;R<Modernizr._q.length;R++)Modernizr._q[R]();e.Modernizr=Modernizr}(window,document);