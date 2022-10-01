const express = require("express");
const app=express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const AuthRouter=require("./Auth");

const StudentRouter=require("./Student");

const TeacherRouter=require("./Teacher");

const MasterRouter=require("./Master");

app.use("/loginApi",AuthRouter);

app.use("/studentApi",StudentRouter);

app.use("/TeacherAction",TeacherRouter);

app.use("/Master",MasterRouter);

app.listen(8000,()=>{
    console.log("server is responding.");
})