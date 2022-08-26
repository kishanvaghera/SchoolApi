<?php
include("includes/connect.php");

if ($_POST['action'] == "addEditIdCard") {
    $iIdCardId = $_POST['iIdCardId'];
    $iUserEmpId = $_POST['iUserEmpId'];
    $iClassId = $_POST['iClassId'];
    $dDateofBirth = $_POST['dDateofBirth'];
    $vAddress = $_POST['vAddress'];
    $vEmail = $_POST['vEmail'];
    $dExpireDate = $_POST['dExpireDate'];
    $iIdCardNo = $_POST['iIdCardNo'];
    $vPhoneNo = $_POST['vPhoneNo'];
    $vPhotoFile = $_POST['vPhotoFile'];

    $insArr = array();
    $insArr['iUserEmpId'] = $iUserEmpId;
    $insArr['iClassId'] = $iClassId;
    $insArr['dDateofBirth'] = $dDateofBirth;
    $insArr['vAddress'] = $vAddress;
    $insArr['vEmail'] = $vEmail;
    $insArr['dExpireDate'] = $dExpireDate;
    $insArr['iIdCardNo'] = $iIdCardNo;
    $insArr['vPhoneNo'] = $vPhoneNo;
    if ($vPhotoFile['File'] != "") {
        $insArr['vPhotoFile'] = $mfp->file_decode($vPhotoFile['File'], 'upload/IdPhoto/', $fileName = "");
    }

    $returnArr = array();
    if ($iIdCardId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("id_card", $insArr, " WHERE iIdCardId=" . $iIdCardId . "");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Id Card detail has been updated succuessfull.";
    } else {
        $insArr['iCreatedBy'] = 1;
        $insArr['iSchoolId'] = 1;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("id_card", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Id Card detail has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "getIdCardList") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT ic.iIdCardId as id,ic.iIdCardId,u.vFullName,ic.vEmail,ic.iIdCardNo,cls.vClassName";
    $singleField = "SELECT ic.iIdCardId";

    $sql = " FROM id_card as ic
            LEFT JOIN class as cls ON cls.iClassId = ic.iClassId
            LEFT JOIN users as u ON u.iUserEmpId = ic.iUserEmpId
         WHERE ic.eStatus='y' AND ic.iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (
                    ic.iIdCardId LIKE '%" . $searchString . "%' OR
                    cls.vClassName LIKE '%" . $searchString . "%' OR
                    ic.vEmail LIKE '%" . $searchString . "%' OR
                    ic.iIdCardNo LIKE '%" . $searchString . "%' OR
                    u.vFullName LIKE '%" . $searchString . "%'
                ) ";
    }

    $sql .= " GROUP BY ic.iIdCardId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";

    $sqlQuery = $mfp->mf_query($selectedField . $sql);

    $dataArr = array();

    $totalRows = $mfp->mf_affected_rows();
    if ($totalRows > 0) {
        while ($row = $mfp->mf_fetch_array($sqlQuery)) {
            $dataArr[] = $row;
        }
    }

    $total_pages = ceil($totalSingleRows / $limit);

    if (!empty($dataArr)) {
        $retArr = array("status" => 200, "data" => $dataArr, "totalPage" => $total_pages);
    } else {
        $retArr = array("status" => 412, "message" => "No Data Found!");
    }

    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "getIDCardDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM id_card WHERE eStatus='y' AND iIdCardId =" . $id . "");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "addEditAssignment") {
    $iAssignmentId = $_POST['iAssignmentId'];
    $vTitle = $_POST['vTitle'];
    $vDetails = $_POST['vDetails'];
    $dIssueDate = $_POST['dIssueDate'];
    $dSubmissionDate = $_POST['dSubmissionDate'];
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $vAssignmentFile = $_POST['vAssignmentFile'];
    $iUserEmpId = $_POST['iUserEmpId'];

    $insArr = array();
    $insArr['vTitle'] = $vTitle;
    $insArr['vDetails'] = $vDetails;
    $insArr['dIssueDate'] = $mfp->date2saveISO($dIssueDate, 'Y-m-d');
    $insArr['dSubmissionDate'] = $mfp->date2saveISO($dSubmissionDate, 'Y-m-d');
    $insArr['iClassId'] = $iClassId;
    $insArr['iSectionId'] = $iSectionId;
    if ($vAssignmentFile['File'] != "") {
        $insArr['vAssignmentFile'] = $mfp->file_decode($vAssignmentFile['File'], 'upload/Assignment/', $fileName = "");
    }
    $insArr['iUserEmpId'] = $iUserEmpId;

    $returnArr = array();
    if ($iAssignmentId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("assignments", $insArr, " WHERE iAssignmentId=" . $iAssignmentId . "");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Assignment has been updated succuessfull.";
    } else {
        $insArr['iCreatedBy'] = 1;
        $insArr['iSchoolId'] = 1;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("assignments", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Assignment has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "getAssignmentList") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT ass.iAssignmentId as id,ass.iAssignmentId,u.vFullName,ass.vTitle,ass.vDetails,ass.dIssueDate,ass.dSubmissionDate,cls.vClassName,sec.vSectionName";
    $singleField = "SELECT ass.iAssignmentId";

    $sql = " FROM assignments as ass
            LEFT JOIN class as cls ON cls.iClassId = ass.iClassId
            LEFT JOIN section as sec ON sec.iSectionId = ass.iSectionId
            LEFT JOIN users as u ON u.iUserEmpId = ass.iUserEmpId
         WHERE ass.eStatus='y' AND ass.iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (
                    ass.iAssignmentId LIKE '%" . $searchString . "%' OR
                    cls.vClassName LIKE '%" . $searchString . "%' OR
                    ass.vTitle LIKE '%" . $searchString . "%' OR
                    sec.vSectionName LIKE '%" . $searchString . "%' OR
                    u.vFullName LIKE '%" . $searchString . "%'
                ) ";
    }

    $sql .= " GROUP BY ass.iAssignmentId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";

    $sqlQuery = $mfp->mf_query($selectedField . $sql);

    $dataArr = array();

    $totalRows = $mfp->mf_affected_rows();
    if ($totalRows > 0) {
        while ($row = $mfp->mf_fetch_array($sqlQuery)) {
            $row['dIssueDate'] = $mfp->date2dispnew($row['dIssueDate']);
            $row['dSubmissionDate'] = $mfp->date2dispnew($row['dSubmissionDate']);
            $dataArr[] = $row;
        }
    }

    $total_pages = ceil($totalSingleRows / $limit);

    if (!empty($dataArr)) {
        $retArr = array("status" => 200, "data" => $dataArr, "totalPage" => $total_pages);
    } else {
        $retArr = array("status" => 412, "message" => "No Data Found!");
    }

    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "getAssignmentDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM assignments WHERE eStatus='y' AND iAssignmentId =" . $id . "");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "addEditCircular") {
    $iCircularId = $_POST['iCircularId'];
    $vTitle = $_POST['vTitle'];
    $vDetails = $_POST['vDetails'];
    $dCircularDate = $_POST['dCircularDate'];
    $iClassId = $_POST['iClassId'];
    $iSectionId = $_POST['iSectionId'];
    $vCircularFile = $_POST['vCircularFile'];

    $insArr = array();
    $insArr['vTitle'] = $vTitle;
    $insArr['vDetails'] = $vDetails;
    $insArr['dCircularDate'] = $mfp->date2saveISO($dCircularDate, 'Y-m-d');
    $insArr['iClassId'] = $iClassId;
    $insArr['iSectionId'] = $iSectionId;
    if ($vCircularFile['File'] != "") {
        $insArr['vCircularFile'] = $mfp->file_decode($vCircularFile['File'], 'upload/Circular/', $fileName = "");
    }

    $returnArr = array();
    if ($iCircularId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("circular", $insArr, " WHERE iCircularId=" . $iCircularId . "");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Circular has been updated succuessfull.";
    } else {
        $insArr['iCreatedBy'] = 1;
        $insArr['iSchoolId'] = 1;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("circular", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Circular has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "getCircularList") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT cir.iCircularId as id,cir.iCircularId,cir.vTitle,cir.vDetails,cir.dCircularDate,cls.vClassName,sec.vSectionName";
    $singleField = "SELECT cir.iCircularId";

    $sql = " FROM circular as cir
            LEFT JOIN class as cls ON cls.iClassId = cir.iClassId
            LEFT JOIN section as sec ON sec.iSectionId = cir.iSectionId
         WHERE cir.eStatus='y' AND cir.iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (
                    cir.iCircularId LIKE '%" . $searchString . "%' OR
                    cls.vClassName LIKE '%" . $searchString . "%' OR
                    cir.vTitle LIKE '%" . $searchString . "%' OR
                    sec.vSectionName LIKE '%" . $searchString . "%'
                ) ";
    }

    $sql .= " GROUP BY cir.iCircularId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";

    $sqlQuery = $mfp->mf_query($selectedField . $sql);

    $dataArr = array();

    $totalRows = $mfp->mf_affected_rows();
    if ($totalRows > 0) {
        while ($row = $mfp->mf_fetch_array($sqlQuery)) {
            $row['dCircularDate'] = $mfp->date2dispnew($row['dCircularDate']);
            $dataArr[] = $row;
        }
    }

    $total_pages = ceil($totalSingleRows / $limit);

    if (!empty($dataArr)) {
        $retArr = array("status" => 200, "data" => $dataArr, "totalPage" => $total_pages);
    } else {
        $retArr = array("status" => 412, "message" => "No Data Found!");
    }

    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "getCircularDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM circular WHERE eStatus='y' AND iCircularId =" . $id . "");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "addEditLeaves") {
    $iLeaveId = (int)$_POST['iLeaveId'];
    $iUserEmpId = $_POST['iUserEmpId'];
    $iLeaveTypeId = $_POST['iLeaveTypeId'];
    $dFromDate = $_POST['dFromDate'];
    $dToDate = $_POST['dToDate'];
    $iReasonId = $_POST['iReasonId'];
    $vRemarks = $_POST['vRemarks'];

    $insArr = array();
    $insArr['iUserEmpId'] = $iUserEmpId;
    $insArr['iLeaveTypeId'] = $iLeaveTypeId;
    $insArr['dFromDate'] = $dFromDate;
    $insArr['dToDate'] = $dToDate;
    $insArr['iReasonId'] = $iReasonId;
    $insArr['vRemarks'] = $vRemarks;
    $returnArr = array();
    if ($iLeaveId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("leaves", $insArr, " WHERE iLeaveId=" . $iLeaveId . "");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Leave has been updated succuessfull.";
    } else {
        $insArr['iCreatedBy'] = 1;
        $insArr['iSchoolId'] = 1;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("leaves", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Leave has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "getLeavesList") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT lv.iLeaveId as id,lv.iLeaveId,lv.dFromDate,lv.dToDate,lv.vRemarks,lv.eLeaveStatus,CONCAT(ur.vRoleName,' | ',u.vFullName) as vFullName,lt.vLeaveType,lr.vLeaveReason";
    $singleField = "SELECT lv.iLeaveId";

    $sql = " FROM leaves as lv
                LEFT JOIN users as u ON u.iUserEmpId = lv.iUserEmpId
                LEFT JOIN user_role as ur ON ur.iRoleId = u.iRoleId
                LEFT JOIN leave_type as lt ON lt.iLeaveTypeId = lv.iLeaveTypeId    
                LEFT JOIN leave_reason as lr ON lr.iReasonId = lv.iReasonId    
            WHERE lv.eStatus='y' AND lv.iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (lv.iLeaveId LIKE '%" . $searchString . "%' OR
        ur.vRoleName LIKE '%" . $searchString . "%' OR
        lt.vFullName LIKE '%" . $searchString . "%' OR
        lt.vLeaveType LIKE '%" . $searchString . "%' OR
        lr.vLeaveReason LIKE '%" . $searchString . "%') ";
    }

    $sql .= " GROUP BY lv.iLeaveId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";


    $sqlQuery = $mfp->mf_query($selectedField . $sql);

    $dataArr = array();

    $totalRows = $mfp->mf_affected_rows();
    if ($totalRows > 0) {
        while ($row = $mfp->mf_fetch_array($sqlQuery)) {
            $row['dFromDate'] = $mfp->date2dispnew($row['dFromDate']);
            $row['dToDate'] = $mfp->date2dispnew($row['dToDate']);
            $dataArr[] = $row;
        }
    }

    $total_pages = ceil($totalSingleRows / $limit);

    if (!empty($dataArr)) {
        $retArr = array("status" => 200, "data" => $dataArr, "totalPage" => $total_pages);
    } else {
        $retArr = array("status" => 412, "message" => "No Data Found!");
    }

    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "getLeavesDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM leaves WHERE eStatus='y' AND iLeaveId =" . $id . "");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}
