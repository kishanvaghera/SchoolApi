const InsertFunc=(array)=>{
    const IsertArr=array;
    const KeyList=Object.keys(IsertArr);
    const Allvalue=Object.values(IsertArr);
    return {keyNames:KeyList,keyValues:Allvalue}
}

module.exports={InsertFunc};