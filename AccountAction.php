<?php
include ("includes/connect.php");

if($_POST['action'] == "addEditStdFee"){
    $iStdInvId = $_POST['iStdInvId'];
    $eType = $_POST['eType'];
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $iUserEmpId = $_POST['iUserEmpId'];
    $vTitle = $_POST['vTitle'];
    $dTotalAmount = $_POST['dTotalAmount'];
    $dPaidAmount = $_POST['dPaidAmount'];
    $eInvStatus = $_POST['eInvStatus'];

    $returnArr=array();
    
    $aInsArr = array();
    $aInsArr['eType'] = $eType;
    $aInsArr['iClassId'] = $iClassId;
    $aInsArr['iSectionId'] = $iSectionId;
    $aInsArr['iUserEmpId'] = $iUserEmpId;
    $aInsArr['vTitle'] = $vTitle;
    $aInsArr['dTotalAmount'] = $dTotalAmount;
    $aInsArr['dPaidAmount'] = $dPaidAmount;
    $aInsArr['eInvStatus'] = $eInvStatus;
    
    if($iStdInvId>0){
        $aInsArr['iLastBy']=1;
        $aInsArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("std_fee_inv",$aInsArr," WHERE iStdInvId=".$iStdInvId."");
        $returnArr['status']=200;
        $returnArr['message']="Invoice detail has been updated succuessfull.";
    }else{
        $aInsArr['iCreatedBy']=1;
        $aInsArr['iSchoolId']=1;
        $aInsArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("std_fee_inv",$aInsArr);
        $returnArr['status']=200;
        $returnArr['message']="Invoice detail has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getStdInvList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT sfv.iStdInvId as id,sfv.iStdInvId,sfv.vTitle,sfv.dTotalAmount,sfv.dPaidAmount,sfv.eInvStatus,cls.vClassName,sec.vSectionName,u.vFullName";
    $singleField="SELECT sfv.iStdInvId";

    $sql=" FROM std_fee_inv as sfv
            LEFT JOIN class as cls ON cls.iClassId = sfv.iClassId 
            LEFT JOIN section as sec ON sec.iSectionId = sfv.iSectionId 
            LEFT JOIN users as u ON u.iUserEmpId = sfv.iUserEmpId 
         WHERE sfv.eStatus='y' AND sfv.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (sfv.iStdInvId LIKE '%".$searchString."%' OR
        sfv.vTitle LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY sfv.iStdInvId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";

    $sqlQuery=$mfp->mf_query($selectedField.$sql);

    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $dataArr[]=$row;
        }
    }

    $total_pages = ceil($totalSingleRows / $limit); 

    if(!empty($dataArr)){
        $retArr=array("status"=>200,"data"=>$dataArr,"totalPage"=>$total_pages);
    }else{
        $retArr=array("status"=>412,"message"=>"No Data Found!");
    }

    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getSyllabusDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM syllabus WHERE eStatus='y' AND iStdInvId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action'] == "GetStudentList"){
    $iClassId = $_POST['iClassId'];

    $rtnArr = array();
    $returnArr = array();
    $selStudent = $mfp->mf_query("SELECT iUserEmpId as value,vFullName as label FROM users WHERE iRoleId = 5 AND eStatus = 'y' AND iClassId = '".$iClassId."' AND iSchoolId = '1'");
    if($mfp->mf_affected_rows()>0){
        while($rowStudent = $mfp->mf_fetch_array($selStudent)){
            $returnArr[] = $rowStudent;
        }
        $rtnArr = array("status"=>200,"data"=>$returnArr);
    }else{
        $rtnArr = array("status"=>412,"data"=>"No Data Found!");
    }

    echo json_encode($rtnArr);
    exit;
}
?>