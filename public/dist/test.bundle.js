!function(e){function t(t){for(var a,i,l=t[0],u=t[1],s=t[2],m=0,_=[];m<l.length;m++)i=l[m],Object.prototype.hasOwnProperty.call(o,i)&&o[i]&&_.push(o[i][0]),o[i]=0;for(a in u)Object.prototype.hasOwnProperty.call(u,a)&&(e[a]=u[a]);for(c&&c(t);_.length;)_.shift()();return r.push.apply(r,s||[]),n()}function n(){for(var e,t=0;t<r.length;t++){for(var n=r[t],a=!0,l=1;l<n.length;l++){var u=n[l];0!==o[u]&&(a=!1)}a&&(r.splice(t--,1),e=i(i.s=n[0]))}return e}var a={},o={4:0},r=[];function i(t){if(a[t])return a[t].exports;var n=a[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.e=function(e){var t=[],n=o[e];if(0!==n)if(n)t.push(n[2]);else{var a=new Promise((function(t,a){n=o[e]=[t,a]}));t.push(n[2]=a);var r,l=document.createElement("script");l.charset="utf-8",l.timeout=120,i.nc&&l.setAttribute("nonce",i.nc),l.src=function(e){return i.p+""+({3:"print"}[e]||e)+".bundle.js"}(e);var u=new Error;r=function(t){l.onerror=l.onload=null,clearTimeout(s);var n=o[e];if(0!==n){if(n){var a=t&&("load"===t.type?"missing":t.type),r=t&&t.target&&t.target.src;u.message="Loading chunk "+e+" failed.\n("+a+": "+r+")",u.name="ChunkLoadError",u.type=a,u.request=r,n[1](u)}o[e]=void 0}};var s=setTimeout((function(){r({type:"timeout",target:l})}),12e4);l.onerror=l.onload=r,document.head.appendChild(l)}return Promise.all(t)},i.m=e,i.c=a,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)i.d(n,a,function(t){return e[t]}.bind(null,a));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="/dist/",i.oe=function(e){throw console.error(e),e};var l=window.webpackJsonp=window.webpackJsonp||[],u=l.push.bind(l);l.push=t,l=l.slice();for(var s=0;s<l.length;s++)t(l[s]);var c=u;r.push([20,0]),n()}({20:function(e,t,n){"use strict";n.r(t);var a=n(0),o=n.n(a),r=n(2),i=n.n(r);const l={light:{foreground:"#000000",background:"#eeeeee",name:"light"},dark:{foreground:"#ffffff",background:"#222222",name:"dark"}},u=o.a.createContext(l.light);function s(){return o.a.createElement("div",null,o.a.createElement(c,null))}function c(){const e=o.a.useContext(u);return o.a.createElement("button",{style:{background:e.background,color:e.foreground}},e.name)}var m=function(){return o.a.createElement(u.Provider,{value:l.dark},o.a.createElement(s,null))};const _=()=>{const[e,t]=o.a.useState(!0);return o.a.createElement(o.a.Fragment,null,o.a.createElement("h1",null,"Test Testing Testy"),e&&o.a.createElement(o.a.Fragment,null,o.a.createElement("p",null,o.a.createElement("span",{className:"foo"},"Lorem ipsum")," dolor sit amet ",o.a.createElement("a",{href:"#consectetur"},"consectetur adipisicing elit"),". Eveniet voluptas incidunt atque ipsam, nobis quis inventore, velit libero vel autem tempora, fugit soluta excepturi ",o.a.createElement("a",{href:"#foo"},"voluptatum"),"! Soluta possimus nihil dolore hic."),o.a.createElement("p",null,"Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam, repellendus ullam cumque sequi deserunt cum possimus, deleniti impedit pariatur atque eligendi. Eius debitis delectus maxime esse a, odio sint mollitia!"),o.a.createElement("p",null,o.a.createElement("a",{href:"#bar"},"Lorem ipsum dolor sit amet consectetur"),", adipisicing elit. Quis tenetur facilis ipsum doloremque magni cum. Praesentium reiciendis vitae omnis ex sint eaque eos necessitatibus assumenda atque reprehenderit, commodi quod. Nam! Lorem ipsum dolor sit amet consectetur adipisicing elit. Obcaecati consectetur similique nulla veritatis a impedit provident eaque dignissimos facere soluta voluptate ab aliquam quidem culpa dolores hic excepturi, eius quae?"),o.a.createElement("p",null,"Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias facere magni culpa molestiae voluptates ducimus? Ducimus minus nesciunt tempora ad asperiores! Totam autem dolore eos delectus reprehenderit ipsa animi omnis."),o.a.createElement("p",null,"Lorem ipsum dolor sit, amet consectetur adipisicing elit. ",o.a.createElement("a",{href:"#baz"},"Itaque beatae")," eaque praesentium modi voluptates libero obcaecati earum? Officia impedit distinctio deleniti exercitationem delectus! Assumenda, hic a eaque nobis velit quis."),o.a.createElement("p",null,"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Accusamus magni ut aliquam officiis nostrum consequatur tempore, at repudiandae, laudantium exercitationem itaque cum, et voluptate suscipit modi unde ad doloremque sit! Lorem ipsum dolor sit amet consectetur adipisicing elit. Autem maiores quisquam distinctio quos qui adipisci voluptates perferendis officia commodi, fugit eius est ut corrupti reprehenderit fuga quibusdam, cum itaque sequi?"),o.a.createElement("p",null,"Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aperiam deserunt ea natus iusto ipsa, labore in consectetur, beatae commodi voluptas hic, ratione asperiores dicta accusantium optio quas unde omnis error!"),o.a.createElement("p",null,"Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa maxime quod ex iure eius et, sint doloremque! Libero exercitationem pariatur hic dignissimos, dolorum consequuntur odio consectetur voluptate accusamus voluptatem a."),o.a.createElement("p",null,"Fin."),o.a.createElement("h1",null,">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________ (Layout font) Pixel Emulator"),o.a.createElement("h1",{style:{fontFamily:"Press Start"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________ (Monospace font) Press Start"),o.a.createElement("h1",{style:{fontFamily:"Press Start 2P"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________ Press Start 2P"),o.a.createElement("h1",{style:{fontFamily:"Emulogic"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________ Emulogic"),o.a.createElement("h1",{style:{fontFamily:"Yoster Island"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"Bc.BMP07_A"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"Bc.BMP07_K"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"NineteenNinetySeven"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"Barcade Brawl"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"Barcade Brawl"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________"),o.a.createElement("h1",{style:{fontFamily:"Super Legend Boy"}},">START GAME Options Foo___foo.bar",o.a.createElement("br",null),"____________")),o.a.createElement("button",{type:"button",onClick:()=>t(!e)},"Toggle filler text"),o.a.createElement("p",null,"Env"),o.a.createElement("ul",null,o.a.createElement("li",null,"ENVIRONMENT: ","production"),o.a.createElement("li",null,"HOST_DOMAIN: ","startgameoptions.com")),o.a.createElement("h2",null,"Testing"),o.a.createElement(m,null))};i.a.render(o.a.createElement(_),document.getElementById("content")),document.getElementById("content").appendChild(function(){const e=document.createElement("div"),t=document.createElement("button"),a=document.createElement("br");return t.innerHTML="Click me and look at the console! But not before I lazy load a js component...",e.appendChild(a),e.appendChild(t),t.onclick=e=>Promise.all([n.e(0),n.e(3)]).then(n.bind(null,23)).then(e=>{(0,e.default)()}),e}())}});
//# sourceMappingURL=test.bundle.js.map