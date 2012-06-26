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
