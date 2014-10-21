<?php

if( strlen( $_GET[ 'hash' ] ) == 0 || strlen( $_GET[ 'hash' ] ) > 20 ) die ;
$hash = strip_tags( $_GET[ 'hash' ] ) ;

$width = 120;          				//Ширина изображения
$height = 60;          				//Высота изображения
$font_size = 17;     				//Размер шрифта
$min_amount = 4;       				//Минимальное количество символов, которые нужно набрать
$max_amount = 5;       				//Максимальное количество символов, которые нужно набрать
$fon_let_amount = 30;  				//Количество символов, которые находятся на фоне
$path_fonts = 'fonts/';				//Путь к шрифтам
$hash_path = 'hash/';  				//Путь к файлам с хешем

$letters = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z','2','3','4','5','6','7','9');
$colors = array('10','30','50','70','90','110','130','150','170','190','210');

$src = imagecreatetruecolor($width,$height);
$fon = imagecolorallocate($src,255,255,255);
imagefill($src,0,0,$fon);

$fonts = array();
$dir=opendir($path_fonts);
while($fontName = readdir($dir)){
  if($fontName != "." && $fontName != ".."){
    $fonts[] = $fontName;
  }
}
closedir($dir);

for($i=0;$i<$fon_let_amount;$i++){
  $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100); 
  $font = $path_fonts.$fonts[rand(0,sizeof($fonts)-1)];
  $letter = $letters[rand(0,sizeof($letters)-1)];
  $size = rand($font_size-2,$font_size+2);
  imagettftext($src,$size,rand(0,45),rand($width*0.1,$width-$width*0.1),rand($height*0.2,$height),$color,$font,$letter);
}

$code = '' ;
$let_amount = mt_rand( $min_amount, $max_amount ) ;
for($i=0;$i<$let_amount;$i++){
  $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],rand(20,40)); 
  $font = $path_fonts.$fonts[rand(0,sizeof($fonts)-1)];
  $letter = $letters[rand(0,sizeof($letters)-1)];
  $code .= $letter;
  $size = rand($font_size*2.1-2,$font_size*2.1+2);
  $x = ( $i + 0 ) * ( $font_size * 1.3 ) + rand( 4, 7 ) ;
  $y = (($height*2)/3) + rand(0,5);
  imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
}

file_put_contents( $hash_path.$hash.'.txt', md5( $code ) ) ;

header ("Content-type: image/gif"); 
imagegif($src);

?> 