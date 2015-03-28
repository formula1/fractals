<?php

$final_radius = 32;

$quarter = 360/4;
$third = 360/3;
$full = 360;
$gold_ratio = (1+sqrt(5))/2;

function clr(){
	global $im;
	global $colors;
	global $alpha;
	global $black;
	global $white;
	$alpha = imagecolorallocatealpha($im, 0x00 , 0x00, 0x00, 127);
	$black = imagecolorallocate($im, 0x00 , 0x00, 0x00);
	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
	$colors = array(
		imagecolorallocate($im, 0xFF, 0xFF, 0x00),	//yellow
	//	"#FF7800",	//orange
		imagecolorallocate($im, 0xFF, 0x00, 0x00),	//red
	//	"#FF00FF", 	//purp
		imagecolorallocate($im, 0x00, 0x00, 0xFF),	//blue
	//	"#00FF00"	//green
	);


}
$gr = addFade();

$frames = array();
$times = array();



for($r=0;$r<360;$r+=10){
	$im = @imagecreate($final_radius*2, $final_radius*2);
	imagesetthickness($im,1);
	clr();
	for($i=0;$i<3;$i++){
		$im = drawSpi($im, $i, $third*$i + $r);
	}
	$i= array($im, $gr);
	$im = imagemergealpha($i);

	ob_start();
	imagegif($im);
	imagedestroy($im);
	$frames[$r/10]=ob_get_contents();
	ob_end_clean();
	$times[$r/10] = 4;
}

header ('Content-type:image/gif');
include('GIFEncoder.class.php');
$gif = new GIFEncoder($frames,$times,0,2,0,0,0,'bin');
echo $gif->GetAnimation();
$fp = fopen('spiral.gif', 'w');
fwrite($fp, $gif->GetAnimation());
fclose($fp);



/*
header('Content-type: image/png'); 
imagepng($im); 
imagedestroy($im);
*/



function drawSpi($im, $i, $angle){
	global $quarter;
	global $third;
	global $gold_ratio;
	global $full;
	global $alpha;
	global $colors;
	global $white;
	global $black;
	global $final_radius;
	
	$radius=1;
	$spicen = array($final_radius-1,$final_radius-1);
	$starting_ang = $angle;//i*quarter%full;
	$ending_ang = $quarter+$angle;//(i+1)*quarter%full;

	while($radius < $final_radius*2){
		imagearc($im, $spicen[0],$spicen[1],$radius*2,$radius*2,$starting_ang,$ending_ang, $colors[$i]);
		
		$old_r = $radius;
		$radius = $radius*$gold_ratio;
		$spicen[0] += -1*cos(deg2rad($ending_ang))*($radius-$old_r);
		$spicen[1] += -1*sin(deg2rad($ending_ang))*($radius-$old_r);
		$starting_ang = ($starting_ang +$quarter)%$full;
		$ending_ang = ($ending_ang+$quarter)%$full;
		
	}

	$radius=1;
	$spicen = array($final_radius-1,$final_radius-1);
	$starting_ang = $angle+$third;//i*quarter%full;
	$ending_ang = $quarter+$angle+$third;//(i+1)*quarter%full;

	while($radius < $final_radius*4){
		imagearc($im, $spicen[0],$spicen[1],$radius*2,$radius*2,$starting_ang,$ending_ang, $colors[$i]);
		
		$old_r = $radius;
		$radius = $radius*$gold_ratio;
		$spicen[0] += -1*cos(deg2rad($ending_ang))*($radius-$old_r);
		$spicen[1] += -1*sin(deg2rad($ending_ang))*($radius-$old_r);
		$starting_ang = ($starting_ang +$quarter)%$full;
		$ending_ang = ($ending_ang+$quarter)%$full;
		
	}

	
	imagefill($im,$final_radius-cos(deg2rad($angle+$third))*16,$final_radius-sin(deg2rad($angle+$third))*16,$colors[$i]); 
//	imagefilledellipse($im,$final_radius-cos(deg2rad($angle+$third))*16,$final_radius-sin(deg2rad($angle+$third))*16,4,4,$black); 

//	imagefilledellipse($im,$final_radius-1,$final_radius-1,40,40,$white); 


	return $im;

}
function addFade(){
	global $final_radius;
	$gr = @imagecreate($final_radius*2, $final_radius*2);
	$bg = imagecolorallocate($gr, 0xFF, 0xFF, 0xFF);
	$alpha = imagecolorallocatealpha($gr, 0xFF , 0xFF, 0xFF, 127);
	$red = imagecolorallocate($gr, 0xFF, 0x00, 0x00);

	$col_inc = 255/$final_radius;
	$alpha_inc = 127/$final_radius;


	$curcol;
	$dia = $final_radius*2;
	$i=0;
	while($final_radius < $dia){
		$t = 255;
		$curcol = imagecolorallocatealpha($gr, $t , $t, $t, $alpha_inc*$i);
		imagefilledellipse($gr, $final_radius-1, $final_radius-1, $dia, $dia, $red);
		imagecolortransparent($gr, $red);
		imagefilledellipse($gr, $final_radius-1, $final_radius-1, $dia, $dia, $curcol);
		$dia--;
		$i++;
	}

	$i=0;
	while(0 < $dia){
		$t = 255;
		$curcol = imagecolorallocatealpha($gr, $t , $t, $t, 127-$alpha_inc*$i);
		imagefilledellipse($gr, $final_radius-1, $final_radius-1, $dia, $dia, $red);
		imagecolortransparent($gr, $red);
		imagefilledellipse($gr, $final_radius-1, $final_radius-1, $dia, $dia, $curcol);
		$dia--;
		$i++;
	}
	return $gr;
}


function imagemergealpha($i) {

 //create a new image
 $s = imagecreatetruecolor(imagesx($i[0]),imagesy($i[1]));
 
 //merge all images
 imagealphablending($s,true);
 $z = $i;
 while($d = each($z)) {
  imagecopy($s,$d[1],0,0,0,0,imagesx($d[1]),imagesy($d[1]));
 }
 
 //restore the transparency
 imagealphablending($s,false);
 $w = imagesx($s);
 $h = imagesy($s);
 for($x=0;$x<$w;$x++) {
  for($y=0;$y<$h;$y++) {
   $c = imagecolorat($s,$x,$y);
   $c = imagecolorsforindex($s,$c);
   $z = $i;
   $t = 0;
   while($d = each($z)) {
   $ta = imagecolorat($d[1],$x,$y);
   $ta = imagecolorsforindex($d[1],$ta);
   $t += 127-$ta['alpha'];
   }
   $t = ($t > 127) ? 127 : $t;
   $t = 127-$t;
   $c = imagecolorallocatealpha($s,$c['red'],$c['green'],$c['blue'],$t);
   imagesetpixel($s,$x,$y,$c);
  }
 }
 imagesavealpha($s,true);
 return $s;
}
?>