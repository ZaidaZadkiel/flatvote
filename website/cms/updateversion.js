const versionpath = './src/version.json';
let version=require(versionpath);
var newcode = version.version.split('.');
    newcode[2]++;
var newdate = new Date();
let newversion =
{
    "version" : newcode[0] + '.' + newcode[1] + '.' + newcode[2],
    "date"    : newdate.getFullYear() +'/'+ (newdate.getMonth()<10 ? '0'+(newdate.getMonth()+1) : (newdate.getMonth()+1) ) + '/' + newdate.getDate()
}
require('fs').writeFile(versionpath, JSON.stringify(newversion), function(err){
  if(err) return console.log(err);
  console.log(JSON.stringify(newversion));
});
