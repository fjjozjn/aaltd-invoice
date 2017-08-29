<?php
// print_r_pre($_SESSION);
// print_r_pre($_POST);

//check permission 
//checkAdminPermission(PERM_ENQ_GAME_ACC);
if($myerror->getWarn()){
    require_once(ROOT_DIR.'model/insend_toe_warn.php');
}else{

// 如果有post资料则给Session，并且清除附在上次翻页时残留的$_GET['page']
    if (count($_POST)){
        $_SESSION['search_criteria'] = $_POST;
        $_GET['page'] = 1;
    }

//get staff group information
// $mysql->sp('CALL backend_detail(?, ?, ?)', '1', 'tw_admingrp', '1');
// $temp_grp = $mysql->fetch(0,1);	
// for($i = 0 ; $i < count($temp_grp); $i++){
    // $temp = array($temp_grp[$i]['AdminGrpName'],$temp_grp[$i]['AdminGrpID']);
    // $row_grp[] = $temp;
// }
// print_r_pre($temp_grp);
    $form = new My_Forms();
    $formItems = array(
        'm_id' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['m_id'],
        ),
        'm_type' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['m_type'],
        ),
        'm_name' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['m_name'],
        ),
        'start_date' => array(
            'type' => 'text',
            'restrict' => 'date',
            'value' => @$_SESSION['search_criteria']['start_date'],
        ),
        'end_date' => array(
            'type' => 'text',
            'restrict' => 'date',
            'value' => @$_SESSION['search_criteria']['end_date'],
        ),
        'submitbutton' => array(
            'type' => 'submit',
            'value' => '确定',
        ),
    );
    $form->init($formItems);
    $form->begin();


// resetJSForm('text', 'admin_name');
// print_r_pre($gameList);
// print_r_pre($_GET);
// print_r_pre($GLOBALS);
    ?>
    <!--h1 class="green">Factory PO<em>* indicates required fields</em></h1-->

    <table width="700" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td align="center">
                <fieldset>
                    <legend class='legend'>搜索物料仓库</legend>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td>物料编号 : </td>
                            <td><? $form->show('m_id'); ?></td>
                            <td>物料类别 : </td>
                            <td><? $form->show('m_type'); ?></td>
                        </tr>
                        <tr>
                            <td>物料名称 : </td>
                            <td><? $form->show('m_name'); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <!--<tr>
                            <td>起始日期 : </td>
                            <td><?/* $form->show('start_date'); */?></td>
                            <td>结束日期 : </td>
                            <td><?/* $form->show('end_date'); */?></td>
                        </tr>-->
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td width="100%" colspan='4'><? $form->show('submitbutton'); ?></td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>

    <?
    $form->end();

    //如果有合法的提交，则 getAnyPost = true。
    //如果不是翻页而是普通的GET，则清除之前的Session，以显示一个空白的表单
    $getAnyPost = false;
    if ($form->check()){
        $getAnyPost = true;
    }elseif(!isset($_GET['page'])){
        unset($_SESSION['search_criteria']);
    }

    if($myerror->getAny()){
        require_once(ROOT_DIR.'model/inside_warn.php');
    }

    if ($getAnyPost || isset($_GET['page'])){
        $rs = new RecordSetControl3;
        $rs->record_per_page = ADMIN_ROW_PER_PAGE;
        $rs->addnew_link = "?act=search_material_warehouse";
        $rs->display_new_button = false;
        $rs->sort_field = "m_id";
        $rs->sort_seq = "ASC";

        $current_page = 1;
        $start_row = 0;
        $end_row = $rs->record_per_page;
        if (set($_GET['page'])){
            $current_page = intval($_GET['page']);
            $start_row = (($current_page-1) * $rs->record_per_page);
        }

        $where_sql = "AND fmbi.m_id Like ? AND fmbi.m_type Like ? AND fmbi.m_name Like ?";
        $where1 = $where2 = $where3 = '';
        if (strlen(@$_SESSION['search_criteria']['m_id'])){
            $where1 = $_SESSION['search_criteria']['m_id'];
        }
        if (strlen(@$_SESSION['search_criteria']['m_type'])){
            $where2 = $_SESSION['search_criteria']['m_type'];
        }
        if (strlen(@$_SESSION['search_criteria']['m_name'])){
            $where3 = $_SESSION['search_criteria']['m_name'];
        }
        /*if (strlen(@$_SESSION['search_criteria']['start_date'])){
            if (strlen(@$_SESSION['search_criteria']['end_date'])){
                $where_sql.= " AND in_date between '".$_SESSION['search_criteria']['start_date']." 00:00:00' AND '".$_SESSION['search_criteria']['end_date']." 23:59:59'";
            }else{
                $where_sql.= " AND in_date > '".$_SESSION['search_criteria']['start_date']." 00:00:00'";
            }
        }elseif (strlen(@$_SESSION['search_criteria']['end_date'])){
            $where_sql.= " AND in_date < '".$_SESSION['search_criteria']['end_date']." 23:59:59'";
        }*/

        //普通用户只能搜索到自己开的单 与 状态不为D的单
        //20141031 改为用aFtyName来判断了，工厂用户的AdminName字段改为保存用户的名字
        /*if (!isFtyAdmin()){
            //$where_sql .= " AND sid = '".$_SESSION['ftylogininfo']['aName']."'";
            $where_sql .= " AND sid = '".$_SESSION['ftylogininfo']['aFtyName']."'";
        }*/

        /*
        SELECT m_id, (m_value+mi_value-mo_value) AS num FROM
        ((SELECT m_type, m_id, m_name, m_value FROM fty_material_buy_item GROUP BY m_id) AS fmbi LEFT JOIN
        (SELECT mo_type, mo_id, mo_name, mo_value FROM fty_material_out_item GROUP BY mo_id) AS fmoi ON fmbi.m_id = fmoi.mo_id) LEFT JOIN
        (SELECT mi_type, mi_id, mi_name, mi_value FROM fty_material_in_item GROUP BY mi_id) AS fmii ON fmbi.m_id = fmii.mi_id
        */


        // echo $where_sql;
        $where_sql.= " ORDER BY fmbi.m_id";
        $_SESSION['search_criteria']['page'] = $current_page;

        $temp_table = " (((SELECT m_type, m_id, m_name, m_value FROM fty_material_buy_item GROUP BY m_id) AS fmbi LEFT JOIN (SELECT mo_type, mo_id, mo_name, mo_value FROM fty_material_out_item GROUP BY mo_id) AS fmoi ON fmbi.m_id = fmoi.mo_id) LEFT JOIN (SELECT mi_type, mi_id, mi_name, mi_value FROM fty_material_in_item GROUP BY mi_id) AS fmii ON fmbi.m_id = fmii.mi_id) ";
        $list_field = " SQL_CALC_FOUND_ROWS *, fmbi.m_id, fmbi.m_name, fmbi.m_type, (ifnull(fmbi.m_value,0) + ifnull(fmii.mi_value,0) - ifnull(fmoi.mo_value,0)) as mw_total";

        //get the row count for this seaching criteria
        //$row_count = $mysql->sp('CALL backend_list_count(?, ?)', $temp_table,$where_sql);
        // echo 'CALL backend_list_count("'.$temp_table.'", "'.$where_sql.'");<BR>';
        //echo 'SELECT '.$list_field.' FROM '.$temp_table.' WHERE 1 '.$where_sql;

        //20150710
        //$info = $mysql->sp('CALL backend_list_withfield(?, ?, ?, ?, ?)', $start_row, $end_row, $temp_table, $where_sql, $list_field);

        //$info = $mysql->sp('CALL backend_list(?, ?, ?, ?)', $start_row, $end_row, $temp_table,$where_sql);
        // echo 'CALL backend_list(0,10,"'.$temp_table.'", "'.$where_sql.'")';

        //CONCAT('SELECT ',list_field,' FROM ',tbl_name,' WHERE 1 ',search_data,' LIMIT ?,?' );

        $mysql->q('SELECT '.$list_field.' FROM '.$temp_table.' WHERE 1 '.$where_sql.' LIMIT '.$start_row.','.$end_row, '%'.$where1.'%', '%'.$where2.'%', '%'.$where3.'%');
        $rtn = $mysql->fetch(0, 1);
        $info = count($rtn);

        //$rs->col_width = "100";
        $rs->SetRecordCol("物料编号", "m_id");
        $rs->SetRecordCol("物料名称", "m_name");
        $rs->SetRecordCol("物料类别", "m_type");
        $rs->SetRecordCol("数量", "mw_total");

        $sort = GENERAL_NO;
        $edit = GENERAL_YES;

        //$rs->SetRecordCol("修改", "id", $sort, $edit, "?act=modify_material_in", "modid");
        //$rs->SetRecordCol("PDF", "pcid", $sort, $edit, "model/viewpurchase_pdf.php?pdf=1","pcid");
        //$rs->SetRecordCol("删除", "id", $sort, $edit, "?act=modify_material_in", "delid");
        $rs->SetRSSorting('?act=search_material_warehouse');

        /*
        $cur_page = 0;
        if (isset($_POST["page"])){
        $cur_page = $_POST["page"] - 1;
        }
        */

        $rs->ShowRecordSet($info);
    }

}
?>

