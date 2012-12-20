<?php

namespace FM\BbcodeBundle\Templating;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
            $path = $this->container->get("kernel")->locateResource($path);
            DecodaManager::addTemplatePath($path);
        }

    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            'bbcode_filter' => new \Twig_Filter_Method($this, 'filter', array('is_safe' => array('html'))),
        );
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

        $messages = $this->container->getParameter('fm_bbcode.config.messages');
        
        if(!empty($messages))
        {
            $messages = $this->container->get("kernel")->locateResource($messages);
            $messages = json_decode(\file_get_contents($messages), true);
        }
        else
        {
            $messages = array();
        }

        $code = new Decoda($value, $messages);
        $currentFilter = $this->filterSets[$filter];
        $locale = $currentFilter['locale'];
        $xhtml = $currentFilter['xhtml'];
        $strict = $currentFilter['strict'];
        if (empty($locale) || 'default' == $locale) {
                $code->setLocale($this->container->get('request')->getLocale());
            }
        else {
            $code->setLocale($locale);
        }

        if (true === $xhtml) {
            $code->setXhtml(true);
        }

        $code->setStrict($strict);

        $decoda_manager = new DecodaManager($code, $currentFilter['filters'], $currentFilter['hooks'], $currentFilter['whitelist']);

        return $decoda_manager->getResult()->parse();
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
