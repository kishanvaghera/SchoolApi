const express =require("express");
const router=express.Router();
const con=require("./config");
const {InsertFunc} =require("./CommoFun");

router.post("/",(req,res)=>{
    const data=req.body;
    if(data.action=="addTeacher"){
        const insData=InsertFunc(data.data);
        var insValue = insData.keyValues.map(d => `'${d}'`).join(','); 
        con.query("INSERT INTO users ("+insData.keyNames.toString()+") VALUES ("+insValue+")", function (err, result) {  
            if(err){
                res.send({status:412,message:"No Record Found!"});
            }else{
                res.send({status:200,message:"Teacher has been added Succesfully."});
            }
        });
    }else{
        res.send({status:412,message:"No Data Found!"});
    }
})

module.exports=router;