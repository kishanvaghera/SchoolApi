<?php
include ("includes/connect.php");


if($_POST['action']=="GradeList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iGradeId as id,iGradeId,vGrade,iStartMark,iEndMark,iGradePoint";
    $singleField="SELECT iGradeId";

    $sql=" FROM grade WHERE eStatus='y'  AND iSchoolId='1'";

    if(!empty($searchString)){
        $sql.=" AND (iGradeId LIKE '%".$searchString."%' OR
        vGrade LIKE '%".$searchString."%' OR
        iStartMark LIKE '%".$searchString."%' OR
        iEndMark LIKE '%".$searchString."%' OR
        iGradePoint LIKE '%".$searchString."%'
        ) ";
    }

    $sql.=" GROUP BY iGradeId ";
    
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

}else if($_POST['action']=="getGradeDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM grade WHERE eStatus='y' AND iGradeId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditGrade"){
    $iGradeId=$_POST['iGradeId'];
    $vGrade=$_POST['vGrade'];
    $iStartMark=$_POST['iStartMark'];
    $iEndMark=$_POST['iEndMark'];
    $iGradePoint=$_POST['iGradePoint'];


    $insArr=array();
    $insArr['iSchoolId']=1;
    $insArr['vGrade']=$vGrade;
    $insArr['iStartMark']=$iStartMark;
    $insArr['iEndMark']=$iEndMark;
    $insArr['iGradePoint']=$iGradePoint;

    $returnArr=array();
    if($iGradeId>0){
        $mfp->mf_dbupdate("grade",$insArr," WHERE iGradeId=".$iGradeId."");
        $returnArr['status']=200;
        $returnArr['message']="Grade detail has been updated succuessfull.";
    }else{
        $mfp->mf_dbinsert("grade",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Grade has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}