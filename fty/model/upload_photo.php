<?php 
if(!defined('BEEN_INCLUDE') || !is_object($myerror))exit('Welcome to The Matrix');
//检查访问者IP，以确定本测试页可以显示
//ipRestrict();
?>
<script language="javascript">
function check()
{
	if ( document.getElementById('photo').value.length == 0 )
	{
		alert("請選擇需要上傳的文件");
		return false;
	}
	return true;
}
function back(){
 history.back(-1);
}
</script>

<?

if(isset($_GET['action']) && $_GET['action'] == 'upfile') 
{
/* 
错误信息 	                数值 	说 明
UPLOAD_ERR_OK 	        0 	    没有错误
UPLOAD_ERR_INI_SIZE 	1 	    上传文件的大小超过php.ini的设置
UPLOAD_ERR_FROM_SIZE 	2 	    上传文件的大小超过HTML表单中MAX_FILE_SIZE的值
UPLOAD_ERR_PARTIAL 	    3 	    只上传部分的文件
UPLOAD_ERR_NO_FILE 	    4 	    没有文件上传
*/


	//將 php.ini 的 upload_max_filesize 從 =2M 改為 =10M 不然允許上傳的文件太小了。。。
	if ($_FILES["photo"]["error"] > 0){
		echo "Error: " . $_FILES["photo"]["error"] . "<br />";
	}else{
		echo "Upload: " . $_FILES["photo"]["name"] . "<br />";
		echo "Type: " . $_FILES["photo"]["type"] . "<br />";
        //20130712 允许png
        if($_FILES['photo']['type'] != 'image/jpg' && $_FILES['photo']['type'] != 'image/jpeg' && $_FILES['photo']['type'] != 'image/gif' && $_FILES["photo"]["type"] != 'image/pjpeg' && $_FILES['photo']['type'] != 'image/png'){
			$myerror->error('上传图片 失败!文件不是JPG、PNG或者GIF图片!', 'sendform');
			die();
		} 
		echo "Size: " . ($_FILES["photo"]["size"] / 1024) . " Kb<br />";
		echo "Temp Stored in: " . $_FILES["photo"]["tmp_name"] . "<br />";
	}

	//20121022 将 upload/temp/ 改为了 upload/photo/ 不需要再有个 temp 文件夹了，麻烦
	$target_path = 'upload/photo/' . $_FILES['photo']['name']; 
	
	//php腳本位置
	//echo $_SERVER["SCRIPT_FILENAME"] . '<br/>';

	//赋权限
	//system("chmod 777 ./upload");
	
	//转码
	//$target_path = mb_convert_encoding($target_path, 'utf8', 'gb2312');
	
	// .\upload\ 写成了 ./upload/ 斜线反了就不行了，搞了半天了。。。。。好像也不關事。。。。
	//move_uploaded_file 和 file_exists 只支持GBK格式中文（因为系统的格式是GBK），UTF-8格式的中文不行
	move_uploaded_file($_FILES['photo']['tmp_name'], iconv('UTF-8', 'GBK', $target_path));
	if(file_exists(iconv('UTF-8','GBK',$target_path))) { 
		//复制一个图片改文件名为时间戳（避免中文文件名）保存在另一个文件夹，给网页读取用，特别是生成pdf图片名中不能有中文
		//决定了先把图片上传到temp文件夹中，直到图片随表单提交成功后才将图片剪切到mysql文件夹和photo文件夹中，避免有时只上传图片而为提交表单造成的图片与表单不对应
		//$temp = explode('.', $_FILES['photo']['name']);
		//$db_img_path = 'upload/mysql/'.time().'.'.$temp[1];
		//copy(iconv('UTF-8','GBK', $target_path), iconv('UTF-8','GBK', $db_img_path));
		//$_SESSION['upload_photo'] = $target_path.'|'.$db_img_path;
		
		$_SESSION['fty_upload_photo'] = $target_path;
		echo 'path：'.$_SESSION['fty_upload_photo'];
		if(isset($_SESSION['chg'])){
			$myerror->ok('上传图片 成功!', 'sendform');
		}else{
			$myerror->ok('上传图片 成功!', 'sendform');
		}
	} else { 
		if(isset($_SESSION['chg'])){
			$myerror->ok('上传图片 失败!', $_SESSION['HTTP_REFERER']);
		}else{
 			$myerror->error('上传图片 失败!', 'sendform');
		}
		
	} 

	//20120930 为了设置上传图片后往哪里跳转
	if(isset($_GET['chg'])){
		//fb('chg');
		$_SESSION['chg'] = true;
		$ref = explode('?act=', $_SERVER['HTTP_REFERER']);
		$_SESSION['HTTP_REFERER'] = $ref[1];
		//fb($_SESSION['HTTP_REFERER']);
	}else{
		$_SESSION['chg'] = false;	
	}	
}

if($myerror->getError()){
	require_once(ROOT_DIR.'model/inside_error.php');
}elseif($myerror->getOk()){
	require_once(ROOT_DIR.'model/inside_ok.php');
}else{
	if($myerror->getWarn()){
		require_once(ROOT_DIR.'model/inside_warn.php');
	}
?>
<h1 class="green">上传图片</h1> 
<form action="?act=upload_photo&action=upfile" method="post" name="UForm" enctype="multipart/form-data" onSubmit="return check();"> 
<fieldset> 
<legend class='legend'>第一步：上传图片</legend> 
<ul> 
<li>选择图片：<input type="file" id="photo" name="photo"></li> 
<li>（注：图片的名字中不要有中文）</li>
</ul> 
<button type="submit">上传</button> 
</fieldset> 

</form>

<?
}
?>