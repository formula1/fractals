<?php

include('GIFEncoder.class.php');

$frames = array();
$times = array();
$path = "saved";
for($i=0;file_exists($path."/".$i.'.png'));$i++) {
    $image = imagecreatefrompng(($path."/".$i.'.png');
    ob_start();
    imagegif($image);
    $frames[]=ob_get_contents();
    $times[]=4; // Delay in the animation.
    ob_end_clean();
}
closedir($handle);
$gif = new GIFEncoder($frames,$framed,0,2,0,0,0,'bin');
$fp = fopen('gifs/spiral.gif', 'w');
fwrite($fp, $gif->GetAnimation());
fclose($fp);

?>
