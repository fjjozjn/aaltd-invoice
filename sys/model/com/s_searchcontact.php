<?php
// print_r_pre($_SESSION);
// print_r_pre($_POST);

//check permission 
//checkAdminPermission(PERM_ENQ_GAME_ACC);
if($myerror->getWarn()){
    require_once(ROOT_DIR.'model/innamee_warn.php');
}else{
    //引用特殊的recordset class 文件
    require_once(ROOT_DIR.'sys/in38/recordset.class2.php');

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
        // 'game_name' => array(
        // 'type' => 'text',
        // 'value' => @$_SESSION['search_criteria']['game_name'],
        // ),
        'sid' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['sid'],
        ),
        'first_name' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['first_name'],
        ),
        'family_name' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['family_name'],
        ),
        'address' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['address'],
        ),
        'email' => array(
            'type' => 'text',
            'value' => @$_SESSION['search_criteria']['email'],
        ),
        'submitbutton' => array(
            'type' => 'submit',
            'value' => 'Search',
        ),
    );
    $form->init($formItems);
    $form->begin();


// resetJSForm('text', 'admin_name');
// print_r_pre($gameList);
// print_r_pre($_GET);
// print_r_pre($GLOBALS);
    ?>
    <!--<h1 class="green">SUPPLIER CONTACT<em>* indicates required fields</em></h1>-->

    <table width="700" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td align="center">
                <!--<fieldset>
                    <legend class='legend'>Search</legend>-->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="right">Supplier ID : </td>
                            <td align="left"><? $form->show('sid'); ?></td>
                            <td align="right"></td>
                            <td align="left"></td>
                        </tr>
                        <!--<tr>
                            <td align="right">First Name : </td>
                            <td align="left"><?/* $form->show('first_name'); */?></td>
                            <td align="right">Family Name : </td>
                            <td align="left"><?/* $form->show('family_name'); */?></td>
                        </tr>
                        <tr>
                            <td align="right">Address : </td>
                            <td align="left"><?/* $form->show('address'); */?></td>
                            <td align="right">Email : </td>
                            <td align="left"><?/* $form->show('email'); */?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>-->
                        <tr>
                            <td width="100%" colspan='4'>
                                <?
                                $form->show('submitbutton');
                                // $form->show('resetbutton');

                                ?></td>
                        </tr>
                    </table>
                <!--</fieldset>-->
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
        $rs = new RecordSetControl2;
        $rs->record_per_page = ADMIN_ROW_PER_PAGE;
        $rs->addnew_link = "?act=com-s_searchcontact";
        $rs->display_new_button = false;
        $rs->sort_field = "sid";
        $rs->sort_seq = "DESC";

        $current_page = 1;
        $start_row = 0;
        $end_row = $rs->record_per_page;
        if (set($_GET['page'])){
            $current_page = intval($_GET['page']);
            $start_row = (($current_page-1) * $rs->record_per_page);
        }

        $where_sql = "";

        if (strlen(@$_SESSION['search_criteria']['sid'])){
            $where_sql.= " AND sid Like '%".$_SESSION['search_criteria']['sid'].'%\'';
        }
        if (strlen(@$_SESSION['search_criteria']['first_name'])){
            $where_sql.= " AND name Like '%".$_SESSION['search_criteria']['first_name'].'%\'';
        }
        if (strlen(@$_SESSION['search_criteria']['family_name'])){
            $where_sql.= " AND family_name Like '%".$_SESSION['search_criteria']['family_name'].'%\'';
        }
        if (strlen(@$_SESSION['search_criteria']['address'])){
            $where_sql.= " AND address Like '%".$_SESSION['search_criteria']['address'].'%\'';
        }
        if (strlen(@$_SESSION['search_criteria']['email'])){
            $where_sql.= " AND email Like '%".$_SESSION['search_criteria']['email'].'%\'';
        }


        $where_sql.= ' AND sid <> \'\' ORDER BY sid DESC ';
        $_SESSION['search_criteria']['page'] = $current_page;

        $temp_table = ' contact';
        $list_field = " SQL_CALC_FOUND_ROWS *, concat(title, ' ', name, ' ', family_name) as full_name ";

        //get the row count for this seaching criteria
        //$row_count = $mysql->sp('CALL backend_list_count(?, ?)', $temp_table,$where_sql);
        // echo 'CALL backend_list_count("'.$temp_table.'", "'.$where_sql.'");<BR>';
        //echo 'SELECT '.$list_field.' FROM '.$temp_table.' WHERE 1 '.$where_sql;
        $info = $mysql->sp('CALL backend_list_withfield(?, ?, ?, ?, ?)', $start_row, $end_row, $temp_table, $where_sql, $list_field);
        //$info = $mysql->sp('CALL backend_list(?, ?, ?, ?)', $start_row, $end_row, $temp_table,$where_sql);
        // echo 'CALL backend_list(0,10,"'.$temp_table.'", "'.$where_sql.'")';

        //$rs->col_width = "100";
        $rs->SetRecordCol("Supplier ID", "sid");
        $rs->SetRecordCol("Full Name", "full_name");
        $rs->SetRecordCol("Address", "address");
        //$rs->SetRecordCol("Tel", "tel1");
        //$rs->SetRecordCol("Fax", "fax");
        //$rs->SetRecordCol("Email", "email");

        $sort = GENERAL_NO;
        $edit = GENERAL_YES;
        $rs->SetRecordCol("MODIFY", "id", $sort, $edit,"?act=com-s_modifycontact","modid");
        $rs->SetRecordCol("DEL", "id", $sort, $edit,"?act=com-s_modifycontact","delid");
        $rs->SetRSSorting('?act=com-s_searchcontact');

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

