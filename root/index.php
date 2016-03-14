<?php
	require_once('/Users/gwd/work/git/blueshit1.0/root/plugins/upyun.class.php');

	$curPage = "1";

	function urlTable() 
	{
		$upyun = new UpYun('beta-xd','guoweidong','gwd860218');
		//get upyun file list
		try
		{
			$list = $upyun->getList('/ShenXD_plist');
		}catch(Exception $e){echo $e->getCode();echo $e->getMessage();}

		// floder amount
		$floderAmount = count($list);
		// limit visible amount per page
		$pageSize = 5;

		echo '<table border="1" cellpadding="3%">';
		for($x=0; $x<$pageSize; $x++)
		{
			echo '<tr>';
				echo '<th>';
				echo "ID= $x";
				echo '</th>';

				echo '<td>';
				echo $list[$x]['name'];
				echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
?>

<!DOCTYPE html>
<html>
	<head> 
		<meta http-equiv="Content-Type" content="text/html"; charset="UTF-8" />
		<title> BlueShit Distribution Tools </title>
		<link rel="stylesheet" href="comm.css" type="text/css" />

		<script type="text/javascript">
			var curPage = 1;
			function prePage()
			{
				//var value = "<?php echo $GLOBALS['curPage']; ?>";
				if(curPage > 1)
				document.getElementById("curPageBtn").innerHTML= "第"+ --curPage +"页";
			}
			function nextPage()
			{
				document.getElementById("curPageBtn").innerHTML= "第"+ ++curPage +"页";
			}
		</script>

	</head>
	<body bgcolor="#EEEEEE">
		<br/>
		<center>
			<h1> <span style='color:#556B2F;'> 测试欢迎页面 </span> </h1>

			<?php urlTable(); ?>

			<button type="button" id="preBtn" onclick="prePage();">上一页!</button><button type="button" id="nextBtn" onclick="nextPage()">下一页</button>

			<div id="curPageBtn">
				<script>document.write("第"+curPage+"页")</script>
			</div>
		<br/>

		<h3>
			<form method='POST' enctype='multipart/form-data' action='/resign'>
				上传IPA包转企业签名包: 
				<input type="file"  class="input-file" name="ipafile">
				<input type="image" src="btnSubmit.png" width="40px"/>
				<a title="iPhone" href="itms-services://?action=download-manifest&url=https://172.26.129.3/test.plist">Download</a>
			</form>
		</h3>
	</body>
</html>