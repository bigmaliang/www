; var bmoon = bmoon || {};
bmoon.admpaperedit = {
    version: '1.0',

    init: function() {
        var o = bmoon.admpaperedit;
        if (o.inited) return o;

        o.e_paper_info = $('#paper-info');
        o.e_content = $('#content');
        o.e_submit = $('#submit');

        o.e_paper_info.data('_postData', {});
        
        o.inited = true;
        return o;
    },
    
    onready: function() {
        var o = bmoon.admpaperedit.init();

        o.e_content.markItUp(myMarkdownSettings);
        o.e_content.uploader({
            fileField: '#mkd-input-file',
            url: '/json/zero/image',
            dataName: 'upfile',
            extraData: {_op: 'add'},
            allowedExtension: 'jpeg, bmp, png, gif',
	        afterUpload: function (data) {
                data = jQuery.parseJSON(data);
                if (data.success == 1) {
                    o.imagenum++;
                    var s = '\n' +
                        '[![图片' + o.imagenum+ '](' + data.imageurl_zoom + ' "' + data.imagename+ '")]'+
                        '(' + data.imageurl + ' "' + '点击查看原图")' +
                        '\n';
                    $.markItUp({replaceWith: s});
                } else alert(data.errmsg || '上传图片失败');
	        },
            error: function(msg) {
                alert(msg);
            }
        });

        o.bindClick();
    },

    bindClick: function() {
        var o = bmoon.admpaperedit.init();

        o.e_submit.click(o.savePaper);

        $('input, textarea', o.e_paper_info).change(function() {
            o.e_paper_info.data('_postData')[$(this).attr('name')] = $(this).val();
        });
    },

    savePaper: function() {
        var o = bmoon.admpaperedit.init();

        if (!$('.VAL_NEWPAPER').inputval()) return;

        var me = $(this),
        p = me.parent(),
        pdata = o.e_paper_info.data('_postData');

        pdata._op = 'mod';
        pdata.id  = mgd.paperid;

        $('.vres', p).remove();
        p.removeClass('success').removeClass('error').addClass('loading');

        $.post('/Manage/papermod', pdata, function(data) {
            p.removeClass('loading');
            if (data.success == 1) {
                p.addClass('success');
                noty({text:'修改成功', type: 'success', theme: 'noty_theme_mitgux'});
            } else {
                p.addClass('error');
                noty({text: data.errmsg, type: 'error', theme: 'noty_theme_mitgux'});
            }
        }, 'json');
    }
};

$(document).ready(bmoon.admpaperedit.onready);

