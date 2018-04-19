<?

if( isset($_GET['value']) && $_GET['value'] != '' && $_GET['ajax'] == 'customer'){
	//20130425 加escape ，因为customer 也出现了中文：青岛。。。
    //20150122 加 family_name
	$rtn = $mysql->q('select t.name, t.family_name, t.title from customer c, contact t where c.cid = t.cid and c.cid = ?', unescape($_GET['value']));
	if($rtn){
		//20121023 加customer的session ，为了在 choose_product_new.php 中能够用到，使用此customer 的 markup_ratio
		$_SESSION['customer'] = $_GET['value'];
		$result = $mysql->fetch();
        $cname_rtn = '';
        for($i = 0; $i < count($result); $i++){
		    $cname_rtn .= ($result[$i]['title'].' '.$result[$i]['name'].' '.$result[$i]['family_name'].'|');
        }
		echo trim($cname_rtn, '|');

        //select2
        /*$cname_rtn = [];
        for($i = 0; $i < count($result); $i++){
            $cname_rtn[$i]['id'] = $result[$i]['title'].' '.$result[$i]['name'].' '.$result[$i]['family_name'];
            $cname_rtn[$i]['text'] = $result[$i]['title'].' '.$result[$i]['name'].' '.$result[$i]['family_name'];
        }
        echo json_encode($cname_rtn);*/

	}else{
		echo 'no-2';	
	}
}elseif( isset($_GET['value']) && $_GET['value'] != '' && $_GET['ajax'] == 'supplier'){
	$rtn = $mysql->q('select t.name, t.family_name, t.title from supplier s, contact t where s.sid = t.sid and s.sid = ?', unescape($_GET['value']));
	if($rtn){
		$result = $mysql->fetch();
		$sname_rtn = '';
		for($i = 0; $i < count($result); $i++){
			$sname_rtn .= ($result[$i]['title'].' '.$result[$i]['name'].' '.$result[$i]['family_name'].'|');
		}
		echo trim($sname_rtn, '|');
	}else{
		echo 'no-3';	
	}		
}elseif( isset($_GET['value']) && $_GET['value'] != '' && $_GET['ajax'] == 'sample_order'){
    $rtn = $mysql->q('select t.name, t.family_name, t.title from supplier s, contact t where s.sid = t.sid and s.name = ?', unescape($_GET['value']));
    if($rtn){
        $result = $mysql->fetch();
        $sname_rtn = '';
        for($i = 0; $i < count($result); $i++){
            $sname_rtn .= ($result[$i]['title'].' '.$result[$i]['name'].' '.$result[$i]['family_name'].'|');
        }
        echo trim($sname_rtn, '|');
    }else{
        echo 'no-3';
    }
}else{
	echo 'no-1';	
}