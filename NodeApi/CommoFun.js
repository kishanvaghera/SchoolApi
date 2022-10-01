const InsertFunc=(array)=>{
    let IsertArr=array;
    for (var key of Object.keys(IsertArr)) {
        if(typeof IsertArr[key]=="object"){
            IsertArr[key]=IsertArr[key]['value'];
        }
    }

    const KeyList=Object.keys(IsertArr);
    const Allvalue=Object.values(IsertArr);
    return {keyNames:KeyList,keyValues:Allvalue}
}

module.exports={InsertFunc};