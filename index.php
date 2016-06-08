<?php

ini_set('memory_limit', -1);

//set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/vendor/google/apiclient/src');

require 'lib.feed.php';
require 'lib.download.php';

$DD = new DomDocument();
$DD->loadHTML(file_get_contents('./event-list.one.html'));
$DomNodeList = $DD->getElementsByTagName('a');

foreach ($DomNodeList as $Node) {
    /**
     * @var $Node DomNode
     */
    $feed_url = getFeedUrl($Node);
    if (isFeedAvailable($feed_url)) {
        echo $feed_url . ' [OK] ' . "\n";

        $feed_items = getFeedItems($feed_url);

        foreach ($feed_items as $feed_item_index => $feed_item) {
            $video_uri = $feed_item['enclosure']['@attributes']['url'];
            download($video_uri);
            die();
        }

    }

}


