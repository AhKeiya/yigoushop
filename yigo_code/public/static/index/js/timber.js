"use strict";(function(b){b.fn.prepareTransition=function(){return this.each(function(){var a=b(this);a.one("TransitionEnd webkitTransitionEnd transitionend oTransitionEnd",function(){a.removeClass("is-transitioning")});var e=["transition-duration","-moz-transition-duration","-webkit-transition-duration","-o-transition-duration"];var f=0;b.each(e,function(d,g){f=parseFloat(a.css(g))||f});if(f!=0){a.addClass("is-transitioning");a[0].offsetWidth}})}})(jQuery);function replaceUrlParam(f,h,b){var g=new RegExp("("+h+"=).*?(&|$)"),d=f;return d=f.search(g)>=0?f.replace(g,"$1"+b+"$2"):d+(d.indexOf("?")>0?"&":"?")+h+"="+b}if((typeof Shopify)==="undefined"){Shopify={}}if(!Shopify.formatMoney){Shopify.formatMoney=function(a,c){var g="",f=/\{\{\s*(\w+)\s*\}\}/,d=(c||this.money_format);if(typeof a=="string"){a=a.replace(".","")}function b(i,h){return(typeof i=="undefined"?h:i)}function e(k,m,n,i){m=b(m,2);n=b(n,",");i=b(i,".");if(isNaN(k)||k==null){return 0}k=(k/100).toFixed(m);var l=k.split("."),j=l[0].replace(/(\d)(?=(\d\d\d)+(?!\d))/g,"$1"+n),h=l[1]?(i+l[1]):"";return j+h}switch(d.match(f)[1]){case"amount":g=e(a,2);break;case"amount_no_decimals":g=e(a,0);break;case"amount_with_comma_separator":g=e(a,2,".",",");break;case"amount_no_decimals_with_comma_separator":g=e(a,0,".",",");break}return d.replace(f,g)}}window.timber=window.timber||{};timber.cacheSelectors=function(){timber.cache={$html:$("html"),$body:$("body"),$navigation:$("#AccessibleNav"),$mobileSubNavToggle:$(".mobile-nav__toggle"),$changeView:$(".change-view"),$productImageWrap:$("#ProductPhoto"),$productImage:$("#ProductPhotoImg"),$thumbImages:$("#ProductThumbs").find("a.product-single__thumbnail"),$productImageGallery:$(".gallery__item"),$recoverPasswordLink:$("#RecoverPassword"),$hideRecoverPasswordLink:$("#HideRecoverPasswordLink"),$recoverPasswordForm:$("#RecoverPasswordForm"),$customerLoginForm:$("#CustomerLoginForm"),$passwordResetSuccess:$("#ResetSuccess")}};timber.init=function(){timber.cacheSelectors();timber.accessibleNav();timber.drawersInit();timber.mobileNavToggle();timber.productImageSwitch();timber.productImageGallery();timber.productImageZoom();timber.responsiveVideos();timber.collectionViews();timber.loginForms();timber.thumbGallerySlider();timber.sliderProduct();timber.lightBoxAccount();timber.quickView();if(tada_index==1){timber.slideshow()}timber.googlemaps();timber.toTop();if(tada_newsletter){timber.ModalNewsletter()}};timber.accessibleNav=function(){var b=timber.cache.$navigation,a=b.find("a"),e=b.children("li").find("a"),c=b.find(".site-nav--has-dropdown"),d=b.find(".site-nav__dropdown").find("a"),f="nav-hover",h="nav-focus";c.on("mouseenter touchstart",function(n){var m=$(this);if(!m.hasClass(f)){n.preventDefault()}l(m)});c.on("mouseleave",function(){j($(this))});d.on("touchstart",function(m){m.stopImmediatePropagation()});a.focus(function(){i($(this))});a.blur(function(){k(e)});function i(m){var o=m.next("ul"),p=o.hasClass("sub-nav")?true:false,q=$(".site-nav__dropdown").has(m).length,n=null;if(!q){k(e);g(m)}else{n=m.closest(".site-nav--has-dropdown").find("a");g(n)}}function l(m){m.addClass(f);setTimeout(function(){timber.cache.$body.on("touchstart",function(){j(m)})},250)}function j(m){m.removeClass(f);timber.cache.$body.off("touchstart")}function g(m){m.addClass(h)}function k(m){m.removeClass(h)}};timber.drawersInit=function(){timber.LeftDrawer=new timber.Drawers("NavDrawer","left");timber.RightDrawer=new timber.Drawers("CartDrawer","right",{})};timber.mobileNavToggle=function(){timber.cache.$mobileSubNavToggle.on("click",function(){$(this).parent().toggleClass("mobile-nav--expanded")})};timber.getHash=function(){return window.location.hash};timber.updateHash=function(a){window.location.hash="#"+a;$("#"+a).attr("tabindex",-1).focus()};timber.responsiveVideos=function(){$('iframe[src*="youtube.com/embed"]').wrap('<div class="video-wrapper"></div>');$('iframe[src*="player.vimeo"]').wrap('<div class="video-wrapper"></div>')};timber.productPage=function(j){var h=j.money_format,l=j.variant,k=j.selector;var d=$("#ProductPhotoImg"),a=$("#AddToCart"),e=$("#ProductPrice"),c=$("#ComparePrice"),f=$(".quantity-selector, label + .js-qty"),b=$("#AddToCartText");if(l){if(l.featured_image){var i=l.featured_image,g=d[0];Shopify.Image.switchImage(i,g,timber.switchImage)}if(l.available){a.removeClass("disabled").prop("disabled",false);b.html("Buy Now");f.show()}else{a.addClass("disabled").prop("disabled",true);b.html("Sold Out");f.hide()}e.html(Shopify.formatMoney(l.price,h));if(l.compare_at_price>l.price){c.html("<del>"+Shopify.formatMoney(l.compare_at_price,h)+"</del>").show()}else{c.hide()}}else{a.addClass("disabled").prop("disabled",true);b.html("Unavailable");f.hide()}};timber.productImageSwitch=function(){if(timber.cache.$thumbImages.length){timber.cache.$thumbImages.on("click",function(a){a.preventDefault();var b=$(this).attr("href");var c=$(this).attr("data-image-id");timber.switchImage(b,{id:c},timber.cache.$productImage)})}};timber.switchImage=function(d,c,b){var a=$(b);a.attr("src",d);a.attr("data-image-id",c.id)};timber.productImageZoom=function(){return;if(!timber.cache.$productImageWrap.length||timber.cache.$html.hasClass("supports-touch")){return}timber.cache.$productImageWrap.trigger("zoom.destroy");timber.cache.$productImageWrap.addClass("image-zoom").zoom({url:timber.cache.$productImage.attr("data-zoom")})};timber.productImageGallery=function(){if(!timber.cache.$productImageGallery.length){return}timber.cache.$productImageGallery.magnificPopup({type:"image",mainClass:"mfp-fade",closeOnBgClick:true,closeBtnInside:false,closeOnContentClick:true,tClose:"translation missing: en.products.zoom.close",removalDelay:500,callbacks:{open:function(){$("html").css("overflow-y","hidden")},close:function(){$("html").css("overflow-y","")}},gallery:{enabled:true,navigateByImgClick:false,arrowMarkup:'<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"><span class="mfp-chevron mfp-chevron-%dir%"></span></button>',tPrev:"translation missing: en.products.zoom.prev",tNext:"translation missing: en.products.zoom.next"}});timber.cache.$productImage.bind("click",function(){var a=$(this).attr("data-image-id");timber.cache.$productImageGallery.filter('[data-image-id="'+a+'"]').trigger("click")})};timber.thumbGallerySlider=function(){if($(".thumb__element").size()>4){$(".product-single__thumbnails").owlCarousel({navigation:true,pagination:false,autoPlay:tada_adsspeed,items:4,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,4],itemsDesktopSmall:[979,3],itemsTablet:[768,3],itemsTabletSmall:[540,2],itemsMobile:[360,2],})}};timber.collectionViews=function(){if(timber.cache.$changeView.length){timber.cache.$changeView.on("click",function(){var c=$(this).data("view"),b=document.URL,a=b.indexOf("?")>-1;$(".collection-view button").removeClass("change-view--active");if(c=="grid"){$(".grid-uniform-category").removeClass("category-full-width");$(".grid-button").addClass("change-view--active")}else{$(".grid-uniform-category").addClass("category-full-width");$(".list-button").addClass("change-view--active")}})}};timber.slideshow=function(){$("#carousel").flexslider({animation:"slide",controlNav:false,animationLoop:false,slideshow:false,itemWidth:292,itemMargin:0,asNavFor:"#slider"});$("#slider").flexslider({animation:"slide",controlNav:false,slideshowSpeed:3000,slideshow:false,sync:"#carousel"})};timber.sliderProduct=function(){$(".ads-banner-slider").owlCarousel({navigation:false,pagination:false,autoPlay:tada_adsspeed,items:1,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,1],itemsDesktopSmall:[979,1],itemsTablet:[768,1],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".home-gallery-slider").owlCarousel({navigation:false,pagination:true,autoPlay:tada_block1gallery,items:1,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,1],itemsDesktopSmall:[979,1],itemsTablet:[768,1],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".home-products-slider").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1product,items:4,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,4],itemsDesktopSmall:[979,4],itemsTablet:[768,3],itemsTabletSmall:[540,2],itemsMobile:[360,1],});$(".home-collection-inner,.slider-tab").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1product,items:4,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,4],itemsDesktopSmall:[979,4],itemsTablet:[768,3],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".sale-products-slider").owlCarousel({navigation:false,pagination:true,autoPlay:tada_block1gallery,items:5,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,5],itemsDesktopSmall:[979,4],itemsTablet:[768,3],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".index2 .brands-elements, .index4 .brands-elements").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1product,items:6,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,6],itemsDesktopSmall:[979,5],itemsTablet:[768,4],itemsTabletSmall:[540,3],itemsMobile:[360,2],});$(".related-products-items").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1product,items:4,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,4],itemsDesktopSmall:[979,4],itemsTablet:[768,3],itemsTabletSmall:[540,2],itemsMobile:[360,1],});$(".index3 .homeblog-content").owlCarousel({navigation:true,pagination:false,autoPlay:false,items:3,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,3],itemsDesktopSmall:[979,3],itemsTablet:[768,2],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".index4 .top-offer-products, .index4 .latest-products, .index4 .special-products, .index4 .best-seller-products, .index4 .special-products").owlCarousel({navigation:true,pagination:false,autoPlay:false,items:1,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,1],itemsDesktopSmall:[979,1],itemsTablet:[768,1],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".index5 .gtslider").owlCarousel({navigation:false,pagination:true,autoPlay:tada_block1gallery,items:3,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,3],itemsDesktopSmall:[979,2],itemsTablet:[768,2],itemsTabletSmall:[540,1],itemsMobile:[360,1],});$(".index5 .tabslider").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1gallery,items:4,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,4],itemsDesktopSmall:[979,3],itemsTablet:[768,3],itemsTabletSmall:[540,2],itemsMobile:[360,2],});$(".index5 .homeblog-content").owlCarousel({navigation:true,pagination:false,autoPlay:tada_block1gallery,items:3,slideSpeed:200,paginationSpeed:1000,rewindSpeed:1000,itemsDesktop:[1199,3],itemsDesktopSmall:[979,2],itemsTablet:[768,2],itemsTabletSmall:[540,1],itemsMobile:[360,1],})};timber.lightBoxAccount=function(){$("#login_link").fancybox({openEffect:"elastic",closeEffect:"elastic"})};timber.loginForms=function(){function b(){timber.cache.$recoverPasswordForm.show();timber.cache.$customerLoginForm.hide()}function a(){timber.cache.$recoverPasswordForm.hide();timber.cache.$customerLoginForm.show()}timber.cache.$recoverPasswordLink.on("click",function(c){c.preventDefault();b()});timber.cache.$hideRecoverPasswordLink.on("click",function(c){c.preventDefault();a()});if(timber.getHash()=="#recover"){b()}};timber.resetPasswordSuccess=function(){timber.cache.$passwordResetSuccess.show()};timber.Drawers=(function(){var a=function(c,e,d){var b={close:".js-drawer-close",open:".js-drawer-open-"+e,openClass:"js-drawer-open",dirOpenClass:"js-drawer-open-"+e};this.$nodes={parent:$("body, html"),page:$("#PageContainer"),moved:$(".is-moved-by-drawer")};this.config=$.extend(b,d);this.position=e;this.$drawer=$("#"+c);if(!this.$drawer.length){return false}this.drawerIsOpen=false;this.init()};a.prototype.init=function(){$(this.config.open).on("click",$.proxy(this.open,this));this.$drawer.find(this.config.close).on("click",$.proxy(this.close,this))};a.prototype.open=function(b){var c=false;if(b){b.preventDefault()}else{c=true}if(b&&b.stopPropagation){b.stopPropagation();this.$activeSource=$(b.currentTarget)}if(this.drawerIsOpen&&!c){return this.close()}this.$nodes.moved.addClass("is-transitioning");this.$drawer.prepareTransition();this.$nodes.parent.addClass(this.config.openClass+" "+this.config.dirOpenClass);this.drawerIsOpen=true;this.trapFocus(this.$drawer,"drawer_focus");if(this.config.onDrawerOpen&&typeof(this.config.onDrawerOpen)=="function"){if(!c){this.config.onDrawerOpen()}}if(this.$activeSource&&this.$activeSource.attr("aria-expanded")){this.$activeSource.attr("aria-expanded","true")}this.$nodes.page.on("touchmove.drawer",function(){return false});this.$nodes.page.on("click.drawer",$.proxy(function(){this.close();return false},this))};a.prototype.close=function(){if(!this.drawerIsOpen){return}$(document.activeElement).trigger("blur");this.$nodes.moved.prepareTransition({disableExisting:true});this.$drawer.prepareTransition({disableExisting:true});this.$nodes.parent.removeClass(this.config.dirOpenClass+" "+this.config.openClass);this.drawerIsOpen=false;this.removeTrapFocus(this.$drawer,"drawer_focus");this.$nodes.page.off(".drawer")};a.prototype.trapFocus=function(b,d){var c=d?"focusin."+d:"focusin";b.attr("tabindex","-1");b.focus();$(document).on(c,function(e){if(b[0]!==e.target&&!b.has(e.target).length){b.focus()}})};a.prototype.removeTrapFocus=function(b,d){var c=d?"focusin."+d:"focusin";b.removeAttr("tabindex");$(document).off(c)};return a})();timber.quickView=function(){$(".quick_shop").fancybox({openEffect:"elastic",closeEffect:"elastic"});$.fancybox.update()};timber.googlemaps=function(){if(jQuery().gMap){if($("#contact_map").length){console.log(89);$("#contact_map").gMap({zoom:17,scrollwheel:false,maptype:"ROADMAP",markers:[{address:"474 Ontario St Toronto, ON M4X 1M7 Canada",html:"_address"}]})}}};timber.toTop=function(){function a(c){var b=$("#scroll-to-top");b.removeClass("off on"),b.addClass("on"==c?"on":"off")}$(window).scroll(function(){var b=$(this).scrollTop(),c=$(this).height();if(b>0){var d=b+c/2}else{var d=1}a(1000>d&&c>d?"off":"on")}),$("#scroll-to-top").click(function(b){b.preventDefault(),$("body,html").animate({scrollTop:0},800,"swing")})};timber.ModalNewsletter=function(){$("#newsletter_popup").fancybox({beforeShow:function(){$(".fancybox-skin").addClass("newsletter-skin")}}).trigger("click")};$(timber.init);