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

// adding morgan to log HTTP requests
// app.use(morgan('combined'));
// app.use(express.urlencoded({ extended: true }));

const AuthRouter = require("./Auth");
const MasterRouter = require("./masterAction");

app.use("/loginApi", AuthRouter);
app.use("/masterActionApi", MasterRouter);

app.listen(8000, () => {
    console.log("server is responding.");
})