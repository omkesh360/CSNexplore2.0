<?php
$dir = 'c:/xampp/htdocs/CSNexplore2.0/images/fevicon/';
$files = glob($dir . '*.png');

foreach ($files as $file) {
    echo "Processing $file...\n";
    $img = @imagecreatefrompng($file);
    if (!$img) {
        echo "Failed to load $file\n";
        continue;
    }
    
    $w = imagesx($img);
    $h = imagesy($img);
    
    $newImg = imagecreatetruecolor($w, $h);
    
    // Fill the background with black
    $black = imagecolorallocate($newImg, 0, 0, 0);
    imagefilledrectangle($newImg, 0, 0, $w, $h, $black);
    
    // Copy original over the black background
    // Since original has transparency, we need to allow alpha blending on $newImg
    imagealphablending($newImg, true);
    imagecopy($newImg, $img, 0, 0, 0, 0, $w, $h);
    
    // Save image
    imagepng($newImg, $file);
    imagedestroy($img);
    imagedestroy($newImg);
    echo "Done processing $file\n";
}

// Convert the 32x32 png to favicon.ico (since many browsers accept a PNG formatted file named .ico, but let's just copy it if needed)
copy($dir . 'favicon-32x32.png', $dir . 'favicon.ico');
echo "Done processing all files.\n";
