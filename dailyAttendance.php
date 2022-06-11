<?php
include ("includes/connect.php");

if($_POST['action'] == "AddAttendance"){
    $dAttendanceDate = $_POST['dAttendanceDate'];
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $aStudentList = $_POST['aStudentList'];

    if(!empty($aStudentList)){
        foreach((array)$aStudentList as $aAttendance){

        }
    }
}else if($_POST['action'] == "GetStudentList"){
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];

    $selStudent = $mfp->mf_query("SELECT * FROM users WHERE iRoleId = 5 AND eStatus = 'y' AND iClassId = '".$iClassId."' AND iSectionId = '".$iSectionId."' AND iSchoolId = '1'");
    if($mfp->mf_affected_rows()>0){
        while($rowStudent = $mfp->mf_fetch_array($selStudent)){
            
        }
    }
}
?>