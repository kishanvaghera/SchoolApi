const express =require("express");
const router=express.Router();
const con=require("./config");
const md5 = require('md5');
//Login API
router.post("/",(req,res)=>{
    const data=req.body;
    if(data.action=="login"){
        con.query("select * from users WHERE vUserEmpPwd='"+md5(data.vUserEmpPwd)+"' AND vUserName='"+data.vUserName+"'",(err,result)=>{
            if(err){
                res.send({status:412,message:"Username or password is wrong!"});
            }else{
                if (result && result.length) {
                    res.send({status:200,data:result[0]});
                }else{
                    res.send({status:412,message:"Username or password is wrong!"});
                }
            }
        });
    }else{
        res.send({status:412,message:"No Data Found!"});
    }
})

module.exports=router;