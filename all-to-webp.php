<?php

ini_set('memory_limit','2G'); //128M, 1G, -1?

$maxWidth = 1200;
$maxHeight = 1200;
$quality = 90;

$iterator = new DirectoryIterator('./');

/*
if (!is_dir('webp')) {
	mkdir('webp');
}
*/

foreach ( $iterator as $file ) {
	if
		(
			strtolower($file->getExtension()) == "jpg" ||
			strtolower($file->getExtension()) == "jpeg" ||
			strtolower($file->getExtension()) == "bmp" ||
			strtolower($file->getExtension()) == "png" &&
			$file->isFile() &&
			!file_exists($file->getBasename('.'.$file->getExtension()).'.webp')
			
		)
	{
			$width = $maxWidth;
			$height = $maxHeight;

			$img = readfile($file->getBasename());

			list($width_orig, $height_orig, $tipo, $atributo) = getimagesize($file->getBasename());

			if ($width_orig > $maxWidth || $height_orig > $maxHeight) {
				if($width_orig > $height_orig){
					$height = ($width/$width_orig)*$height_orig;
				} 
				elseif($width_orig < $height_orig) {
					$width = ($height/$height_orig)*$width_orig;
				}
			}else{
				$width = $width_orig;
				$height = $height_orig;
			}

			$novaimagem = imagecreatetruecolor($width,$height);
			imageAlphaBlending($novaimagem, false);
			imageSaveAlpha($novaimagem, true);
			$im = imagecolorallocatealpha($novaimagem, 0, 0, 0, 127);
			imagefilledrectangle($novaimagem, 0, 0, $width - 1, $height - 1,$im);

			$ext = strtolower($file->getExtension());

			if ($ext == 'jpg' || $ext == 'jpeg') {
				$origem = imagecreatefromjpeg($file->getBasename());
			}elseif($ext == 'png'){
				$origem = imagecreatefrompng($file->getBasename());
			}elseif($ext == 'bmp'){
				$origem = imagecreatefrombmp($file->getBasename());
			}
			else{
				exit('Formato de imagem não compativel!');
			}

			imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,$width, 
				$height, $width_orig, $height_orig);

		// SAVE IN DIRECTORY
		//if (!imagewebp($novaimagem, 'webp/'.$file->getBasename('.'.$file->getExtension()).'.webp',$quality)) {
			if (!imagewebp($novaimagem,$file->getBasename('.'.$file->getExtension()).'.webp',$quality)) {
				return false;
			}
			imagedestroy($novaimagem);
		}
	}