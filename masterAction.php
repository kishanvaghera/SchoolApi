<?php
include ("includes/connect.php");

if($_POST['action']=="addEditSection"){
    $iSectionId=(int)$_POST['iSectionId'];
    $vSectionName=$_POST['vSectionName'];
    $iClassId=$_POST['iClassId'];

    $insArr=array();
    $insArr['vSectionName']=$vSectionName;
    $insArr['iClassId']=$iClassId;
    $returnArr=array();
    if($iSectionId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("section",$insArr," WHERE iSectionId=".$iSectionId."");
        $returnArr['status']=200;
        $returnArr['message']="section detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("section",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="section has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSectionList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iSectionId as id,iSectionId,vSectionName,iClassId";
    $singleField="SELECT iSectionId";

    $sql=" FROM section WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iSectionId LIKE '%".$searchString."%' OR
        vSectionName LIKE '%".$searchString."%' OR
        iClassId LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iSectionId ";
    
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
}else if($_POST['action']=="getSectionDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM section WHERE eStatus='y' AND iSectionId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditSubject"){
    $iSubjectId=$_POST['iSubjectId'];
    $vSubjectName=$_POST['vSubjectName'];

    $insArr=array();
    $insArr['vSubjectName']=$vSubjectName;
    $returnArr=array();
    if($iSubjectId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("subject",$insArr," WHERE iSubjectId=".$iSubjectId."");
        $returnArr['status']=200;
        $returnArr['message']="Subject detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("subject",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Subject has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSubjectList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iSubjectId as id,iSubjectId,vSubjectName";
    $singleField="SELECT iSubjectId";

    $sql=" FROM subject WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iSubjectId LIKE '%".$searchString."%' OR
        vSubjectName LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iSubjectId ";
    
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
}else if($_POST['action']=="getSubjectDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM subject WHERE eStatus='y' AND iSubjectId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditDepartment"){
    $iDepartmentId=$_POST['iDepartmentId'];
    $vDepartment=$_POST['vDepartment'];

    $insArr=array();
    $insArr['vDepartment']=$vDepartment;
    $returnArr=array();
    if($iDepartmentId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("department",$insArr," WHERE iDepartmentId=".$iDepartmentId."");
        $returnArr['status']=200;
        $returnArr['message']="Department detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("department",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Department has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getDepartmentList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iDepartmentId as id,iDepartmentId,vDepartment";
    $singleField="SELECT iDepartmentId";

    $sql=" FROM department WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iDepartmentId LIKE '%".$searchString."%' OR
        vDepartment LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iDepartmentId ";
    
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
}else if($_POST['action']=="getDepartmentDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM department WHERE eStatus='y' AND iDepartmentId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}

?>