<?php
/**
 * Author: zhangjn
 * Date: 2018/3/23
 * Time: 15:07
 */

// http://aaltd-invoice.cxm/auth/auth.php?aAdminEmail=2322289219@qq.com&aFtyName=fjjozjn&aID=2&aLogin=fjjozjn&aName=fjjozjn&aNameChi=zjn

//打开session
if(session_id() == '') {
    session_start();
}
$_SESSION['logininfo'] = $_GET;
$_SESSION['ftylogininfo'] = $_GET;
