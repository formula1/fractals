<?php




$w = (isset($_GET["width"]))?$_GET["width"]:70;

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

$polygon = array(
	-35,0,
	35,0,
	35,70,
	0,70+35*sqrt(2),
	-35,70
);

for($i=0;$i<count($polygon);$i+=2){
	$polygon[$i] += $iw/2;
}

function getcenterpoint($points){
	$avg = array(0,0);
	for($i=0;$i<count($points);$i+=2){
		$avg[0] += $points[$i]*2/count($points);
		$avg[1] += $points[$i+1]*2/count($points);
	}
	return $avg;
}

function getAngles($center, $points){
	$angles = array();
	$j = 0;
	for($i=0;$i<count($points);$i+=2){
		$angles[$j] = atan2($points[$i]-$center[0], $points[$i+1]-$center[1]);
		$j++;
	}
	
	return $angles;
}

function getLengths($center, $points){
	$lengths = array();
	$j = 0;
	for($i=0;$i<count($points);$i+=2){
		$lengths[$j] = hypot($points[$i]-$center[0], $points[$i+1]-$center[1]);
		$j++;
	}
	
	return $lengths;
}


function getNewPolys($points, $oscale){
	$new_polys = array();
//	echo "inhere";
	
	$center = getcenterpoint($points);

	$j = 0;
	for($i=4;$i<count($points)-4;$i+=2){
	echo "inthere";
		$new_polys[$j] = array("scale"=>0, "off_angle" => 0,"midpoint" => array(0,0));

		//new angle
		$delta  = array($points[$i+2] - $points[$i], $points[$i+3] - $points[$i+1]);
		$new_polys[$j]["off_angle"] = atan2($delta[0], $delta[1]);
		
		$orig_mp_delta = array($center[0] -$points[0], $center[1] - $points[1]);
		$orig_mp_angle = atan2($orig_mp_delta[0],$orig_mp_delta[1]);
		$orig_mp_hyp = hypot($orig_mp_delta[0],$orig_mp_delta[1]);
		
		$new_polys[$j]["scale"] = $oscale * hypot ( $delta[0] , $delta[1] )/hypot($points[2]-$points[0], $points[1]-$points[3]);
		if($new_polys[$j]["scale"] > 1) throw new Exception("inifinite loop detected");
		
		$new_polys[$j]["midpoint"] = array( $scale[$i]*$orig_mp_hyp*cos($new_polys[$j]["off_angle"]+$orig_mp_angle), $scale[$i]*$orig_mp_hyp*sin($new_polys[$j]["off_angle"]+$orig_mp_angle));
		
		$j++;
	}

	return $new_polys;
}

$center = getcenterpoint($polygon);
$lengths = getLengths($center, $polygon);
$angles = getAngles($center, $polygon);


		

function cal($angle, $op, $ow=-1){
	global $w;

	$ct = array(
		0=>1,
		45=>(sqrt(2)/2),
		90=>0,
		135=>-(sqrt(2)/2),
		180=>-1,
		225=>-(sqrt(2)/2),
		270=>0,
		315=>(sqrt(2)/2)
	);
	
	if($ow === -1) $ow = $w;
	
	while($angle < 0)	$angle += 360;
	$angle = $angle%360;
	return $ow*$ct[$angle]+$op;
}

function ang($angle){
	while($angle < 0)	$angle += pi()*2;
	$angle = fmod($angle, pi()*2);
	
	return $angle;

}

function calculatePoints($scale, $off_angle, $midpoint){
	global $lengths;
	global $angles;
	
	$new_points = array();
	for($i=0;$i<count($lengths);$i++){
		$nl = $lengths[$i]*$scale;
		$na = ang($off_angle+$angles[$i]);
		echo $nl . "<br />";
		echo $na . "<br />";
		print_r($midpoint);
		$new_points[count($new_points)] = $nl*cos($na) + $midpoint[0];//x
		$new_points[count($new_points)] = $nl*sin($na) + $midpoint[1];//y

	}
	return $new_points;
}

print_r($polygon);
$polys = array();
$polys[0] = array("midpoint"=>getcenterpoint($polygon), "off_angle"=>0, "scale"=>1);
//print_r($polys);
	$j == 0;	

	do{
		$nt = array();
		foreach($polys as $t){
/*			$sqps = array(
				0 => array(
					cal(45+$t["angle"], $t["point"]["x"])
					,cal(45+90+$t["angle"], $t["point"]["y"])
				),
				1 => array(
					cal(45+90+$t["angle"], $t["point"]["x"])
					,cal(45+90+90+$t["angle"], $t["point"]["y"])
				),
				2 => array(
					cal(45+180+$t["angle"], $t["point"]["x"])
					,cal(45+90+180+$t["angle"], $t["point"]["y"])
				),
				3 => array(
					cal(45+270+$t["angle"], $t["point"]["x"])
					,cal(45+90+270+$t["angle"], $t["point"]["y"])
				),
			);
			
			$tps = array(
				0 => $sqps[0],
				1 => $sqps[1],
				2 =>	array(
					cal(90+$t["angle"], $t["point"]["x"], $w/2*(1+sqrt(3)))
					,cal(90+90+$t["angle"], $t["point"]["y"],$w/2*(1+sqrt(3)))
				)
			);
			
			*/
			if($t["scale"] < .7) continue;
			
			$points = calculatePoints( $t["scale"], $t["off_angle"], $t["midpoint"]);
			print_r($points);

//			echo count($points);
/*			
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

			*/
/*			for($i=0;$i<count($sqps);$i++){
				imageline($im, 
					$sqps[$i][0],$sqps[$i][1],
					$sqps[($i+1)%4][0],$sqps[($i+1)%4][1],
				$colors[round(16*$j/$numit)]);
			}
*/
			imagefilledpolygon($im, $points, count($points)/2, $colors[round(16*$t["scale"])]);

/*			
			for($i=0;$i<count($tps);$i++){
				imageline($im, 
					$tps[$i][0],$tps[$i][1],
					$tps[($i+1)%3][0],$tps[($i+1)%3][1],
				$colors[round(16*$j/$numit)]);
			}
*/			
//			imagefilledpolygon($im, $tps, 3, $colors[round(16*$j/$numit)]);

			array_merge($nt,getNewPolys($points, $t["scale"]));
/*			
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
*/	
		}
		$polys = $nt;
		echo count($polys)."<br />";
//		$w = $w*sqrt(2)/2;
		$j++;
	}while(count($polys) > 0 && $j <30);

	ob_start (); 
	imagepng ($im);
	$image_data = ob_get_contents (); 
	imagedestroy($im);
	ob_end_clean (); 

	$i64 = base64_encode ($image_data);	



?>
<img src="data:image/jpeg;base64, <?php echo $i64; ?>" />