<?php
include("includes/connect.php");

if($_POST['action'] == "addEditBook"){

    $iBookId = $_POST['iBookId'];
    $vBookName = $_POST['vBookName'];
    $vBookAuthor = $_POST['vBookAuthor'];
    $iNoOfCopy = $_POST['iNoOfCopy'];

    $insArr = array();
    $insArr['vBookName'] = $vBookName;
    $insArr['vBookAuthor'] = $vBookAuthor;
    $insArr['iNoOfCopy'] = $iNoOfCopy;
    $insArr['iAvailableCopy'] = $iNoOfCopy;

    $returnArr = array();
    if ($iBookId > 0) {
        $insArr['iLastBy'] = 1;
        $insArr['dLastDate'] = $mfp->curTimedate();
        $mfp->mf_dbupdate("library_books", $insArr, " WHERE iBookId = '" . $iBookId . "'");
        $returnArr['status'] = 200;
        $returnArr['message'] = "Book has been updated succuessfull.";
    } else {
        
        $insArr['iCreatedBy'] = 1;
        $insArr['iSchoolId'] = 1;
        $insArr['dCreatedDate'] = $mfp->curTimedate();
        $mfp->mf_dbinsert("library_books", $insArr);
        $returnArr['status'] = 200;
        $returnArr['message'] = "Book has been added succuessfull.";
    }
    echo json_encode($returnArr);
    exit();
} else if ($_POST['action'] == "getBookList") {
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT iBookId as id,iBookId,vBookName,vBookAuthor,iNoOfCopy,iAvailableCopy";
    $singleField = "SELECT iBookId";

    $sql = " FROM library_books
         WHERE eStatus='y' AND iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (
                    iBookId LIKE '%" . $searchString . "%' OR
                    vBookName LIKE '%" . $searchString . "%' OR
                    vBookAuthor LIKE '%" . $searchString . "%' OR
                    iAvailableCopy LIKE '%" . $searchString . "%' OR
                    iNoOfCopy LIKE '%" . $searchString . "%'
                ) ";
    }

    $sql .= " GROUP BY iBookId ";

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
} else if ($_POST['action'] == "getBookDetail") {
    $id = $_POST['id'];

    $sql = $mfp->mf_query("SELECT * FROM library_books WHERE eStatus='y' AND iBookId = '" . $id . "'");
    if ($mfp->mf_affected_rows() > 0) {
        $row = $mfp->mf_fetch_array($sql);
        $retArr = array("status" => 200, "data" => $row);
    } else {
        $retArr = array("status" => 412, "message", "No Data Found!");
    }
    echo json_encode($retArr);
    exit();
} else if($_POST['action'] == "IssueBook"){
    $dIssueDate = $_POST['dIssueDate'];
    $iClassId = $_POST['iClassId'];
    $iUserEmpId = $_POST['iUserEmpId'];
    $iBookId = $_POST['iBookId'];

    $aBookArr = $mfp->mf_getMultiValue("library_books",array("iAvailableCopy","iIssueCopy"),"iBookId",$iBookId);
    $iAvailableCopy = $aBookArr[0];
    $iIssueCopy = $aBookArr[1];

    if($iAvailableCopy == 0){
        $returnArr['status'] = 412;
        $returnArr['message'] = "Book not available.";
        echo json_encode($returnArr);
        exit;
    }

    $insArr = array();
    $insArr['dIssueDate'] = $dIssueDate;
    $insArr['iClassId'] = $iClassId;
    $insArr['iUserEmpId'] = $iUserEmpId;
    $insArr['iBookId'] = $iBookId;

    $insArr['iCreatedBy'] = 1;
    $insArr['iSchoolId'] = 1;
    $insArr['dCreatedDate'] = $mfp->curTimedate();
    if($mfp->mf_dbinsert("library_book_issue", $insArr)){
        $aUpdArr = array();
        $aUpdArr['iAvailableCopy'] = $iAvailableCopy - 1;
        $aUpdArr['iIssueCopy'] = $iIssueCopy + 1;
        $mfp->mf_dbupdate("library_books", $aUpdArr, " WHERE iBookId = '" . $iBookId . "'");


        $returnArr['status'] = 200;
        $returnArr['message'] = "Book has been added succuessfull.";
    }else{
        $returnArr['status'] = 412;
        $returnArr['message'] = "Error Occured!";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action'] == "getIssueBookList"){
    $page = $_POST['page'];
    $searchString = $_POST['searchString'];
    // $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page - 1) * $limit;

    $selectedField = "SELECT lbi.iIssueId as id,lbi.iIssueId,lbi.dIssueDate,lb.vBookName,cl.vClassName,u.vFullName";
    $singleField = "SELECT lbi.iIssueId";

    $sql = " FROM library_book_issue as lbi
            LEFT JOIN library_books as lb ON lb.iBookId = lbi.iBookId
            LEFT JOIN class as cl ON cl.iClassId = lbi.iClassId
            LEFT JOIN users as u ON u.iUserEmpId = lbi.iUserEmpId
         WHERE lbi.eStatus='y' AND lbi.iSchoolId=1 ";

    if (!empty($searchString)) {
        $sql .= " AND (
                    lbi.iIssueId LIKE '%" . $searchString . "%' OR
                    vBookName LIKE '%" . $searchString . "%' OR
                    vBookAuthor LIKE '%" . $searchString . "%' OR
                    iAvailableCopy LIKE '%" . $searchString . "%' OR
                    iNoOfCopy LIKE '%" . $searchString . "%'
                ) ";
    }

    $sql .= " GROUP BY iIssueId ";

    $sqlSingle = $mfp->mf_query($singleField . $sql);
    $totalSingleRows = $mfp->mf_affected_rows();

    $sql .= " limit $page_index, $limit";

    $sqlQuery = $mfp->mf_query($selectedField . $sql);

    $dataArr = array();

    $totalRows = $mfp->mf_affected_rows();
    if ($totalRows > 0) {
        while ($row = $mfp->mf_fetch_array($sqlQuery)) {
            $row['dIssueDate'] = $mfp->date2dispnew($row['dIssueDate']);
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
}
?>