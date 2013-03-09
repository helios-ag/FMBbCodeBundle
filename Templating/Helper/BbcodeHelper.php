<?php

namespace FM\BbcodeBundle\Templating\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;
use FM\BbcodeBundle\Decoda\Decoda as Decoda;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbcodeHelper extends Helper
{
    protected $container;
    protected $filter_sets;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $extra_filters = $this->container->getParameter('fm_bbcode.config.filters');
        $extra_hooks = $this->container->getParameter('fm_bbcode.config.hooks');
        $extra_templatePaths = $this->container->getParameter('fm_bbcode.config.templates');
        $this->filter_sets = $this->container->getParameter('fm_bbcode.filter_sets');

        foreach ($extra_filters as $extra_filter) {
            if (strpos($extra_filter['class'], '@') === 0) {
                $extra_filter_class = $this->container->get(substr($extra_filter['class'], 1));
            } else {
                $extra_filter_class = new $extra_filter['class']();
            }
            DecodaManager::addFilter($extra_filter['classname'], $extra_filter_class);
        }
        foreach ($extra_hooks as $extra_hook) {
            if (strpos($extra_hook['class'], '@') === 0) {
                $extra_hook_class = $this->container->get(substr($extra_hook['class'], 1));
            } else {
                $extra_hook_class = new $extra_hook['class']();
            }

            DecodaManager::addHook($extra_hook['classname'], $extra_hook_class);
        }
        foreach ($extra_templatePaths as $extra_path) {
            $path = $extra_path['path'];
            $path = $this->container->get('file_locator')->locate($path);
            DecodaManager::addTemplatePath($path);
        }
    }

    /**
     * @param $value
     * @param $filter
     * @throws \Twig_Error_Runtime
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    public function filter($value, $filter)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        $messagesPath = $this->container->getParameter('fm_bbcode.config.messages');

        $emoticonsWebFolder = $this->container->getParameter('fm_bbcode.config.emoticonpath');
        $extraEmoticonPath = $this->container->getParameter('fm_bbcode.config.extraemoticonpath');

        if (!empty($messagesPath)) {
            $messagesPath = $this->container->get("file_locator")->locate($messagesPath);
            $messages = json_decode(\file_get_contents($messagesPath), true);
        } else {
            $messages = array();
        }

        $code = new Decoda($value, $messages);

        $currentFilter = $this->filter_sets[$filter];


        $locale = $currentFilter['locale'];
        $isXhtml = $currentFilter['xhtml'];
        $isStrict = $currentFilter['strict'];

        if (empty($locale) || 'default' == $locale) {
            $code->setLocale($this->container->get('request')->getLocale());
        } else {
            $code->setLocale($locale);
        }

        $code->setXhtml($isXhtml);
        $code->setStrict($isStrict);

        $decodaManager = new DecodaManager($code,
                                           $currentFilter['filters'],
                                           $currentFilter['hooks'],
                                           $currentFilter['whitelist'],
                                           $emoticonsWebFolder,
                                           $extraEmoticonPath
        );

        return $decodaManager->getResult()->parse();
    }

    public function getName()
    {
        return 'fm_bbcode';
    }

}
