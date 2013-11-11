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
	
	$(document).ready(function($) { 
	
	$(window).bind('scroll',function(e){
		parallaxScroll();
	});
		
	function parallaxScroll(){
		var scrolled = $(window).scrollTop();
		$('#rede').css('top',-(0+(scrolled*.05))+'px');
	} 
	
	$('ul.menu, ul#children, ul.sub-menu').superfish({ 
		delay:       100,								// 0.1 second delay on mouseout 
		animation:   {opacity:'show',height:'show'},	// fade-in and slide-down animation 
		dropShadows: false								// disable drop shadows 
	});
	
});

	



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

	// Fazer a sidebar seguir o scroll

	var top = $('#sidebar').offset().top - parseFloat($('#sidebar').css('marginTop').replace(/auto/, 0));
    $(window).scroll(function (event) {
        var y = $(this).scrollTop() + 100;
        //if y > top, it means that if we scroll down any more, parts of our element will be outside the viewport
        //so we move the element down so that it remains in view.
      
        if (y >= top) {
           	var difference = y - top;
           	$('#sidebar').css("top",difference);
       }else{
       		$('#sidebar').css("top",0);
       }

       var z = $(this).scrollTop();
       if (z >= 100) {
		  $('#main-header').css({"height":"65px"});
		  $('#main-header').addClass("header-ativo");
		} else {
		  $('#main-header').css({"height":"98px"});
		  $('#main-header').removeClass("header-ativo");
		}
   });




	// Alterar tamanho da header e background


	
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

        // UL = .tabs
        // Tab contents = .inside
        
       var tag_cloud_class = '#tag-cloud'; 
       
              //Fix for tag clouds - unexpected height before .hide() 
            var tag_cloud_height = jQuery('#tag-cloud').height();

       jQuery('.inside ul li:last-child').css('border-bottom','0px') // remove last border-bottom from list in tab conten
       jQuery('.tabs').each(function(){
       	jQuery(this).children('li').children('a:first').addClass('selected'); // Add .selected class to first tab on load
       });
       jQuery('.inside > *').hide();
       jQuery('.inside > *:first-child').show();
       

       jQuery('.tabs li a').click(function(evt){ // Init Click funtion on Tabs
        
            var clicked_tab_ref = jQuery(this).attr('href'); // Strore Href value
            
            jQuery(this).parent().parent().children('li').children('a').removeClass('selected'); //Remove selected from all tabs
            jQuery(this).addClass('selected');
            jQuery(this).parent().parent().parent().children('.inside').children('*').hide();
            
            /*
            if(clicked_tab_ref === tag_cloud_class) // Initiate tab fix (+20 for padding fix)
            {
                clicked_tab_ref_height = tag_cloud_height + 20;
            }
            else // Other height calculations
            {
                clicked_tab_ref_height = jQuery('.inside ' + clicked_tab_ref).height();
            }
            */
             //jQuery('.inside').stop().animate({
            //    height: clicked_tab_ref_height
            // },400,"linear",function(){
                    //Callback after new tab content's height animation
                    jQuery('.inside ' + clicked_tab_ref).fadeIn(500);
            // })
             
             evt.preventDefault();

        })
    
})

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

function swt_format_twitter(twitters) {
  var statusHTML = [];
  for (var i=0; i<twitters.length; i++){
    var username = twitters[i].user.screen_name;
    var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
      return '<a href="'+url+'">'+url+'</a>';
    }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
      return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
    });
    statusHTML.push('<li><span>'+status+'</span> <a style="font-size:90%; color:#bbb;" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id_str+'">'+relative_time(twitters[i].created_at)+'</a></li>');
  }
  return statusHTML.join('');
}

function relative_time(time_value) {
  var values = time_value.split(" ");
  time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
  var parsed_date = Date.parse(time_value);
  var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
  var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
  delta = delta + (relative_to.getTimezoneOffset() * 60);

  if (delta < 60) {
    return 'less than a minute ago';
  } else if(delta < 120) {
    return 'about a minute ago';
  } else if(delta < (60*60)) {
    return (parseInt(delta / 60)).toString() + ' minutes ago';
  } else if(delta < (120*60)) {
    return 'about an hour ago';
  } else if(delta < (24*60*60)) {
    return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
  } else if(delta < (48*60*60)) {
    return '1 day ago';
  } else {
    return (parseInt(delta / 86400)).toString() + ' days ago';
  }
}