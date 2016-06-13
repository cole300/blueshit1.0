<?php

	require_once('/Users/gwd/work/git/blueshit1.0/root/plugins/upyun.class.php');
	// $upyun = new UpYun('beta-xd','guoweidong','gwd860218');
	$upyun = new UpYun('xindong-app','xdapp','L9tk4YRxq8EdhiBK');

	$dir = "/Users/gwd/work/git/blueshit1.0/root/Resigned/";
	$uplist = array();

		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh))!= false)
			{
				//skip . ..
				if($file=='.' || $file=='..' || $file=='.DS_Store')
					continue;

				$filePath = $dir.$file;
				echo $filePath;
				echo "\r\n";

				if($ah = opendir($filePath))
				{
					while(($ipafile = readdir($ah))!=false)
					{
						if($ipafile == '.' || $ipafile=='..' || $ipafile=='.DS_Store')
							continue;
						$ipafile = $filePath."/".$ipafile;
						$uplist[]=$ipafile;
					}
				}
				closedir($ah);
			}
			closedir($dh);
		}
	
	try
	{
	   for($i=0; $i<count($uplist);$i++)
	   {
		   	echo "=== 上传文件".$uplist[$i]." ==="."\r\n";
		    $fh = fopen($uplist[$i], 'rb');
		    // $uplist[$i] = strstr($uplist[$i], '_ipa_');
		    // $uplist[$i] = substr($uplist[$i], 5);
		    // $uplist[$i] = str_replace('/', '_', $uplist[$i]);
		    $uplist[$i] = substr(strrchr($uplist[$i], "/"), 1);
		    echo "截取ipaName=".$uplist[$i];
		    echo "\r\n";
		    $rsp = $upyun->writeFile('/ShenXD_ipa/'.$uplist[$i], $fh, True);
		    fclose($fh);
		    var_dump($rsp);
		    echo "=== 上传成功". "===\n\r\n";
	   }
	}catch(Exception $e)
	{
	    echo $e->getCode();
	    echo $e->getMessage();
	}

	require_once('/Users/gwd/work/git/blueshit1.0/root/plugins/CFPropertyList-master/classes/CFPropertyList/CFPropertyList.php'); 
	use CFPropertyList as CF;
	
	//iOs.app folder
	$floder = $argv[1];
	//read original ipa plist info
	$content = file_get_contents($floder.'/Info.plist');
	$plist = new CF\CFPropertyList();
	$plist->parse($content);
	
	$items = $plist->toArray();

	$url = 'https://xindong-app.b0.upaiyun.com/ShenXD_ipa/'.$uplist[0];
	$bundleId = $items['CFBundleIdentifier'];
	$bundleVer = $items['CFBundleVersion'];
	$title = $items['CFBundleName'];

	// echo "根据游戏名称显示图标：".$argv[2];
	$imgURL = '';
	if($argv[2]=='hsqj'){ // 横扫千军
		$imgURL = 'https://xindong-res.b0.upaiyun.com/hsqj/hsqj03.png';
	}else if($argv[2]=='boli'){ // 天天打波利
		$imgURL = 'https://xindong-res.b0.upaiyun.com/hsqj/hsqj03.png';
	}else if($argv[2]=='ssd'){ // 神仙道
		$imgURL = 'https://xindong-res.b0.upaiyun.com/hsqj/hsqj03.png';
	}else if($argv[2]=='kzkd'){ // 快斩狂刀
		$imgURL = 'https://xindong-res.b0.upaiyun.com/hsqj/hsqj03.png';
	}else if($argv[2]=='sglms'){ // 三国罗曼史
		$imgURL = 'https://xindong-res.b0.upaiyun.com/sglms/sglms.png';
	}else if($argv[2]=='ksfs'){ // 口水封神
		$imgURL = 'https://xindong-res.b0.upaiyun.com/ksfs/ksfs.png';
	}else if($argv[2]=='kp'){ // 横冲直撞
		$imgURL = 'https://xindong-res.b0.upaiyun.com/kp/kp.png';
	}

	//create new plist file
	$plist = new CF\CFPropertyList();
	$plist->add($dict = new CF\CFDictionary());
	$dict->add('items', $arrayItems = new CF\CFArray());
	$arrayItems->add($dict2 = new CF\CFDictionary());
	$dict2->add('assets', $arrayAssets = new CF\CFArray());

	$arrayAssets->add($dict3 = new CF\CFDictionary());
	$dict3->add('kind',new CF\CFString('software-package'));
	$dict3->add('url',new CF\CFString($url));

	// create display image
	$arrayAssets->add($dict4 = new CF\CFDictionary());
	$dict4->add('kind',new CF\CFString('display-image'));
	$dict4->add('needs-shine',new CF\CFBoolean(true));
	$dict4->add('url',new CF\CFString($imgURL));
	
	// create full-size-image
	$arrayAssets->add($dict5 = new CF\CFDictionary());
	$dict5->add('kind',new CF\CFString('full-size-image'));
	$dict5->add('needs-shine',new CF\CFBoolean(true));
	$dict5->add('url',new CF\CFString($imgURL));

	$dict2->add('metadata', $dictMeta = new CF\CFDictionary());
	$dictMeta->add('bundle-identifier',new CF\CFString($bundleId));
	$dictMeta->add('bundle-version',new CF\CFString($bundleVer));
	$dictMeta->add('kind',new CF\CFString('software'));
	$dictMeta->add('title', new CF\CFString($title));
	$plistURL = $floder.$uplist[0].'.plist';

	// echo "\n\r";echo "\n\r";
	// echo "查看floder=".$floder;
	// echo "\n\r";echo "\n\r";
	// echo "查看uplist[0]".$uplist[0];
	// echo "\n\r";echo "\n\r";

	$plist->saveXML($plistURL);

	try
	{
			echo "=== 上传plist ".$plistURL." ==="."\r\n";
			$fh = fopen($plistURL, 'rb');
			$rsp = $upyun->writeFile('/ShenXD_plist/'.$uplist[0].'.plist', $fh, True);
		    fclose($fh);
		    var_dump($rsp);
		    echo "=== 上传成功". "===\n\r\n";
	}catch(Exception $e)
	{
		echo $e->getCode();
		echo $e->getMessage();
	}
?>