    <?php
include ("includes/connect.php");

if($_POST['action']=="addEditParent"){

    $iParentId=(int)$_POST['iParentId'];
    $vName=$_POST['vName'];
    $vAddress=$_POST['vAddress'];
    $vEmail=$_POST['vEmail'];
    $eGender=$_POST['eGender'];
    $vPhone=$_POST['vPhone'];
    $vPassword=$_POST['vPassword'];
    $eBloodGrp=$_POST['eBloodGrp'];

    $insArr=array();
    $insArr['vName']=$vName;
    $insArr['vAddress']=$vAddress;
    $insArr['vEmail']=$vEmail;
    $insArr['eGender']=$eGender;
    $insArr['vPhone']=$vPhone;
    $insArr['vPassword']=$vPassword;
    $insArr['eBloodGrp']=$eBloodGrp;
    $insArr['iSchoolId']=1;

    $returnArr=array();
    if($iParentId>0){
        $insArr['iLastBy']=1;
        $insArr['dLastDate']=$mfp->curTimedate();
        $mfp->mf_dbupdate("parent",$insArr," WHERE iParentId=".$iParentId."");
        $returnArr['status']=200;
        $returnArr['message']="parent detail has been updated succuessfull.";
    }else{
        $insArr['iCreatedBy']=1;
        $insArr['dCreatedDate']=$mfp->curTimedate();
        $mfp->mf_dbinsert("parent",$insArr);
        $returnArr['status']=200;
        $returnArr['message']="parent has been added succuessfull.";
    }

    echo json_encode($returnArr);
    exit();
}else if($_POST['action']=="getParentsList"){
    $page=$_POST['page'];
    $searchString=$_POST['searchString'];
    $extraFilter=$_POST['extraFilter'];

    $limit = $_POST['limit'];
    $page_index = ($page-1) * $limit;

    $selectedField="SELECT iParentId as id,iParentId,vName,vEmail";
    $singleField="SELECT iParentId";

    $sql=" FROM parent WHERE eStatus='y' AND iSchoolId=1 ";

    if(!empty($searchString)){
        $sql.=" AND (iParentId LIKE '%".$searchString."%' OR
        vName LIKE '%".$searchString."%' OR
        vEmail LIKE '%".$searchString."%') ";
    }

    if(!empty($extraFilter)){
        if($extraFilter['vSection']!=""){
            $sql.=" AND vSection='".$extraFilter['vSection']."'";
        }
        if($extraFilter['iClassId']!=""){
            $sql.=" AND iClassId=".$extraFilter['iClassId']."";
        }
    }

    $sql.=" GROUP BY iParentId ";
    
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
}else if($_POST['action']=="getParentDetail"){
    $id=$_POST['id'];

    $sql=$mfp->mf_query("SELECT * FROM parent WHERE eStatus='y' AND iParentId =".$id."");
    if($mfp->mf_affected_rows()>0){
        $row=$mfp->mf_fetch_array($sql);
        $retArr=array("status"=>200,"data"=>$row);  
    }else{
        $retArr=array("status"=>412,"message","No Data Found!");
    }
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="deleteParentList"){
    $totalRecord=$_POST['totalRecord'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate("parent",$updArr," WHERE iParentId=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>"Parent Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}

