<?php

namespace FM\BbcodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
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
            ->setDescription('dump emoticons to public folder')
            ->addOption('emoticons-folder', null, InputOption::VALUE_OPTIONAL, null, null)
        ;
    }

    /**
     * Copies one folder to another.
     *
     * @param $src
     * @param $dst
     */
    private function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.'/'.$file)) {
                    $this->recurseCopy($src.'/'.$file, $dst.'/'.$file);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webFolder = sprintf('%s%s',
            $this->getContainer()->getParameter('assetic.write_to'),
            $this->getContainer()->getParameter('fm_bbcode.emoticon.path')
        );
        @mkdir($webFolder);

        $emoticonsFolder = $input->getOption('emoticons-folder');
        if (!$emoticonsFolder) {
            $emoticonsFolder = $this->getContainer()->getParameter('fm_bbcode.emoticon.folder');
        }

        if (!file_exists($emoticonsFolder) && !is_dir($emoticonsFolder)) {
            return $output->writeln('<error>Emoticons folder does not exist</error>');
        }

        $this->recurseCopy($emoticonsFolder, $webFolder);

        $output->writeln('<comment>Emoticons dumped succesfully</comment>');
    }
}
