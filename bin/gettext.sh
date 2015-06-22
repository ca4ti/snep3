#!/bin/sh -x

echo "" > messages.po
echo "Fase 1 : Arquivos php/phtml"
find $1 -type f \( -name "*.php" -o -name "*.phtml" \) | grep -v ".git" | grep -v "Zend/" | xgettext --keyword=translate --language=PHP --from-code=utf-8 -j -f -;
echo "" >> messages.po;
echo "Fase 2 : Arquivos xml"
export PYTHONIOENCODING=utf_8;
for file in `find $1 -type f -name *.xml | grep -v .git | grep -v Zend/`; do python findstrings.py -f $file >> messages.po; done;
xgettext -s messages.po
