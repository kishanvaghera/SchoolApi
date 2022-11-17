const express =require("express");
const router=express.Router();
const con=require("./config");
const sha1 = require('sha1');
const {InsertFunc} =require("./CommoFun");

router.post("/",(req,res)=>{
    const data=req.body;
    if(data.action=="addClass"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO class ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Class has been added Succesfully."});
            }
        });
    }else if(data.action=="addSubject"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO subject ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Subject has been added Succesfully."});
            }
        });
    }else if(data.action == "addEditSection"){
        console.log("helooo");
    }
})

module.exports=router;