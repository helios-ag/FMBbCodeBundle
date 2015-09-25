<?php

namespace FM\BbcodeBundle\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseHandler;
use Composer\Script\CommandEvent;

/**
 * @author Al Ganiev, original from LiipMonitorBundle by @pulse00
 * Composer ScriptHandler can be used to run postInstall/postUpdate dump emoticons
 * when running composer.phar update/install.
 */
class ScriptHandler extends BaseHandler
{
    public static function installEmoticons(CommandEvent $event)
    {
        $options = self::getOptions($event);
        // use Symfony 3.0 dir structure if available
        $consoleDir = isset($options['symfony-bin-dir']) ? $options['symfony-bin-dir'] : $options['symfony-app-dir'];
        $event->getIO()->write('<info>Dumping emoticons...</info>');
        static::executeCommand($event, $consoleDir, 'bbcode:dump');
    }
}
