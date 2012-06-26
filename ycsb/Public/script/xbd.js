; var bmoon = bmoon || {};
bmoon.spdxbd = {
    version: '1.0',

    init: function() {
        var o = bmoon.spdxbd;
        if (o.inited) return o;

        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.spdxbd.init();

        o.href = location.href;

        if (o.href.match(/mp3.xbd61.com\/File_classd_62_4.html/)) {
            o.parseDir();
        } else if (o.href.match(/mp3.xbd61.com\/File_classd_62_4_[0-9]+.html/)) {
            o.parseNode();
        }

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.spdxbd.init();

    },

    parseDir: function() {
        var o = bmoon.spdxbd.init();

        for (var i = 0; i < 1; i++) {
            var url = 'http://mp3.xbd61.com/File_classd_62_4_'+i+'.html';
            setTimeout(function() {window.open(url)}, Math.random()*6*1000);
        }
    },

    parseNode: function() {
        var o = bmoon.spdxbd.init();

        console.log('parse node');

        var t = $('table')[55],
        ms = $('a[target="_blank"]', t);

        $.each(ms, function(i, obj) {
            var me = $(obj),
            title = me.attr('title'),
            href = me.attr('href'),
            pdata = {
                _op: 'add',
                mid: href.match(/File_Show_([0-9]+)\.html/)[1],
                title: title.match(/^(.*)—简介/)[1]
            };

            $.getJSON('http://www.hnycsb.com/json/zero/mp3?JsonCallback=?',
                      pdata,
                      function(data) {;});
        });
    }
};

$(document).ready(bmoon.spdxbd.onready);
