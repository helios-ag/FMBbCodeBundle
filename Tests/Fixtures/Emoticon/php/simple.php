<?php

use FM\BbcodeBundle\Emoticon\EmoticonCollection;
use FM\BbcodeBundle\Emoticon\Emoticon;

$collection = new EmoticonCollection();

$emoticon = new Emoticon();
$emoticon->addSmilies(array(':foo:'));

$collection->add('foo', $emoticon);

return  $collection;
