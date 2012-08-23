//     (c) 2012 Airbnb, Inc.
//     
//     infinity.js may be freely distributed under the terms of the BSD
//     license. For all licensing information, details, and documention:
//     http://airbnb.github.com/infinity
!function(e,t,n){"use strict";function f(e,t){t=t||{},this.$el=k(),this.$shadow=k(),e.append(this.$el),this.lazy=!!t.lazy,this.lazyFn=t.lazy||null,l(this),this.top=this.$el.offset().top,this.width=0,this.height=0,this.pages=[],this.startIndex=0,w.attach(this)}function l(e){e._$buffer=k().prependTo(e.$el)}function c(e){var t,n=e.pages,r=e._$buffer;n.length>0?(t=n[e.startIndex],r.height(t.top)):r.height(0)}function h(e,t){t.$el.remove(),e.$el.append(t.$el),N(t,e.height),t.$el.remove()}function p(e){var n,r,i,s=e.pages,o=!1,u=!0;n=e.startIndex,r=t.min(n+a,s.length);for(n;n<r;n++)i=s[n],e.lazy&&i.lazyload(e.lazyFn),o&&i.onscreen&&(u=!1),u?i.onscreen||(o=!0,i.appendTo(e.$el)):(i.stash(e.$shadow),i.appendTo(e.$el))}function d(r){var i,s,o,u,f,l,h,d=r.startIndex,v=n(e).scrollTop()-r.top,m=n(e).height(),g=v+m,b=y(r,v,g);if(b<0||b===d)return d;u=r.pages,d=r.startIndex,f=new Array(u.length),l=t.min(d+a,u.length),h=t.min(b+a,u.length);for(i=b,s=h;i<s;i++)f[i]=!0;for(i=d,s=l;i<s;i++)f[i]||u[i].stash(r.$shadow);return r.startIndex=b,p(r),c(r),b}function v(e){return e instanceof T?e:(typeof e=="string"&&(e=n(e)),new T(e))}function m(e,t){g(e)}function g(e){var t,n,r,i,s,o,u,a,f=e.pages,l=[];n=new E(e),l.push(n);for(r=0,i=f.length;r<i;r++){t=f[r];for(s=0,o=t.items.length;s<o;s++)u=t.items[r],a=v(u.$el),n.hasVacancy()?n.append(a):(n=new E(e),l.push(n),n.append(a)),u.cleanup();t.cleanup()}e.pages=l}function y(e,n,r){var i=b(e,n,r);return i=t.max(i-u,0),i=t.min(i,e.pages.length),i}function b(e,n,r){var i,s,o,a,f,l,c,h=e.pages,p=n+(r-n)/2;a=t.min(e.startIndex+u,h.length-1);if(h.length<=0)return-1;o=h[a],f=o.top+o.height/2,c=p-f;if(c<0){for(i=a-1;i>=0;i--){o=h[i],f=o.top+o.height/2,l=p-f;if(l>0)return l<-c?i:i+1;c=l}return 0}if(c>0){for(i=a+1,s=h.length;i<s;i++){o=h[i],f=o.top+o.height/2,l=p-f;if(l<0)return-l<c?i:i-1;c=l}return h.length-1}return a}function E(e){this.parent=e,this.items=[],this.$el=k(),this.id=S.generatePageId(this),this.$el.attr(o,this.id),this.top=0,this.bottom=0,this.width=0,this.height=0,this.lazyloaded=!1,this.onscreen=!1}function x(e,t){var n,r,i,s=t.items;for(n=0,r=s.length;n<r;n++)if(s[n]===e){i=n;break}return i==null?!1:(s.splice(i,1),t.bottom-=e.height,t.height=t.bottom-t.top,t.hasVacancy()||m(t.parent,t),!0)}function T(e){this.$el=e,this.parent=null,this.top=0,this.bottom=0,this.width=0,this.height=0}function N(e,t){var n=e.$el,r=n.offset();e.top=t,e.height=n.outerHeight(!0),e.bottom=e.top+e.height,e.width=n.width()}function C(){return n("<div></div>")}function k(){return C().css({margin:0,padding:0,border:"none"})}function L(e){return parseInt(e.replace("px",""),10)}var r=e.infinity,i=e.infinity={},s=i.config={},o="data-infinity-pageid",u=1,a=u*2+1;s.PAGE_TO_SCREEN_RATIO=3,s.SCROLL_THROTTLE=350,f.prototype.append=function(e){if(!e||e.length&&e.length===0)return null;var t,n=v(e),r=this.pages,i=!1;h(this,n),this.height+=n.height,this.$el.height(this.height),r.length>0&&(t=r[r.length-1]);if(!t||!t.hasVacancy())t=new E(this),r.push(t),i=!0;return t.append(n),p(this),n},f.prototype.remove=function(){this.$el.remove(),this.cleanup()},f.prototype.find=function(e){var t,r,i;return typeof e=="string"?(r=this.$el.find(e),i=this.$shadow.find(e),this.find(r).concat(this.find(i))):e instanceof T?[e]:(t=[],e.each(function(){var e,r,i,s,u,a,f=n(this),l=f.parent();while(!l.attr(o)&&l.length>0)f=l,l=l.parent();e=parseInt(l.attr(o),10),r=S.lookup(e);if(r){i=r.items;for(s=0,u=i.length;s<u;s++){a=i[s];if(a.$el.is(f)){t.push(a);break}}}}),t)},f.prototype.cleanup=function(){var e=this.pages;w.detach(this);while(e.length>0)e.pop().cleanup()};var w=function(e,t){function o(){r||(setTimeout(u,s.SCROLL_THROTTLE),r=!0)}function u(){var e,t;for(e=0,t=i.length;e<t;e++)d(i[e]);r=!1}var n=!1,r=!1,i=[];return{attach:function(r){n||(t(e).on("scroll",o),n=!0),i.push(r)},detach:function(r){var s,u;for(s=0,u=i.length;s<u;s++)if(i[s]===r)return i.splice(s,1),i.length===0&&(t(e).off("scroll",o),n=!1),!0;return!1}}}(e,n);E.prototype.append=function(e){var t=this.items;t.length===0&&(this.top=e.top),this.bottom=e.bottom,this.width=this.width>e.width?this.width:e.width,this.height=this.bottom-this.top,t.push(e),e.parent=this,this.$el.append(e.$el),this.lazyloaded=!1},E.prototype.prepend=function(e){var t=this.items;this.bottom+=e.height,this.width=this.width>e.width?this.width:e.width,this.height=this.bottom-this.top,t.push(e),e.parent=this,this.$el.prepend(e.$el),this.lazyloaded=!1},E.prototype.hasVacancy=function(){return this.height<n(e).height()*s.PAGE_TO_SCREEN_RATIO},E.prototype.appendTo=function(e){this.onscreen||(this.$el.remove(),this.$el.appendTo(e),this.onscreen=!0)},E.prototype.prependTo=function(e){this.onscreen||(this.$el.prependTo(e),this.onscreen=!0)},E.prototype.stash=function(e){this.onscreen&&(this.$el.remove(),this.onscreen=!1,e.append(this.$el))},E.prototype.remove=function(){this.onscreen&&(this.$el.remove(),this.cleanup(),this.onscreen=!1)},E.prototype.cleanup=function(){var e=this.items;this.parent=null,S.remove(this);while(e.length>0)e.pop().cleanup()},E.prototype.lazyload=function(e){var t,n;if(!this.lazyloaded){for(t=0,n=this.$el.length;t<n;t++)e.call(this.$el[t],this.$el[t]);this.lazyloaded=!0}};var S=function(){var e=[];return{generatePageId:function(t){return e.push(t)-1},lookup:function(t){return t>=e.length?null:e[t]},remove:function(t){var n=t.id;return n>=e.length?!1:e[n]?(e[n]=null,!0):!1}}}();T.prototype.remove=function(){this.$el.remove(),x(this,this.parent),this.cleanup()},T.prototype.cleanup=function(){this.parent=null},i.ListView=f,i.Page=E,i.ListItem=T,i.noConflict=function(){return e.infinity=r,i}}(window,Math,jQuery);