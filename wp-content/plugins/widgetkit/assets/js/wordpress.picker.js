(function(r){"use strict";function w(o){return o===void 0}function a(o){const{widgetkit:{iframe:t}}=window;window.tb_show("",t),r("#TB_overlay").css("zIndex",9e5),r("#TB_window").css("zIndex",900001),d(),r(window).on("message",n),r(window).on("resize",d),r("body").one("thickbox:removed",function(){r(window).off("message",n),r(window).off("resize",d)});function n({originalEvent:{data:e}}){e.widget&&(window.tb_remove(),o(e.widget))}}function d(){const o=r("#TB_window"),t=r(window).width(),n=r(window).height(),e=1280<t?1280:t,i=r("#wpadminbar"),s=i.length?parseInt(i.css("height"),10):0;o.length&&(o.width(e-50).height(n-45-s),r("#TB_iframeContent").width(e-50).height(n-75-s),o.css({"margin-left":"-"+parseInt((e-50)/2,10)+"px"}),w(document.body.style.maxWidth)||o.css({top:20+s+"px","margin-top":"0"}))}function h(o){let t;return(window.WFEditor||window.JContentEditor||window.tinyMCE)&&!o.is(":visible")?t=new p(o):window.CodeMirror&&o.next()[0]&&o.next()[0].CodeMirror?t=new C(o):window.ace?t=new b(o):window.CKEDITOR?t=new k(o):t=new m(o),t}function p(o){const t=window.tinymce.editors?.[o.attr("id")]||window.tinymce.EditorManager?.get(o.attr("id"));return{getContent:function(){return t.getContent()},insertContent:function(n){t.execCommand("mceInsertContent",!1,n)},updateContent:function(n,e,i){let s=this.getContent();s=s.substring(0,e)+n+'<span id="tmp-wkid"></span>'+s.substring(i),t.setContent(s),t.selection.select(t.dom.select("#tmp-wkid")[0],!0),t.selection.collapse(!1),t.dom.remove("tmp-wkid",!1),t.focus()},getCursorPosition:function(){const n=t.selection.getBookmark(0),e="[data-mce-type=bookmark]",i=t.dom.select(e);t.selection.select(i[0]),t.selection.collapse();const s="######cursor######",u='<span id="'+s+'"></span>';t.selection.setContent(u);const f=t.getContent({format:"html"}).indexOf(u);return t.dom.remove(s,!1),t.selection.moveToBookmark(n),f}}}function m(o){return{getContent:function(){return o.val()},insertContent:function(t){this.updateContent(t,o.prop("selectionStart"),o.prop("selectionEnd"))},updateContent:function(t,n,e){let i=o.val();const s=n+t.length;i=i.substring(0,n)+t+i.substring(e),o.val(i),o[0].setSelectionRange(s,s),o.focus().trigger("change")},getCursorPosition:function(){return o.prop("selectionStart")}}}function C(o){const t=o.next()[0].CodeMirror;return{getContent:function(){return t.getValue()},insertContent:function(n){t.replaceRange(n,t.getCursor()),t.focus()},updateContent:function(n,e,i){t.replaceRange(n,this.translateOffset(e),this.translateOffset(i)),t.focus()},getCursorPosition:function(){return this.translatePosition(t.getCursor())},translatePosition:function(n){return t.getValue().split(`
`,n.line).join("").length+n.line+n.ch},translateOffset:function(n){const e=t.getValue().substring(0,n).split(`
`);return{line:e.length-1,ch:e[e.length-1].length}}}}function b(o){const t=window.ace.edit(o.parent().attr("id"));return{getContent:function(){return t.getValue()},insertContent:function(n){t.insert(n),t.focus()},updateContent:function(n,e,i){e=this.translateOffset(e),i=this.translateOffset(i);const s=t.getSelectionRange();s.setStart(e.row,e.column),s.setEnd(i.row,i.column),t.getSession().getDocument().replace(s,n),t.focus()},getCursorPosition:function(){return this.translatePosition(t.getSelection().getCursor())},translatePosition:function(n){return this.getContent().split(`
`,n.row).join("").length+n.row+n.column},translateOffset:function(n){const e=this.getContent().substring(0,n).split(`
`);return{row:e.length-1,column:e[e.length-1].length}}}}function k(o){const t=window.CKEDITOR.instances[o.attr("id")];return{getContent:function(){return t.getData()},insertContent:function(n){this.updateContent(n,this.getCursorPosition(),this.getCursorPosition())},updateContent:function(n,e,i){let s=t.getData();s=s.substring(0,e)+n+s.substring(i),t.setData(s)},getCursorPosition:function(){return t.mode==="source"?r(t.textarea.$).prop("selectionStart"):this.getCursorPositionInWYSIWYG()},getCursorPositionInWYSIWYG:function(){const n=t.getSelection().createBookmarks(),i='<span id="'+"######cursor######"+'">&nbsp;</span>',s=window.CKEDITOR.dom.element.createFromHtml(i);s.insertBefore(n[0].startNode);const c=this.getContent().indexOf(i);return s.remove(),t.getSelection().selectBookmarks(n),c}}}const l=function(o){r.extend(this,{tag:"",attrs:{},type:"single",content:""},o)};r.extend(l,{parse:function(o,t){const n=this.regexp(o).exec(t);let e={tag:o},i;return n&&(n[4]?i="self-closing":n[6]?i="closed":i="single",e={tag:n[2],attrs:this.attrs(n[3]),type:i,content:n[5]}),new l(e)},attrs:function(o){const t=/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*'([^']*)'(?:\s|$)|(\w+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/g,n={};let e;for(o=o.replace(/[\u00a0\u200b]/g," ");e=t.exec(o);)e[1]?n[e[1].toLowerCase()]=e[2]:e[3]?n[e[3].toLowerCase()]=e[4]:e[5]&&(e[6]==="true"||e[6]==="1")?n[e[5].toLowerCase()]=!0:e[5]&&(e[6]==="false"||e[6]==="0")?n[e[5].toLowerCase()]=!1:e[5]?n[e[5].toLowerCase()]=e[6]:e[7]?n[e[7]]=!0:e[8]&&(n[e[8]]=!0);return n},regexp:function(o){return new RegExp("\\[(\\[?)("+o+")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)","g")}}),r.extend(l.prototype,{string:function(){let o="["+this.tag;return r.each(this.attrs,function(t,n){typeof n=="boolean"?o+=" "+t+"="+(n?1:0):n!==""&&(o+=" "+t+'="'+n+'"')}),this.type==="single"?o+"]":this.type==="self-closing"?o+" /]":(o+="]",this.content&&(o+=this.content),o+"[/"+this.tag+"]")}}),r(document).on("click",".widgetkit-widget a",function(o){o.preventDefault(),a(t=>{r(this).next("input").val(JSON.stringify(t)),r(this).text(`Widget: ${t.name}`),r(this).closest("form").find('input[type="text"]').trigger("change")})}),r(document).on("click",".widgetkit-editor",function(o){o.preventDefault();let t=r(`#${r(this).data("source")}`);const n=h(t),e=n.getContent(),i=n.getCursorPosition(),s=/\[widgetkit([^\]]*)\]/gi;let u,c;for(;c=s.exec(e);)if(c.index<=i&&s.lastIndex>i){u=c[0];break}a(f=>{const g=new l({tag:"widgetkit",attrs:f}).string();u?n.updateContent(g,c.index,s.lastIndex):n.insertContent(g)})}),r(()=>{r.each(window.tinyMCE?.editors,(o,t)=>{r(t.targetElm).closest(".wp-editor-wrap").find(".widgetkit-editor").data("source",t?.id)}),window.tinyMCE?.on("AddEditor",function(t){const n=r(t.editor?.formElement);if(!n.length)return;const e=n[0]._button||n.find(".widgetkit-editor"),i=n.find(".wp-media-buttons");n[0]._button=e,e.data("source",t.editor?.id),i.has(e).length||i.append(e)})})})(jQuery);
