const express = require("express");
const router = express.Router();
const con = require("./config");

function mf_dbinsert(table, data) // FUNCTION TO INSERT NEW RECORD IN SPECIFIED TABLE
{
    let qry = "INSERT INTO " + table + " set ";
    data.forEach((element, index) => { 
        qry += element + "='" + val + "',";
    })
    console.log("qry => ",qry);
    // qry = substr($qry, 0, -1);
    // echo $qry; exit;
    return qry;
}

module.exports = {
    mf_dbinsert
  };