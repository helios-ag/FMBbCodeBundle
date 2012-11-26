<?php

namespace FM\BbcodeBundle\Decoda\Hook;

use mjohnson\decoda\hooks\EmoticonHook;

/**
 * Need to implement
 */
class EmoticonHook2 extends EmoticonHook
{

    public function setConfig(array $config) {
        $this->_config = $config;
    }

}