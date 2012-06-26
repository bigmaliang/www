; var bmoon = bmoon || {};
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
