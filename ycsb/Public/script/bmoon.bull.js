; var g_site_domain = "hnycsb.com",
g_site_www = "http://www.hnycsb.com/";

var bmoon = bmoon || {};
bmoon.bull = {
    version: '1.0',

    tracetype: {
        pageview:   0,
        
        plansearch: 11,
        planleave:  12,
        planmodify: 13,
        planspd:    14,
        
        memberreg:  51,
        membermsg:  52,
        
        commentadd: 71
    },

    tracespec: {
        0:  {
            s: '访问',
            v: function(t) {
                return [
                    '<a target="_blank" href="', t.es_one, t.es_two, '">',
                        t.es_three,
                    '</a>'
                ].join('');
            }
        },
        11: {
            s: '搜索',
            v: function(t) {
                return [
                    '<span class="sdate">', t.es_three, '</span>',
                    '<span class="saddr">', t.es_one, '</span>', ' - ',
                    '<span class="eaddr">', t.es_two, '</span>',
                    '<span class="rescnt">', t.ei_one, '</span>'
                ].join('');
            }
        },
        12: {
            s: '留下路线',
            v: function(t) {
                return [
                    '<span class="sdate">', t.es_three, '</span>',
                    '<span class="saddr">', t.es_one, '</span>', ' - ',
                    '<span class="eaddr">', t.es_two, '</span>',
                    '<span class="subscribe">', t.ei_one, '</span>'
                ].join('');
            }
        },
        13: {
            s: '修改路线',
            v: function(t) {
                return [
                    '<span class="pid">', t.ei_one, '</span>',
                    '<span class="subscribe">', t.ei_two, '</span>',
                    '<span class="statu">', t.ei_three, '</span>'
                ].join('');
            }
        },
        14: {
            s: '爬到路线',
            v: function(t) {;}
        },
        51: {
            s: '注册',
            v: function(t) {
                return [
                    '<span class="mnick">', t.es_one, '</span>',
                    '<span class="mname">', t.es_two, '</span>'
                ].join('');
            }
        },
        52: {
            s: '私信',
            v: function(t) {;}
        },
        71: {
            s: '评论',
            v: function(t) {
                return [
                    '<span class="type">', t.ei_one, '</span>',
                    '<span class="oid">', t.ei_two, '</span>',
                    '<span class="content">', t.es_one, '</span>'
                ].join('');
            }
        }
    },

    browsers: ['mozilla', 'webkit', 'opera', 'msie'],

    dateinputzh: {
        months:        '一月,二月,三月,四月,五月,六月,七月,八月,九月,十月,十一月,十二月',
        shortMonths:   '一,二,三,四,五,六,七,八,九,十,十一,十二',
        days:          '星期日,星期一,星期二,星期三,星期四,星期五,星期六',
        shortDays:     '日,一,二,三,四,五,六'
    },
    
    init: function() {
        var o = bmoon.bull;

        if (o.inited) return o;
        o.inited = true;

        o.c_username = $.cookie('username');
        o.c_mname    = $.cookie('mname_esc');
        o.c_mmsn     = $.cookie('mmsn');

        try {
            o.c_city     = $.parseJSON($.cookie('city'));
            o.c_province = $.parseJSON($.cookie('province'));
        } catch (err) {
            return o;
        }

        // bmoon.bull.js will used on other site
        // so, we return on other site to avoid js error
        o.invoov = false;
        if (!jQuery('a[rel="#bd-login"]').length) return o;
        o.invoov = true;
        
        o.e_content = $('#bd-content');

        o.mnick = $('#bd-mnick');
        o.member = $('#bd-member');
        o.guest = $('#bd-guest');
        o.loginhint = $('#login-hint');
        o.loginmname = $('#login-mname');
        o.loginmsn = $('#login-msn');
        o.loginoverlay = $('a[rel="#bd-login"]').overlay({
            mask: '#666', api: true,
            onLoad: function() {
                $.cookie('mmsn', null, {path: '/', domain: g_site_www});
                o.c_mmsn  = null;
                if (o.loginmname.val().length <= 0)
                    o.loginmname.focus();
                else
                    o.loginmsn.focus();
                //o.logincheckID = setInterval(o.loginCheck, 500);
            },
            onClose: function() {
                o.logincheckID && clearInterval(o.logincheckID);
            }
        });
        o.reloadAfterLogin = true;
        
        return o;
    },

    onready: function() {
        var o = bmoon.bull.init();

        if (!o.invoov) return;
        
        o.bindClick();
        //o.loginCheck();
        
        o.loginref = bmoon.utl.getQueryString("loginref");
        if (o.loginref) {
            o.loginoverlay.load();
        }
        o.vikierr = bmoon.utl.getQueryString("vikierr");
        if (o.vikierr) {
            $('#content').empty().append('<div class="text-error">'+o.vikierr+'</div>')
        }

        if (!o.c_username) {
            o.c_username = bmoon.utl.randomName();
            $.cookie('username', o.c_username, {'path': '/', 'expires': 36500});
        }

        $('.slideshow').cycle({fx: 'fade'});
        /*
        if (!o.c_city) {
            $.getJSON('/json/city/ip', null, function(data) {
                if (data.success == 1 && bmoon.utl.type(data.citys) == 'Array') {
                    o.setCityCookie(data.citys);
                } else o.setCityCookie([{
                    id: '0',
                    pid: '0',
                    grade: '1',
                    geopos: '(0,0)',
                    s: '未知'
                }])
                o.tracePageview();
            });
        } else {
            o.tracePageview();
        }
        */
    },

    bindClick: function() {
        var o = bmoon.bull.init();
        
        $('#login-submit').click(o.login);
        $('#userlogout').click(o.logout);
        o.loginmsn.bind('keydown', 'return', o.login);
    },

    tracePageview: function(pdata) {
        var o = bmoon.bull.init();

        pdata = $.extend({
            _op: 'add',
            type: o.tracetype.pageview,
            sender: o.c_mname || o.c_username,
            provid: o.c_province.id,
            cityid: o.c_city.id,
            browser: o.getBrowserType(),
            bversion: $.browser.version,

            es_one: bmoon.utl.urlclean,
            es_two: bmoon.utl.urlparm,
            es_three: bmoon.utl.title
        }, pdata || {});
        
        $.post('/json/trace', pdata, function(data) {
            ;
        }, 'json');
    },

    login: function() {
        var o = bmoon.bull.init();
        
        if (!$(".VAL_LOGIN").inputval()) return;

        var mname = o.loginmname.val(),
        msn = o.loginmsn.val();

        $.getJSON("/manage/login", {mname: mname, msn: msn}, function(data) {
            if (data.success != 1) {
                alert(data.errmsg || "操作失败， 请稍后再试");
                return;
            }
            $.cookie('mname', mname, {'path': '/', 'expires': 36500});
            $.cookie('mname_esc', mname, {'path': '/', 'expires': 36500});
            $.cookie('mmsn', mname, {'path': '/', 'expires': 36500});
            o.loginCheck();
            o.reloadAfterLogin && setTimeout(function() {location.href = o.loginref || location.href;}, 1000);
        });
    },
    
    logout: function() {
        var o = bmoon.bull.init();
        
        $.getJSON('/Manage/logout');
        
        $.cookie('mname', null, {path: '/', domain: g_site_www});
        $.cookie('mname_esc', null, {path: '/', domain: g_site_www});
        $.cookie('mmsn', null, {path: '/', domain: g_site_www});
        o.c_mname = null;
        o.c_mmsn  = null;
        o.loginmname.val("");
        o.member.addClass('hide');
        //o.loginCheck();
    },
    
    loginCheck: function() {
        var o = bmoon.bull.init();

        if (o.c_mname == null) o.c_mname = $.cookie('mname_esc');
        if (o.c_mmsn  == null) o.c_mmsn  = $.cookie('mmsn');
        
        if (o.c_mname != null && o.c_mmsn != null) {
            o.loginoverlay.close();
            o.mnick.text(o.c_mname);
            //o.guest.addClass('hide');
            o.member.removeClass('hide');
            o.loginmname.val(o.c_mname);
            return true;
        } else {
            //o.guest.removeClass('hide');
            o.member.addClass('hide');
            return false;
        }
    },

    setCityCookie: function(citys) {
        var o = bmoon.bull.init();

        if (bmoon.utl.type(citys) != 'Array') return;
        
        var c = citys[0];

        o.c_city = c;
        $.cookie('city', JSON.stringify(c), {'path': '/', 'expires': 7});
        
        c = null;
        for (var i = 0; i < citys.length; i++) {
            if (citys[i].pid == 0) {
                c = citys[i];
                break;
            }
        }
        if (c) {
            o.c_province = c;
            $.cookie('province', JSON.stringify(c), {'path': '/', 'expires': 7});
        }
    },

    getBrowserType: function() {
        var o = bmoon.bull.init();

        for (var i = 0; i < o.browsers.length; i++) {
            if ($.browser[o.browsers[i]])
                return i;
        }
    }
};

$(document).ready(bmoon.bull.onready);
