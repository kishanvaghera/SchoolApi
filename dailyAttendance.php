<?php
include ("includes/connect.php");

if($_POST['action'] == "AddAttendance"){
    $dAttendanceDate = $_POST['dAttendanceDate'];
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $aStudentList = $_POST['aStudentList'];

    $action = 0;
    $rtnArr = array();
    if(!empty($aStudentList)){
        foreach((array)$aStudentList as $aAttendance){
            $aInsArr = array();
            $aInsArr['iUserEmpId'] = $aAttendance['iUserEmpId'];
            $aInsArr['dAttendanceDate'] = $dAttendanceDate;
            $aInsArr['eAttendStatus'] = $aAttendance['eAttendStatus'];
            $aInsArr['iCreatedBy'] = 1;
            $aInsArr['dCreatedDate'] = $mfp->curTimedate();
            if($mfp->mf_dbinsert("attendance",$aInsArr)){
                $action++;
            }
        }
    }

    if($action > 0){
        $rtnArr = array("status"=>200,"message"=>"Attendance insert successfully.");
    }else{
        $rtnArr = array("status"=>412,"message"=>"Error Occured!");
    }

    echo json_encode($rtnArr);
    exit;
}else if($_POST['action'] == "GetStudentList"){
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];

    $rtnArr = array();
    $returnArr = array();
    $selStudent = $mfp->mf_query("SELECT iUserEmpId,vFullName FROM users WHERE iRoleId = 5 AND eStatus = 'y' AND iClassId = '".$iClassId."' AND iSectionId = '".$iSectionId."' AND iSchoolId = '1'");
    if($mfp->mf_affected_rows()>0){
        while($rowStudent = $mfp->mf_fetch_array($selStudent)){
            $returnArr[] = $rowStudent;
        }
        $rtnArr = array("status"=>200,"data"=>$returnArr);
    }else{
        $rtnArr = array("status"=>412,"data"=>"No Data Found!");
    }

    echo json_encode($rtnArr);
    exit;
}else if($_POST['action'] == "GetDailyAttendace"){
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $iMonth = $_POST['iMonth'];
    $iYear = $_POST['iYear'];

    $days = cal_days_in_month(CAL_GREGORIAN,$iMonth,$iYear);

    $dStartDate = $iYear.'-'.$iMonth.'-01';
    $dEndDate = $iYear.'-'.$iMonth.'-'.$days;

    $aMonthDateRange = $mfp->getDatesFromRange($dStartDate,$dEndDate);

    $returnArr = array();
    $selUser = $mfp->mf_query("SELECT iUserEmpId,vFullName FROM users WHERE eStatus = 'y' AND iRoleId = 5 AND iClassId = '".$iClassId."' AND iSectionId = '".$iSectionId."' AND iClassId > 0 AND iSectionId > 0");
    if($mfp->mf_affected_rows()>0){
        while($rowUser = $mfp->mf_fetch_array($selUser)){
            $aAttendanceData = array();
            $iUserEmpId = $rowUser['iUserEmpId'];
            $aAttendanceData['vFullName'] = $rowUser['vFullName'];

            $aAttendArr = array();
            foreach($aMonthDateRange as $dDate){
                if(strtotime($dDate) <= strtotime($mfp->curDate())){
                    $selAttend = $mfp->mf_query("SELECT iAttendaceId,dAttendanceDate,eAttendStatus FROM attendance WHERE iUserEmpId = '".$iUserEmpId."' AND dAttendanceDate = '".$dDate."' AND eStatus = 'y'");
                    if($mfp->mf_affected_rows()>0){
                        $rowAttend = $mfp->mf_fetch_array($selAttend);

                        $aAttendArr[$dDate] = array("iAttendaceId"=>$rowAttend['iAttendaceId'],"eAttendStatus"=>$rowAttend['eAttendStatus']);
                    }else{
                        $aAttendArr[$dDate] = array("iAttendaceId"=>0,"eAttendStatus"=>'A');
                    }
                }
            }
            $aAttendanceData['attendance'] = $aAttendArr;
            $returnArr[] = $aAttendanceData;
        }

        $rtnArr = array("status"=>200,"data"=>$returnArr);
    }else{
        $rtnArr = array("status"=>412,"message"=>"No record found!");
    }

    echo json_encode($rtnArr);
    exit;
}
?>