<?php
/**
 * Author: zhangjn
 * Date: 2018/7/16 0016
 * Time: 1:41
 */

if (isset($_GET['value']) && $_GET['value'] != '') {
    $rtn = $mysql->qone('select attention, attention_address from supplier where sid = ?', $_GET['value']);

    if ($rtn) {
        echo $rtn['attention'] . '|' . $rtn['attention_address'];
    } else {
        echo 'no-2';
    }
} else {
    echo 'no-1';
}