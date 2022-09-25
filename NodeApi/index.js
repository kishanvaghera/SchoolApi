const express = require("express");
const app=express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const AuthRouter=require("./Auth");

app.use("/loginApi",AuthRouter);

app.listen(8000,()=>{
    console.log("server is responding.");
})