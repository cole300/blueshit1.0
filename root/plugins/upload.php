<?php
require_once('upyun.class.php');

$upyun = new UpYun('beta-xd','guoweidong','gwd860218');

$dir = '/Users/gwd/work/git/blueshit1.0/root/Resigned';// ipa location of local storage

try {
    echo "=========上传ipa\r\n";
    $fh = fopen('../Resigned/signed.ipa', 'rb');
    $rsp = $upyun->writeFile('/ShenXD_ipa/signed2.ipa', $fh, True);
    fclose($fh);
    var_dump($rsp);
    echo "=========DONE\n\r\n";

    echo "=========上传plist\r\n";
    $fh = fopen('../Resigned/signed.plist', 'rb');
    $rsp = $upyun->writeFile('/ShenXD_plist/signed2.plist', $fh, True);
    fclose($fh);
    var_dump($rsp);
    echo "=========DONE\n\r\n";
}
catch(Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
