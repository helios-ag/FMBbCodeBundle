<?php

namespace FM\BbcodeBundle\Composer;

use Symfony\Component\Process\Process;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseHandler;

/**
 * @author Al Ganiev, original from LiipMonitorBundle by @pulse00
 * Composer ScriptHandler can be used to run postInstall/postUpdate dump emoticons
 * when running composer.phar update/install.
 */
class ScriptHandler extends BaseHandler
{
    public static function installEmoticons($event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];
        $event->getIO()->write('<info>Dumping emoticons...</info>');
        static::executeCommand($event, $appDir, 'bbcode:dump');
    }

}
