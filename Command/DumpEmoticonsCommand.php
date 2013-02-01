<?php

namespace FM\BbcodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperInterface;
/**
*
* @author Al Ganiev <helios.ag@gmail.com>
* @copyright 2013 Al Ganiev
* @license http://www.opensource.org/licenses/mit-license.php MIT License
*/
class DumpEmoticonsCommand extends ContainerAwareCommand
{
    /**
    * @see Command
    */
    protected function configure()
    {
        $this
            ->setName('bbcode:dump')
            ->setDescription('dump emoticons to public folder');
    }

    /**
     * Copies one folder to another
     * @param $src
     * @param $dst
     */
    private function recurse_copy($src,$dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while(false !== ( $file = readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( is_dir($src . '/' . $file) ) {
                        $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webFolder = $this->getContainer()->get('kernel')->getRootDir().'/../web/emoticons';
        @mkdir($webFolder);
        $emoticonsFolder = $this->getContainer()->get('kernel')->getRootDir().'/../vendor/mjohnson/decoda/emoticons';
        $this->recurse_copy($emoticonsFolder,$webFolder);

        $output->writeln('<comment>Emoticons dumped succesfully</comment>');
    }

}