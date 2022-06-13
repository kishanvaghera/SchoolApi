<?php
include ("includes/connect.php");

if($_POST['action']=="getStudentMarksList"){
    $iTermId=$_POST['iTermId'];
    $iClassId=$_POST['iClassId'];
    $iSectionId=$_POST['iSectionId'];
    $iSubjectId=$_POST['iSubjectId'];

    $returnArr=array();
    $sqlStudentList=$mfp->mf_query("SELECT adm.iAdmissionId,adm.vName,COALESCE(mrk.dMarks,'NA') as dMarks,COALESCE(CONCAT(grd.vGrade,'(',grd.iGradePoint,')'),'NA') as vGrade,COALESCE(mrk.vComment,'NA') as vComment,mrk.iMarkId,mrk.iSchoolId,mrk.iTermId,mrk.iClassId,mrk.iSectionId,mrk.iSubjectId,mrk.iStudentId,mrk.iGradeId
                FROM admission as adm
                    LEFT JOIN marks as mrk ON mrk.iTermId=".$iTermId." AND mrk.iClassId=".$iClassId." AND mrk.iSectionId=".$iSectionId." AND iSubjectId=".$iSubjectId." AND mrk.iStudentId=adm.iAdmissionId
                    LEFT JOIN grade as grd ON grd.iGradeId=mrk.iGradeId
                WHERE adm.iSchoolId=1 AND adm.iClassId=".$iClassId." AND adm.vSection='".$iSectionId."' AND adm.eStatus='y'");
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
    $dMarks=$_POST['dMarks'];
    $iGradeId=$_POST['iGradeId'];
    $vComment=$_POST['vComment'];


    $insArr=array();
    $insArr['iSchoolId']=$iSchoolId;
    $insArr['iTermId']=$iTermId;
    $insArr['iClassId']=$iClassId;
    $insArr['iSectionId']=$iSectionId;
    $insArr['iSubjectId']=$iSubjectId;
    $insArr['iStudentId']=$iStudentId;
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
}