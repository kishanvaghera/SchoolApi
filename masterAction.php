<?php
include ("includes/connect.php");

if($_POST['action']=="addEditSection"){
    $iSectionId=(int)$_POST['iSectionId'];
    $vSectionName=$_POST['vSectionName'];
    $iClassId=$_POST['iClassId'];

    $insArr=array();
    $insArr['vSectionName']=$vSectionName;
    $insArr['iClassId']=$iClassId;
    $returnArr=array();
    if($iSectionId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("section",$insArr," WHERE iSectionId=".$iSectionId."");
        $returnArr['status']=200;
        $returnArr['message']="section has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("section",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="section has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSectionList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT sec.iSectionId as id,sec.iSectionId,sec.vSectionName,sec.iClassId,class.vClassName";
    $singleField="SELECT sec.iSectionId";

    $sql=" FROM section as sec LEFT JOIN class as class ON class.iClassId = sec.iClassId WHERE sec.eStatus='y' AND sec.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iSectionId LIKE '%".$searchString."%' OR
        sec.vSectionName LIKE '%".$searchString."%' OR
        class.vClassName LIKE '%".$searchString."%' OR
        iClassId LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY sec.iSectionId ";
    
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
}else if($_POST['action']=="getSectionDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM section WHERE eStatus='y' AND iSectionId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditSubject"){
    $iSubjectId=$_POST['iSubjectId'];
    $vSubjectName=$_POST['vSubjectName'];

    $insArr=array();
    $insArr['vSubjectName']=$vSubjectName;
    $returnArr=array();
    if($iSubjectId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("subject",$insArr," WHERE iSubjectId=".$iSubjectId."");
        $returnArr['status']=200;
        $returnArr['message']="Subject detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("subject",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Subject has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSubjectList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iSubjectId as id,iSubjectId,vSubjectName";
    $singleField="SELECT iSubjectId";

    $sql=" FROM subject WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iSubjectId LIKE '%".$searchString."%' OR
        vSubjectName LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iSubjectId ";
    
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
}else if($_POST['action']=="getSubjectDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM subject WHERE eStatus='y' AND iSubjectId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditDepartment"){
    $iDepartmentId=$_POST['iDepartmentId'];
    $vDepartment=$_POST['vDepartment'];

    $insArr=array();
    $insArr['vDepartment']=$vDepartment;
    $returnArr=array();
    if($iDepartmentId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("department",$insArr," WHERE iDepartmentId=".$iDepartmentId."");
        $returnArr['status']=200;
        $returnArr['message']="Department detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("department",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Department has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getDepartmentList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iDepartmentId as id,iDepartmentId,vDepartment";
    $singleField="SELECT iDepartmentId";

    $sql=" FROM department WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iDepartmentId LIKE '%".$searchString."%' OR
        vDepartment LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iDepartmentId ";
    
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
}else if($_POST['action']=="getDepartmentDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM department WHERE eStatus='y' AND iDepartmentId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditDesignation"){
    $iDesignationId=$_POST['iDesignationId'];
    $vDesignation=$_POST['vDesignation'];

    $insArr=array();
    $insArr['vDesignation']=$vDesignation;
    $returnArr=array();
    if($iDesignationId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("designation",$insArr," WHERE iDesignationId=".$iDesignationId."");
        $returnArr['status']=200;
        $returnArr['message']="Designation detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("designation",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Designation has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getDesignationList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iDesignationId as id,iDesignationId,vDesignation";
    $singleField="SELECT iDesignationId";

    $sql=" FROM designation WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iDesignationId LIKE '%".$searchString."%' OR
        vDesignation LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iDesignationId ";
    
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
}else if($_POST['action']=="getDesignationDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM designation WHERE eStatus='y' AND iDesignationId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditHoliday"){
    $iHolidayId=$_POST['iHolidayId'];
    $vHolidayName=$_POST['vHolidayName'];
    $dFromDate=$_POST['dFromDate'];
    $dToDate=$_POST['dToDate'];

    $insArr=array();
    $insArr['vHolidayName']=$vHolidayName;
    $insArr['dFromDate']=$dFromDate;
    $insArr['dToDate']=$dToDate;
    $returnArr=array();
    if($iHolidayId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("holiday",$insArr," WHERE iHolidayId=".$iHolidayId."");
        $returnArr['status']=200;
        $returnArr['message']="Holiday detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("holiday",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Holiday detail has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getHolidayList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iHolidayId as id,iHolidayId,vHolidayName,dFromDate,dToDate";
    $singleField="SELECT iHolidayId";

    $sql=" FROM holiday WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iHolidayId LIKE '%".$searchString."%' OR
        vHolidayName LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iHolidayId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";


    $sqlQuery=$mfp->mf_query($selectedField.$sql);

    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $row['dFromDate']=$mfp->date2dispnew($row['dFromDate']);
            $row['dToDate']=$mfp->date2dispnew($row['dToDate']);
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
}else if($_POST['action']=="getHolidayDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM holiday WHERE eStatus='y' AND iHolidayId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditSyllabus"){
    $iSyllabusId=$_POST['iSyllabusId'];
    $vTitle=$_POST['vTitle'];
    $iClassId=$_POST['iClassId'];
    $iSectionId=$_POST['iSectionId'];
    $iSubjectId=$_POST['iSubjectId'];
    $vSyllabusFile=$_POST['vSyllabusFile'];

    $insArr=array();
    $insArr['vTitle']=$vTitle;
    $insArr['iClassId']=$iClassId;
    $insArr['iSectionId']=$iSectionId;
    $insArr['iSubjectId']=$iSubjectId;
    if($vSyllabusFile['File'] != ""){
        $insArr['vSyllabusFile']=$mfp->file_decode($vSyllabusFile['File'], 'upload/Syllabus/', $fileName = "");
    }
    $returnArr=array();
    if($iSyllabusId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("syllabus",$insArr," WHERE iSyllabusId=".$iSyllabusId."");
        $returnArr['status']=200;
        $returnArr['message']="Syllabus detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("syllabus",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Syllabus detail has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getSyllabusList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT syll.iSyllabusId as id,syll.iSyllabusId,syll.vTitle,syll.vSyllabusFile,cls.vClassName,sec.vSectionName,sub.vSubjectName";
    $singleField="SELECT syll.iSyllabusId";

    $sql=" FROM syllabus as syll
            LEFT JOIN class as cls ON cls.iClassId = syll.iClassId 
            LEFT JOIN section as sec ON sec.iSectionId = syll.iSectionId 
            LEFT JOIN subject as sub ON sub.iSubjectId = syll.iSubjectId 
         WHERE syll.eStatus='y' AND syll.iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (syll.iSyllabusId LIKE '%".$searchString."%' OR
        syll.vTitle LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY syll.iSyllabusId ";
    
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
}else if($_POST['action']=="getSyllabusDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM syllabus WHERE eStatus='y' AND iSyllabusId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditEvent"){
    $iEventId=$_POST['iEventId'];
    $vEventName=$_POST['vEventName'];
    $dFromDate=$_POST['dFromDate'];
    $dToDate=$_POST['dToDate'];

    $insArr=array();
    $insArr['vEventName']=$vEventName;
    $insArr['dFromDate']=$dFromDate;
    $insArr['dToDate']=$dToDate;
    $returnArr=array();
    if($iEventId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("events",$insArr," WHERE iEventId=".$iEventId."");
        $returnArr['status']=200;
        $returnArr['message']="Event detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("events",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Event detail has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getEventList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iEventId as id,iEventId,vEventName,dFromDate,dToDate";
    $singleField="SELECT iEventId";

    $sql=" FROM events WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iEventId LIKE '%".$searchString."%' OR
        vEventName LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iEventId ";
    
    $sqlSingle=$mfp->mf_query($singleField.$sql);
    $totalSingleRows=$mfp->mf_affected_rows();
    
    $sql.=" limit $page_index, $limit";


    $sqlQuery=$mfp->mf_query($selectedField.$sql);

    $dataArr=array();

    $totalRows=$mfp->mf_affected_rows();
    if($totalRows>0){
        while($row=$mfp->mf_fetch_array($sqlQuery)){
            $row['dFromDate']=$mfp->date2dispnew($row['dFromDate']);
            $row['dToDate']=$mfp->date2dispnew($row['dToDate']);
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
}else if($_POST['action']=="getEventDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM events WHERE eStatus='y' AND iEventId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditLeaveType"){
    $iLeaveTypeId=(int)$_POST['iLeaveTypeId'];
    $vLeaveType=$_POST['vLeaveType'];

    $insArr=array();
    $insArr['vLeaveType']=$vLeaveType;
    $returnArr=array();
    if($iLeaveTypeId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("leave_type",$insArr," WHERE iLeaveTypeId=".$iLeaveTypeId."");
        $returnArr['status']=200;
        $returnArr['message']="Leave Type has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("leave_type",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Leave Type has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getLeaveTypeList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iLeaveTypeId as id,iLeaveTypeId,vLeaveType";
    $singleField="SELECT iLeaveTypeId";

    $sql=" FROM leave_type WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iLeaveTypeId LIKE '%".$searchString."%' OR
        vLeaveType LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iLeaveTypeId ";
    
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
}else if($_POST['action']=="getLeaveTypeDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM leave_type WHERE eStatus='y' AND iLeaveTypeId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="addEditLeaveReason"){
    $iReasonId=(int)$_POST['iReasonId'];
    $vLeaveReason=$_POST['vLeaveReason'];

    $insArr=array();
    $insArr['vLeaveReason']=$vLeaveReason;
    $returnArr=array();
    if($iReasonId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("leave_reason",$insArr," WHERE iReasonId=".$iReasonId."");
        $returnArr['status']=200;
        $returnArr['message']="Leave Reason has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['iSchoolId']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("leave_reason",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="Leave Reason has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getLeaveReasonList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iReasonId as id,iReasonId,vLeaveReason";
    $singleField="SELECT iReasonId";

    $sql=" FROM leave_reason WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iReasonId LIKE '%".$searchString."%' OR
        vLeaveReason LIKE '%".$searchString."%') ";
    }

    $sql.=" GROUP BY iReasonId ";
    
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
}else if($_POST['action']=="getLeaveReasonDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM leave_reason WHERE eStatus='y' AND iReasonId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}

?>