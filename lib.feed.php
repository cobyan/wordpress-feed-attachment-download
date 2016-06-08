<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 08/06/16
 * Time: 07:34
 */

function getFeedUrl($Node)
{
    $event_href = $Node->attributes->getNamedItem('href')->value;
    return $event_href . 'feed';
}

function isFeedAvailable($feed)
{
    $ch = curl_init($feed);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $ret_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $ret_code == 301;
}

function getFeedItems($feed_url)
{
    $xml = simplexml_load_string(file_get_contents($feed_url));
    $json = json_encode($xml);
    $array = json_decode($json, true);
    if (!isset($array['channel']['item'])) die('feed empty');
    return $array['channel']['item'];
}