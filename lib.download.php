<?php

/*
ob_start();

download($mediaUrl, 'show_status');

echo "Done";
ob_flush();
flush();
*/

function download($mediaUrl, $progress_callback = 'show_status')
{
  ob_start();
  echo "Downloading $mediaUrl ...\n";

  $mediaUrlParts = explode('/', $mediaUrl);
  $mediaName = array_pop($mediaUrlParts);
  $out = fopen("./$mediaName", 'wb');

  ob_flush();
  flush();

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "$mediaUrl");
  //curl_setopt($ch, CURLOPT_BUFFERSIZE,128);
  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FILE, $out);
  curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progress_callback);
  curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
  curl_setopt($ch, CURLOPT_HEADER, 0);
  if(isset($_SERVER['HTTP_USER_AGENT']))
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  $html = curl_exec($ch);
  curl_close($ch);
}

function show_status($resource,$total, $done, $upload_size, $uploaded, $size=30) {

    static $start_time;
    static $finish;

    // if we go over our bound, just ignore it
    if($done > $total || $finish) return;

    if(empty($start_time)) $start_time=time();
    $now = time();

    $perc = 0;
    if($total>0)
      $perc=(double)($done/$total);

    $bar=floor($perc*$size);

    $status_bar="\r[";
    $status_bar.=str_repeat("=", $bar);
    if($bar<$size){
        $status_bar.=">";
        $status_bar.=str_repeat(" ", $size-$bar);
    } else {
        $status_bar.="=";
    }

    $disp=number_format($perc*100, 0);

    $status_bar.="] $disp%  $done/$total";

    $rate = 0;
    if($done>0)
      $rate = ($now-$start_time)/$done;

    $left = $total - $done;
    $eta = round($rate * $left, 2);

    $elapsed = $now - $start_time;

    $status_bar.= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

    echo "$status_bar  ";

    ob_flush();
    flush();

    // when done, send a newline
    if(!isset($finish) && $done > 0 && ($done == $total)) {
        $finish = true;
        echo "\n";
    }
}
