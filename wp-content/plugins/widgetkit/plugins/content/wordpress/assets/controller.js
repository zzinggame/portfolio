"use strict";angular.module("widgetkit").controller("wordpressCtrl",["$scope",function(a){a.content.data.mapping||(a.content.data.mapping=[]);var t=this,n=a.content.data.mapping;t.mapping=n,angular.forEach(n,function(i,p){angular.isArray(i)&&(n[p]={})}),t.addMap=function(){n.push({})},t.deleteMap=function(i){n.splice(n.indexOf(i),1)}}]);
