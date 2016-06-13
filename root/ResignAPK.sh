#!/bin/bash

WORKDIR=`pwd`
UNSIGNED_IPA=`basename "${1}"`
RESIGNED_IPA="Resigned_${UNSIGNED_IPA}"
TMPDIR=`mktemp -d -t apk`

# echo "看下TMP=" $TMPDIR

# echo "参数1是=${1}"
# echo "参数2是=${2}"
# echo "参数3是=${3}"
# echo "参数4是=${4}"

if [ -e "${1}" ]
then
 echo "" # file received
else
 echo "ERROR: 未找到上传的APK文件"
 exit 0
fi

if [ -d "${2}" ]; then
  WORKDIR=$2
fi

cp "${1}" "${TMPDIR}" # -v

cd $TMPDIR

# 拿用户填写的名称重命名apk
mv "${3}" "${4}"

# echo "上传域名:"'http://v0.api.upyun.com/xindong-res/'"${5}"'/xindong/'"${4}"

# 上传apk到YpYun
curl -s -XPUT 'http://v0.api.upyun.com/xindong-res/'"${5}"'/xindong/'"${4}" -H 'Authorization: Basic eGRjb206djdYQXpxSHo2SmszNzlYbQ==' -T "${4}" >> /dev/null

# 清空本地Resigned目录
rm -rf /Users/gwd/work/git/blueshit1.0/root/Resigned/*
# echo 本地清理完成

exit 0
