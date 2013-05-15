<?php

/* ------------------------------------------------------------------------------------ */


$image = new imageBuffer("http://blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg");
$image->thumb("fixed-w", 100, 100)->show();
$image->thumb("fixed-h", 100, 100)->show();


/* ------------------------------------------------------------------------------------ */


$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
$image->thumb("stretch", 100, 100)->show();


/* ------------------------------------------------------------------------------------ */


$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
$image->thumb("inner", 500, 500, "#330000")->show();


/* ------------------------------------------------------------------------------------ */


$image = new imageBuffer("http://blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg");
$image->thumb("crop", 500, 500, "center", "top")->show();
$image->thumb("crop", 500, 500, "center", "center")->show();
$image->thumb("crop", 500, 500, "center", "bottom")->show();
$image->thumb("crop", 500, 500, "center", "10%")->show();


/* ------------------------------------------------------------------------------------ */


$image = new imageBuffer("http://1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg");
$image->thumb("crop", 500, 500, "left", "center")->show();
$image->thumb("crop", 500, 500, "center", "center")->show();
$image->thumb("crop", 500, 500, "right", "center")->show();
$image->thumb("crop", 500, 500, "10%", "center%")->show();
