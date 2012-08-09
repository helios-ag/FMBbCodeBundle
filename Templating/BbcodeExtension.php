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
    protected $filter_sets;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $extra_filters = $this->container->getParameter('fm_bbcode.config.filters');
        $extra_hooks   = $this->container->getParameter('fm_bbcode.config.hooks');
        $extra_templatePaths = $this->container->getParameter('fm_bbcode.config.templates');
        $this->filter_sets = $this->container->getParameter('fm_bbcode.filter_sets');

        foreach($extra_filters as $extra_filter){
            DecodaManager::add_filter($extra_filter['classname'], $extra_filter['class'] );
        }
        foreach($extra_hooks as $extra_hook){
            DecodaManager::add_hook($extra_hook['classname'], $extra_hook['class'] );
        }
        foreach($extra_templatePaths as $extra_path){
            DecodaManager::add_templatePath($extra_path['path']);
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

        $messages = empty($messages) ? array() : json_decode(\file_get_contents($messages), true);

        $code = new Decoda($value,$messages);

        $current_filter = $this->filter_sets[$filter];

        $locale = $current_filter['locale'];
        $xhtml = $current_filter['xhtml'];

        if (empty($locale)) {
            // apply locale from the session
            if ('default' == $this->locale) {
                $code->setLocale($this->container->get('session')->getLocale());
                // apply locale defined in the configuration
            } else {
                // apply locale from the template
                $code->setLocale($this->locale);
            }
        } else {
            $code->setLocale($locale);
        }

        if (true === $xhtml) {
            $code->setXhtml(true);
        }

        $decoda_manager = new DecodaManager($code, $current_filter['filters'], $current_filter['hooks'], $current_filter['whitelist']);

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