
function Cloud(canvas, oAngle, flip){
  var width = canvas.width;
  var height = canvas.height;
  this.parts = [[
    new Vec2(width/4, height/4),
    new Vec2(width/2, 3 * height/4),
    new Vec2(3* width/4, height / 4)
  ]];
  this.canvas = canvas;
  this.angle = oAngle;
  var ctx = canvas.getContext("2d");
  this.ctx = ctx;
  ctx.strokeStyle = 'rgba(0, 0, 0, 0.25)';
  this.ctx.translate(canvas.width / 2, canvas.height / 2);
  this.ctx.scale(flip ? -1 : 1, -1);
  this.ctx.translate(- canvas.width / 2, - canvas.height / 2);
}

Cloud.prototype.run = function(){
  var t = makeNextTriangles(this, this.parts[0]);
  if(!t) return false;
  this.parts = this.parts.reduce((netParts, p)=>(
    netParts.concat(makeNextTriangles(this, p))
  ), []);
  return true;
  function makeNextTriangles(config, oldTriangle){
    var h = oldTriangle[1].sub(oldTriangle[0]).distance();
    if(h < 1) return false;
    var triA = getInitialTriangle(config, oldTriangle);
    var triB = getInitialTriangle(config, [oldTriangle[1], oldTriangle[2]]);
    // var triB = getNextTriangle(config, oldTriangle, triA);
    return [triA, triB];
  }

  function getInitialTriangle(config, line){
    var h = line[1].sub(line[0]).distance();
    // We know the expected angle, the big side and that we have an isoceles
    // we have an AAA + S triangle
    // This is a solvable triangle, so we solve each side
    var newSideLength = h*Math.sin((Math.PI - config.angle)/2)/Math.sin(config.angle);
    // From here we need to rotate the trangle so that it makes a 45 degree angle on the angle which is not the starting point and is not the big angle
    var cornerAngle = Math.PI/4;
    var bigAngle = config.angle;
    var leftOver = Math.PI - cornerAngle - bigAngle;
    // We have an AAA + S since we have one of the lengths, and all three of the angles
    // We want to know point B which can be acheived by turning the starting angle and the big angle and big side into a right triangle
    // To do this, getting the height can be acheived by solving the side with the 45 degree angle
    var bigLine = Math.sin(bigAngle) * (newSideLength/Math.sin(cornerAngle));
    var cutoffLine =  Math.sin(leftOver)*(newSideLength/Math.sin(cornerAngle));
    var hypAng = line[1].sub(line[0]).getAngle();
    var pointA = line[0];
    var pointB = line[0].add(
      Vec2.fromAngle((hypAng + leftOver)).scale(newSideLength)
    );
    var pointC = pointB.add(
      line[0].add(line[1].sub(line[0]).normalize().scale(bigLine))
      .sub(pointB).normalize().scale(newSideLength)
    );

    return [pointA, pointB, pointC];
  }
  function getNextTriangle(config, line, initTri){
    // We are going to decide that the length of A, C will be equivelent on both triangles
    var d = getIntersectDistance(config, line)
    var basLine = line[2].sub(line[1]);
    var hypAng = basLine.getAngle()
    var pointA = line[1];
    var intersectPoint = pointA.add(line[2].sub(pointA).normalize().scale(d));
    // Math.PI / 2 - (config.angle / 2) - (config.angle - Math.PI/2)/2
    // MAth.PI / 2 - (config / 2) - (config / 2) + MAth.PI/4
    // 3 * PI / 4 - config
    var topAng = 3 * Math.PI / 4 - config.angle;
    var botLen = Math.sin(topAng) * d / Math.sin(config.angle);
    var netAng = Math.PI / 2 - ((config.angle - (Math.PI / 2)) /2);
    var pointB = pointA.add(Vec2.fromAngle(hypAng + netAng).scale(botLen));
    var pointC = pointB.add(
      intersectPoint.sub(pointB).normalize().scale(botLen)
    );
    return [pointA, pointB, pointC];
  }
  function getIntersectDistance(config, line){
    var h = line[1].sub(line[0]).distance();
    // We know the expected angle, the big side and that we have an isoceles
    // we have an AAA + S triangle
    // This is a solvable triangle, so we solve each side
    var newSideLength = h*Math.sin((Math.PI - config.angle)/2)/Math.sin(config.angle);
    // From here we need to rotate the trangle so that it makes a 45 degree angle on the angle which is not the starting point and is not the big angle
    var cornerAngle = Math.PI/4;
    return Math.sin(config.angle) * (newSideLength/Math.sin(cornerAngle));
  }
}

Cloud.prototype.draw = function(){
  var offset = Math.sqrt(this.parts.length * 2);
  this.ctx.lineWidth=offset;
  this.ctx.strokeStyle = `rgba(255, 255, 255, ${
    (Math.floor(100/offset)/100).toString()
  })`;
  this.ctx.clearRect(0,0,this.canvas.width,this.canvas.height)
  this.parts.forEach((p)=>(drawTriangle(this, p)));

  function drawTriangle(config, triangle){
    const { ctx } = config;
    ctx.beginPath();
    ctx.moveTo(triangle[0][0], triangle[0][1])
    ctx.lineTo(triangle[1][0], triangle[1][1])
    ctx.lineTo(triangle[2][0], triangle[2][1])
    ctx.stroke();
  }
}

// A triangles angles sum always reaches half a circle
// let ap* = angle Portion of angle * (example apA, apB, apC)
// scale * Math.sin(apA) / Math.sin(apA) = scale * Math.sin(apB) / Math.sin(apB)
// This is true because scale * 1 = scale * 1
// It's important to note that this equation expects the lengths of the
// Final triangle to be relative to sin
// A seperate proof may be that splitting the triangle into two triangles
// 1 [120, 30, 30] = 2 [30, 60, 90]
// 1 [sqrt(3), 1, 1] = 2 [1/2, sqrt(3)/2, 1]
// sin(120) = sqrt(3)/2
// - 120 - 90 = 30, meaning after the 90 angle, it continues 30 more degrees
// At axis, the angles need to sum 90 degrees, and there is already a 30 degrees from the top, as a result the remaining angle should be 60.
// The triangle is a 60 degree triangle flipped over the y access
// sin(30) = 1/2
// sin(60) = sqrt(3)/2
// The triangle's lengths are relative to eachother rather than unit circle
// As a result sqrt(3), 1, 1 are still valid lengths
// can sss be solved with law of sines?
// a bit of a guessing game


function Vec2(x, y){
  this[0] = x; this[1] = y;
  Object.defineProperty(this, "x", {
    get:()=>(this[0]),
    set:(x)=>(this[0] = x)
  })
  Object.defineProperty(this, "y", {
    get:()=>(this[1]),
    set:(y)=>(this[1] = y)
  })
}

Vec2.prototype.add = function(oV){
  return new Vec2(
    this.x + oV.x, this.y + oV.y
  )
}
Vec2.prototype.scale = function(n){
 return new Vec2(
   this.x * n, this.y * n
 )
}
Vec2.prototype.sub = function(oV){
  return this.add(oV.scale(-1));
}
Vec2.prototype.normalize = function(oV){
  return this.scale(1/this.distance());
}
Vec2.prototype.distance = function(){
  return Math.pow(Math.pow(this.x, 2) + Math.pow(this.y, 2), 1/2);
}
Vec2.prototype.getAngle = function(){
  var d = this.distance();
  var ang = Math.asin(this.y/d);
  if(this.x >= 0){
    return ang;
  }
  if(ang > 0){
    return Math.PI - ang;
  }
  return - Math.PI - ang;
}
Vec2.fromAngle = function(a){
  return new Vec2(Math.cos(a), Math.sin(a));
}
