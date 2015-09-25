<?php

use Decoda\Decoda;
use Decoda\Hook\EmoticonHook;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;
use FM\BbcodeBundle\Emoticon\Emoticon;

// Convert a default decoda emoticons array to an EmoticonCollection
$collection = new EmoticonCollection();

$decoda = new Decoda();
$hook   = new EmoticonHook();
$hook->setParser($decoda);
$hook->startup();
$emoticons = $hook->getEmoticons();

foreach ($emoticons as $name => $smilies) {
    $emoticon = new Emoticon();
    foreach ($smilies as $smiley) {
        $emoticon->setSmiley($smiley);
    }
    $collection->add($name, $emoticon);
}

return  $collection;
