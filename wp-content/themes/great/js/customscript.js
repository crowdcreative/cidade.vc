	/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */
(function($){$.fn.superfish=function(op){var sf=$.fn.superfish,c=sf.c,$arrow=$(['<span class="',c.arrowClass,'"> &#xbb;</span>'].join("")),over=function(){var $$=$(this),menu=getMenu($$);clearTimeout(menu.sfTimer);$$.showSuperfishUl().siblings().hideSuperfishUl();},out=function(){var $$=$(this),menu=getMenu($$),o=sf.op;clearTimeout(menu.sfTimer);menu.sfTimer=setTimeout(function(){o.retainPath=($.inArray($$[0],o.$path)>-1);$$.hideSuperfishUl();if(o.$path.length&&$$.parents(["li.",o.hoverClass].join("")).length<1){over.call(o.$path);}},o.delay);},getMenu=function($menu){var menu=$menu.parents(["ul.",c.menuClass,":first"].join(""))[0];sf.op=sf.o[menu.serial];return menu;},addArrow=function($a){$a.addClass(c.anchorClass).append($arrow.clone());};return this.each(function(){var s=this.serial=sf.o.length;var o=$.extend({},sf.defaults,op);o.$path=$("li."+o.pathClass,this).slice(0,o.pathLevels).each(function(){$(this).addClass([o.hoverClass,c.bcClass].join(" ")).filter("li:has(ul)").removeClass(o.pathClass);});sf.o[s]=sf.op=o;$("li:has(ul)",this)[($.fn.hoverIntent&&!o.disableHI)?"hoverIntent":"hover"](over,out).each(function(){if(o.autoArrows){addArrow($(">a:first-child",this));}}).not("."+c.bcClass).hideSuperfishUl();var $a=$("a",this);$a.each(function(i){var $li=$a.eq(i).parents("li");$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});});o.onInit.call(this);}).each(function(){var menuClasses=[c.menuClass];if(sf.op.dropShadows&&!($.browser.msie&&$.browser.version<7)){menuClasses.push(c.shadowClass);}$(this).addClass(menuClasses.join(" "));});};var sf=$.fn.superfish;sf.o=[];sf.op={};sf.IE7fix=function(){var o=sf.op;if($.browser.msie&&$.browser.version>6&&o.dropShadows&&o.animation.opacity!=undefined){this.toggleClass(sf.c.shadowClass+"-off");}};sf.c={bcClass:"sf-breadcrumb",menuClass:"sf-js-enabled",anchorClass:"sf-with-ul",arrowClass:"sf-sub-indicator",shadowClass:"sf-shadow"};sf.defaults={hoverClass:"sfHover",pathClass:"overideThisToUse",pathLevels:1,delay:800,animation:{opacity:"show"},speed:"normal",autoArrows:true,dropShadows:true,disableHI:false,onInit:function(){},onBeforeShow:function(){},onShow:function(){},onHide:function(){}};$.fn.extend({hideSuperfishUl:function(){var o=sf.op,not=(o.retainPath===true)?o.$path:"";o.retainPath=false;var $ul=$(["li.",o.hoverClass].join(""),this).add(this).not(not).removeClass(o.hoverClass).find(">ul").hide().css("visibility","hidden");o.onHide.call($ul);return this;},showSuperfishUl:function(){var o=sf.op,sh=sf.c.shadowClass+"-off",$ul=this.addClass(o.hoverClass).find(">ul:hidden").css("visibility","visible");sf.IE7fix.call($ul);o.onBeforeShow.call($ul);$ul.animate(o.animation,o.speed,function(){sf.IE7fix.call($ul);o.onShow.call($ul);});return this;}});})(jQuery);
	





	// Sistema de escrita automática

	var contador = 0;
	
	
	(function ($) {
	  // writes the string
	  //
	  // @param jQuery $target
	  // @param String str
	  // @param Numeric cursor
	  // @param Numeric delay
	  // @param Function cb
	  // @return void
	  
	  function typeString($target, str, cursor, delay, cb) {
		$target.html(function (_, html) {
		  return html + str[cursor];
		});
		
		if (cursor < str.length - 1) {
		  setTimeout(function () {
			typeString($target, str, cursor + 1, delay, cb);
		  }, delay);
		}
		else {
		  cb();
		}
	  }
	  
	  // clears the string
	  //
	  // @param jQuery $target
	  // @param Numeric delay
	  // @param Function cb
	  // @return void
	  function deleteString($target, delay, cb) {
		var length;
		
		$target.html(function (_, html) {
		  length = html.length;
		  return html.substr(0, length - 1);
		});
		
		if (length > 1) {
		  setTimeout(function () {
			deleteString($target, delay, cb);
		  }, delay);
		}
		else {
		  cb();
		}
	  }

	  // jQuery hook
	  $.fn.extend({
		teletype: function (opts) {
		  var settings = $.extend({}, $.teletype.defaults, opts);
		  
		  return $(this).each(function () {
			(function loop($tar, idx) {
			  // type
			  var seuSua = $("#seusua");
			  var destacado = $("#destacado");
			  typeString($tar, settings.text[idx], 0, settings.delay, function () {
				// delete
				
				if(contador < 4){
					// Sistema de troca Seu/Sua
					var targetText = $("#target").text();
					if(targetText == 'marca' || targetText == 'empresa' || targetText == 'loja' || targetText == 'organização'){
						seuSua.text('Sua');
						destacado.text('destacada');
					}
					if(targetText == 'comércio' ){
						seuSua.text('Seu');
						destacado.text('destacado');
					}
					// tempo de espera até executar o delete
					setTimeout(function () {
						deleteString($tar, settings.delayb, function () {
							loop($tar, (idx + 1) % settings.text.length);
						});
						contador++;
					}, settings.pause);
					}else{
						seuSua.text('Seu');
						destacado.text('destacado');
					}
			  });
			  
				
			
			}($(this), 0));
		  });
		}
	  });

	  // plugin defaults  
	  $.extend({
		teletype: {
		  defaults: {
			delay: 80,
			delayb:40,
			pause: 2700,
			text: []
		  }
		}
	  });
	}(jQuery));

$(document).ready(function() {





	
	// Abrir resto do texo ao clicar em 'ler mais'

	$('.readExpander').click(function(){
		$(this).parents('.post').find('.pontinhos').hide();
		$(this).parents('.post').find('#contentComplete').css('display','inline');
		$(this).hide();
	});
	
	// Mostrar imagem do infográfico inteiro ao clicar em 'ver infográfico'

	$('.infoExpander').click(function(){
		var img = $(this).attr('imga');
		
		$("html").css({"overflow":"hidden"});
		$("#bloqueio").fadeIn();
		$("#bloqueio .conteudo").find('img').attr('src',img);
		$("#bloqueio .conteudo").scrollTop(0);
	});
	
	$("#bloqueio .fechar").click(function(){
		$("#bloqueio").fadeOut("slow");
		$("html").css({"overflow-y":"auto"});
	});
	
	$("#bloqueio .topo").click(function(){
		$("#bloqueio .conteudo").animate({ scrollTop: 0 }, 800)
	});
	
	// Sistema de escrita automática
	
	$('#target').teletype({
	  text: [
		'site',
		'empresa',
		'marca',
		'comércio'
	  ]
	});

$('#cursor').teletype({
  text: ['_', ' '],
  delay: 0,
  pause: 500
});

	// Create the dropdown base
   $("<select />").appendTo("#navigation");
      
      // Create default option "Go to..."
      $("<option />", {
         "selected": "selected",
         "value"   : "",
         "text"    : "Go to..."
      }).appendTo("#navigation select");
      
      // Populate dropdown with menu items
      $("#navigation > ul > li:not([data-toggle])").each(function() {
      
      	var el = $(this);
      
      	var hasChildren = el.find("ul"),
      	    children    = el.find("li > a");
       
      	if (hasChildren.length) {
      	
      		$("<optgroup />", {
      			"label": el.find("> a").text()
      		}).appendTo("#navigation select");
      		
      		children.each(function() {
      		      			
      			$("<option />", {
					"value"   : $(this).attr("href"),
      				"text": " - " + $(this).text()
      			}).appendTo("optgroup:last");
      		
      		});
      		      	
      	} else {
      	
      		$("<option />", {
	           "value"   : el.find("> a").attr("href"),
	           "text"    : el.find("> a").text()
	       }).appendTo("#navigation select");
      	
      	} 
             
      });
 
      $("#navigation select").change(function() {
        window.location = $(this).find("option:selected").val();
      });
	
	//END -- Menus to <SELECT>	
	
	}); //END -- JQUERY document.ready
	
jQuery(document).ready(function(){

        
// Scroll to Top script
jQuery(document).ready(function($){
    $('a[href=#top]').click(function(){
        $('html, body').animate({scrollTop:0}, 'slow');
        return false;
    });
$(".togglec").hide();
    	
    	$(".togglet").click(function(){
    	
    	$(this).toggleClass("toggleta").next(".togglec").slideToggle("normal");
    	   return true;
    	});
});


