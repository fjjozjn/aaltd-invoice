<?php
/**
 * Author: zhangjn
 * Date: 2018/3/23
 * Time: 15:07
 */
//需要试一下直接在index页面加入iframe，而不是用get请求，看行不行
if(isset($_GET['session_id'])){
	// http://aaltd-invoice.cxm/auth/auth.php?session_id=xxxxxx
	session_id($_GET['session_id']);
	session_start();
}else{
	// http://aaltd-invoice.cxm/auth/auth.php?aAdminEmail=2322289219@qq.com&aFtyName=fjjozjn&aID=2&aLogin=fjjozjn&aName=fjjozjn&aNameChi=zjn
	if(session_id() == '') {
	    session_start();
	}
	$_SESSION['logininfo'] = $_GET;
	$_SESSION['ftylogininfo'] = $_GET;

	echo session_id();
}