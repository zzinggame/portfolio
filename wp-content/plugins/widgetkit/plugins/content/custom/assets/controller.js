"use strict";angular.module("widgetkit").controller("customCtrl",["$scope","$timeout","UIkit","mediaInfo","mediaPicker","Fields","Application","Translator",function(i,m,l,s,d,u,g,h){i.content.data._fields||(i.content.data._fields=[]);var n=this,a,o=i.content.data._fields;(!i.content.data.items||!i.content.data.items.length)&&(i.content.data.items=[{media:""}]),Object.prototype.hasOwnProperty.call(i.content.data,"parse_shortcodes")||(i.content.data.parse_shortcodes=1),a=i.content.data.items,i.item=a[0],i.extrafields=o,n.corefields=g.config.types.custom.fields,n.fields=u.fields(),i.tinyMCE=window.tinyMCE||!1,n.previewItem=function(e){var t=e.options&&e.options.media&&e.options.media.poster;return s(t||e.media).image},n.addItem=function(e){i.item=e||{media:""},a.push(i.item)},n.importItems=function(){d.select({multiple:!0}).then(function(e){e.length&&a.length==1&&(!i.item.title||!i.item.media||!i.item.content)&&(a.length=0),angular.forEach(e,function(t){t.title=String(t.title).replace(/(-|_)/g," ").replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g,function(r){return r.toUpperCase()}),n.addItem({title:t.title,media:t.url,width:t.width,height:t.height})})})},n.editItem=function(e){i.item=e},n.deleteItem=function(e){a.splice(a.indexOf(e),1),i.item=a[0]},n.addField=function(e){if(e=e||{type:"text",name:"field-x",label:"Field X"},n.corefields[e.name]&&(e.type=n.corefields[e.name].type,e.label=n.corefields[e.name].label),n.hasField(e.name)){alert('Field name "'+e.name+'" is already in use.');return}a.forEach(function(t){t[e.name]||(t[e.name]="")}),o.push(angular.copy(e))},n.deleteField=function(e){confirm(h.trans("Are you sure you want to delete this field?"))&&(a.forEach(function(t){t[e.name]&&delete t[e.name]}),o.splice(o.indexOf(e),1))},n.hasField=function(e){if(["title","media","link"].indexOf(e)>-1)return!0;for(var t=0;t<o.length;t++)if(o[t].name==e)return!0;return!1},n.toggleEditFields=function(){n.editfields=!n.editfields,n.editfields||setTimeout(function(){window.dispatchEvent(new Event("resize"))},150),n.custom={field:{}},n.addCustomField=!1},n.getFieldOptions=function(e){var t={},r=n.corefields[e.name];return r&&r.options&&(t=angular.extend(t,r.options)),JSON.stringify(t)},i.$watch("content",function(e){var t=a.indexOf(i.item);a=e.data.items,i.item=a[t]});var c=l.util.$(".uk-scope");c&&(l.container=c),l.util.on(document,"moved",function(e,t,r){var f=angular.element(r);t.$el.id=="js-content-items"&&a.splice(f.index(),0,a.splice(a.indexOf(f.scope().item),1)[0]),t.$el.id=="js-fields-items"&&o.splice(f.index(),0,o.splice(o.indexOf(f.scope().field),1)[0])}),angular.isArray(i.widget.fields)&&i.widget.fields.forEach(function(e){e&&e.name&&!n.hasField(e.name)&&n.addField(e)})}]).run(["$rootScope","mediaInfo",function(i,m){i.$on("wk.preview.content",function(l,s){if(s.type=="custom"&&s.data.items.length){var d=s.data.items[0],u=d.options&&d.options.media&&d.options.media.poster;l.preview=m(u||d.media).image.replace(/preview(-.+\.svg)$/g,"content$1")}})}]);