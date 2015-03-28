var express = require("express");
vas fs = require("fs");
var server = express();
var dynamic_gif = new DynamicGifStack("rgba");
var i = 0;
server.get("/",function(req,res){
  res.sendFile(__dirname+"/public/test.html");
})
server.use(express.static("./public"));
server.post("/png",function(req,res){
  req.pipe(
    fs.createWriteStream(__dirname+"/public/saved/"+i+".png",{encoding:"base64"})
  ).on("end",function(){
    res.status(200).end("saved to "+__dirname+"/public/saved/"+i+".png");
  }).on("error",function(e){
    res.status(500).end(JSON.stringify(e));
  });
});

server.listen(3000);
