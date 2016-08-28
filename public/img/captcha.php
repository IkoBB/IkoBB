<?php
/*
 * IkoBB Captcha
 *
 */

session_start();

$characters = str_split('abcdefghijklmnopqrstuvwxyz'
    . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    . '0123456789!@#$%^&*()');
shuffle($characters);

$string = '';

foreach (array_rand($characters, 5) as $k) {
    $string .= $characters[$k];
}

if (function_exists('imagecreate')) {

    function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    header('Content-type: image/png');

    //Setting font color.
    $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

    $bgc = array(
        '#ffffff',
        '#eeeeee',
        '#cccccc'
    );
    /* Check if color conflicts with the background color. */
    if (in_array($color, $bgc)) {
        $color = '#000000';
    }
    $color = hex2rgb($color);
    $bg = array(255, 255, 255);

    $font = '../fonts/2.ttf';
    $size = 20;
    $angle = 0;
    $field = imagettfbbox($size, $angle, $font, $string);
    $size_x = (abs($field[4] - $field[0]) + 5);
    $size_y = (abs($field[1] - $field[7]) + 5);
    $pos_x = 5;
    $pos_y = ($size_y - 5);
    $image = imagecreate($size_x + 5, $size_y + 5) or die ("Cannot Create image");
    $bg_color = imagecolorallocate($image, $bg[0], $bg[1], $bg[2]);
    $txt_color = imagecolorallocate($image, $color[0], $color[1], $color[2]);
    imagefill($image, 0, 0, $bg_color);
    imagettftext($image, $size, $angle, $pos_x, $pos_y, $txt_color, $font, $string);
    #imagecolortransparent($image, $bg_color);
    imagepng($image);
    imagedestroy($image);
}
$_SESSION['IkoBB_Captcha'] = md5($string);