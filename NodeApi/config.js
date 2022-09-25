const mysql=require("mysql");
const con = mysql.createConnection({
    host:"localhost",
    user:"root",
    password:"",
    database:"schoolopathy"
})

con.connect((err)=>{
    if(err){
        console.log("Connection Error!");
    }else{
        console.log("Connection Successfull.");
    }
})

module.exports=con;