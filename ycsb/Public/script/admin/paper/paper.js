; var bmoon = bmoon || {};
bmoon.admpaperpaper = {
    version: '1.0',

    init: function() {
        var o = bmoon.admpaperpaper;
        if (o.inited) return o;

        o.e_nav = $('#paper-nav');
        o.e_paper = $('#paper-list');
        o.e_paper_del = $('a.del', o.e_paper);
        o.e_paper_rev = $('a.revert', o.e_paper);
        o.e_paper_pids = $('.pids', o.e_paper);
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admpaperpaper.init();

        o.getClass();
        
        bmoon.utl.after(o.rendNav, 'mgd.ntt != undefined', 10);

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admpaperpaper.init();

        o.e_paper_del.click(o.modPaper);
        o.e_paper_rev.click(o.modPaper);
    },

    getClass: function() {
        var o = bmoon.admpaperpaper.init();

        $.each(o.e_paper_pids, function(k, e) {
            var id = $(e).attr('relid'),
            pid = $(e).attr('relpid');

            $.getJSON('/paper/info/'+pid, null, function(data) {
                if (data.success == 1) {
                    $('#pid-'+id).html(data.info.title);
                }
            });
        });
    },
    
    rendNav: function() {
        var o = bmoon.admpaperpaper.init();

        if (mgd.ntt > mgd.npp) {
            o.e_nav.mnnav({
                ntt: mgd.ntt,
                npg: mgd.npg,
                npp: mgd.npp,
                url: '/admin/paper'
            });
        }
    },

    modPaper: function() {
        var o = bmoon.admpaperpaper.init();

        var me = $(this),
        id = me.attr('rel'),
        statu = me.hasClass('del') ? 1 : 0,
        pdata = {
            _op: 'mod',
            id: id,
            statu: statu
        };

        $.post('/Manage/papermod', pdata, function(data) {
            if (data.success == 1) {
                noty({text:'操作成功', type: 'success', theme: 'noty_theme_mitgux'});
                me.parent().parent().remove();
            } else {
                noty({text: data.errmsg, type: 'error', theme: 'noty_theme_mitgux'});
            }
        }, 'json');
    }
};

$(document).ready(bmoon.admpaperpaper.onready);
