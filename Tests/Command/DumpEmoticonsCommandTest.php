<?php

namespace FM\BbcodeBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use FM\BbcodeBundle\Command\DumpEmoticonsCommand;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class DumpEmoticonsCommandTest extends \PHPUnit_Framework_TestCase
{
    private $rootDir;
    private $webDir;
    private $emoticonPath;
    private $emoticonFolder;

    public function setUp()
    {
        $this->rootDir = __DIR__.'/..';
        $this->webDir  = sys_get_temp_dir().'/symfonyFMBbcodeweb';
        if (!file_exists($this->webDir)) {
            mkdir($this->webDir);
        }
        $this->emoticonPath   = '/emoticons';
        $this->emoticonFolder = $this->rootDir.'/../vendor/mjohnson/decoda/emoticons';
    }

    public function tearDown()
    {
        if (!is_dir($this->webDir)) {
            return;
        }
        $this->removeDirectory($this->webDir);
    }

    protected function removeDirectory($directory)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $path) {
            if (preg_match('#[/\\\\]\.\.?$#', $path->__toString())) {
                continue;
            }
            if ($path->isDir()) {
                rmdir($path->__toString());
            } else {
                unlink($path->__toString());
            }
        }
        @rmdir($directory);
    }

    public function testExecute()
    {
        $webDir         = $this->webDir;
        $emoticonPath   = $this->emoticonPath;
        $rootDir        = $this->rootDir;
        $emoticonFolder = $this->emoticonFolder;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('getParameter')
            ->withAnyParameters()
            ->will($this->returnCallback(function ($v) use ($webDir, $emoticonPath, $rootDir, $emoticonFolder) {
                switch ($v) {
                    case 'assetic.write_to':
                        return $webDir;
                    case 'fm_bbcode.emoticon.path':
                        return $emoticonPath;
                    case 'fm_bbcode.emoticon.folder':
                        return $emoticonFolder;
                    case 'kernel.root_dir':
                        return $rootDir;
                    default:
                        throw new \RuntimeException(sprintf('Unknown parameter "%s".', $v));
                }
            }))
        ;

        $command = new DumpEmoticonsCommand();
        $command->setContainer($container);

        $tester = new CommandTester($command);
        $tester->execute(array());

        $this->assertFileExists($this->webDir.$this->emoticonPath);
        $this->assertEquals('Emoticons dumped succesfully'.PHP_EOL, $tester->getDisplay());
    }
}
