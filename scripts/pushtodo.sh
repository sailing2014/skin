#!/bin/bash
apikey=$1
apitoken=$2
env=$3
/usr/bin/curl -X POST \
-H "Content-Type: application/json"  \
-H "apikey:${apikey}" \
-H "apitoken:${apitoken}"  \
-H "time:123" \
https://${env}skin.qiwocloud1.com/v1/user/todo/push/intl
/bin/echo "$(date +'%Y-%m-%d %H:%M:%S')" >> /home/skin/log/crontab_log
