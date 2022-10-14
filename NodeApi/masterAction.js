const express = require("express");
const router = express.Router();
const con = require("./config");
const { mf_dbinsert } = require("./functions");

router.post("/", (req, res) => {
    const data = req.body;
    console.log("data",data);
    console.log("data.action",data);
    // res.json({requestBody: req.body})
    if (data.action == "addEditSection") {
        console.log(data);
        /* let aInsArr = [];
        aInsArr['iClassId'] = data.iClassId;
        aInsArr['vSectionName'] = data.vSectionName;
        const qry = mf_dbinsert("section",aInsArr);
        console.log("aInsArr => ",aInsArr); */
        /* con.query("INSERT INTO section SET iClassId = "+iClassId+",vSectionName = "+vSectionName+" ", (err, result) => {
            if (err) {
                res.send({ status: 412, message: "Error Occured!" });
            } else {
                if (result && result.length) {
                    res.send({ status: 200, data: result[0] });
                } else {
                    res.send({ status: 412, message: "Error Occured!" });
                }
            }
        }); */
    } else {
        res.send({ status: 412, message: "No Data Found!" });
    }
})

module.exports = router;