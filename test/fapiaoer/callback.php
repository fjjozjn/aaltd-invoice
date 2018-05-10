<?php
/**
 * Author: zhangjn
 * Date: 2018/5/10
 * Time: 16:02
 */

file_put_contents('log.txt', var_dump($_POST)."\r\n", FILE_APPEND);