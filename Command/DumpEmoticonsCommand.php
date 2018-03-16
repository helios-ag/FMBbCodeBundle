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
            ->setDescription('Dumps the emoticons to their configured folder')
            ->addOption('emoticons-folder', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    /**
     * Copies one folder to another.
     *
     * @param string $src
     * @param string $dst
     */
    private function recurseCopy(string $src, string $dst)
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webFolder = sprintf('%s%s',
            $this->getContainer()->getParameter('fm_bbcode.public_path'),
            $this->getContainer()->getParameter('fm_bbcode.emoticon.path')
        );
        @mkdir($webFolder);

        $emoticonsFolder = $input->getOption('emoticons-folder');
        if (!$emoticonsFolder) {
            $emoticonsFolder = $this->getContainer()->getParameter('fm_bbcode.emoticon.folder');
        }

        if (!file_exists($emoticonsFolder) && !is_dir($emoticonsFolder)) {
            $output->writeln('<error>Emoticons folder does not exist</error>');
            return 2; // ENOENT - No such file or directory
        }

        $this->recurseCopy($emoticonsFolder, $webFolder);

        $output->writeln('<comment>Emoticons dumped succesfully</comment>');

        return 0;
    }
}
