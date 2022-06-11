<?php
include("includes/connect.php");

if ($_POST['action'] == "AddEditAdmin") {

    $iUserEmpId = (int)$_POST['iUserEmpId'];
    $vFirstName = $_POST['vFirstName'];
    $vMiddleName = $_POST['vMiddleName'];
    $vLastName = $_POST['vLastName'];
    $vEmail = $_POST['vEmail'];
    $vPhone = $_POST['vPhone'];
    $vPassword = $_POST['vPassword'];
    $iSchoolId = $_POST['iSchoolId'];
    $iDDGenderId = $_POST['iDDGenderId'];
    $iDDBloodGroupId = $_POST['iDDBloodGroupId'];
    $vAddress = $_POST['vAddress'];

    $vFullName = '';
    if($vFirstName!=""){
        $vFullName .= ucfirst($vFirstName);
    }
    if($vMiddleName!=""){
        $vFullName .= ' '.strtoupper(substr($vMiddleName, 0, 1)).'.';
    }
    if($vLastName!=""){
        $vFullName .= ' '.ucfirst($vLastName);
    }

    $insArr = array();
    $insArr['vFirstName'] = $vFirstName;
    $insArr['vMiddleName'] = $vMiddleName;
    $insArr['vLastName'] = $vLastName;
    $insArr['vFullName'] = $vFullName;
    $insArr['vEmail'] = $vEmail;
    $insArr['vMobileNo'] = $vPhone;
    $insArr['vUserName'] = $vFirstName;
    $insArr['vUserEmpPwd'] = sha1($vPassword);
    $insArr['iSchoolId'] = $iSchoolId;
    $insArr['iDDGenderId'] = $iDDGenderId;
    $insArr['iDDBloodGroupId'] = $iDDBloodGroupId;
    $insArr['vAddress1'] = $vAddress;

    $returnArr = array();
    if ($iUserEmpId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("users", $insArr, " WHERE iUserEmpId=" . $iUserEmpId . "");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Admin detail has been updated succuessfull.";
    } else {
        $insArr['iCreatedBy'] = 1;
        // $insArr['iSchoolId'] = 1;
        $insArr['iRoleId'] = 2;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("users", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Admin has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "Adminlist") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    $extraFilter = $_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT u.iUserEmpId as id,u.iUserEmpId,u.vFirstName,u.vMiddleName,u.vLastName,u.vEmail,u.vMobileNo,u.vAddress1,sch.vSchoolName";
    $singleField = "SELECT u.iUserEmpId";

    $sql = " FROM users as u 
                LEFT JOIN schools as sch ON sch.iSchoolId = u.iSchoolId
            WHERE u.eStatus='y' AND u.iRoleId = 2 AND u.iSchoolId='1'";

    if (!empty($searchString)) {
        $sql .= " AND (iUserEmpId LIKE '%" . $searchString . "%' OR
        u.vFirstName LIKE '%" . $searchString . "%' OR
        u.vMiddleName LIKE '%" . $searchString . "%' OR
        u.vLastName LIKE '%" . $searchString . "%' OR
        u.vEmail LIKE '%" . $searchString . "%' OR
        u.vMobileNo LIKE '%" . $searchString . "%'
        ) ";
    }

    // if(!empty($extraFilter)){
    //     if($extraFilter['vSection']!=""){
    //         $sql.=" AND vSection='".$extraFilter['vSection']."'";
    //     }
    //     if($extraFilter['iClassId']!=""){
    //         $sql.=" AND iClassId=".$extraFilter['iClassId']."";
    //     }
    // }

    $sql .= " GROUP BY iUserEmpId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";

    // echo $selectedField . $sql; exit;
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
} else if ($_POST['action'] == "getAdminDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM users WHERE eStatus='y' AND iUserEmpId =" . $id . "");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
} else if ($_POST['action'] == "deleteAdminList") {
    $totalRecord = $_POST['totalRecord'];

    if (!empty($totalRecord)) {
        foreach ($totalRecord as $value) {
            $updArr = array();
            $updArr['eStatus'] = "d";
            $mfp->mf_dbupdate("users", $updArr, " WHERE iUserEmpId=" . $value . "");
        }
    }
    $retArr = array("status" => 200, "message" => "Admin Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}
