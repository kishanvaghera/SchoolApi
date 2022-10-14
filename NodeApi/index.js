const express = require("express");
const bodyParser = require('body-parser');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const app = express();

// adding Helmet to enhance your API's security
app.use(helmet());

// using bodyParser to parse JSON bodies into JS objects
app.use(express.json());

// enabling CORS for all requests
app.use(cors());

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