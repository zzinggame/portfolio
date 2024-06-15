(function(){"use strict";var C={colors:{black:[0,0,0,1],blue:[0,0,255,1],brown:[165,42,42,1],cyan:[0,255,255,1],fuchsia:[255,0,255,1],gold:[255,215,0,1],green:[0,128,0,1],indigo:[75,0,130,1],khaki:[240,230,140,1],lime:[0,255,0,1],magenta:[255,0,255,1],maroon:[128,0,0,1],navy:[0,0,128,1],olive:[128,128,0,1],orange:[255,165,0,1],pink:[255,192,203,1],purple:[128,0,128,1],violet:[128,0,128,1],red:[255,0,0,1],silver:[192,192,192,1],white:[255,255,255,1],yellow:[255,255,0,1],transparent:[255,255,255,0]},getSVG:function(t,e){return t=this.parseColor(t||"#E65857"),e=this.parseColor(e||"rgba(255,255,255,0)"),'<?xml version="1.0" encoding="utf-8"?>            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"             width="22px" height="34px" viewBox="0 0 22 34" enable-background="new 0 0 22 34" xml:space="preserve">             <circle id="circle" fill="'+e+'" cx="11" cy="11" r="6.5"/>            <path id="path" d="M11,0C4.94,0,0,4.876,0,10.9C0,19.458,11,34,11,34s11-14.581,11-23.1C22,4.876,17.061,0,11,0z M11,16.5             c-3.038,0-5.5-2.463-5.5-5.5c0-3.038,2.462-5.5,5.5-5.5c3.037,0,5.5,2.462,5.5,5.5C16.5,14.037,14.037,16.5,11,16.5z" fill="'+t+'"/>            </svg>'},parseColor:function(t){var e,i;return(e=/#([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/.exec(t))?i=[parseInt(e[1],16),parseInt(e[2],16),parseInt(e[3],16),1]:(e=/#([0-9a-fA-F])([0-9a-fA-F])([0-9a-fA-F])/.exec(t))?i=[parseInt(e[1],16)*17,parseInt(e[2],16)*17,parseInt(e[3],16)*17,1]:(e=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(t))?i=[parseInt(e[1]),parseInt(e[2]),parseInt(e[3]),1]:(e=/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9.]*)\s*\)/.exec(t))?i=[parseInt(e[1],10),parseInt(e[2],10),parseInt(e[3],10),parseFloat(e[4])]:i=this.colors[t]||[230,88,87,1],"rgba("+i[0]+", "+i[1]+", "+i[2]+", "+i[3]+")"},setIcon:function(t,e){var i,s=new google.maps.Point(11,40),r=function(){t.setIcon({url:i,anchor:s})};if(!e.trim())return i="https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi.png",r();if(e.indexOf("/")!==-1){var n=new Image;n.onload=function(){i=n.src,s=new google.maps.Point(Math.ceil(n.width/2),n.height),r()},n.onerror=function(){return i="https://maps.gstatic.com/mapfiles/api-3/images/spotlight-poi.png",r()},n.src=e}else i="data:image/svg+xml;base64,"+btoa(this.getSVG.apply(this,e.split(" "))),s=new google.maps.Point(11,34),r()}};function o(t,e,i){this.extend(o,google.maps.OverlayView),this.map_=t,this.markers_=[],this.clusters_=[],this.sizes=[53,56,66,78,90],this.styles_=[],this.ready_=!1;var s=i||{};this.gridSize_=s.gridSize||60,this.minClusterSize_=s.minimumClusterSize||2,this.maxZoom_=s.maxZoom||null,this.styles_=s.styles||[],this.imagePath_=s.imagePath||this.MARKER_CLUSTER_IMAGE_PATH_,this.imageExtension_=s.imageExtension||this.MARKER_CLUSTER_IMAGE_EXTENSION_,this.zoomOnClick_=!0,s.zoomOnClick!=null&&(this.zoomOnClick_=s.zoomOnClick),this.averageCenter_=!1,s.averageCenter!=null&&(this.averageCenter_=s.averageCenter),this.setupStyles_(),this.setMap(t),this.prevZoom_=this.map_.getZoom();var r=this;google.maps.event.addListener(this.map_,"zoom_changed",function(){var n=r.map_.getZoom();r.prevZoom_!=n&&(r.prevZoom_=n,r.resetViewport())}),google.maps.event.addListener(this.map_,"idle",function(){r.redraw()}),e&&e.length&&this.addMarkers(e,!1)}o.prototype.MARKER_CLUSTER_IMAGE_PATH_="https://raw.githubusercontent.com/googlemaps/js-marker-clusterer/gh-pages/images/m",o.prototype.MARKER_CLUSTER_IMAGE_EXTENSION_="png",o.prototype.extend=function(t,e){return(function(i){for(var s in i.prototype)this.prototype[s]=i.prototype[s];return this}).apply(t,[e])},o.prototype.onAdd=function(){this.setReady_(!0)},o.prototype.draw=function(){},o.prototype.setupStyles_=function(){if(!this.styles_.length)for(var t=0,e;e=this.sizes[t];t++)this.styles_.push({url:this.imagePath_+(t+1)+"."+this.imageExtension_,height:e,width:e})},o.prototype.fitMapToMarkers=function(){for(var t=this.getMarkers(),e=new google.maps.LatLngBounds,i=0,s;s=t[i];i++)e.extend(s.getPosition());this.map_.fitBounds(e)},o.prototype.setStyles=function(t){this.styles_=t},o.prototype.getStyles=function(){return this.styles_},o.prototype.isZoomOnClick=function(){return this.zoomOnClick_},o.prototype.isAverageCenter=function(){return this.averageCenter_},o.prototype.getMarkers=function(){return this.markers_},o.prototype.getTotalMarkers=function(){return this.markers_.length},o.prototype.setMaxZoom=function(t){this.maxZoom_=t},o.prototype.getMaxZoom=function(){return this.maxZoom_},o.prototype.calculator_=function(t,e){for(var i=0,s=t.length,r=s;r!==0;)r=parseInt(r/10,10),i++;return i=Math.min(i,e),{text:s,index:i}},o.prototype.setCalculator=function(t){this.calculator_=t},o.prototype.getCalculator=function(){return this.calculator_},o.prototype.addMarkers=function(t,e){for(var i=0,s;s=t[i];i++)this.pushMarkerTo_(s);e||this.redraw()},o.prototype.pushMarkerTo_=function(t){if(t.isAdded=!1,t.draggable){var e=this;google.maps.event.addListener(t,"dragend",function(){t.isAdded=!1,e.repaint()})}this.markers_.push(t)},o.prototype.addMarker=function(t,e){this.pushMarkerTo_(t),e||this.redraw()},o.prototype.removeMarker_=function(t){var e=-1;if(this.markers_.indexOf)e=this.markers_.indexOf(t);else for(var i=0,s;s=this.markers_[i];i++)if(s==t){e=i;break}return e==-1?!1:(t.setMap(null),this.markers_.splice(e,1),!0)},o.prototype.removeMarker=function(t,e){var i=this.removeMarker_(t);return!e&&i?(this.resetViewport(),this.redraw(),!0):!1},o.prototype.removeMarkers=function(t,e){for(var i=!1,s=0,r;r=t[s];s++){var n=this.removeMarker_(r);i=i||n}if(!e&&i)return this.resetViewport(),this.redraw(),!0},o.prototype.setReady_=function(t){this.ready_||(this.ready_=t,this.createClusters_())},o.prototype.getTotalClusters=function(){return this.clusters_.length},o.prototype.getMap=function(){return this.map_},o.prototype.setMap=function(t){this.map_=t},o.prototype.getGridSize=function(){return this.gridSize_},o.prototype.setGridSize=function(t){this.gridSize_=t},o.prototype.getMinClusterSize=function(){return this.minClusterSize_},o.prototype.setMinClusterSize=function(t){this.minClusterSize_=t},o.prototype.getExtendedBounds=function(t){var e=this.getProjection(),i=new google.maps.LatLng(t.getNorthEast().lat(),t.getNorthEast().lng()),s=new google.maps.LatLng(t.getSouthWest().lat(),t.getSouthWest().lng()),r=e.fromLatLngToDivPixel(i);r.x+=this.gridSize_,r.y-=this.gridSize_;var n=e.fromLatLngToDivPixel(s);n.x-=this.gridSize_,n.y+=this.gridSize_;var h=e.fromDivPixelToLatLng(r),c=e.fromDivPixelToLatLng(n);return t.extend(h),t.extend(c),t},o.prototype.isMarkerInBounds_=function(t,e){return e.contains(t.getPosition())},o.prototype.clearMarkers=function(){this.resetViewport(!0),this.markers_=[]},o.prototype.resetViewport=function(t){let e,i=0,s;for(;s=this.clusters_[i];i++)s.remove();for(;e=this.markers_[i];i++)e.isAdded=!1,t&&e.setMap(null);this.clusters_=[]},o.prototype.repaint=function(){const t=this.clusters_.slice();this.clusters_.length=0,this.resetViewport(),this.redraw(),window.setTimeout(function(){for(var e=0,i;i=t[e];e++)i.remove()},0)},o.prototype.redraw=function(){this.createClusters_()},o.prototype.distanceBetweenPoints_=function(t,e){if(!t||!e)return 0;var i=6371,s=(e.lat()-t.lat())*Math.PI/180,r=(e.lng()-t.lng())*Math.PI/180,n=Math.sin(s/2)*Math.sin(s/2)+Math.cos(t.lat()*Math.PI/180)*Math.cos(e.lat()*Math.PI/180)*Math.sin(r/2)*Math.sin(r/2),h=2*Math.atan2(Math.sqrt(n),Math.sqrt(1-n)),c=i*h;return c},o.prototype.addToClosestCluster_=function(t){for(var e=4e4,i=null,s=0,r;r=this.clusters_[s];s++){var n=r.getCenter();if(n){var h=this.distanceBetweenPoints_(n,t.getPosition());h<e&&(e=h,i=r)}}if(i&&i.isMarkerInClusterBounds(t))i.addMarker(t);else{const c=new l(this);c.addMarker(t),this.clusters_.push(c)}},o.prototype.createClusters_=function(){if(this.ready_)for(var t=new google.maps.LatLngBounds(this.map_.getBounds().getSouthWest(),this.map_.getBounds().getNorthEast()),e=this.getExtendedBounds(t),i=0,s;s=this.markers_[i];i++)!s.isAdded&&this.isMarkerInBounds_(s,e)&&this.addToClosestCluster_(s)};function l(t){this.markerClusterer_=t,this.map_=t.getMap(),this.gridSize_=t.getGridSize(),this.minClusterSize_=t.getMinClusterSize(),this.averageCenter_=t.isAverageCenter(),this.center_=null,this.markers_=[],this.bounds_=null,this.clusterIcon_=new p(this,t.getStyles(),t.getGridSize())}l.prototype.isMarkerAlreadyAdded=function(t){if(this.markers_.indexOf)return this.markers_.indexOf(t)!=-1;for(var e=0,i;i=this.markers_[e];e++)if(i==t)return!0;return!1},l.prototype.addMarker=function(t){if(this.isMarkerAlreadyAdded(t))return!1;if(!this.center_)this.center_=t.getPosition(),this.calculateBounds_();else if(this.averageCenter_){var e=this.markers_.length+1,i=(this.center_.lat()*(e-1)+t.getPosition().lat())/e,s=(this.center_.lng()*(e-1)+t.getPosition().lng())/e;this.center_=new google.maps.LatLng(i,s),this.calculateBounds_()}t.isAdded=!0,this.markers_.push(t);var r=this.markers_.length;if(r<this.minClusterSize_&&t.getMap()!=this.map_&&t.setMap(this.map_),r==this.minClusterSize_)for(var n=0;n<r;n++)this.markers_[n].setMap(null);return r>=this.minClusterSize_&&t.setMap(null),this.updateIcon(),!0},l.prototype.getMarkerClusterer=function(){return this.markerClusterer_},l.prototype.getBounds=function(){for(var t=new google.maps.LatLngBounds(this.center_,this.center_),e=this.getMarkers(),i=0,s;s=e[i];i++)t.extend(s.getPosition());return t},l.prototype.remove=function(){this.clusterIcon_.remove(),this.markers_.length=0,delete this.markers_},l.prototype.getSize=function(){return this.markers_.length},l.prototype.getMarkers=function(){return this.markers_},l.prototype.getCenter=function(){return this.center_},l.prototype.calculateBounds_=function(){var t=new google.maps.LatLngBounds(this.center_,this.center_);this.bounds_=this.markerClusterer_.getExtendedBounds(t)},l.prototype.isMarkerInClusterBounds=function(t){return this.bounds_.contains(t.getPosition())},l.prototype.getMap=function(){return this.map_},l.prototype.updateIcon=function(){var t=this.map_.getZoom(),e=this.markerClusterer_.getMaxZoom();if(e&&t>e){for(var i=0,s;s=this.markers_[i];i++)s.setMap(this.map_);return}if(this.markers_.length<this.minClusterSize_){this.clusterIcon_.hide();return}var r=this.markerClusterer_.getStyles().length,n=this.markerClusterer_.getCalculator()(this.markers_,r);this.clusterIcon_.setCenter(this.center_),this.clusterIcon_.setSums(n),this.clusterIcon_.show()};function p(t,e,i){t.getMarkerClusterer().extend(p,google.maps.OverlayView),this.styles_=e,this.padding_=i||0,this.cluster_=t,this.center_=null,this.map_=t.getMap(),this.div_=null,this.sums_=null,this.visible_=!1,this.setMap(this.map_)}p.prototype.triggerClusterClick=function(){var t=this.cluster_.getMarkerClusterer();google.maps.event.trigger(t,"clusterclick",this.cluster_),t.isZoomOnClick()&&this.map_.fitBounds(this.cluster_.getBounds())},p.prototype.onAdd=function(){if(this.div_=document.createElement("DIV"),this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.innerHTML=this.sums_.text}var e=this.getPanes();e.overlayMouseTarget.appendChild(this.div_);var i=this;google.maps.event.addDomListener(this.div_,"click",function(){i.triggerClusterClick()})},p.prototype.getPosFromLatLng_=function(t){var e=this.getProjection().fromLatLngToDivPixel(t);return e.x-=parseInt(this.width_/2,10),e.y-=parseInt(this.height_/2,10),e},p.prototype.draw=function(){if(this.visible_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.top=t.y+"px",this.div_.style.left=t.x+"px"}},p.prototype.hide=function(){this.div_&&(this.div_.style.display="none"),this.visible_=!1},p.prototype.show=function(){if(this.div_){var t=this.getPosFromLatLng_(this.center_);this.div_.style.cssText=this.createCss(t),this.div_.style.display=""}this.visible_=!0},p.prototype.remove=function(){this.setMap(null)},p.prototype.onRemove=function(){this.div_&&this.div_.parentNode&&(this.hide(),this.div_.parentNode.removeChild(this.div_),this.div_=null)},p.prototype.setSums=function(t){this.sums_=t,this.text_=t.text,this.index_=t.index,this.div_&&(this.div_.innerHTML=t.text),this.useStyle()},p.prototype.useStyle=function(){var t=Math.max(0,this.sums_.index-1);t=Math.min(this.styles_.length-1,t);var e=this.styles_[t];this.url_=e.url,this.height_=e.height,this.width_=e.width,this.textColor_=e.textColor,this.anchor_=e.anchor,this.textSize_=e.textSize,this.backgroundPosition_=e.backgroundPosition},p.prototype.setCenter=function(t){this.center_=t},p.prototype.createCss=function(t){var e=[];e.push("background-image:url("+this.url_+");");var i=this.backgroundPosition_?this.backgroundPosition_:"0 0";e.push("background-position:"+i+";"),typeof this.anchor_=="object"?(typeof this.anchor_[0]=="number"&&this.anchor_[0]>0&&this.anchor_[0]<this.height_?e.push("height:"+(this.height_-this.anchor_[0])+"px; padding-top:"+this.anchor_[0]+"px;"):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px;"),typeof this.anchor_[1]=="number"&&this.anchor_[1]>0&&this.anchor_[1]<this.width_?e.push("width:"+(this.width_-this.anchor_[1])+"px; padding-left:"+this.anchor_[1]+"px;"):e.push("width:"+this.width_+"px; text-align:center;")):e.push("height:"+this.height_+"px; line-height:"+this.height_+"px; width:"+this.width_+"px; text-align:center;");var s=this.textColor_?this.textColor_:"black",r=this.textSize_?this.textSize_:11;return e.push("cursor:pointer; top:"+t.y+"px; left:"+t.x+"px; color:"+s+"; position:absolute; font-size:"+r+"px; font-family:Arial,sans-serif; font-weight:bold"),e.join("")};const w=window.UIkitwk||window.UIkit,{$:_,$$:k,append:S,attr:v,css:d,html:I,ready:L}=w?.util??{};L?.(async function(){const t=k('script[type="widgetkit/map"]');if(t.length){await b();for(const e of t){const i=_("<div>");v(i,e.dataset);const s=JSON.parse(e.innerHTML);e.parentNode.replaceChild(i,e);let r=s.markers,n=[],h,c,x,u,f,M=C;Object.keys(s).forEach(function(a){isNaN(s[a])||(s[a]=Number(s[a]))}),x=r.length?new google.maps.LatLng(r[0].lat,r[0].lng):new google.maps.LatLng(-34.397,150.644),c={zoom:parseInt(s.zoom,10),center:x,streetViewControl:s.mapctrl,navigationControl:s.mapctrl,mapTypeId:google.maps.MapTypeId[s.maptypeid.toUpperCase()],mapTypeControl:s.maptypecontrol,zoomControl:s.zoomcontrol,disableDefaultUI:s.disabledefaultui,gestureHandling:s.draggable||s.zoomwheel?"auto":"none",mapTypeControlOptions:{style:google.maps.MapTypeControlStyle.DROPDOWN_MENU,mapTypeIds:["styled_map",google.maps.MapTypeId.ROADMAP,google.maps.MapTypeId.SATELLITE]},zoomControlOptions:{style:s.mapctrl?google.maps.ZoomControlStyle.DEFAULT:google.maps.ZoomControlStyle.SMALL}},h=new google.maps.Map(i,c),r.length&&s.directions&&(u=d(_('<a target="_blank"></a>'),{padding:"5px 1px","text-decoration":"none"}),f=d(_("<div></div>"),{"-webkit-background-clip":"padding-box",padding:"1px 4px",backgroundColor:"white",borderColor:"rgba(0, 0, 0, 0.14902)",borderStyle:"solid",borderWidth:"1px",cursor:"pointer",textAlign:"center",fontFamily:"Roboto, Arial, sans-serif",fontWeight:500,boxShadow:"rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px",index:1}),I(f,'<span style="color:#000;"><span style="color:blue;">&#8627;</span>'+(s.directionsText||"Get Directions")+"</span>"),S(u,f),u.setHref=function(a,m){v(this,"href","https://maps.google.com/?daddr="+a+","+m)},h.controls[google.maps.ControlPosition.TOP_RIGHT].push(u)),r.length&&s.marker&&(r.forEach(function(a,m){let g=new google.maps.Marker({position:new google.maps.LatLng(a.lat,a.lng),map:h,title:a.title}),y;(M&&a.icon||s.marker_icon)&&M.setIcon(g,a.icon||s.marker_icon),n.push(g),s.marker>=1&&(y=new google.maps.InfoWindow({content:a.content,maxWidth:s.popup_max_width?parseInt(s.popup_max_width,10):300}),google.maps.event.addListener(g,"click",function(){s.marker>=2&&a.content&&y.open(h,g),u&&(u.setHref(a.lat,a.lng),d(u,"display","block"))}),m===0&&(s.marker===3&&a.content&&y.open(h,g),u&&(u.setHref(a.lat,a.lng),d(u,"display","block"))))}),h.panTo(new google.maps.LatLng(r[0].lat,r[0].lng))),s.markercluster&&new o(h,n);const z=new google.maps.StyledMapType([{featureType:"all",elementType:"all",stylers:[{invert_lightness:s.styler_invert_lightness},{hue:s.styler_hue},{saturation:s.styler_saturation},{lightness:s.styler_lightness},{gamma:s.styler_gamma}]}],{name:"Styled"});h.mapTypes.set("styled_map",z),s.maptypeid.toUpperCase()==="ROADMAP"&&h.setMapTypeId("styled_map")}}});function b(){return T(`https://maps.google.com/maps/api/js?key=${window.GOOGLE_MAPS_API_KEY||""}&callback=Function.prototype`)}function T(t){return new Promise((e,i)=>{const s=document.createElement("script");s.src=t,s.onload=()=>e(t),s.onerror=()=>i(t),document.head.appendChild(s)})}})();
