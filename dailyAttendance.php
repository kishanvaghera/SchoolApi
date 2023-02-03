<?php
include("includes/connect.php");
include("CalenderUtility.php");
error_reporting(0);
if ($_POST['action'] == "AddAttendance") {
     $dAttendanceDate = $_POST['dAttendanceDate'];
     $iClassId = $_POST['iClassId'];
     $iSectionId = $_POST['iSectionId'];
     $aStudentList = $_POST['aStudentList'];

     $action = 0;
     $rtnArr = array();
     if (!empty($aStudentList)) {
          foreach ((array)$aStudentList as $aAttendance) {
               $aInsArr = array();
               $aInsArr['iUserEmpId'] = $aAttendance['iUserEmpId'];
               $aInsArr['dAttendanceDate'] = $dAttendanceDate;
               $aInsArr['eAttendStatus'] = $aAttendance['eAttendStatus'];
               $aInsArr['iCreatedBy'] = 1;
               $aInsArr['dCreatedDate'] = $mfp->curTimedate();
               if ($mfp->mf_dbinsert("attendance", $aInsArr)) {
                    $action++;
               }
          }
     }

     if ($action > 0) {
          $rtnArr = array("status" => 200, "message" => "Attendance insert successfully.");
     } else {
          $rtnArr = array("status" => 412, "message" => "Error Occured!");
     }

     echo json_encode($rtnArr);
     exit;
} else if ($_POST['action'] == "GetStudentList") {
     $iClassId = $_POST['iClassId'];
     $iSectionId = $_POST['iSectionId'];

     $rtnArr = array();
     $returnArr = array();
     $selStudent = $mfp->mf_query("SELECT iUserEmpId,vFullName FROM users WHERE iRoleId = 5 AND eStatus = 'y' AND iClassId = '" . $iClassId . "' AND iSectionId = '" . $iSectionId . "' AND iSchoolId = '1'");
     
     if ($mfp->mf_affected_rows() > 0) {
          while ($rowStudent = $mfp->mf_fetch_array($selStudent)) {
               $returnArr[] = $rowStudent;
          }
          $rtnArr = array("status" => 200, "data" => $returnArr);
     } else {
          $rtnArr = array("status" => 412, "data" => "No Data Found!");
     }

     echo json_encode($rtnArr);
     exit;
} else if ($_POST['action'] == "GetDailyAttendace") {
     $iClassId = $_POST['iClassId'];
     $iSectionId = $_POST['iSectionId'];
     $iMonth = $_POST['iMonth'];
     $iYear = $_POST['iYear'];

     $days = cal_days_in_month(CAL_GREGORIAN, $iMonth, $iYear);

     $dStartDate = $iYear . '-' . $iMonth . '-01';
     $dEndDate = $iYear . '-' . $iMonth . '-' . $days;

     $aMonthDateRange = $mfp->getDatesFromRange($dStartDate, $dEndDate);

     $returnArr = array();
     $selUser = $mfp->mf_query("SELECT iUserEmpId,vFullName FROM users WHERE eStatus = 'y' AND iRoleId = 5 AND iClassId = '" . $iClassId . "' AND iSectionId = '" . $iSectionId . "' AND iClassId > 0 AND iSectionId > 0");
     if ($mfp->mf_affected_rows() > 0) {
          while ($rowUser = $mfp->mf_fetch_array($selUser)) {
               $aAttendanceData = array();
               $iUserEmpId = $rowUser['iUserEmpId'];
               $aAttendanceData['vFullName'] = $rowUser['vFullName'];

               $aAttendArr = array();
               foreach ($aMonthDateRange as $dDate) {
                    if (strtotime($dDate) <= strtotime($mfp->curDate())) {
                         $selAttend = $mfp->mf_query("SELECT iAttendaceId,dAttendanceDate,eAttendStatus FROM attendance WHERE iUserEmpId = '" . $iUserEmpId . "' AND dAttendanceDate = '" . $dDate . "' AND eStatus = 'y'");
                         if ($mfp->mf_affected_rows() > 0) {
                              $rowAttend = $mfp->mf_fetch_array($selAttend);

                              $aAttendArr[$dDate] = array("iAttendaceId" => $rowAttend['iAttendaceId'], "eAttendStatus" => $rowAttend['eAttendStatus']);
                         } else {
                              $aAttendArr[$dDate] = array("iAttendaceId" => 0, "eAttendStatus" => 'A');
                         }
                    }
               }
               $aAttendanceData['attendance'] = $aAttendArr;
               $returnArr[] = $aAttendanceData;
          }

          $rtnArr = array("status" => 200, "data" => $returnArr);
     } else {
          $rtnArr = array("status" => 412, "message" => "No record found!");
     }

     echo json_encode($rtnArr);
     exit;
} else if ($_POST['action'] == "GetCalenderAttendance") {
     // echo "in";
     // print_r($_POST);
     
     $curDate = $mfp->curDate();
     $timeZone = new DateTimeZone('Asia/Kolkata');
     $range_start = parseDateTime($_POST['start']);
     $range_end = parseDateTime($_POST['end']);
     $dStartDate = date("Y-m-d", strtotime(date($_POST['start'])));
     $dEndDate = date("Y-m-d", strtotime(date($_POST['end'])));
     $monthYear = $_POST['monthYear'];

     $iMonth = date('m', strtotime($vMonthYear));
     $iYear = date('Y', strtotime($vMonthYear));
     $iUserEmpId = (int)$_POST['iUserEmpId'];
     // print_r($range_start);
     // print_r($range_end);
     // echo '<br/>'.$range_start;
     // echo '<br/>'.$range_end;

     // echo '<br/>'.$dStartDate;
     // echo '<br/>'.$dEndDate;
     // exit;
     $dMonthDateRange = $mfp->getDatesFromRange($dStartDate, $dEndDate);
     // print_r($dMonthDateRange);
     // exit;

     $i = 0;
     $taskListArr = array();
     $output_arrays = array();
     if($iUserEmpId > 0){
          $aUserDataArr = $mfp->getUserAllData($iUserEmpId,$dMonthDateRange);
          $aLeaveArr = $aUserDataArr['aLeaveArr'];
          $aLeaveDateArr = $aUserDataArr['aLeaveDateArr'];
          $aHolidayArr = $aUserDataArr['aHolidayArr'];
		$aHolidayDateArr = $aUserDataArr['aHolidayDateArr'];

          foreach($dMonthDateRange as $dDate){ 
               if(strtotime($dDate) <= strtotime($curDate)){
                    $i++;
                    $taskArr = array();
                    $selAttend = $mfp->mf_query("SELECT iAttendaceId,dAttendanceDate,eAttendStatus FROM attendance WHERE iUserEmpId = '" . $iUserEmpId . "' AND dAttendanceDate = '" . $dDate . "' AND eStatus = 'y'");
                    if ($mfp->mf_affected_rows() > 0) {
                         $rowAttend = $mfp->mf_fetch_array($selAttend);
                         $taskArr['title'] = 'P';
                    } else if(in_array($dDate,(array)array_keys($aLeaveDateArr))){
                         $taskArr['title'] = $aLeaveDateArr[$dDate]['vLeaveType'];
                    }else if(in_array($dDate,(array)array_keys($aHolidayDateArr))){
                         $taskArr['title'] = $aHolidayDateArr[$dDate]['vHolidayName'];
                    }else{
                         $taskArr['title'] = 'A';
                    }
                    $taskArr['start'] = $dDate;
                    $taskArr['end'] = $dDate;
          
                    $taskListArr[] = $taskArr;
               }
          }
     }
     // print_r($taskListArr);
     // exit;
     foreach ($taskListArr as $array) {
          // print_r($array);
          $event = new Event($array, $timeZone);
          // echo $event;
          // print_r($event);
          if ($event->isWithinDayRange($range_start, $range_end)) {
               $output_arrays[] = $event->toArray();
          }
     }
     // print_r($output_arrays);
     // exit;

     //echo json_encode(array('event'=>$output_arrays));
     $rtnArr = array("status" => 200, "data" => $output_arrays);
     echo json_encode($rtnArr);
     exit;
}
