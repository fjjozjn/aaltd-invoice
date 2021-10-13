<?php
/**
 * 上傳product excel自動生成product
 * csv格式見 sys/upload/temp/Copy of Format (Sept 30, 2021) (Revised).csv，如果是xlsx則要另存為csv
 * User: Johnny Zhang
 * Date: 2021/10/11
 * Time: 19:10
 */
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
</script>

<?

if(isset($_GET['action']) && $_GET['action'] == 'upload')
{
    /*
    错误信息 	                数值 	说 明
    UPLOAD_ERR_OK 	        0 	    没有错误
    UPLOAD_ERR_INI_SIZE 	1 	    上传文件的大小超过php.ini的设置
    UPLOAD_ERR_FROM_SIZE 	2 	    上传文件的大小超过HTML表单中MAX_FILE_SIZE的值
    UPLOAD_ERR_PARTIAL 	    3 	    只上传部分的文件
    UPLOAD_ERR_NO_FILE 	    4 	    没有文件上传
    */

    $uploadFile = $_FILES['product_excel'];
    $target_path = '';
    if ($uploadFile["error"] > 0){
        echo "Error: " . $uploadFile["error"] . "<br />";
    }else{
        echo "Upload: " . $uploadFile["name"] . "<br />";
        echo "Type: " . $uploadFile["type"] . "<br />";
        echo "Size: " . ($uploadFile["size"] / 1024) . " Kb<br />";
        echo "Temp Stored in: " . $uploadFile["tmp_name"] . "<br />";
        $target_path = 'upload/temp/' . $uploadFile['name'];
        echo 'Path: ' . $target_path . "<br />";
    }

    //赋权限
    //system("chmod 777 ./upload");

    move_uploaded_file($uploadFile['tmp_name'], $target_path);
    if(file_exists($target_path)) {

        $file = fopen($target_path, 'r');
        while ($row = fgetcsv($file)) {
            //print_r($row);
            $pid = $row[0];
            $description = trim(($row[1] ? $row[1] . "\r\n" : '') .
                ($row[2] ? $row[2] . "\r\n" : '') .
                ($row[3] ? $row[3] . "\r\n" : '') .
                ($row[4] ? $row[4] . "\r\n" : ''));
            $cost_rmb = $row[5];
            $scode = $row[7];
            $description_chi = trim(($row[8] ? $row[8] . "\r\n" : '') .
                ($row[9] ? $row[9] . "\r\n" : '') .
                ($row[10] ? $row[10] . "\r\n" : '') .
                ($row[11] ? $row[11] . "\r\n" : '') .
                ($row[12] ? $row[12] . "\r\n" : '') .
                ($row[13] ? $row[13] . "\r\n" : ''));
            $now = date('Y-m-d H:i:s');
            $judge = $mysql->q('select pid from product where pid = ?', $pid);
            if (!$judge) {
                $result = $mysql->q('insert into product (pid, theme, `type`, in_date, created_by, description, description_chi, scode, cost_rmb) values (' . moreQm(9) . ')', $pid, 0, 'Temp-Product(TEMP)', $now, $created_by, $description, $description_chi, $scode, $cost_rmb);
                if ($result) {
                    echo $pid . ' create success' . "<br />";
                } else {
                    echo $pid . ' create fail' . "<br />";
                }
            } else {
                echo $pid . ' already exist' . "<br />";
            }
        }
        die('All done');
    } else {
        die('Upload fail');
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
    <form action="?act=com-upload_product_excel&action=upload" method="post" name="UForm" enctype="multipart/form-data" onSubmit="return check();">
        <fieldset>
            <legend class='legend'>上傳文件</legend>
            <ul>
                <li>選擇文件：<input type="file" id="product_excel" name="product_excel"></li>
                <li>（注：文件的名字中不要有中文）</li>
            </ul>
            <button type="submit">上傳</button>
        </fieldset>

    </form>

    <?
}
?>
