LOCALBASE = /home/bigml/web/moon/bull/
include $(LOCALBASE)Make.env
include $(BASEDIR)Make.env

FILES = b/base.js \
        b/admin/paper/paper.js b/admin/paper/new.js b/admin/paper/edit.js \
        b/admin/nav/nav.js \
        b/paper/paper.js \
        b/xbd.js

all: preload $(FILES)

preload:
	@mkdir -p b/
	@mkdir -p b/admin/paper/
	@mkdir -p b/admin/nav/
	@mkdir -p b/paper/

b/base.js: pub/jquery-1.7.1.js pub/jquery.cookie.js pub/jquery.md5.js \
           pub/jquery.bmoon.js pub/jquery.hotkeys.js pub/jquery.mninpval.js \
           pub/jquerytools/src/toolbox/toolbox.expose.js \
           pub/jquerytools/src/overlay/overlay.js \
           pub/cycle/jquery.cycle.all.js \
           pub/bmoon.js bmoon.bull.js

b/admin/paper/paper.js: pub/noty/js/jquery.noty.js \
                        pub/jquery.tablesorter.js \
                        pub/jquery.mnnav.js admin/paper/paper.js
b/admin/paper/new.js: pub/markitup/markitup/jquery.markitup.js admin/mku_set.js \
                      pub/jqueryUploader/src/jquery.uploader.js \
                      pub/jquery.mntips.js admin/admin.js \
                      pub/noty/js/jquery.noty.js \
                      admin/paper/new.js
b/admin/paper/edit.js: pub/markitup/markitup/jquery.markitup.js admin/mku_set.js \
                      pub/jqueryUploader/src/jquery.uploader.js \
                      pub/noty/js/jquery.noty.js \
                      admin/paper/edit.js

b/admin/nav/nav.js: pub/jquery.mntips.js \
                    pub/noty/js/jquery.noty.js \
                    admin/admin.js admin/nav/nav.js

b/paper/paper.js: pub/jquery.mnnav.js paper/paper.js

b/xbd.js: pub/jquery-1.7.1.js xbd-gb.js

%.js:
	@echo -n > $@
	$(JSGLUE) $^ > $@


clean:
	@rm -f $(FILES)
