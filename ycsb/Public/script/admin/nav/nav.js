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
