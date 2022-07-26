<?php
include ("includes/connect.php");
include("CalenderUtility.php");

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
}else if($_POST['action'] == "GetCalenderAttendance"){
    $curDate = $mfp->curDate();
    $timeZone = new DateTimeZone('Asia/Kolkata');
    
//     $dStartDate = date("Y-m-d", strtotime(date($_POST['start'])));
//     $dEndDate = date("Y-m-d", strtotime(date($_POST['end'])));
//     $vMonthYear = $_POST['monthYear'];

    $timeZone = new DateTimeZone('Asia/Kolkata');
    $range_start = parseDateTime('2022-07-01');
    $range_end = parseDateTime('2022-07-31');

    $i = 0;
    $taskListArr = array();
    $taskArr = array();
    $class = "green2Box";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P+</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-01';
    $taskArr['end'] = '2022-07-01';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "yellowBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">MSP</span>
                   <div class="hour-name-main">
                        <span class="hour">3 Hrs<i class="feather w-18 text-yellow mgl-5"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></i></span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-02';
    $taskArr['end'] = '2022-07-02';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "pinkBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">HD</span>
                   <div class="hour-name-main">
                        <span class="hour">3 Hrs<i class="feather w-18 text-light mgl-5"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path></svg></i></span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-07';
    $taskArr['end'] = '2022-07-07';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-04';
    $taskArr['end'] = '2022-07-04';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-05';
    $taskArr['end'] = '2022-07-05';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "skyblueBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main two-event">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">W</span>
              </div>
              <div class="punch-regularization-list status pinkBox">
                   <span class="attendance-status-top">HD</span>
                   <div class="hour-name-main">
                        <span class="hour">3 Hrs</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-06';
    $taskArr['end'] = '2022-07-06';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-07';
    $taskArr['end'] = '2022-07-07';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-08';
    $taskArr['end'] = '2022-07-08';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-09';
    $taskArr['end'] = '2022-07-09';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-10';
    $taskArr['end'] = '2022-07-10';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-11';
    $taskArr['end'] = '2022-07-11';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-12';
    $taskArr['end'] = '2022-07-12';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "skyblueBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main two-event">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">W</span>
              </div>
              <div class="punch-regularization-list status yellowBox">
                   <span class="attendance-status-top">MSP</span>
                   <div class="hour-name-main">
                        <span class="hour">08 Hrs 54 Min<i class="feather w-18 text-yellow mgl-5"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg></i></span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-13';
    $taskArr['end'] = '2022-07-13';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-14';
    $taskArr['end'] = '2022-07-14';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-15';
    $taskArr['end'] = '2022-07-15';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-16';
    $taskArr['end'] = '2022-07-16';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-17';
    $taskArr['end'] = '2022-07-17';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "blueBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main two-event">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">H<span class="holiday-name text-blue">Holi Long festival Name Long festival Name</span></span>
              </div>
              <div class="punch-regularization-list status green2Box">
                   <span class="attendance-status-top">P+</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-18';
    $taskArr['end'] = '2022-07-18';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-19';
    $taskArr['end'] = '2022-07-19';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "skyblueBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">W</span>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-20';
    $taskArr['end'] = '2022-07-20';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "redBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">Absent</span>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-21';
    $taskArr['end'] = '2022-07-21';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-22';
    $taskArr['end'] = '2022-07-22';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-23';
    $taskArr['end'] = '2022-07-23';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-24';
    $taskArr['end'] = '2022-07-24';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "orangeBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">L</span>
                   <div class="hour-name-main">
                        <span class="hour">Earn Leave</span>
                        <span class="name">Lorem ipsum dolor sit amet, consectetur...</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-25';
    $taskArr['end'] = '2022-07-25';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "orangeBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main two-event">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">L</span>
              </div>
              <div class="punch-regularization-list status pinkBox">
                   <span class="attendance-status-top">HD</span>
                   <div class="hour-name-main">
                        <span class="hour">08 Hrs 54 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-26';
    $taskArr['end'] = '2022-07-26';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "skyblueBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">W</span>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-27';
    $taskArr['end'] = '2022-07-27';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-28';
    $taskArr['end'] = '2022-07-28';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-29';
    $taskArr['end'] = '2022-07-29';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "greenBox";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-30';
    $taskArr['end'] = '2022-07-30';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $taskArr = array();
    $class = "green2Box";
    $vTitle = '<div class="icheck-primary checkbox-custom mg-0">
              <input class="list-child" id="EventChkBox_' . $i . '"  name="EventChkBox_' . $i . '" type="checkbox">
              <label class="label-text" for="EventChkBox_' . $i . '">&nbsp;</label>
         </div>
         <div class="punch-regularization-list-main">
              <div class="punch-regularization-list status">
                   <span class="attendance-status-top">P+</span>
                   <div class="hour-name-main">
                        <span class="hour">8 Hrs 48 Min</span>
                        <span class="name">Total Working Hours</span>
                   </div>
              </div>
         </div>';
    $taskArr['id'] = $i;
    $taskArr['title'] = $vTitle;
    $taskArr['start'] = '2022-07-31';
    $taskArr['end'] = '2022-07-31';
    $taskArr['className'] = $class;
    $taskArr['extendedProps'] = array('EventChkBox_' . $i);
    $taskListArr[] = $taskArr;
    $i++;

    $output_arrays = array();
    foreach ($taskListArr as $array) {
         $event = new Event($array, $timeZone);
         if ($event->isWithinDayRange($range_start, $range_end)) {
              $output_arrays[] = $event->toArray();
         }
    }

    //echo json_encode(array('event'=>$output_arrays));
    $rtnArr = array("status" => 200, "data" => $output_arrays);
    echo json_encode($rtnArr);
    exit;
}
?>