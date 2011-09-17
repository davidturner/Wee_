/*
 * jQuery Nivo Slider v2.1
 * http://nivo.dev7studios.com
 *
 * Copyright 2010, Gilbert Pellegrom
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * May 2010 - Pick random effect from specified set of effects by toronegro
 * May 2010 - controlNavThumbsFromRel option added by nerd-sh
 * May 2010 - Do not start nivoRun timer if there is only 1 slide by msielski
 * April 2010 - controlNavThumbs option added by Jamie Thompson (http://jamiethompson.co.uk)
 * March 2010 - manualAdvance option added by HelloPablo (http://hellopablo.co.uk)
 */

(function($) {

	$.fn.nivoSlider = function(options) {

		//Defaults are below
		var settings = $.extend({}, $.fn.nivoSlider.defaults, options);

		return this.each(function() {
			//Useful variables. Play carefully.
			var vars = {
				currentSlide: 0,
				currentImage: '',
				totalSlides: 0,
				randAnim: '',
				running: false,
				paused: false,
				stop:false
			};
		
			//Get this slider
			var slider = $(this);
			slider.data('nivo:vars', vars);
			slider.css('position','relative');
			slider.addClass('nivoSlider');
			
			//Find our slider children
			var kids = slider.children();
			kids.each(function() {
				var child = $(this);
				var link = '';
				if(!child.is('img')){
					if(child.is('a')){
						child.addClass('nivo-imageLink');
						link = child;
					}
					child = child.find('img:first');
				}
				//Get img width & height
                var childWidth = child.width();
                if(childWidth == 0) childWidth = child.attr('width');
                var childHeight = child.height();
                if(childHeight == 0) childHeight = child.attr('height');
                //Resize the slider
                if(childWidth > slider.width()){
                    slider.width(childWidth);
                }
                if(childHeight > slider.height()){
                    slider.height(childHeight);
                }
                if(link != ''){
                    link.css('display','none');
                }
                child.css('display','none');
                vars.totalSlides++;
			});
			
			//Set startSlide
			if(settings.startSlide > 0){
				if(settings.startSlide >= vars.totalSlides) settings.startSlide = vars.totalSlides - 1;
				vars.currentSlide = settings.startSlide;
			}
			
			//Get initial image
			if($(kids[vars.currentSlide]).is('img')){
				vars.currentImage = $(kids[vars.currentSlide]);
			} else {
				vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
			}
			
			//Show initial link
			if($(kids[vars.currentSlide]).is('a')){
				$(kids[vars.currentSlide]).css('display','block');
			}
			
			//Set first background
			slider.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
			
			//Add initial slices
			for(var i = 0; i < settings.slices; i++){
				var sliceWidth = Math.round(slider.width()/settings.slices);
				if(i == settings.slices-1){
					slider.append(
						$('<div class="nivo-slice"></div>').css({ left:(sliceWidth*i)+'px', width:(slider.width()-(sliceWidth*i))+'px' })
					);
				} else {
					slider.append(
						$('<div class="nivo-slice"></div>').css({ left:(sliceWidth*i)+'px', width:sliceWidth+'px' })
					);
				}
			}
			
			//Create caption
			slider.append(
				$('<div class="nivo-caption"><p></p></div>').css({ display:'none', opacity:settings.captionOpacity })
			);			
			//Process initial  caption
			if(vars.currentImage.attr('title') != ''){
                var title = vars.currentImage.attr('title');
                if(title.substr(0,1) == '#') title = $(title).html();
                $('.nivo-caption p', slider).html(title);					
				$('.nivo-caption', slider).fadeIn(settings.animSpeed);
			}
			
			//In the words of Super Mario "let's a go!"
			var timer = 0;
			if(!settings.manualAdvance && kids.length > 1){
				timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
			}

			//Add Direction nav
			if(settings.directionNav){
				slider.append('<div class="nivo-directionNav"><a class="nivo-prevNav">Prev</a><a class="nivo-nextNav">Next</a></div>');
				
				//Hide Direction nav
				if(settings.directionNavHide){
					$('.nivo-directionNav', slider).hide();
					slider.hover(function(){
						$('.nivo-directionNav', slider).show();
					}, function(){
						$('.nivo-directionNav', slider).hide();
					});
				}
				
				$('a.nivo-prevNav', slider).live('click', function(){
					if(vars.running) return false;
					clearInterval(timer);
					timer = '';
					vars.currentSlide-=2;
					nivoRun(slider, kids, settings, 'prev');
				});
				
				$('a.nivo-nextNav', slider).live('click', function(){
					if(vars.running) return false;
					clearInterval(timer);
					timer = '';
					nivoRun(slider, kids, settings, 'next');
				});
			}
			
			//Add Control nav
			if(settings.controlNav){
				var nivoControl = $('<div class="nivo-controlNav"></div>');
				slider.append(nivoControl);
				for(var i = 0; i < kids.length; i++){
					if(settings.controlNavThumbs){
						var child = kids.eq(i);
						if(!child.is('img')){
							child = child.find('img:first');
						}
                        if (settings.controlNavThumbsFromRel) {
                            nivoControl.append('<a class="nivo-control" rel="'+ i +'"><img src="'+ child.attr('rel') + '" alt="" /></a>');
                        } else {
                            nivoControl.append('<a class="nivo-control" rel="'+ i +'"><img src="'+ child.attr('src').replace(settings.controlNavThumbsSearch, settings.controlNavThumbsReplace) +'" alt="" /></a>');
                        }
					} else {
						nivoControl.append('<a class="nivo-control" rel="'+ i +'">'+ (i + 1) +'</a>');
					}
					
				}
				//Set initial active link
				$('.nivo-controlNav a:eq('+ vars.currentSlide +')', slider).addClass('active');
				
				$('.nivo-controlNav a', slider).live('click', function(){
					if(vars.running) return false;
					if($(this).hasClass('active')) return false;
					clearInterval(timer);
					timer = '';
					slider.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
					vars.currentSlide = $(this).attr('rel') - 1;
					nivoRun(slider, kids, settings, 'control');
				});
			}
			
			//Keyboard Navigation
			if(settings.keyboardNav){
				$(window).keypress(function(event){
					//Left
					if(event.keyCode == '37'){
						if(vars.running) return false;
						clearInterval(timer);
						timer = '';
						vars.currentSlide-=2;
						nivoRun(slider, kids, settings, 'prev');
					}
					//Right
					if(event.keyCode == '39'){
						if(vars.running) return false;
						clearInterval(timer);
						timer = '';
						nivoRun(slider, kids, settings, 'next');
					}
				});
			}
			
			//For pauseOnHover setting
			if(settings.pauseOnHover){
				slider.hover(function(){
					vars.paused = true;
					clearInterval(timer);
					timer = '';
				}, function(){
					vars.paused = false;
					//Restart the timer
					if(timer == '' && !settings.manualAdvance){
						timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
					}
				});
			}
			
			//Event when Animation finishes
			slider.bind('nivo:animFinished', function(){ 
				vars.running = false; 
				//Hide child links
				$(kids).each(function(){
					if($(this).is('a')){
						$(this).css('display','none');
					}
				});
				//Show current link
				if($(kids[vars.currentSlide]).is('a')){
					$(kids[vars.currentSlide]).css('display','block');
				}
				//Restart the timer
				if(timer == '' && !vars.paused && !settings.manualAdvance){
					timer = setInterval(function(){ nivoRun(slider, kids, settings, false); }, settings.pauseTime);
				}
				//Trigger the afterChange callback
				settings.afterChange.call(this);
			});
		});
		
		function nivoRun(slider, kids, settings, nudge){
			//Get our vars
			var vars = slider.data('nivo:vars');
			if((!vars || vars.stop) && !nudge) return false;
			
			//Trigger the beforeChange callback
			settings.beforeChange.call(this);
					
			//Set current background before change
			if(!nudge){
				slider.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
			} else {
				if(nudge == 'prev'){
					slider.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
				}
				if(nudge == 'next'){
					slider.css('background','url('+ vars.currentImage.attr('src') +') no-repeat');
				}
			}
			vars.currentSlide++;
			if(vars.currentSlide == vars.totalSlides){ 
				vars.currentSlide = 0;
				//Trigger the slideshowEnd callback
				settings.slideshowEnd.call(this);
			}
			if(vars.currentSlide < 0) vars.currentSlide = (vars.totalSlides - 1);
			//Set vars.currentImage
			if($(kids[vars.currentSlide]).is('img')){
				vars.currentImage = $(kids[vars.currentSlide]);
			} else {
				vars.currentImage = $(kids[vars.currentSlide]).find('img:first');
			}
			
			//Set acitve links
			if(settings.controlNav){
				$('.nivo-controlNav a', slider).removeClass('active');
				$('.nivo-controlNav a:eq('+ vars.currentSlide +')', slider).addClass('active');
			}
			
			//Process caption
			if(vars.currentImage.attr('title') != ''){
                var title = vars.currentImage.attr('title');
                if(title.substr(0,1) == '#') title = $(title).html();	
                    
				if($('.nivo-caption', slider).css('display') == 'block'){
					$('.nivo-caption p', slider).fadeOut(settings.animSpeed, function(){
						$(this).html(title);
						$(this).fadeIn(settings.animSpeed);
					});
				} else {
					$('.nivo-caption p', slider).html(title);
				}					
				$('.nivo-caption', slider).fadeIn(settings.animSpeed);
			} else {
				$('.nivo-caption', slider).fadeOut(settings.animSpeed);
			}
			
			//Set new slice backgrounds
			var  i = 0;
			$('.nivo-slice', slider).each(function(){
				var sliceWidth = Math.round(slider.width()/settings.slices);
				$(this).css({ height:'0px', opacity:'0', 
					background: 'url('+ vars.currentImage.attr('src') +') no-repeat -'+ ((sliceWidth + (i * sliceWidth)) - sliceWidth) +'px 0%' });
				i++;
			});
			
			if(settings.effect == 'random'){
				var anims = new Array("sliceDownRight","sliceDownLeft","sliceUpRight","sliceUpLeft","sliceUpDown","sliceUpDownLeft","fold","fade");
				vars.randAnim = anims[Math.floor(Math.random()*(anims.length + 1))];
				if(vars.randAnim == undefined) vars.randAnim = 'fade';
			}
            
            //Run random effect from specified set (eg: effect:'fold,fade')
            if(settings.effect.indexOf(',') != -1){
                var anims = settings.effect.split(',');
                vars.randAnim = $.trim(anims[Math.floor(Math.random()*anims.length)]);
            }
		
			//Run effects
			vars.running = true;
			if(settings.effect == 'sliceDown' || settings.effect == 'sliceDownRight' || vars.randAnim == 'sliceDownRight' ||
				settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft'){
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceDownLeft' || vars.randAnim == 'sliceDownLeft') slices = $('.nivo-slice', slider)._reverse();
				slices.each(function(){
					var slice = $(this);
					slice.css('top','0px');
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUp' || settings.effect == 'sliceUpRight' || vars.randAnim == 'sliceUpRight' ||
					settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft'){
				var timeBuff = 0;
				var i = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceUpLeft' || vars.randAnim == 'sliceUpLeft') slices = $('.nivo-slice', slider)._reverse();
				slices.each(function(){
					var slice = $(this);
					slice.css('bottom','0px');
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			} 
			else if(settings.effect == 'sliceUpDown' || settings.effect == 'sliceUpDownRight' || vars.randAnim == 'sliceUpDown' || 
					settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft'){
				var timeBuff = 0;
				var i = 0;
				var v = 0;
				var slices = $('.nivo-slice', slider);
				if(settings.effect == 'sliceUpDownLeft' || vars.randAnim == 'sliceUpDownLeft') slices = $('.nivo-slice', slider)._reverse();
				slices.each(function(){
					var slice = $(this);
					if(i == 0){
						slice.css('top','0px');
						i++;
					} else {
						slice.css('bottom','0px');
						i = 0;
					}
					
					if(v == settings.slices-1){
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ height:'100%', opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					v++;
				});
			} 
			else if(settings.effect == 'fold' || vars.randAnim == 'fold'){
				var timeBuff = 0;
				var i = 0;
				$('.nivo-slice', slider).each(function(){
					var slice = $(this);
					var origWidth = slice.width();
					slice.css({ top:'0px', height:'100%', width:'0px' });
					if(i == settings.slices-1){
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed, '', function(){ slider.trigger('nivo:animFinished'); });
						}, (100 + timeBuff));
					} else {
						setTimeout(function(){
							slice.animate({ width:origWidth, opacity:'1.0' }, settings.animSpeed);
						}, (100 + timeBuff));
					}
					timeBuff += 50;
					i++;
				});
			}  
			else if(settings.effect == 'fade' || vars.randAnim == 'fade'){
				var i = 0;
				$('.nivo-slice', slider).each(function(){
					$(this).css('height','100%');
					if(i == settings.slices-1){
						$(this).animate({ opacity:'1.0' }, (settings.animSpeed*2), '', function(){ slider.trigger('nivo:animFinished'); });
					} else {
						$(this).animate({ opacity:'1.0' }, (settings.animSpeed*2));
					}
					i++;
				});
			}
		}
	};
	
	//Default settings
	$.fn.nivoSlider.defaults = {
		effect:'random',
		slices:15,
		animSpeed:500,
		pauseTime:3000,
		startSlide:0,
		directionNav:true,
		directionNavHide:true,
		controlNav:true,
		controlNavThumbs:false,
        controlNavThumbsFromRel:false,
		controlNavThumbsSearch:'.jpg',
		controlNavThumbsReplace:'_thumb.jpg',
		keyboardNav:true,
		pauseOnHover:true,
		manualAdvance:false,
		captionOpacity:0.8,
		beforeChange: function(){},
		afterChange: function(){},
		slideshowEnd: function(){}
	};
	
	$.fn._reverse = [].reverse;
	
})(jQuery);

jQuery.beautyOfCode = {

    settings: {
        // should the syntax highlighter and brushes
        // be loaded dynamically
        autoLoad: true,
        // the base url to alex' hosted sources
        // http://alexgorbatchev.com/wiki/SyntaxHighlighter:Hosting
        baseUrl: 'http://alexgorbatchev.com/pub/sh/2.1.364/',
        // the baseurl for the hosted scripts
        scripts: 'scripts/',
        // the baseurl for the hosted styles
        styles: 'styles/',
        // themes from http://alexgorbatchev.com/wiki/SyntaxHighlighter:Themes
        theme: 'Default',
        // the brushes that should be loaded - case sensitive!
        // http://alexgorbatchev.com/wiki/SyntaxHighlighter:Brushes
        brushes: ['Xml', 'JScript', 'CSharp', 'Plain'],
        // overrides for configurations and defaults
        // http://alexgorbatchev.com/wiki/SyntaxHighlighter:Configuration
        config: {},
        defaults: {},
        // function to be called, when all scripts are loaded
        ready: function() {
            jQuery.beautyOfCode.beautifyAll();
        }
    },

    init: function(settings) {
        settings = jQuery.extend({},
        jQuery.beautyOfCode.settings, settings);

        if (!settings.config.clipboardSwf)
        settings.config.clipboardSwf = settings.baseUrl + settings.scripts + 'clipboard.swf';

        $(document).ready(function() {
            if (!settings.autoLoad) {
                settings.ready();
            }
            else {
                jQuery.beautyOfCode.utils.loadCss(settings.baseUrl + settings.styles + 'shCore.css');
                jQuery.beautyOfCode.utils.loadCss(settings.baseUrl + settings.styles + 'shTheme' + settings.theme + '.css', 'shTheme');

                var scripts = new Array();
                scripts.push(settings.baseUrl + settings.scripts + 'shCore.js');
                jQuery.each(settings.brushes,
                function(i, item) {
                    scripts.push(settings.baseUrl + settings.scripts + 'shBrush' + item + ".js")
                });

                jQuery.beautyOfCode.utils.loadAllScripts(
                scripts,
                function() {
                    if (settings && settings.config)
                    jQuery.extend(SyntaxHighlighter.config, settings.config);

                    if (settings && settings.defaults)
                    jQuery.extend(SyntaxHighlighter.defaults, settings.defaults);

                    settings.ready();
                });
            }
        });
    },

    beautifyAll: function() {
        jQuery("pre.code:has(code[class])").beautifyCode();
    },
    utils: {
        loadScript: function(url, complete) {
            jQuery.ajax({
                url: url,
                complete: function() {
                    complete();
                },
                type: 'GET',
                dataType: 'script',
                cache: true
            });
        },
        loadAllScripts: function(urls, complete) {
            if (!urls || urls.length == 0)
            {
                complete();
                return;
            }
            var first = urls[0];
            jQuery.beautyOfCode.utils.loadScript(
            first,
            function() {
                jQuery.beautyOfCode.utils.loadAllScripts(
                urls.slice(1, urls.length),
                complete
                );
            }
            );
        },
        loadCss: function(url, id) {
            //var headNode = jQuery("head")[0];
            //if (url && headNode)
            //{
            //    var styleNode = document.createElement('link');
            //    styleNode.setAttribute('rel', 'stylesheet');
            //    styleNode.setAttribute('href', url);
            //    if (id) styleNode.id = id;
            //    headNode.appendChild(styleNode);
            //}
        },
        addCss: function(css, id) {
            var headNode = jQuery("head")[0];
            if (css && headNode)
            {
                var styleNode = document.createElement('style');

                styleNode.setAttribute('type', 'text/css');

                if (id) styleNode.id = id;

                if (styleNode.styleSheet) {
                    // for IE	
                    styleNode.styleSheet.cssText = css;
                }
                else {
                    $(styleNode).text(css);
                }

                headNode.appendChild(styleNode);
            }
        },
        addCssForBrush: function(brush, highlighter) {
            //if (brush.isCssInitialized)
            //    return;

            //jQuery.beautyOfCode.utils.addCss(highlighter.Style);

            //brush.isCssInitialized = true;
        },
        parseParams: function(params) {
            var trimmed = jQuery.map(params, jQuery.trim);

            var paramObject = {};

            var getOptionValue = function(name, list) {
                var regex = new RegExp('^' + name + '\\[([^\\]]+)\\]$', 'gi');
                var matches = null;

                for (var i = 0; i < list.length; i++)
                    if ((matches = regex.exec(list[i])) != null)
                        return matches[1];

                return null;
            }

            var handleValue = function(flag) {
                var flagValue = getOptionValue('boc-' + flag, trimmed);
                if (flagValue) paramObject[flag] = flagValue;
            };

            handleValue('class-name');
            handleValue('first-line');
            handleValue('tab-size');

            var highlight = getOptionValue('boc-highlight', trimmed);
            if (highlight) paramObject['highlight'] = jQuery.map(highlight.split(','), jQuery.trim);

            var handleFlag = function(flag) {
                if (jQuery.inArray('boc-' + flag, trimmed) != -1)
                    paramObject[flag] = true;
                else if (jQuery.inArray('boc-no-' + flag, trimmed) != -1)
                    paramObject[flag] = false;
            };

            handleFlag('smart-tabs');
            handleFlag('ruler');
            handleFlag('gutter');
            handleFlag('toolbar');
            handleFlag('collapse');
            handleFlag('auto-links');
            handleFlag('light');
            handleFlag('wrap-lines');
            handleFlag('html-script');

            return paramObject;
        }
    }
};

jQuery.fn.beautifyCode = function(brush, params) {
    var saveBrush = brush;
    var saveParams = params;

    // iterate all elements
    this.each(function(i, item) {
        var $item = jQuery(item);

        // for now, only supports <pre><code>...</code></pre>
        // support for only pre, or only code could be added
        var $code = $item.children("code");
        var code = $code[0];
        var classItems = code.className.split(" ");

        var brush = saveBrush ? saveBrush: classItems[0];
        var elementParams = jQuery.beautyOfCode.utils.parseParams(classItems);

        var params = jQuery.extend({},
        SyntaxHighlighter.defaults, saveParams, elementParams);

        // Instantiate a brush
        if (params['html-script'] == 'true')
        {
            highlighter = new SyntaxHighlighter.HtmlScript(brush);
        }
        else
        {
            var brush = SyntaxHighlighter.utils.findBrush(brush);

            if (brush)
                highlighter = new brush();
            else
                return;
        }

        // i'm not sure if this is still neccessary
        //jQuery.beautyOfCode.utils.addCssForBrush(brush, highlighter);

        // IE Bug?: code in pre has to be skipped
        // in order to preserve line breaks.
        if ($item.is("pre") && ($code = $item.children("code")))
           $item.text($code.text());

        highlighter.highlight($item.html(), params);
        highlighter.source = item;

        $item.replaceWith(highlighter.div);
    });
}    
                           

$(window).load(function() {
//$(function (){  
	$('.gallery').nivoSlider({
		effect:'fade', //Specify sets like: 'fold,fade,sliceDown' 
		directionNav:false, //Next & Prev 
		controlNavThumbsReplace: '.jpg', //...this in thumb Image src
		pauseTime:5000,
		controlNavThumbs:true
	});     
  $("figure:not(.book)").each(function() {
    if($(this).has("img").length && !$(this).has("video").length){
      imageWidth = $(this).find('img').width();
      figureWidth = $(this).width();
      if(imageWidth<figureWidth){
        $(this).width(imageWidth);
      }
    }    
  });                  
});

$.beautyOfCode.init({
	theme: 'Django',
	brushes: ['Xml', 'JScript', 'Plain', 'Php', 'Css'],
	ready: function() {
		$.beautyOfCode.beautifyAll('javascript', {gutter:false});    
	}
});