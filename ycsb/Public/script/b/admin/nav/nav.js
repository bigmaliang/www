;(function($) {
    $.fn.mntips = function(rows, opts) {
        opts = $.extend({
			// 'top', 'bottom', 'right', 'left', 'center'
			position: ['bottom', 'center'], 
			offset: [0, 0],
			relative: false,

            tipClass: 'mntip',

            rowspec: null,      // ['id',...]

            clickRow: function(row, evt) {return false;}
        }, opts||{});

	    /* calculate tip position relative to the trigger */  	
	    function getPosition(trigger, tip) {
		    // get origin top/left position 
		    var conf = opts,
            top = conf.relative ? trigger.position().top : trigger.offset().top, 
			left = conf.relative ? trigger.position().left : trigger.offset().left,
			pos = conf.position[0];

		    top  -= tip.outerHeight() - conf.offset[0];
		    left += trigger.outerWidth() + conf.offset[1];
		    
		    // iPad position fix
		    if (/iPad/i.test(navigator.userAgent)) {
			    top -= $(window).scrollTop();
		    }
		    
		    // adjust Y		
		    var height = tip.outerHeight() + trigger.outerHeight();
		    if (pos == 'center') 	{ top += height / 2; }
		    if (pos == 'bottom') 	{ top += height; }
		    
		    
		    // adjust X
		    pos = conf.position[1]; 	
		    var width = tip.outerWidth() + trigger.outerWidth();
		    if (pos == 'center') 	{ left -= width / 2; }
		    if (pos == 'left')   	{ left -= width; }	 
		    
		    return {top: top, left: left};
	    }		

        $(this).each(function() {
            var me = $(this),
            tipdiv = $('<ul class="'+opts.tipClass+'">');

            if (me.data('mntip')) {
                 me.data('mntip').remove();
            }
            
            $.each(rows, function(i, row) {
                var item = $("<li>").appendTo(tipdiv).click(function(evt) {
                    tipdiv.hide();
                    opts.clickRow(row, evt);
                });
                if (bmoon.utl.type(opts.rowspec) == 'Array') {
                    $.each(opts.rowspec, function(i, col) {
                        $("<span class='tipscol"+i+"'>"+row[col]+"</span>").appendTo(item);
                    });
                } else {
                    $('<span>'+row+'</span>').appendTo(item);
                }
            });

            tipdiv.appendTo(document.body).hide();

            var pos = getPosition(me, tipdiv);

            tipdiv.css({position: 'absolute', left: pos.left, top: pos.top}).show();

            me.data('mntip', tipdiv);
        });
    };
})(jQuery);
/**
* jQuery Noty Plugin v1.1.1
* Authors: Nedim Arabacı (http://ned.im), Muhittin Özer (http://muhittinozer.com)
*
* Examples and Documentation - http://needim.github.com/noty/
*
* Licensed under the MIT licenses:
* http://www.opensource.org/licenses/mit-license.php
*
**/
(function($) {
	$.noty = function(options, customContainer) {

		var base = this;
		var $noty = null;
		var isCustom = false;

		base.init = function(options) {
			base.options = $.extend({}, $.noty.defaultOptions, options);
			base.options.type = base.options.cssPrefix+base.options.type;
			base.options.id = base.options.type+'_'+new Date().getTime();
			base.options.layout = base.options.cssPrefix+'layout_'+base.options.layout;

			if (base.options.custom.container) customContainer = base.options.custom.container;
			isCustom = ($.type(customContainer) === 'object') ? true : false;

			return base.addQueue();
		};

		// Push notification to queue
		base.addQueue = function() {
			var isGrowl = ($.inArray(base.options.layout, $.noty.growls) == -1) ? false : true;
	  	if (!isGrowl) (base.options.force) ? $.noty.queue.unshift({options: base.options}) : $.noty.queue.push({options: base.options});
	  	return base.render(isGrowl);
		};

		// Render the noty
		base.render = function(isGrowl) {

			// Layout spesific container settings
			var container = (isCustom) ? customContainer.addClass(base.options.theme+' '+base.options.layout+' noty_custom_container') : $('body');
	  	if (isGrowl) {
	  		if ($('ul.noty_cont.' + base.options.layout).length == 0)
	  			container.prepend($('<ul/>').addClass('noty_cont ' + base.options.layout));
	  		container = $('ul.noty_cont.' + base.options.layout);
	  	} else {
	  		if ($.noty.available) {
					var fromQueue = $.noty.queue.shift(); // Get noty from queue
					if ($.type(fromQueue) === 'object') {
						$.noty.available = false;
						base.options = fromQueue.options;
					} else {
						$.noty.available = true; // Queue is over
						return base.options.id;
					}
	  		} else {
	  			return base.options.id;
	  		}
	  	}
	  	base.container = container;

	  	// Generating noty bar
	  	base.bar = $('<div class="noty_bar"/>').attr('id', base.options.id).addClass(base.options.theme+' '+base.options.layout+' '+base.options.type);
	  	$noty = base.bar;
	  	$noty.append(base.options.template).find('.noty_text').html(base.options.text);
	  	$noty.data('noty_options', base.options);

	  	// Close button display
	  	(base.options.closeButton) ? $noty.addClass('noty_closable').find('.noty_close').show() : $noty.find('.noty_close').remove();

	  	// Bind close event to button
	  	$noty.find('.noty_close').bind('click', function() { $noty.trigger('noty.close'); });

	  	// If we have a button we must disable closeOnSelfClick and closeOnSelfOver option
	  	if (base.options.buttons) base.options.closeOnSelfClick = base.options.closeOnSelfOver = false;
	  	// Close on self click
	  	if (base.options.closeOnSelfClick) $noty.bind('click', function() { $noty.trigger('noty.close'); }).css('cursor', 'pointer');
	  	// Close on self mouseover
	  	if (base.options.closeOnSelfOver) $noty.bind('mouseover', function() { $noty.trigger('noty.close'); }).css('cursor', 'pointer');

	  	// Set buttons if available
	  	if (base.options.buttons) {
				$buttons = $('<div/>').addClass('noty_buttons');
				$noty.find('.noty_message').append($buttons);
				$.each(base.options.buttons, function(i, button) {
					bclass = (button.type) ? button.type : 'gray';
					$button = $('<button/>').addClass(bclass).html(button.text).appendTo($noty.find('.noty_buttons'))
					.bind('click', function() {
						if ($.isFunction(button.click)) {
							button.click.call($button, $noty);
						}
					});
				});
			}

	  	return base.show(isGrowl);
		};

		base.show = function(isGrowl) {

			// is Modal?
			if (base.options.modal) $('<div/>').addClass('noty_modal').addClass(base.options.theme).prependTo($('body')).fadeIn('fast');

			$noty.close = function() { return this.trigger('noty.close'); };

			// Prepend noty to container
			(isGrowl) ? base.container.prepend($('<li/>').append($noty)) : base.container.prepend($noty);

	  	// topCenter and center specific options
	  	if (base.options.layout == 'noty_layout_topCenter' || base.options.layout == 'noty_layout_center') {
				$.noty.reCenter($noty);
			}

	  	$noty.bind('noty.setText', function(event, text) {
	  		$noty.find('.noty_text').html(text); $.noty.reCenter($noty);
	  	});

	  	$noty.bind('noty.getId', function(event) {
	  		return $noty.data('noty_options').id;
	  	});

	  	// Bind close event
	  	$noty.one('noty.close', function(event) {
				var options = $noty.data('noty_options');

				// Modal Cleaning
				if (options.modal) $('.noty_modal').fadeOut('fast', function() { $(this).remove(); });

				$noty.clearQueue().stop().animate(
						$noty.data('noty_options').animateClose,
						$noty.data('noty_options').speed,
						$noty.data('noty_options').easing,
						$noty.data('noty_options').onClose)
				.promise().done(function() {

					// Layout spesific cleaning
					if ($.inArray($noty.data('noty_options').layout, $.noty.growls) > -1) {
						$noty.parent().remove();
					} else {
						$noty.remove();

						// queue render
						$.noty.available = true;
						base.render(false);
					}

				});
			});

	  	// Start the show
	  	$noty.animate(base.options.animateOpen, base.options.speed, base.options.easing, base.options.onShow);

	  	// If noty is have a timeout option
	  	if (base.options.timeout) $noty.delay(base.options.timeout).promise().done(function() { $noty.trigger('noty.close'); });
			return base.options.id;
		};

		// Run initializer
		return base.init(options);
	};

	// API
	$.noty.get = function(id) { return $('#'+id); };
	$.noty.close = function(id) {
		$.noty.get(id).trigger('noty.close');
	};
	$.noty.setText = function(id, text) {
		$.noty.get(id).trigger('noty.setText', text);
	};
	$.noty.closeAll = function() {
		$.noty.clearQueue();
		$('.noty_bar').trigger('noty.close');
	};
	$.noty.reCenter = function(noty) {
		noty.css({'left': ($(window).width() - noty.outerWidth()) / 2 + 'px'});
	};
	$.noty.clearQueue = function() {
		$.noty.queue = [];
	};

	$.noty.queue = [];
	$.noty.growls = ['noty_layout_topLeft', 'noty_layout_topRight', 'noty_layout_bottomLeft', 'noty_layout_bottomRight'];
	$.noty.available = true;
	$.noty.defaultOptions = {
		layout: 'top',
		theme: 'noty_theme_default',
		animateOpen: {height: 'toggle'},
		animateClose: {height: 'toggle'},
		easing: 'swing',
		text: '',
		type: 'alert',
		speed: 500,
		timeout: 5000,
		closeButton: false,
		closeOnSelfClick: true,
		closeOnSelfOver: false,
		force: false,
		onShow: false,
		onClose: false,
		buttons: false,
		modal: false,
		template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
		cssPrefix: 'noty_',
		custom: {
			container: null
		}
	};

	$.fn.noty = function(options) {
		return this.each(function() {
			 (new $.noty(options, $(this)));
		});
	};

})(jQuery);

//Helper
function noty(options) {
	return jQuery.noty(options); // returns an id
}; var bmoon = bmoon || {};
bmoon.admin = {
    version: '1.0',

    init: function() {
        var o = bmoon.admin;
        if (o.inited) return o;

        o.e_content = $('#bd-content');
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admin.init();

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admin.init();

    },

    inTip: function(obj, opts) {
        var o = bmoon.admin.init();

        var me = $(obj),
        v = me.val();

        o.e_current_tip = me;

        if (!me.inputTipID) {
            me.inputTipID = setInterval(function() {
                if (me.val() != v) {
                    v = me.val();
                    o.getClassTip(opts.clickRow);
                }
            }, 500);
        }
    },

    outTip: function() {
        var o = bmoon.admin.init();

        var me = o.e_current_tip;

        me.inputTipID && clearInterval(me.inputTipID);
        me.inputTipID = 0;
    },

    getClassTip: function(clickRow) {
        var o = bmoon.admin.init();

        var me = o.e_current_tip,
        v = me.val();

        $.getJSON('/json/paper/matchtitle', {title: v}, function(data) {
            if (data.success == 1 && bmoon.utl.type(data.titles) == 'Array') {
                me.mntips(data.titles, {
                    rowspec: ['title'],
                    clickRow: clickRow
                });
            }
        });
    }
};

$(document).ready(bmoon.admin.onready);
; var bmoon = bmoon || {};
bmoon.admnavnav = {
    version: '1.0',

    init: function() {
        var o = bmoon.admnavnav;
        if (o.inited) return o;

        o.e_nav = $('#nav-list');
        o.e_nav_title = $('input.title', o.e_nav);
        o.e_nav_del = $('a.del', o.e_nav);
        o.e_nav_add = $('#nav-add');

        o.e_cur_title = null;
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admnavnav.init();

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admnavnav.init();

        o.e_nav_title.focus(o.inTip);
        o.e_nav_title.blur(bmoon.admin.outTip);
        o.e_nav_del.click(o.delNav);
        o.e_nav_add.click(o.addRow);
    },
    
    inTip: function() {
        var o = bmoon.admnavnav.init();

        var me = $(this),
        pos = me.attr('rel');

        bmoon.admin.inTip(me, {
            clickRow: function (row) {
                bmoon.admin.outTip();
                me.val(row.title);
                me.parent().next().html(row.id);

                var pdata = {
                    pid: row.id
                };
                
                if (pos > 0) {
                    // modify nav
                    pdata._op = 'mod';
                    pdata.pos = pos;
                } else {
                    // add nav
                    pdata._op = 'add';
                }
                $.getJSON('/json/admin/nav', pdata, function(data) {
                    if (data.success == 1) {
                        noty({text:'操作成功', type: 'success', theme: 'noty_theme_mitgux'});
                    } else {
                        noty({text: data.errmsg, type: 'error', theme: 'noty_theme_mitgux'});
                    }
                });
            }
        });
    },

    delNav: function() {
        var o = bmoon.admnavnav.init();

        var me = $(this),
        pos = me.attr('rel'),
        pdata = {
            _op: 'mod',
            pos: pos,
            statu: 1
        };

        $.getJSON('/json/admin/nav', pdata, function(data) {
            if (data.success == 1) {
                noty({text:'操作成功', type: 'success', theme: 'noty_theme_mitgux'});
                me.parent().parent().remove();
            } else {
                noty({text: data.errmsg, type: 'error', theme: 'noty_theme_mitgux'});
            }
        });
    },

    addRow: function() {
        var o = bmoon.admnavnav.init();

        var html = [
            '<tr>',
              '<td class="text-center decimal">', mgd.lastpos++, '</td>',
              '<td><input class="llong" type="text" /></td>',
              '<td class="text-center"></td>',
              '<td></td>',
            '</tr>'
        ].join('');

        var ele = $(html).appendTo(o.e_nav);

        $('input', ele).focus(o.inTip);
        $('input', ele).blur(bmoon.admin.outTip);
    }
};

$(document).ready(bmoon.admnavnav.onready);
