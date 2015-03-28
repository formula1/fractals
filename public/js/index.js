
  var type = -1;
  var draw_interval = void(0);
  var obj;
  var canvas = document.getElementById("thecanvas");
  var r = Math.min(jQuery("body").width(),jQuery("body").height())/2;
  canvas.width = r*2;
  canvas.height = r*2;

  var ctx = canvas.getContext("2d");
  var rnfn;
  var main_interval = setInterval(switcher,15*1000);
  function switcher(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    clearInterval(draw_interval);
    type = (type+1)%3;
    switch(type){
      case 0:
        obj = new Spiral(
          canvas,
          getSpiralColors(),
          r
        );
        rnfn = obj.move.bind(obj);
        break;
      case 1:
        obj = new PhyTree(
          canvas,
          getTreeColors(),
          r
        );
        rnfn = obj.draw.bind(obj);
        break;
      case 2:
        obj = new KochCrystal(
          canvas,
          getKochColors(),
          r
        );
        rnfn = obj.draw.bind(obj);
        break;
    }
    draw_interval = setInterval(function(){
      rnfn();
    },10);
  }
  switcher();

  function getSpiralColors(){
    if(window.spiralSwitch){
      window.spiralSwitch = false;
      return [
       "#FFFF00",	//yellow
     //	"#FF7800",	//orange
       "#FF0000",	//red
     //	"#FF00FF", 	//purp
       "#0000FF",	//blue
     //	"#00FF00"	//green
     ];
    }
    window.spiralSwitch = true;
    return [
    // "#FFFF00",	//yellow
      "#FFA000",	//orange
    //  "#FF0000",	//red
      "#FF00FF", 	//purp
    //  "#0000FF",	//blue
      "#00FF00"	//green
   ];
  }

  function getKochColors(){
    return [
      "#787878",	//white
      "#7878BE",	//Light Blue
      "#BE78BE",	//purp
      "#BEBEBE",	//light grey
      "#BEBEFF",	//light blue
      "#FFBEFF"	//light purp
    ];
  }

  function getTreeColors(){
    var t_2_b = [];
    var i = 0;
    while(0xFF - i * 0x0F >= 0x78){
      t_2_b[i] = "#"+(
        ((0xFF) << 16 )
        | ((0x78 + i * 0x0F) << 8)
        | (0x00)
      ).toString(16);
      i++;
    }

    var b_2_l = [];
    i = 0;
    while(0xFF - i * 0x0F >= 0x78){
      b_2_l[i] = "#" +(
        (0xFF - i * 0x1E) << 16
        | (0xFF << 8)
        | (0x00)
      ).toString(16);
      i++;
    }

    return  t_2_b.concat(b_2_l);
  }
