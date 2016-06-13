#!/bin/bash

WORKDIR=`pwd`
UNSIGNED_IPA=`basename "${1}"`
RESIGNED_IPA="${UNSIGNED_IPA}"
TMPDIR=`mktemp -d -t ipa`

val_resign="${6}"

echo "看下TMP=" $TMPDIR

if [ -e "${1}" ]
then
 echo file received
else
 echo "ERROR: ipa not exist"
 echo "Usage: ResignIPA.sh <ipafile> <output dir>"
 exit 0
fi

if [ -d "${2}" ]; then
  WORKDIR=$2
fi

cp -v "${1}" "${TMPDIR}"
echo "${1}" - "${TMPDIR}"

cd $TMPDIR
cd ..
sudo chmod -R 777 * -rf
cd $TMPDIR

unzip -q "${UNSIGNED_IPA}"

if [ $? != 0 ]; 
then
  echo "ERROR: unzip failed"
  exit 0
fi

cd Payload

APPNAME=`ls | grep \.app$`

cd $TMPDIR

echo app is $APPNAME

if [ "${val_resign}" == "pinidea" ];
then
	echo 开始使用品质重新签名
	rm -rf "Payload/${APPNAME}/_CodeSignature/"
	cp ~/work/git/blueshit1.0/root/Provisionings/embedded.mobileprovision "Payload/${APPNAME}/embedded.mobileprovision"
	security unlock -p s999
	/usr/bin/codesign -f -s "iPhone Distribution: PinIdea co., Ltd" --entitlements /Users/gwd/work/git/blueshit1.0/root/entitlements.plist Payload/*.app/
else
	echo 不重新签名
fi
 
zip -r -q "${RESIGNED_IPA}" Payload

echo "完成未重签名ipa= ${RESIGNED_IPA}"

# 拿用户填写的名称重命名apk
mv -v "${3}" "${4}"

chmod 777 "${WORKDIR}"

cp -v "${4}" "${WORKDIR}"

cd $WORKDIR

echo "${WORKDIR}/${4}"
#rm -rf $TMPDIR

if [ ! -e "${WORKDIR}/${4}" ]
then
echo fffff!
fi

echo "上传前=${APPNAME}"

PLISTDIR=$TMPDIR"/Payload/${APPNAME}/"

echo "上传后="$PLISTDIR

cd ..
cd ..
echo 上传ipa到UpYun
 php getpath.php "$PLISTDIR" "${5}"
echo 上传结束

# 清空本地Resigned目录
rm -rf /Users/gwd/work/git/blueshit1.0/root/Resigned/*
echo 本地清理完成

exit 0
