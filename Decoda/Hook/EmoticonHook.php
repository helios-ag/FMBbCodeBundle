<?php

namespace FM\BbcodeBundle\Decoda\Hook;

use mjohnson\decoda\hooks\EmoticonHook;

class EmoticonHook2 extends EmoticonHook
{

    public function setConfig(array $config) {
        $this->_config = $config;
    }

}