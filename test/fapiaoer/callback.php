<?php
/**
 * Author: zhangjn
 * Date: 2018/5/10
 * Time: 16:02
 */

$postData = file_get_contents('php://input');
file_put_contents('log.txt', $postData."\r\n", FILE_APPEND);
echo '{"code":0,"msg":"ok"}';