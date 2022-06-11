<?php
include ("includes/connect.php");

if($_POST['action']=="addSchool"){

    $iSchoolId=(int)$_POST['iSchoolId'];
    $vSchoolName=$_POST['vSchoolName'];
    $vAddress1=$_POST['vAddress1'];
    $vAddress2=$_POST['vAddress2'];
    $iCountryId=$_POST['iCountryId'];
    $iStateId=$_POST['iStateId'];
    $vCity=$_POST['vCity'];
    $vPhoneNo=$_POST['vPhoneNo'];
    $vOfficeNo=$_POST['vOfficeNo'];
    $vLanLineNo=$_POST['vLanLineNo'];
    $vPincode=$_POST['vPincode'];

    $insArr=array();
    $insArr['iSchoolId']=$iSchoolId;
    $insArr['vSchoolName']=$vSchoolName;
    $insArr['vAddress1']=$vAddress1;
    $insArr['vAddress2']=$vAddress2;
    $insArr['iCountryId']=$iCountryId;
    $insArr['iStateId']=$iStateId;
    $insArr['vCity']=$vCity;
    $insArr['vPhoneNo']=$vPhoneNo;
    $insArr['vOfficeNo']=$vOfficeNo;
    $insArr['vLanLineNo']=$vLanLineNo;
    $insArr['vPincode']=$vPincode;

    $returnArr=array();
    if($iSchoolId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("schools",$insArr," WHERE iSchoolId=".$iSchoolId."");
        $returnArr['status']=200;
        $returnArr['message']="School detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("schools",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="School has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="schoollist"){

    $pageno = 1;
    if($_POST['pageno']){
        $pageno = $_POST['pageno'];
    }
    
    $no_of_records_per_page = $_POST['record_per_pgae']?$_POST['record_per_pgae']:10;
    $offset = ($pageno-1) * $no_of_records_per_page;

    $dataArr=array();
    $sqlSchool=$mfp->mf_query("SELECT * FROM schools WHERE eStatus='y' LIMIT ".$offset.", ".$no_of_records_per_page."");
    $totalRecords=$mfp->mf_affected_rows();

    $total_pages = ceil($totalRecords / $no_of_records_per_page);

    if($totalRecords>0){
        while($row=$mfp->mf_fetch_array($sqlSchool)){
            $dataArr[]=$row;
        }
    }

    $returnArr=array();
    if(!empty($dataArr)){
        $returnArr['status']=200;
        $returnArr['data']=$dataArr;
    }else{
        $returnArr['status']=412;
        $returnArr['message']="No Data Found!";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="deleteschool"){

    $id=$_POST['id'];

    $returnArr=array();
    $updArr=array();
    $updArr['eStatus']="d";
    if($mfp->mf_dbupdate("schools",$updArr," WHERE iSchoolId=".$id."")){
        $returnArr['status']=200;
        $returnArr['message']="School detail has beeen deleted succuessfully.";
    }else{
        $returnArr['status']=412;
        $returnArr['message']="No Data Found!";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getUserList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iUserEmpId as id,iUserEmpId,vFirstName,vMiddleName,vLastName,vFullName";
    $singleField="SELECT iUserEmpId";

    $sql=" FROM users WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iUserEmpId LIKE '%".$searchString."%' OR
        vFirstName LIKE '%".$searchString."%' OR
        vMiddleName LIKE '%".$searchString."%' OR
        vLastName LIKE '%".$searchString."%' OR
        vFullName LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vFirstName']!=""){
            $sql.=" AND vFirstName='".$extraFilter['vFirstName']."'";
        }
        if($extraFilter['iClassId']!=""){
            $sql.=" AND iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY iUserEmpId ";
    
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
}else if($_POST['action']=="deleteUserList"){
    $totalRecord=$_POST['totalRecord'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate("users",$updArr," WHERE iUserEmpId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"User Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getUserDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM users WHERE eStatus='y' AND iUserEmpId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getListData"){

    $fieldValue=$_POST['fieldValue'];
    $fieldLabel=$_POST['fieldLabel'];
    $tableName=$_POST['tableName'];
    $extraWhere=$_POST['extraWhere'];

    $selectedField="SELECT ".$fieldValue." as value,".$fieldLabel." as label";

    $sql=" FROM ".$tableName." ";

    if($extraWhere!=""){
        $sql.=" WHERE ".$extraWhere;
    }

    $sql.=" GROUP BY ".$fieldValue." ";
    
    $sqlQuery=$mfp->mf_query($selectedField.$sql);
    
    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $dataArr[]=$row;
        }
    }

    if(!empty($dataArr)){
        $retArr=array("status"=>200,"data"=>$dataArr);
    }else{
        $retArr=array("status"=>412,"message"=>"No Data Found!");
    }

    echo json_encode($retArr);
    exit();
}
