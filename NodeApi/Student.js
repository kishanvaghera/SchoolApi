const express =require("express");
const router=express.Router();
const con=require("./config");
const sha1 = require('sha1');
const {InsertFunc} =require("./CommoFun");
var moment = require('moment'); // require

//Login API
router.post("/",(req,res)=>{
    const data=req.body;
    if(data.action=="studentList"){
        con.query("select * from users WHERE iSchoolId="+data.iSchoolId+" AND eActiveStatus='Active'",(err,result)=>{
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                if (result && result.length) {
                    res.send({status:200,data:result});
                }else{
                    res.send({status:412,message:"No Record Found!"});
                }
            }
        });
    }else if(data.action=="addStudent"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO users ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Student has been added Succesfully."});
            }
        });
    }else if(data.action=="addHomeWork"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO homework ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Homework has been added Succesfully."});
            }
        });
    }else if(data.action=="addSyllabus"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO syllabus ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Syllabus has been added Succesfully."});
            }
        });
    }else if(data.action=="addExamTimeTable"){
        data.data['dStartDate']=moment(data.data['dStartDate']).format('YYYY-MM-DD');
        data.data['dEndDate']=moment(data.data['dEndDate']).format('YYYY-MM-DD');
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO exams ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Exam Time Table has been added Succesfully."});
            }
        });
    }else{
        res.send({status:412,message:"No Data Found!"});
    }
})

module.exports=router;