

function blendColors(c1, c2, iterations, easing){
  var easingFn = EASING[easing || 'linear'];
  var rgb1 = hexToRGB(c1);
  var rgb2 = hexToRGB(c2);
  iterations = (iterations || 16);
  var diff = rgb1.map((value, i)=>(
    (rgb2[i] - value)
  ));
  console.log(diff);
  console.log(rgb1, rgb2);
  var ret = [];
  for(var i = 0, l = iterations; i < l; i++){
    ret.push(rgb1.map((value, m)=>(
      Math.round(value + diff[m] * easingFn(i/l))
    )))
  }
  console.log(ret);
  return ret.map((rgb)=>(
    rgb.reduce((str, n)=>{
      n = ("0" + n.toString(16));
      var offset = n.length - 2;
      return str + n.slice(offset, offset + 2)
    }, "#")
  ));

  function hexToRGB(hex){
    return [1,3,5].map((offset)=>(
      parseInt(hex.slice(offset, offset + 2).toUpperCase(), 16)
    ));
  }
}
