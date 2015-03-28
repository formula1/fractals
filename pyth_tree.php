<?php




$w = (isset($_GET["width"]))?$_GET["width"]:120;

//width=1.5
//iw = 8.25 => 132 => 1056
//ih = 5.25 => 47.25 => 378

$iw = $w*8.5;
$ih = $w*5.5;

$startpoint = (isset($_GET["st"]))?$_GET["st"]:array("x"=>$iw/2,"y"=>$ih-$w/2);


$numit = 0;
$i = $w;
while($i > 1){
	$i = $i*sqrt(2)/2;
	$numit++;
}



$im = @imagecreate($iw, $ih)
	or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 0, 0);

$t_2_b = array();
$i = 0;
while(0xFF - $i * 0x0F >= 0x78){
	$t_2_b[$i] = imagecolorallocate($im, 0xFF - $i * 0x0F, 0x77, 0x00 + $i * 0x0F);
	$i++;
}

$b_2_l = array();
$i = 0;
while(0xFF - $i * 0x0F >= 0x78){
	$b_2_l[$i] = imagecolorallocate($im, 0x78 - $i * 0x0F, 0x78 + $i * 0x0F, 0x78 - $i * 0x0F);
	$i++;
}

$colors = array_merge ($t_2_b, $b_2_l);


	

	
//$text_color = imagecolorallocate($im, 233, 14, 91);
//imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
	
	$triangles = array();
	$triangles[0] = array("point"=>$startpoint, "angle"=>0);
		

	function cal($angle, $op, $ow=-1){
		global $w;

	$ct = array(
		0=>1,
		15=>cos(pi/12),
		30=>sqrt(3)/2,
		45=>(sqrt(2)/2),
		60=>1/2,
		75=>cos(5*pi/12),
		90=>0,
		105=>-cos(5*pi/12),
		120=>-1/2,
		135=>-(sqrt(2)/2),
		150=>-sqrt(3)/2,
		165=>-cos(pi/12),
		180=>-1,
		195=>-cos(pi/12),
		210=>-sqrt(3)/2,
		225=>-(sqrt(2)/2),
		240=>-1/2,
		255=>-cos(5*pi/12),
		270=>0,
		285=>cos(5*pi/12),
		300=>1/2,
		315=>(sqrt(2)/2),
		330=>sqrt(3)/2,
		345=>cos(pi/12)
	);
		
		if($ow === -1) $ow = $w;
		
		while($angle < 0)	$angle += 360;
		$angle = $angle%360;
		return $ow*$ct[$angle]+$op;
	}

	$j == 0;	

	do{
		$nt = array();
		foreach($triangles as $t){

			$sqps = array(
				cal(45+$t["angle"], $t["point"]["x"]), cal(45+90+$t["angle"], $t["point"]["y"])
				,cal(45+90+$t["angle"], $t["point"]["x"]), cal(45+90+90+$t["angle"], $t["point"]["y"])
				,cal(45+180+$t["angle"], $t["point"]["x"]), cal(45+90+180+$t["angle"], $t["point"]["y"])
				,cal(45+270+$t["angle"], $t["point"]["x"]), cal(45+90+270+$t["angle"], $t["point"]["y"])
			);
			$tps = array(
				$sqps[0], $sqps[1],
				$sqps[2], $sqps[3],
				cal(90+$t["angle"], $t["point"]["x"], $w/2*(1+sqrt(3))) ,cal(90+90+$t["angle"], $t["point"]["y"],$w/2*(1+sqrt(3)))
			);

			
/*			for($i=0;$i<count($sqps);$i++){
				imageline($im, 
					$sqps[$i][0],$sqps[$i][1],
					$sqps[($i+1)%4][0],$sqps[($i+1)%4][1],
				$colors[round(16*$j/$numit)]);
			}
*/
			imagefilledpolygon($im, $sqps, 4, $colors[round(16*$j/$numit)]);

/*			
			for($i=0;$i<count($tps);$i++){
				imageline($im, 
					$tps[$i][0],$tps[$i][1],
					$tps[($i+1)%3][0],$tps[($i+1)%3][1],
				$colors[round(16*$j/$numit)]);
			}
*/			
			imagefilledpolygon($im, $tps, 3, $colors[round(16*$j/$numit)]);


			$nt[count($nt)] = array(
				"point"=>array(
					"x"=>cal($t["angle"], $tps[4],$w*sqrt(2)/2),
					"y"=>cal(90+$t["angle"], $tps[5],$w*sqrt(2)/2)
				),
				"angle"=>$t["angle"]-45
			);
			$nt[count($nt)] = array(
				"point"=>array(
					"x"=>cal(180+$t["angle"], $tps[4],$w*sqrt(2)/2),
					"y"=>cal(180+90+$t["angle"], $tps[5],$w*sqrt(2)/2)
				),
				"angle"=>$t["angle"]+45
			);
			
		}
		$triangles = $nt;

		$w = $w*sqrt(2)/2;
		$j++;
	}while($w > 1 && $j < 12);

	ob_start (); 
	imagepng ($im);
	$image_data = ob_get_contents (); 
	imagedestroy($im);
	ob_end_clean (); 

	$i64 = base64_encode ($image_data);	



?>
<img src="data:image/jpeg;base64, <?php echo $i64; ?>" />