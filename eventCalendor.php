<?php
include ("includes/connect.php");
include("CalenderUtility.php");

$curDate = $mfp->curDate();
$timeZone = new DateTimeZone('Asia/Kolkata');
    
$range_start = parseDateTime($_POST['start']);
$range_end = parseDateTime($_POST['end']);
$dStartDate = date("Y-m-d", strtotime(date($_POST['start'])));
$dEndDate = date("Y-m-d", strtotime(date($_POST['end'])));
    $vMonthYear = $_POST['monthYear'];

    $dMonthDateRange = $mfp->getDatesFromRange($dStartDate, $dEndDate);
    print_r($dMonthDateRange);

    $i = 0;
    foreach($dMonthDateRange as $dDate){
        $taskArr = array();
        $taskArr['id'] = $i;
        $taskArr['title'] = 'Event - '.$i;
        $taskArr['start'] = $dDate;
        $taskArr['end'] = $dDate;
        $taskArr['className'] = '';
        $taskArr['extendedProps'] = array('EventChkBox_' . $i);
        $taskListArr[] = $taskArr;
    }

?>