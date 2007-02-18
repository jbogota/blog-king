#!/bin/sh
TEMPLATE=ImageManager.pot


files="../thumbs.php ../editor.php ../editorframe.php ../configure/index.php ../images.php ../manager.php ../newfolder.php ../assets/editorframe_js.php ../assets/images_js.php ../assets/manager_js.php"

for f in $files ; do
  xgettext --keyword=__ --keyword=_e  --default-domain=ImageManager -j \
      --language=php $f --output=$TEMPLATE
done

if [ -n "$2" ]; then
  if [ x$2 == 'x-p' ]; then
    msgfmt --statistics $TEMPLATE
  else
		if [ -f $2.po ]; then
      msgmerge -o .tmp$2.po $2.po $TEMPLATE
			mv .tmp$2.po $2.po
			msgfmt --statistics -o ImageManager-$2.mo $2.po
 	  else
	   echo "Usage: $0 directoy [-p|locale]"
    fi
  fi
fi