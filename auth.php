<?php
include ("includes/connect.php");

if($_POST['action'] == "login"){
    $vUserName = $_POST['vUserName'];
    $vUserEmpPwd = $_POST['vUserEmpPwd'];

    $rtnArr = array();
    $selUser = $mfp->mf_query("SELECT iUserEmpId,iSchoolId,iRoleId,vFullName,iDepartmentId,iDesignationId FROM users WHERE vUserName = '".$vUserName."' AND vUserEmpPwd = '".sha1($vUserEmpPwd)."' AND eStatus = 'y'");
    if($mfp->mf_affected_rows()>0){
        $rowUser = $mfp->mf_fetch_array($selUser);

        $rtnArr = array("status"=>200,"data"=>$rowUser);
    }else{
        $rtnArr = array("status"=>412,"message"=>"User or password invalid!");
    }

    echo json_encode($rtnArr);
    exit;
}
?>