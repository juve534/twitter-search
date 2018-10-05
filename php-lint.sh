#!/bin/sh

RESULT=`find . -type f -name "*.php" -exec php -l {} \; 2>&1 | grep "PHP Parse error"`

if [ "$RESULT" != "" ];then
    echo "$RESULT"
    exit 1
fi
