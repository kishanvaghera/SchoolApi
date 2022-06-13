<?php
include ("includes/connect.php");


if($_POST['action']=="ExamList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iExamId as id,iExamId,vExam,dStartDate,dEndDate";
    $singleField="SELECT iExamId";

    $sql=" FROM exams WHERE eStatus='y'  AND iSchoolId='1'";

    if(!empty($searchString)){
        $sql.=" AND (iExamId LIKE '%".$searchString."%' OR
        vExam LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iExamId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";

    $sqlQuery=$mfp->mf_query($selectedField.$sql);

    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $row['dStartDate']=$mfp->date2dispnew($row['dStartDate']);
            $row['dEndDate']=$mfp->date2dispnew($row['dEndDate']);
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

}else if($_POST['action']=="getExamDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM exams WHERE eStatus='y' AND iExamId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditExam"){
    $iExamId=$_POST['iExamId'];
    $vExam=$_POST['vExam'];
    $dStartDate=$_POST['dStartDate'];
    $dEndDate=$_POST['dEndDate'];


    $insArr=array();
    $insArr['iSchoolId']=1;
    $insArr['vExam']=$vExam;
    $insArr['dStartDate']=$dStartDate;
    $insArr['dEndDate']=$dEndDate;

    $returnArr=array();
    if($iExamId>0){
        $mfp->mf_dbupdate("exams",$insArr," WHERE iExamId=".$iExamId."");
        $returnArr['status']=200;
        $returnArr['message']="Exam detail has been updated succuessfull.";
    }else{
        $mfp->mf_dbinsert("exams",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Exam has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}
