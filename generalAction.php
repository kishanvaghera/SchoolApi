<?php
include ("includes/connect.php");

if ($_POST['action']=="deleteDetailsList"){
    $totalRecord=$_POST['totalRecord'];
    $tblName=$_POST['tblName'];
    $tblId=$_POST['tblId'];
    $rtnMsg=$_POST['rtnMsg'];

    if(!empty($totalRecord)){
        foreach($totalRecord as $value){
            $updArr=array();
            $updArr['eStatus']="d";
            $mfp->mf_dbupdate($tblName,$updArr," WHERE ".$tblId."=".$value."");
        }
    }

    $retArr=array("status"=>200,"message"=>$rtnMsg." Detail has been deleted successfully.");
    echo json_encode($retArr);
    exit();
}else if($_POST['action']=="getDropDownListApi"){
    $iSchoolId="1";
    $key=$_POST['key'];

    $dataArr=array();

    if(in_array("ClassList", $key)){
        $sqlClass=$mfp->mf_query("SELECT iClassId as value,vClassName as label FROM class WHERE eStatus='y' AND iSchoolId=".$iSchoolId."");
        $subClassList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlClass)){
                $subClassList[]=$row;
            }
        }
        $dataArr['ClassList']=$subClassList;
    }

    if(in_array("SectionList", $key)){
        $sqlSection=$mfp->mf_query("SELECT iSectionId as value,vSectionName as label FROM section WHERE eStatus='y' AND iSchoolId=".$iSchoolId."");
        $subSectionList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlSection)){
                $subSectionList[]=$row;
            }
        }
        $dataArr['SectionList']=$subSectionList;
    }


    if(in_array("TermExamList", $key)){
        $sqlTermsList=$mfp->mf_query("SELECT iValueId as value,vName as label FROM dd_value WHERE iKeyId=1");
        $subTermsList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlTermsList)){
                $subTermsList[]=$row;
            }
        }
        $dataArr['TermExamList']=$subTermsList;
    }

    if(in_array("SubjectList", $key)){
        $sqlSubject=$mfp->mf_query("SELECT iSubjectId as value,vSubjectName as label FROM subject WHERE eStatus='y' AND iSchoolId=".$iSchoolId."");
        $subSubjectList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlSubject)){
                $subSubjectList[]=$row;
            }
        }
        $dataArr['SubjectList']=$subSubjectList;
    }

    if(in_array("SchoolList", $key)){
        $sqlSchool=$mfp->mf_query("SELECT iSchoolId as value,vSchoolName as label FROM schools WHERE eStatus='y'");
        $subSchoolList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlSchool)){
                $subSchoolList[]=$row;
            }
        }
        $dataArr['SchoolList']=$subSchoolList;
    }
    
    if(in_array("GenderList", $key)){
        $sqlGender=$mfp->mf_query("SELECT iValueId as value,vName as label FROM dd_value WHERE iKeyId=2");
        $GenderList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlGender)){
                $GenderList[]=$row;
            }
        }
        $dataArr['GenderList']=$GenderList;
    }

    if(in_array("BloodGroupList", $key)){
        $sqlBloodGroup=$mfp->mf_query("SELECT iValueId as value,vName as label FROM dd_value WHERE iKeyId=3");
        $BloodGroupList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlBloodGroup)){
                $BloodGroupList[]=$row;
            }
        }
        $dataArr['BloodGroupList']=$BloodGroupList;
    }
    
    if(in_array("DepartmentList", $key)){
        $sqlDept=$mfp->mf_query("SELECT iDepartmentId as value,vDepartment as label FROM department WHERE eStatus='y' AND iSchoolId=".$iSchoolId."");
        $DepartmentList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlDept)){
                $DepartmentList[]=$row;
            }
        }
        $dataArr['DepartmentList']=$DepartmentList;
    }
    
    if(in_array("DesignationList", $key)){
        $sqlDept=$mfp->mf_query("SELECT iDesignationId as value,vDesignation as label FROM designation WHERE eStatus='y' AND iSchoolId=".$iSchoolId."");
        $DesignationList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlDept)){
                $DesignationList[]=$row;
            }
        }
        $dataArr['DesignationList']=$DesignationList;
    }

    if(in_array("ParentsList", $key)){
        $sqlParents=$mfp->mf_query("SELECT iUserEmpId as value,vFullName as label FROM users WHERE eStatus='y' AND iSchoolId=".$iSchoolId." AND iRoleId = '6' AND eActiveStatus = 'Active'");
        $ParentsList=array();
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlParents)){
                $ParentsList[]=$row;
            }
        }
        $dataArr['ParentsList']=$ParentsList;
    }

    if(in_array("StudentList", $key)){
        $sqlStd=$mfp->mf_query("SELECT iUserEmpId as value,vFullName as label FROM users WHERE eStatus='y' AND iSchoolId=".$iSchoolId." AND iRoleId = '5' AND eActiveStatus = 'Active'");
        $StudentList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlStd)){
                $StudentList[]=$row;
            }
        }
        $dataArr['StudentList']=$StudentList;
    }
    
    if(in_array("TeacherList", $key)){
        $sqlTeacher=$mfp->mf_query("SELECT iUserEmpId as value,vFullName as label FROM users WHERE eStatus='y' AND iSchoolId=".$iSchoolId." AND iRoleId = '4' AND eActiveStatus = 'Active'");
        $TeacherList=array();      
        if($mfp->mf_affected_rows()>0){
            while($row=$mfp->mf_fetch_array($sqlTeacher)){
                $TeacherList[]=$row;
            }
        }
        $dataArr['TeacherList']=$TeacherList;
    }

    if(!empty($dataArr)){
        $returnArr['status']=200;
        $returnArr['data']=$dataArr;
    }else{
        $returnArr['status']=412;
        $returnArr['message']="No Data Found!";
    }

    echo json_encode($returnArr);
    exit();
}

?>