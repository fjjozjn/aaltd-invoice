<?php
/**
 * Author: zhangjn
 * Date: 2018/3/23
 * Time: 15:42
 */

//打开session
if(session_id() == '') {
    session_start();
}
if (isset($_SESSION['logininfo']) && !empty($_SESSION['logininfo']) && isset($_SESSION['ftylogininfo']) && !empty($_SESSION['ftylogininfo'])) {
    echo 1;
} else {
    echo 0;
}