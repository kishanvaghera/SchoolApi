<?php
include ("includes/connect.php");

if($_POST['action']=="getStudentMarksList"){
    $iTermsExam=$_POST['iTermsExam'];
    $iClassId=$_POST['iClassId'];
    $vSection=$_POST['vSection'];
    $iSubjectId=$_POST['iSubjectId'];

    $returnArr=array();
    $sqlStudentList=$mfp->mf_query("SELECT adm.iAdmissionId,adm.vName,COALESCE(mrk.dMarks,'NA') as dMarks,COALESCE(grd.vGrade,'NA') as vGrade,COALESCE(mrk.vComment,'NA') as vComment,mrk.iMarkId,mrk.iSchoolId,mrk.iTermId,mrk.iClassId,mrk.iSectionId,mrk.iSubjectId,mrk.iStudentId,mrk.iGradeId
                FROM admission as adm
                    LEFT JOIN marks as mrk ON mrk.iTermId=".$iTermsExam." AND mrk.iClassId=".$iClassId." AND mrk.iSectionId=".$vSection." AND iSubjectId=".$iSubjectId." AND mrk.iStudentId=adm.iAdmissionId
                    LEFT JOIN grade as grd ON grd.iGradeId=mrk.iGradeId
                WHERE adm.iSchoolId=1 AND adm.iClassId=".$iClassId." AND adm.vSection='".$vSection."' AND adm.eStatus='y'");
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

    
}