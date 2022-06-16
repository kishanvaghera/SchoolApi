<?php
include ("includes/connect.php");

if($_POST['action']=="AddEditTeacher"){

    $iUserEmpId=(int)$_POST['iUserEmpId'];
    $vFirstName=$_POST['vFirstName'];
    $vMiddleName=$_POST['vMiddleName'];
    $vLastName=$_POST['vLastName'];
    $vEmail=$_POST['vEmail'];
    $vUserEmpPwd=$_POST['vUserEmpPwd'];
    $iDesignationId=$_POST['iDesignationId'];
    $iDepartmentId=$_POST['iDepartmentId'];
    $vMobileNo=$_POST['vMobileNo'];
    $iDDGenderId=$_POST['iDDGenderId'];
    $iDDBloodGroupId=$_POST['iDDBloodGroupId'];
    $vAddress1=$_POST['vAddress1'];
    $vAbout=$_POST['vAbout'];
    $isShowWebsite=$_POST['isShowWebsite'];

    $vFullName = '';
    if($vFirstName!=""){
        $vFullName .= ucfirst($vFirstName);
    }
    if($vMiddleName!=""){
        $vFullName .= ' '.strtoupper(substr($vMiddleName, 0, 1)).'.';
    }
    if($vLastName!=""){
        $vFullName .= ' '.ucfirst($vLastName);
    }

    $insArr=array();
    $insArr['vFirstName'] = $vFirstName;
    $insArr['vMiddleName'] = $vMiddleName;
    $insArr['vLastName'] = $vLastName;
    $insArr['vFullName'] = $vFullName;
    $insArr['vEmail'] = $vEmail;
    $insArr['vUserEmpPwd'] = $vUserEmpPwd;
    $insArr['iDesignationId'] = $iDesignationId;
    $insArr['iDepartmentId'] = $iDepartmentId;
    $insArr['vMobileNo'] = $vMobileNo;
    $insArr['iDDGenderId'] = $iDDGenderId;
    $insArr['iDDBloodGroupId'] = $iDDBloodGroupId;
    $insArr['vAddress1'] = $vAddress1;
    $insArr['vAbout'] = $vAbout;
    $insArr['isShowWebsite'] = $isShowWebsite;
    $insArr['iSchoolId']="1";

    $returnArr=array();
    if($iUserEmpId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("users",$insArr," WHERE iUserEmpId=".$iUserEmpId."");
        $returnArr['status']=200;
        $returnArr['message']="Teacher detail has been updated succuessfull.";
    }else{
        $insArr['iRoleId']=4;
        $insArr['iCreatedBy']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("users",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Teacher detail has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getTeacherList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT u.iUserEmpId as id,u.iUserEmpId,u.vFullName,desi.vDesignation,dept.vDepartment";
    $singleField="SELECT u.iUserEmpId";

    $sql=" FROM users as u 
            LEFT JOIN department as dept ON dept.iDepartmentId=u.iDepartmentId
            LEFT JOIN designation as desi ON desi.iDesignationId=u.iDesignationId
            WHERE u.eStatus='y' AND iRoleId = 4 AND u.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (u.iUserEmpId LIKE '%".$searchString."%' OR
        u.vFullName LIKE '%".$searchString."%' OR
        desi.vDesignation LIKE '%".$searchString."%' OR
        dept.vDepartment LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vFullName']!=""){
            $sql.=" AND u.vFullName='".$extraFilter['vFullName']."'";
        }
        if($extraFilter['iClassId']!=""){
            $sql.=" AND u.iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY u.iUserEmpId ";
    
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
}else if($_POST['action']=="deleteTeacherList"){
    $totalRecord=$_POST['totalRecord'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate("users",$updArr," WHERE iUserEmpId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"Teacher Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getTeacherDetail"){
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
}

