<?php

/**
 * php create pdf
 * @package 
 * @abstract 
 * @author zjn
 * @since 2011.9
 */

require($_SERVER['DOCUMENT_ROOT'] . '\in7\global.php');
 
if(!isset($_SESSION['logininfo'])){
	die();	
}

require($_SERVER['DOCUMENT_ROOT'] . '\sys\in38\global_admin.php');
require_once('../../../tcpdf/config/lang/eng.php');
require_once('../../../tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// Logo
		$image_file = K_PATH_IMAGES.'header.jpg';
		$this->Image($image_file, '', '', 180, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	}	
	
	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('arial', 'I', 10);
		// Page number
		$this->Cell(200, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}	
}

if(isset($_GET['qid']) && $_GET['qid'] != ''){
	$result1 = $mysql->qone('select * from quotation where qid = ?', $_GET['qid']);
	if(!$result1){
		die('Error(1)');	
	}
	$rs2 = $mysql->q('select * from quote_item where qid = ?', $_GET['qid']);
	if($rs2){
		$result2 = $mysql->fetch();	
	}else{
		die('Error(2)');	
	}

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('TCPDF Example 006');
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	
	// set my header img
	$pdf->SetHeaderData('header.jpg', 180, '', '');
	
	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	
	// ---------------------------------------------------------
	
	// set font
	//$pdf->SetFont('stsongstdlight', '', 9);//这个字体的英文显示太2了，挤到一坨
	//$pdf->SetFont('arialunicid0', '', 10);//這個有的中文顯示不出來。。。
	//$pdf->SetFont('courier', '', 10);//纯英文了，用这个吧
	//$pdf->SetFont('times', '', 10);
	
	// add a page
	$pdf->AddPage();//怎麼能自動分頁？這樣我就不用AddPage了，每頁頂上的表頭問題也能解決
	
	// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
	// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
	
	//獲取總頁數，也是通過product個數來計算，暫時不知道怎麼通過tcpdf函數獲取
	if(isset($result2)){
		$page_nums = (count($result2) <= 5)?1:(intval((count($result2)-5)/7)+2);
	}
	// create some HTML content
	$pdf->SetFont('times', '', 20);
	//div的高度不可調，用span方便多了！！！
	//找到調高度的方法了
	$pdf->Ln(1);
	$html = '<span align="right"><b>Photo Quotation</b><span/>';
	$pdf->Line(15,81,195,81);//為了解決<hr />太高不能控制的的bug，用這個來畫表頭下面的直線了
	$pdf->writeHTML($html, true, false, true, false, '');
	//$pdf->Ln(1);
	
	$pdf->SetFont('arial', '', 10);
	$html = '';
	$html .= '<hr height="2"><table align="left">
				<tr>
					<td width="15%">TO: &nbsp;</td>
					<td width="35%"><b>'.$result1['send_to'].'</b></td>
					<td width="20%">QUOTATION NO.: &nbsp;</td>
					<td width="30%"><b>'.$result1['qid'].'</b></td>
				</tr>
				<tr>
					<td width="15%">ATTENTION: &nbsp;</td>
					<td width="35%"><b>'.$result1['attention'].'</b></td>
					<td width="20%">DATE: &nbsp;</td>
					<td width="30%"><b>'.$result1['mark_date'].'</b></td>
				</tr>
				<tr>
					<td width="15%">REFERENCE: &nbsp;</td>
					<td width="35%"><b>'.$result1['reference'].'</b></td>
					<td width="20%">CURRENCY: &nbsp;</td>
					<td width="30%"><b>'.$result1['currency'].'</b></td>
				</tr>
				<tr>
					<td width="15%">REMARK: &nbsp;</td>
					<td width="35%" rowspan="2"><b>'.$result1['remark'].'</b></td>
					<td width="20%">UNIT: &nbsp;</td>
					<td width="30%"><b>'.$result1['unit'].'</b></td>
				</tr>		
				<tr>
					<td width="15%">&nbsp;</td>
					<td width="20%">PAGE: &nbsp;</td>
					<td width="30%"><b>'.$page_nums.'</b></td>
				</tr>																	
				</table><div></div><div></div>';
	$pdf->writeHTML($html, false, false, true, false, '');
	
	$html = '';
	$html .= '<table align="center">
				<tr>
					<td width="5%">&nbsp;</td>
					<td width="13%" align="left">ITEM</td>
					<td width="20%" align="left">DESC</td>
					<td width="10%">CAT NO.</td>
					<td width="6%">QTY</td>
					<td width="16%">NET PRICE</td>
					<td width="10%">AMOUNT</td>
					<td width="20%">PHOTO</td>
				</tr></table>';	
	$pdf->SetFont('arial', '', 10);//20120704 ie6不显示//$pdf->SetFont('arialbd', '', 10);
	$pdf->writeHTML($html, false, false, true, false, '');
	
	$html = '';

	$pdf->SetFont('arial', '', 10);
// 新方法
$total = 0;
//product 的個數
$rtn_num = count($result2);


for($i = 0; $i < count($result2); $i++){
	$img_html = '';
	if (is_file('../../' . $pic_path_com . $result2[$i]['photos']) == true) { 
		$arr = getimagesize('../../' . $pic_path_com . $result2[$i]['photos']);
		$pic_width = $arr[0];
		$pic_height = $arr[1];
		$image_size = getimgsize(70, 70, $pic_width, $pic_height);
		$img_html = '<div class="imgdiv"><img src="/sys/'.$pic_path_com . $result2[$i]['photos'].'" align="middle" width="'.$image_size['width'].'" height="'.$image_size['height'].'"/></div>';
		//$img_html = '<img src="/sys/'.$pic_path_com . $result2[$i]['photos'].'" align="middle" width="'.$image_size['width'].'" height="'.$image_size['height'].'"/>';
	}
	$html = '';
	if( $i < 5){
		//CSS必須包含在每一次的 writeHTML 中，否則沒有效，這並不是像普通的html頁，包含在最頂，就整個頁面有效
		$html .= '
		<style>
		.imgdiv {
			background-color: #ddd;
			border: 1px solid black;
			height: 80px;
		}
		</style>';
		$html .= '<table align="center">';
		$html .= '<tr>
					<td width="5%">'.($i+1).'</td>
					<td width="33%" colspan="2" align="left"><b>'.$result2[$i]['pid'].'</b></td>
					<td width="10%">000-000</td>
					<td width="6%">'.intval($result2[$i]['quantity']).'</td>
					<td width="16%">'.formatMoney($result2[$i]['price']).'</td>
					<td width="10%" align="center"><b>'.formatMoney(intval($result2[$i]['quantity'])*sprintf("%01.2f", round(floatval($result2[$i]['price']),2))).'</b></td>
					<td width="20%" rowspan="2" valign="middle">'.$img_html.'</td>
				</tr>
				<tr>
					<td height="95">&nbsp;</td>
					<td width="13%">&nbsp;</td>
					<td width="36%" colspan="3" align="left">'.$result2[$i]['description'].'</td>
					<td width="16%">&nbsp;</td>
					<td width="10%">&nbsp;</td>
				</tr>';
		$html .= '</table><hr height="1"/>';			
		$pdf->writeHTML($html, false, false, true, false, '');
	}else{
		$html = '';
		//為在新的一頁加表頭
		if( ($i - 5) % 6 == 0 ){
			$j = 0; //标记第二也起每页的product个数，7个满，输出html
			$pdf->AddPage();
			$html .= '<div></div><div></div><table align="center">
					<tr>
					<td width="5%">&nbsp;</td>
					<td width="13%" align="left">ITEM</td>
					<td width="20%" align="left">DESC</td>
					<td width="10%">CAT NO.</td>
					<td width="6%">QTY</td>
					<td width="16%">NET PRICE</td>
					<td width="10%">AMOUNT</td>
					<td width="20%">PHOTO</td>
				</tr></table>';
			$pdf->SetFont('arial', '', 10);//20120704 ie6不显示//$pdf->SetFont('arialbd', '', 10);
			$pdf->writeHTML($html, false, false, true, false, '');
			$pdf->Line(15,44.5,195,44.5);	
			$html = '';
		}
		
		$pdf->SetFont('arial', '', 10);
		$html .= '
		<style>
		.imgdiv {
			background-color: #ddd;
			border: 1px solid black;
			margin-top:5px;
		}
		</style>';
		$html .= '<table align="center" border="1" >
		<tr>
			<td width="5%">'.($i+1).'</td>
			<td width="33%" colspan="2" align="left"><b>'.$result2[$i]['pid'].'</b></td>
			<td width="10%">000-000</td>
			<td width="6%">'.intval($result2[$i]['quantity']).'</td>
			<td width="16%">'.formatMoney($result2[$i]['price']).'</td>
			<td width="10%" align="center"><b>'.formatMoney(intval($result2[$i]['quantity'])*sprintf("%01.2f", round(floatval($result2[$i]['price']),2))).'</b></td>
			<td width="20%" rowspan="2">'.$img_html.'</td>
		</tr>
		<tr>
			<td height="95">&nbsp;</td>
			<td width="13%">&nbsp;</td>
			<td width="36%" colspan="3" align="left">'.$result2[$i]['description'].'</td>
			<td width="16%">&nbsp;</td>
			<td width="10%">&nbsp;</td>
		</tr>
		</table>
		<hr />';
		$pdf->writeHTML($html, false, false, true, false, '');	
		$html = '';
	}
	$total += intval($result2[$i]['quantity'])*sprintf("%01.2f", round(floatval($result2[$i]['price']),2));
}
$total = formatMoney($total);

/* 原來的做法，導致只有一頁，但也有可能是我不知道怎麼讀取頁數
$total = 0;
$i = 1;
//當前的page，從1開始
$page_now = 1;
foreach($result2 as $v){
	$img_html = '';
	if (is_file('../../' . $pic_path_com . $v['photos']) == true) { 
		$arr = getimagesize('../../' . $pic_path_com . $v['photos']);
		$pic_width = $arr[0];
		$pic_height = $arr[1];
		$image_size = getimgsize(160, 120, $pic_width, $pic_height);
		$img_html = '<img src="/sys/'.$pic_path_com . $v['photos'].'" border="0" align="middle" width="'.$image_size['width'].'" height="'.$image_size['height'].'"/>';
	}
	if($pdf->getPage() > $page_now){
		$page_now = $pdf->getPage();
		$html .= '<tr>
					<td width="5%">&nbsp;</td>
					<td width="13%">ITEM</td>
					<td width="20%">DESC</td>
					<td width="10%">CAT NO.</td>
					<td width="6%">QTY</td>
					<td width="16%">NET PRICE</td>
					<td width="10%">AMOUNT</td>
					<td width="20%">PHOTO</td>
				</tr>
				<hr />';
	}
	
	$html .= '<tr>
				<td height="110">'.$i.'</td>
				<td><b>'.$v['pid'].'</b></td>
				<td>'.$v['description'].'</td>
				<td>&nbsp;</td>
				<td><b>'.intval($v['quantity']).'</b></td>
				<td><b>'.floatval($v['price']).'</b></td>
				<td><b>'.(intval($v['quantity'])*floatval($v['price'])).'</b></td>
				<td>'.$img_html.'</td>
				</tr><hr />';	//這裡加了<tr><td>&nbsp;</td></tr>這個居然會每一頁最後一張圖片，一定會移到下一頁。。。
				
	$total += (intval($v['quantity'])*floatval($v['price']));
	$i++;	
}
*/
	
	$html = '';
	$html .= '<table>
				<tr>
				<td width="5%">&nbsp;</td>
				<td width="13%">&nbsp;</td>
				<td width="20%">&nbsp;</td>
				<td width="10%">&nbsp;</td>
				<td width="6%">&nbsp;</td>
				<td align="right" width="16%"><b>TOTAL: </b></td>
				<td width="10%" align="center"><b>'.$total.'</b></td>
				<td width="20%">&nbsp;</td>
				</tr>';
				
	if($result1['discount'] != '' && $result1['discount'] != 0){				
		$html .= '<tr>
					<td colspan="6" align="right"><b>DISCOUNT: </b></td>
					<td width="10%" align="center"><b>'.formatMoney($result1['discount']).'</b></td>
					<td width="20%">&nbsp;</td>				
				</tr>';
	}else{
		$html .= '<tr><td>&nbsp;</td></tr>';	
	}
	
	$html .= '<hr><tr>
				<td colspan="6" align="right"><b>EX-FACTORY TOTAL ('.$result1['currency'].'): </b></td>
				<td width="10%" align="center"><b>'.formatMoney(mySub($total, $result1['discount'])).'</b></td>
				<td width="20%">&nbsp;</td>
				</tr>';
		
	$html .= '<tr><td>&nbsp;</td></tr>';
				
	$html .= '</table>';
	$pdf->SetFont('arial', '', 10);//20120704 ie6不显示//$pdf->SetFont('arialbd', '', 10);
	$pdf->writeHTML($html, false, false, true, false, '');
	
	$html = '';

/* 例子	
$num=1220.01;
echo fmoney($num);//结果：1,220.21
echo umoney($num);
//结果：ONE THOUSAND AND TWO HUNDRED TWENTY DOLLARS AND TWENTY-ONE CENTS ONLY
echo umoney($num,"rmb");
//结果：ONE THOUSAND AND TWO HUNDRED TWENTY YUAN AND TWENTY-ONE FEN ONLY 	
*/	
	//為了將remarks數據庫中存儲的 \r\n 轉為<br />
	$remarks = str_replace("\r\n", '<br />', $result1['remarks']);
	//不知什麼原因textarea的所有文字前面的回車會被省略沒有保存如數據庫，所以這裡判斷文字中如果有回車，就在最前面加了一個
	if(strpos($result1['remarks'], "\r\n")){
		$remarks = '<br />' . $remarks;
	}
	$html .= '<div align="left">SAY EX-FACTORY: ('.$result1['currency'].') &nbsp;'.umoney(mySub($total, $result1['discount'])).'</div>';
	$html .= '<div align="left">REMARKS: '.$remarks.'</div>';				
	$pdf->SetFont('arial', '', 10);
	// output the HTML content
	$pdf->writeHTML($html, false, false, true, false, '');
	
	// reset pointer to the last page
	$pdf->lastPage();
	
	// ---------------------------------------------------------
	
	//Close and output PDF document
	$pdf->Output('lux.pdf', 'I');
	
	//============================================================+
	// END OF FILE                                                
	//============================================================+
	

}else{
	die('Error(3)');	
}
?>
