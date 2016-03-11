#!/bin/bash

WORKDIR=`pwd`
UNSIGNED_IPA=`basename "${1}"`
RESIGNED_IPA="Resigned_${UNSIGNED_IPA}"
TMPDIR=`mktemp -d -t ipa`

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
 
echo unzip  "${UNSIGNED_IPA}"

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

rm -rf "Payload/${APPNAME}/_CodeSignature/"

cp ~/work/git/blueshit1.0/root/Provisionings/Everything.mobileprovision "Payload/${APPNAME}/embedded.mobileprovision"

security unlock -p s999

if [ -f "Payload/${APPNAME}/ResourceRules.plist" ];
then
  codesign -f -s "iPhone Distribution: Shanghai Xindong Enterprise Development Co., Ltd." --entitlements ~/Projects/entitlements.plist "Payload/${APPNAME}" --resource-rules="Payload/${APPNAME}/ResourceRules.plist"
else
#UE5H8B62F9 --entitlements ~/Projects/entitlements.plist
  codesign -f -s "iPhone Distribution: Shanghai Xindong Enterprise Development Co., Ltd." --entitlements ~/Projects/entitlements.plist "Payload/${APPNAME}" 
fi

#verify
codesign -dvvv "Payload/${APPNAME}"

echo zip "${RESIGNED_IPA}"
 
zip -qr "${RESIGNED_IPA}" Payload

if [ $? != 0 ]; 
then
  echo "ERROR: zip failed"
  exit 0
fi

chmod 777 "${WORKDIR}"

cp -v "${RESIGNED_IPA}" "${WORKDIR}"

cd $WORKDIR

echo "${WORKDIR}/${RESIGNED_IPA}"
#rm -rf $TMPDIR

if [ ! -e "${WORKDIR}/${RESIGNED_IPA}" ]
then
echo fffff!
fi

exit 0
