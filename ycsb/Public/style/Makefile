LOCALBASE = /home/bigml/web/moon/bull/
include $(LOCALBASE)Make.env
include $(BASEDIR)Make.env

FILES = b/base.css \
        b/admin/paper/paper.css b/admin/paper/new.css b/admin/paper/edit.css \
        b/admin/nav/nav.css \
        b/paper/paper.css

all: preload $(FILES)

preload:
	@mkdir -p b/
	@mkdir -p b/admin/paper/
	@mkdir -p b/admin/nav/
	@mkdir -p b/paper/paper/

b/base.css: reset.css base.css layout.css modules/overlay.css modules/cycle.css

b/admin/paper/paper.css: modules/accordion.css modules/box.css \
                         modules/noty/jquery.noty.css modules/noty/noty_theme_mitgux.css \
                         modules/tablesorter/tablesorter.css \
                         admin/admin.css
b/admin/paper/new.css: modules/accordion.css modules/box.css modules/indicator.css \
                       modules/noty/jquery.noty.css modules/noty/noty_theme_mitgux.css \
                       modules/mntip.css \
                       admin/mku_skin.css admin/mku_set.css \
                       admin/admin.css admin/paper/new.css
b/admin/paper/edit.css: modules/accordion.css modules/box.css modules/indicator.css \
                       modules/noty/jquery.noty.css modules/noty/noty_theme_mitgux.css \
                       admin/mku_skin.css admin/mku_set.css \
                       admin/admin.css admin/paper/edit.css

b/admin/nav/nav.css: modules/accordion.css modules/box.css modules/minibtn.css \
                     modules/noty/jquery.noty.css modules/noty/noty_theme_mitgux.css \
                     modules/mntip.css \
                     admin/admin.css

b/paper/paper.css: paper/paper.css

%.css:
	$(CSSGLUE) $^ > $@


clean:
	@rm -f $(FILES)
