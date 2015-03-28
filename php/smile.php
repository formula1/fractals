<?php 

header('Content-type: image/png'); 

$smile=imagecreate(400,400); 
$kek=imagecolorallocate($smile,0,0,255); 
$feher=imagecolorallocate($smile,255,255,255); 
$sarga=imagecolorallocate($smile,255,255,0); 
$fekete=imagecolorallocate($smile,0,0,0); 
imagefill($smile,0,0,$kek); 

imagearc($smile,200,200,300,300,0,360,$fekete); 
imagearc($smile,200,225,200,150,0,180,$fekete); 
imagearc($smile,200,225,200,123,0,180,$fekete); 
imagearc($smile,150,150,20,20,0,360,$fekete); 
imagearc($smile,250,150,20,20,0,360,$fekete); 
imagefill($smile,200,200,$sarga); 
imagefill($smile,200,290,$fekete); 
imagefill($smile,155,155,$fekete); 
imagefill($smile,255,155,$fekete); 
imagepng($smile); 

?>