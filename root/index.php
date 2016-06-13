<?php
	require_once('/Users/gwd/work/git/blueshit1.0/root/plugins/upyun.class.php');

	$upyun;

	function getList()
	{
		if(empty($upyun))
			// $upyun = new UpYun('beta-xd','guoweidong','gwd860218');;
			$upyun = new UpYun('xindong-app','xdapp','L9tk4YRxq8EdhiBK');
		//get upyun file list
		try
		{
			if(empty($list))
				$list = $upyun->getList('/ShenXD_plist');
				$list = array_reverse($list);
		}catch(Exception $e){echo $e->getCode();echo $e->getMessage();}

		return $list;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html"; charset="UTF-8" />
		<title> BlueShit Distribution Tools </title>
		<link rel="stylesheet" href="mediagroove.css" type="text/css" />
		<script type="text/javascript">
			var curPage = 1;
			var index = 0;
			var pageVisNum = 5;
			var phpArray;
			var jsonArray;
			var amount;
			function createTable()
			{
				// var div1 = document.getElementById('tb1');
				phpArray = '<?php echo JSON_encode(getList());?>';
				jsonArray = JSON.parse(phpArray);
				// list length from upyun
				amount = eval(jsonArray).length;
				return "";
			}
			function refreshTable()
			{
				var div1 = document.getElementById('tb1');
				var code="";
				for(var i = index*pageVisNum; i < index*pageVisNum + pageVisNum; i ++)
				{
					//stop when data is null
					if(!jsonArray[i])
						break;

					code += "<tr>";
					code += "<td>"+i+"</td>";
					//code += "<td><a href=>"+jsonArray[i]+"</a></td>";
					code += "<td><a href="+"itms-services://?action=download-manifest&url=https://xindong-app.b0.upaiyun.com/ShenXD_plist/"+jsonArray[i]+">"+jsonArray[i]+"</a></td>";
					code += "</tr>";
				}
				div1.innerHTML = code;
				return "";
			}
			function prePage()
			{
				if(index * pageVisNum - pageVisNum >= 0)
					index --;
				document.getElementById("curPageBtn").innerHTML= "第"+ (index+1) +"页";
				refreshTable();
			}
			function nextPage()
			{
				if(index * pageVisNum + pageVisNum < amount)
					index++;
				document.getElementById("curPageBtn").innerHTML= "第"+ (index+1) +"页";
				refreshTable();
			}
			function setChannel(channelID)
			{
				var obj = document.getElementById('gameChoice')
				var idx = obj.selectedIndex; // index
				var val = obj.options[idx].value;
				document.getElementById(channelID).value = val;
			}
			function submitIOS(channelID)
			{
				setChannel(channelID)

				var obj_resign = document.getElementById('gameResign');
				var idx_resign = obj_resign.selectedIndex; // index
				var val_resign = obj_resign.options[idx_resign].value;
				document.getElementById("resignIOS").value = val_resign;
			}
		</script>
	</head>
	<body bgcolor="#EEEEEE">
		<br/>
		<center>
			<h1> <span style='color:#556B2F;'> IOS打包分发工具 </span> </h1>

			<?php getList(); ?>

			<div>
				<table>
				<caption>IOS已上传包列表</caption>
				<THEAD><TR><TH width=50px>应用序号</TH><TH width=100px>下载链接地址</TH></TR></THEAD>
				<tbody id='tb1'>
					<script>
						document.write(createTable())
						document.write(refreshTable())
					</script>
				</tbody>
				</table>
				<button type="button" id="preBtn" onclick="prePage();">上一页!</button>
				<button type="button" id="nextBtn" onclick="nextPage()">下一页</button>
			</div>
			<div id="curPageBtn">
				<script>document.write("第"+(index+1)+"页")</script>
			</div>
		<br/>

		<h3>
			<form method='POST' enctype='multipart/form-data' action='/resign' onsubmit="submitIOS('channelIOS')">
				上传IPA包转企业签名包:
				<input type="file"  class="input-file" name="ipafile">
				<input type="image" src="btnSubmit.png" width="30px"/>
				<input type="hidden" name="channel" id="channelIOS" value="defaultIOS"/>
				<input type="hidden" name="resign" id="resignIOS" value="defaultResignIOS"/>
				</br></br>
			  自定义ipa名<input type="text" name="customName" class="text" value="game_channel_ver.ipa"/>
			</br></br>
				选择签名文件: <select id="gameResign">
											<option value="noneed">不签名</option>
											<option value="pinidea">PinIdea品志</option>
								</select>
			</br></br>
			</form>
		</h3>

		<h3>
		</br></br>
		<h1> <span style='color:#556B2F;'> 安卓做链接工具 </span> </h1>
		</br>
		选择要打包的游戏: <select id="gameChoice">
												<option value="hsqj">横扫千军</option>
												<option value="ssd">神仙道2016</option>
												<option value="boli">天天打波利</option>
												<option value="kzkd">快斩狂刀</option>
												<option value="sglms">三国罗曼史</option>
												<option value="ksfs">口水封神</option>
												<option value="kp">横冲直撞</option>
										</select>
		</br></br>
		<form method='POST' enctype='multipart/form-data' action='/resign' onsubmit="setChannel('channelAND')">
		   上传APK包:
		   <input type="file"  class="input-file" name="ipafile"/>
			 <input type="image" src="btnSubmit.png" width="30px"/>
			 <input type="hidden" name="channel" id="channelAND" value="defaultAND"/>
		 </br></br>
			 自定义apk名<input type="text" name="customName" class="text" value="游戏_渠道_版本.apk"/>
		</form>
		</h3>

	</body>
</html>
