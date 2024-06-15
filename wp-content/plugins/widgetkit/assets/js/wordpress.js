(function(l,f){"use strict";function k(r){r.module("Fields").directive("fieldWysiwygeditor",["$timeout","mediaPicker",function(u,d){return{restrict:"EA",require:"?ngModel",template:'<div><textarea class="uk-textarea" name="wk_{{id}}" id="wk_{{id}}"></textarea></div>',link(c,m,p,e){if(!window.tinyMCE)return;c.id=String(Math.ceil(Math.random()*1e3));const i=`wk_${c.id}`;u(function(){const t=window.tinyMCE;let a=t.settings?.toolbar1||"bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,dfw,wp_adv";a.indexOf("wk_media")===-1&&(a+=" wk_media");const o=t.settings?.toolbar2||"formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",s=t.settings?.plugins||"charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",v=l.extend({},t.settings,{menubar:!1,plugins:s,toolbar1:a,toolbar2:o,width:"100%",height:500,relative_urls:t.settings?.relative_urls||!1,setup:function(n){n.addButton("wk_media",{text:"",icon:"image",onclick:function(){d.select({editor:i})}}),n.on("change",function(){e.$setViewValue(t.get(i).getContent())}),n.on("input",function(){e.$setViewValue(t.get(i).getContent())}),n.on("init",function(){n.setContent(e.$viewValue||"")})}}),g=new t.Editor(i,v,t.EditorManager);g.render(),e.$render=function(){try{g.setContent(e.$viewValue||""),l(`#${i}`).val(e.$viewValue||"")}catch{}},e.$render()})}}}])}function h(r){r.module("widgetkit").service("mediaPicker",["$location","$q","Application",function(u,d,c){const m=new RegExp("^"+c.baseUrl());return{select:function(e){e=r.extend({title:"Pick media",multiple:!1,button:{text:"Select"}},e);const i=d.defer();if(e.editor)e=r.extend({frame:"post"},e),wp.media.editor.open(e.editor,e);else{const w=wp.media(e).on("select",function(){const t=w.state().get("selection").map(function(a){const o=a.toJSON(),s=p(o.url);return s.host===u.host()&&(o.url=s.pathname.replace(m,"").replace(/^\//,"")),o});i.resolve(e.multiple?t:t[0])}).open()}return i.promise}};function p(e){const i=document.createElement("a");return i.href=e,i}}])}k(f),h(f),l(()=>l("[data-app]").addClass("wrap"))})(jQuery,angular);