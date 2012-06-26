/*
 * o.nav.mnnav({ntt: mgd.ntt, npp: mgd.npp, npg: mgd.npg});
 */
;(function($){

    function gotoPage(pn, tpg, npp, cbk, url) {
        var o = $('#_nav-inp');
        pn = parseInt(pn);

        if (!pn || pn < 0 || pn > tpg) {
            o.addClass('invalid')
            return;
        }
        o.removeClass('invalid');
        document._cpg = pn;
        o.val(pn);
        if (url.length) {
            var regu = /\?/;
            if (regu.test(url)) url = url + '&_npg=' + pn + '&_npp=' + npp;
            else url = url + '?_npg=' + pn + '&_npp=' + npp;
            window.location = url;
        } else cbk(pn);
    }

    function navLeft(cpg, tpg, npp, cbk, url) {
        var html = '<span class="left">第 <input type="text" id="_nav-inp" value="'+
            cpg+'" />'+' <span title="跳转到该页" class="confirm tooltip">&nbsp;</span> 页/共 ' +
            tpg + ' 页</span>',
        r = $(html),
        i = $('#_nav-inp', r),
        c = $('.confirm', r);

        i.bind('keydown', 'return', function(){
            gotoPage(i.val(), tpg, npp, cbk, url);
        });

        c.click(function() {
            gotoPage(i.val(), tpg, npp, cbk, url);
        });
        
        return r;
    }

    function navCenter(cpg, tpg, npp, cbk, url) {
        var html = '<span class="center">';
        if (cpg > 1) html += '<span title="上一页" class="nav-prev tooltip" rel="'+(parseInt(cpg)-1)+'">&nbsp;</span> ';
        if (cpg < tpg) html += '<span title="下一页" class="nav-next tooltip" rel="'+(parseInt(cpg)+1)+'">&nbsp;</span>';
        html += '</span>';

        var r = $(html),
        a = $('span', r);

        a.click(function() {
            gotoPage($(this).attr('rel'), tpg, npp, cbk, url);
        });
        
        return r;
    }
    
    function navRight(tpg, npp, cbk, url) {
        var html = [
            '<span class="right">',
              '<a href="javascript:void(0);" rel="1">首页</a> ',
              '<a href="javascript:void(0);" rel="', tpg, '">末页</a>',
            '</span>'
        ].join(''),
        r = $(html),
        a = $('a', r);

        a.click(function() {
            gotoPage($(this).attr('rel'), tpg, npp, cbk, url);
        });
        
        return r;
    }

    $.fn.mnnav = function(opts) {
        opts = $.extend({
            npp: 15,
            npg: 1,
            ntt: 0,
            url: "",            // (url) means no ajax mode, need offer pg meanwhile
            cbk: function(pg) {alert("goto page " + pg); return true;}
        }, opts||{});

        opts.npp = parseInt(opts.npp);
        opts.npg = parseInt(opts.npg);
        opts.ntt = parseInt(opts.ntt);

        var cpg = bmoon.utl.type(document._cpg) == 'Number'? document._cpg: opts.npg,
        tpg = parseInt((opts.ntt+opts.npp-1) / opts.npp);

        $(this).each(function(k, obj){
            var o = $(obj);

            o.empty();

            navLeft(cpg, tpg, opts.npp, opts.cbk, opts.url).appendTo(o);
            navCenter(cpg, tpg, opts.npp, opts.cbk, opts.url).appendTo(o);
            navRight(tpg, opts.npp, opts.cbk, opts.url).appendTo(o);
        });
    };
})(jQuery);
; var bmoon = bmoon || {};
bmoon.paperpaper = {
    version: '1.0',

    init: function() {
        var o = bmoon.paperpaper;
        if (o.inited) return o;

        o.e_nav = $('#paper-nav');
        o.e_papers = $('#paper-list');
        o.e_papers_a = $('li a', o.e_papers);
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.paperpaper.init();

        bmoon.utl.after(o.rendNav, 'mgd.ntt != undefined', 10);

        var tab = bmoon.utl.getQueryString('_tab');

        if (tab.length > 0) {
            $.each(o.e_papers_a, function(k, a) {
                $(a).attr('href', $(a).attr('href') + '&_tab=' + tab);
            });
        }

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.paperpaper.init();

    },

    rendNav: function() {
        var o = bmoon.paperpaper.init();

        if (mgd.ntt > mgd.npp) {
            o.e_nav.mnnav({
                ntt: mgd.ntt,
                npg: mgd.npg,
                npp: mgd.npp,
                url: '/paper'
            });
        }
    }
};

$(document).ready(bmoon.paperpaper.onready);
