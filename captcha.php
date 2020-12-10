<?php
    ob_start();

    session_start();
    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 6; $i++) 
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $captcha = $randomString;
    $_SESSION["captcha"] = $captcha;

    $im = imagecreate( 200, 40 );
    $background_color = imagecolorallocate($im, 255, 160, 119);
    $text_color = imagecolorallocate($im, 233, 14, 91);
    imagestring($im, 15, 70, 10, $captcha, $text_color);
    imagepng($im);
    imagedestroy($im);
?>