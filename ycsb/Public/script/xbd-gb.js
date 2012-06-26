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

        if (o.href.match(/mp3.xbd61.com\/File_classd_62_[0-9].html/)) {
            o.stage = o.href.match(/mp3.xbd61.com\/File_classd_62_([0-9]).html/)[1];
            o.parseDir();
        } else if (o.href.match(/mp3.xbd61.com\/File_classd_62_[0-9]_[0-9]+.html/)) {
            o.stage = o.href.match(/mp3.xbd61.com\/File_classd_62_([0-9])_[0-9]+.html/)[1];
            o.parseNode();
        }

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.spdxbd.init();

    },

    parseDir: function() {
        var o = bmoon.spdxbd.init();

        for (var i = 0; i < 20; i++) {
            var url = 'http://mp3.xbd61.com/File_classd_62_' + o.stage + '_' + i + '.html';
            window.open(url);
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
                stage: o.stage,
                mid: href.match(/File_Show_([0-9]+)\.html/)[1],
                title: title.match(/^(.*)¡ª¼ò½é/)[1]
            };

            $.getJSON('http://www.hnycsb.com/json/zero/mp3?JsonCallback=?',
                      pdata,
                      function(data) {;});
        });

        setTimeout(function() {
            window.opener = null;
            window.open('', '_self', '');
            window.close();
        }, 60*1000);
    }
};

$(document).ready(bmoon.spdxbd.onready);
