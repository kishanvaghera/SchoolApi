<?php
include ("includes/connect.php");

if($_POST['action']=="addAdmission"){

    $iAdmissionId=(int)$_POST['iAdmissionId'];
    $vName=$_POST['vName'];
    $vParent=$_POST['vParent'];
    $dBirthday=$_POST['dBirthday'];
    $vAddress=$_POST['vAddress'];
    $vEmail=$_POST['vEmail'];
    $iClassId=$_POST['iClassId'];
    $eGender=$_POST['eGender'];
    $vPhone=$_POST['vPhone'];
    $vPassword=$_POST['vPassword'];
    $vSection=$_POST['vSection'];
    $eBloodGrp=$_POST['eBloodGrp'];

    $insArr=array();
    $insArr['vName']=$vName;
    $insArr['vParent']=$vParent;
    $insArr['dBirthday']=$dBirthday;
    $insArr['vAddress']=$vAddress;
    $insArr['vEmail']=$vEmail;
    $insArr['eGender']=$eGender;
    $insArr['vPhone']=$vPhone;
    $insArr['vPassword']=$vPassword;
    $insArr['vSection']=$vSection;
    $insArr['eBloodGrp']=$eBloodGrp;
    $insArr['iSchoolId']=1;
    $insArr['iClassId']=$iClassId;

    $returnArr=array();
    if($iAdmissionId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("admission",$insArr," WHERE iAdmissionId=".$iAdmissionId."");
        $returnArr['status']=200;
        $returnArr['message']="Admission detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("admission",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Admission has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getAdmissionList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];


    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iAdmissionId as id,iAdmissionId,vName,vParent,dBirthday,vPhone";
    $singleField="SELECT iAdmissionId";

    $sql=" FROM admission WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iAdmissionId LIKE '%".$searchString."%' OR
        vName LIKE '%".$searchString."%' OR
        vParent LIKE '%".$searchString."%' OR
        vPhone LIKE '%".$searchString."%' OR
        dBirthday LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vSection'] && $extraFilter['vSection']!=""){
            $sql.=" AND vSection='".$extraFilter['vSection']."'";
        }
        if($extraFilter['iClassId'] && $extraFilter['iClassId']!=""){
            $sql.=" AND iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY iAdmissionId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";


    $sqlQuery=$mfp->mf_query($selectedField.$sql);

    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $row['dBirthday']=$mfp->date2dispnew($row['dBirthday']);
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
}else if($_POST['action']=="getAdmissionDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM admission WHERE eStatus='y' AND iAdmissionId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="deleteAdmissionList"){
    $totalRecord=$_POST['totalRecord'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate("admission",$updArr," WHERE iAdmissionId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"Admission Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}

