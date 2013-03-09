<?php

namespace FM\BbcodeBundle\Templating;

use FM\BbcodeBundle\Translation\Loader\FileLoader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use FM\BbcodeBundle\Decoda\Decoda as Decoda;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbcodeExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var mixed
     */
    protected $filterSets;

    /**
     * @var array
     */
    protected $messageTable;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, FileLoader $loader)
    {
        $this->container = $container;

        $messagesPath = $this->container->getParameter('fm_bbcode.config.messages');
        $this->messageTable = $loader->load($messagesPath);

        $extraFilters = $this->container->getParameter('fm_bbcode.config.filters');
        $extraHooks   = $this->container->getParameter('fm_bbcode.config.hooks');
        $extraTemplatePaths = $this->container->getParameter('fm_bbcode.config.templates');
        $this->filterSets = $this->container->getParameter('fm_bbcode.filter_sets');

        foreach ($extraFilters as $extraFilter) {
            DecodaManager::addFilter($extraFilter['classname'], $extraFilter['class'] );
        }
        foreach ($extraHooks as $extraHook) {
            DecodaManager::addHook($extraHook['classname'], $extraHook['class'] );
        }
        foreach ($extraTemplatePaths as $extraPath) {
            $path = $extraPath['path'];
            $path = $this->container->get('file_locator')->locate($path);
            DecodaManager::addTemplatePath($path);
        }

    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     * @return array
     */
    public function getFilters()
    {
        return array(
            'bbcode_filter' => new \Twig_Filter_Method($this, 'filter', array('is_safe' => array('html'))),
            'bbcode_clean' => new \Twig_Filter_Method($this, 'clean', array('is_safe' => array('html')))
        );
    }

    /**
     * @param $value
     * @param $filter
     * @return string
     * @throws \Twig_Error_Runtime
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    public function filter($value, $filter)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        $emoticonsWebFolder = $this->container->getParameter('fm_bbcode.config.emoticonpath');
        $extraEmoticonPath = $this->container->getParameter('fm_bbcode.config.extraemoticonpath');

        $code = new Decoda($value, $this->messageTable);

        $currentFilter = $this->filterSets[$filter];

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

    /**
     *
     * Strip tags
     * @param $value
     * @return string
     * @throws \Twig_Error_Runtime
     */
    public function clean($value)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        $code = new Decoda($value);

        $emoticonsWebFolder = $this->container->getParameter('fm_bbcode.config.emoticonpath');

        $filters = array('default','block','code','email','image','list','quote','text','url','video');

        $decodaManager = new DecodaManager($code,
                                           $filters,
                                           array(),
                                           array(),
                                           $emoticonsWebFolder
        );

        return $decodaManager->getResult()->strip(true);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'fm_bbcode';
    }
}
