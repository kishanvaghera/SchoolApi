<?php
include ("includes/connect.php");
include("CalenderUtility.php");
if($_POST['action'] == "GetEventCalender"){

    $curDate = $mfp->curDate();
    $timeZone = new DateTimeZone('Asia/Kolkata');
        
    $range_start = parseDateTime($_POST['start']);
    $range_end = parseDateTime($_POST['end']);
    $dStartDate = date("Y-m-d", strtotime(date($_POST['start'])));
    $dEndDate = date("Y-m-d", strtotime(date($_POST['end'])));
    $vMonthYear = $_POST['monthYear'];
    
    $dMonthDateRange = $mfp->getDatesFromRange($dStartDate, $dEndDate);
    // print_r($dMonthDateRange);
    $taskListArr = array();
    $output_arrays = array();
    $i = 0;
    $aUsedEvent = array();
    foreach($dMonthDateRange as $dDate){ $i++;
        $selEvents = $mfp->mf_query("SELECT * FROM events WHERE eStatus = 'y' AND iSchoolId = '1' AND ('".$dDate."' BETWEEN dFromDate AND 
        dToDate) ");
        if($mfp->mf_affected_rows() > 0){
            while($rowEvents = $mfp->mf_fetch_array($selEvents)){
                if(!in_array($rowEvents['iEventId'],$aUsedEvent)){
                    $taskArr = array();
                    $taskArr['id'] = $i;
                    $taskArr['allday'] = true;
                    $taskArr['title'] = $rowEvents['vEventName'];
                    $taskArr['start'] = $rowEvents['dFromDate'];
                    $taskArr['end'] = date('Y-m-d',strtotime("+1 days",strtotime($rowEvents['dToDate'])));
                    $taskArr['color'] = rndRGBColorCode();
                    $taskListArr[] = $taskArr;
    
                    $aUsedEvent[] = $rowEvents['iEventId'];
                }
            }
        }
    }
    
    foreach ($taskListArr as $array) {
        $event = new Event($array, $timeZone);
        if ($event->isWithinDayRange($range_start, $range_end)) {
             $output_arrays[] = $event->toArray();
        }
    }
    
    echo json_encode(array("status"=>200,"data"=>$output_arrays));
    exit;
}else if($_POST['action'] == "GetDateEvents"){
    $selectDate = $_POST['selectDate'];

    $rtnArr = array();
    $returnArr = array();
    $selEvents = $mfp->mf_query("SELECT * FROM events WHERE eStatus = 'y' AND iSchoolId = '1' AND ('".$selectDate."' BETWEEN dFromDate AND 
        dToDate) ");
    $aUsedEvent = array();
    if($mfp->mf_affected_rows() > 0){
        while($rowEvents = $mfp->mf_fetch_array($selEvents)){
            if(!in_array($rowEvents['iEventId'],$aUsedEvent)){
                $rowEvents['dFromDate'] = $mfp->date2dispnew($rowEvents['dFromDate']);
                $rowEvents['dToDate'] = $mfp->date2dispnew($rowEvents['dToDate']);
                $returnArr[] = $rowEvents;

                $aUsedEvent[] = $rowEvents['iEventId'];
            }
        }

        $rtnArr = array("status"=>200,"data"=>$returnArr);
    }else{
        $rtnArr = array("status"=>412,"message"=>"No data found!");
    }

    echo json_encode($rtnArr);
    exit;
}

?>