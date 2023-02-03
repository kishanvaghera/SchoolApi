<?php
include ("includes/connect.php");

if($_POST['action']=="getStudentMarksList"){
    $iTermId=$_POST['iTermId'];
    $iClassId=$_POST['iClassId'];
    $iSectionId=$_POST['iSectionId'];
    $iSubjectId=$_POST['iSubjectId'];

    $returnArr=array();
    $sqlStudentList=$mfp->mf_query("SELECT u.iUserEmpId,u.vFullName,COALESCE(mrk.dMarks,'NA') as dMarks,COALESCE(CONCAT(grd.vGrade,'(',grd.iGradePoint,')'),'NA') as vGrade,COALESCE(mrk.vComment,'NA') as vComment,mrk.iMarkId,mrk.iSchoolId,mrk.iTermId,mrk.iClassId,mrk.iSectionId,mrk.iSubjectId,mrk.iStudentId,mrk.iGradeId
                FROM users as u
                    LEFT JOIN marks as mrk ON mrk.iTermId=".$iTermId." AND mrk.iClassId=".$iClassId." AND mrk.iSectionId=".$iSectionId." AND iSubjectId=".$iSubjectId." AND mrk.iStudentId = u.iUserEmpId
                    LEFT JOIN grade as grd ON grd.iGradeId=mrk.iGradeId
                WHERE u.iSchoolId=1 AND u.iClassId=".$iClassId." AND u.iSectionId='".$iSectionId."' AND u.eStatus='y'");
    if($mfp->mf_affected_rows()>0){
        $returnArr['status']=200;
        while($row=$mfp->mf_fetch_array($sqlStudentList)){
            $returnArr['data'][]=$row;
        }
    }else{
        $returnArr['status']=412;
        $returnArr['message']="No Data Found!";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSingleMarksSave"){
    $iMarkId=$_POST['iMarkId'];
    $iSchoolId=1;
    $iTermId=$_POST['iTermId'];
    $iClassId=$_POST['iClassId'];
    $iSectionId=$_POST['iSectionId'];
    $iSubjectId=$_POST['iSubjectId'];
    $iStudentId=$_POST['iAdmissionId'];
    $iUserEmpId=$_POST['iUserEmpId'];
    $dMarks=$_POST['dMarks'];
    $iGradeId=$_POST['iGradeId'];
    $vComment=$_POST['vComment'];


    $insArr=array();
    $insArr['iSchoolId']=$iSchoolId;
    $insArr['iTermId']=$iTermId;
    $insArr['iClassId']=$iClassId;
    $insArr['iSectionId']=$iSectionId;
    $insArr['iSubjectId']=$iSubjectId;
    $insArr['iStudentId']=$iUserEmpId;
    $insArr['dMarks']=$dMarks;
    $insArr['iGradeId']=$iGradeId;
    $insArr['vComment']=$vComment;
    // $insArr['iCreatedBy']="";
    // $insArr['dCreatedDate']="";

    if($iMarkId>0){
        $mfp->mf_dbupdate("marks",$insArr," WHERE iMarkId=".$iMarkId."");
        $returnArr['status']=200;
        $returnArr['message']="Student Marks detail has been updated succuessfull.";
    }else{
        $mfp->mf_dbinsert("marks",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Student Marks has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getGradePoint"){
    $dMarks=$_POST['dMarks'];
    $iSchoolId=1;

    $returnArr=array();
    $sql=$mfp->mf_query("SELECT iGradeId,CONCAT(vGrade,'(',iGradePoint,')') as vGrade FROM grade WHERE iSchoolId=".$iSchoolId." AND iStartMark<=$dMarks AND iEndMark>=$dMarks AND eStatus='y'");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $returnArr['status']=200;
        $returnArr['data']=$row;
    }else{
        $returnArr['status']=412;
        $returnArr['message']="No Data Found";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action'] == "getReportCardList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT src.iReportCardId as id,src.iReportCardId,sec.vSectionName,src.eReportStatus,src.iClassId,class.vClassName,ex.vExam,u.vFullName";
    $singleField="SELECT src.iReportCardId";

    $sql=" FROM student_report_card as src
                LEFT JOIN class as class ON class.iClassId = src.iClassId
                LEFT JOIN section as sec ON sec.iSectionId = src.iSectionId
                LEFT JOIN exams as ex ON ex.iExamId = src.iExamId
                LEFT JOIN users as u ON u.iUserEmpId = src.iUserEmpId
            WHERE src.eStatus='y' AND src.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iReportCardId LIKE '%".$searchString."%' OR
        src.vSectionName LIKE '%".$searchString."%' OR
        class.vClassName LIKE '%".$searchString."%' OR
        sec.vSectionName LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY src.iReportCardId ";
    
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
}else if($_POST['action'] == "addEditReportCard"){

}