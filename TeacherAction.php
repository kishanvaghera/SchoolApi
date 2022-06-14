<?php
include ("includes/connect.php");

if($_POST['action']=="AddEditTeacher"){

    $iTeacherId=(int)$_POST['iTeacherId'];
    $vTeacherName=$_POST['vTeacherName'];
    $vTeacherMail=$_POST['vTeacherMail'];
    $vPass=$_POST['vPass'];
    $vDesignation=$_POST['vDesignation'];
    $iDepartmentId=$_POST['iDepartmentId'];
    $vPhone=$_POST['vPhone'];
    $eGender=$_POST['eGender'];
    $eBloodGrp=$_POST['eBloodGrp'];
    $vAddress=$_POST['vAddress'];
    $vAbout=$_POST['vAbout'];
    $isShowWebsite=$_POST['isShowWebsite'];

    $insArr=array();
    $insArr['vTeacherName']=$vTeacherName;
    $insArr['vTeacherMail']=$vTeacherMail;
    $insArr['vPass']=$vPass;
    $insArr['vDesignation']=$vDesignation;
    $insArr['vDepartment']=$iDepartmentId;
    $insArr['vPhone']=$vPhone;
    $insArr['eGender']=$eGender;
    $insArr['eBloodGrp']=$eBloodGrp;
    $insArr['vAddress']=$vAddress;
    $insArr['vAbout']=$vAbout;
    $insArr['isShowWebsite']=$isShowWebsite;
    $insArr['iSchoolId']="1";

    $returnArr=array();
    if($iTeacherId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("teacher",$insArr," WHERE iTeacherId=".$iTeacherId."");
        $returnArr['status']=200;
        $returnArr['message']="teacher detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("teacher",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Admin has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getTeacherList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT tech.iTeacherId as id,tech.iTeacherId,tech.vTeacherName,tech.vDesignation,dept.vDepartment";
    $singleField="SELECT tech.iTeacherId";

    $sql=" FROM teacher as tech 
            LEFT JOIN department as dept ON dept.iDepartmentId=tech.vDepartment
            WHERE tech.eStatus='y' AND tech.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (tech.iTeacherId LIKE '%".$searchString."%' OR
        tech.vTeacherName LIKE '%".$searchString."%' OR
        tech.vDesignation LIKE '%".$searchString."%' OR
        dept.vDepartment LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vTeacherName']!=""){
            $sql.=" AND tech.vTeacherName='".$extraFilter['vTeacherName']."'";
        }
        if($extraFilter['iClassId']!=""){
            $sql.=" AND tech.iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY tech.iTeacherId ";
    
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
            $mfp->mf_dbupdate("teacher",$updArr," WHERE iTeacherId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"Teacher Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getTeacherDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM teacher WHERE eStatus='y' AND iTeacherId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}

