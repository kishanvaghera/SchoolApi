<?php
include ("includes/connect.php");

if($_POST['action']=="addEditRoutine"){

    $iRoutineId=(int)$_POST['iRoutineId'];
    $iClassId=$_POST['iClassId'];
    $iSectionId=$_POST['iSectionId'];
    $iTeacherId=$_POST['iTeacherId'];
    $iClassRoomId=$_POST['iClassRoomId'];
    $iDayId=$_POST['iDayId'];
    $dStartTime=$_POST['dStartTime'];
    $dEndTime=$_POST['dEndTime'];

    $insArr=array();
    $insArr['iClassId']=$iClassId;
    $insArr['iSectionId']=$iSectionId;
    $insArr['iTeacherId']=$iTeacherId;
    $insArr['iClassRoomId']=$iClassRoomId;
    $insArr['iDayId']=$iDayId;
    $insArr['dStartTime']=$mfp->timetosave($dStartTime);
    $insArr['dEndTime']=$mfp->timetosave($dEndTime);

    // print_r($insArr);
    // exit;
    $returnArr=array();
    if($iRoutineId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("class_routine",$insArr," WHERE iRoutineId=".$iRoutineId."");
        $returnArr['status']=200;
        $returnArr['message']="Class Routine detail has been updated successfully.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("class_routine",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Class Routine has been added successfully.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getRoutineList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iRoutineId as id,iRoutineId,vName,vEmail";
    $singleField="SELECT iRoutineId";

    $sql=" FROM class_routine WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iRoutineId LIKE '%".$searchString."%' OR
        vName LIKE '%".$searchString."%' OR
        vEmail LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vSection']!=""){
            $sql.=" AND vSection='".$extraFilter['vSection']."'";
        }
        if($extraFilter['iClassId']!=""){
            $sql.=" AND iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY iRoutineId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";

    echo $selectedField.$sql; exit;
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
}else if($_POST['action']=="getParentDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM parent WHERE eStatus='y' AND iParentId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="deleteParentList"){
    $totalRecord=$_POST['totalRecord'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate("parent",$updArr," WHERE iParentId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"Parent Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}

