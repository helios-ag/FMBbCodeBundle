<?php

namespace FM\BbCodeBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Decoda;
/**
 * Twig extension providing useful array handling filters.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbCodeHelperExtension extends \Twig_Extension {

    protected $locale;
    
    protected $xhtml;

    protected $default;
    protected $block;
    protected $code;
    protected $email;
    protected $image;
    protected $list;
    protected $quote;
    protected $text;
    protected $url;
    protected $video;

    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->locale  = $container->getParameter('fm_bb_code.locale');
        $this->xhtml  = $container->getParameter('fm_bb_code.xhtml');
        $this->default = $container->getParameter('fm_bb_code.filters.default');
        $this->block   = $container->getParameter('fm_bb_code.filters.block');
        $this->code    = $container->getParameter('fm_bb_code.filters.code');
        $this->email   = $container->getParameter('fm_bb_code.filters.email');
        $this->image   = $container->getParameter('fm_bb_code.filters.image');
        $this->list    = $container->getParameter('fm_bb_code.filters.list');
        $this->quote   = $container->getParameter('fm_bb_code.filters.quote');
        $this->text    = $container->getParameter('fm_bb_code.filters.text');
        $this->url     = $container->getParameter('fm_bb_code.filters.url');
        $this->video   = $container->getParameter('fm_bb_code.filters.video');
    }

	/**
	 * @var string
	 */
	protected $BBCodeAlias = null;

	/**
	 * @param string $withoutAlias Alias for the without filter.
	 */
	public function setAlias($BBCodeAlias = null) {
		if (!empty($BBCodeAlias)) {
			$this->BBCodeAlias = $BBCodeAlias;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'BBCodeHelper';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFilters() {
		$filters = array();

		$BBCodeMethod = new \Twig_Filter_Method($this, 'BBCode',array('is_safe' => array('html')));
		$filters['BBCode'] = $BBCodeMethod;
		if (!empty($this->BBCodeAlias)) {
			$filters[$this->BBCodeAlias] = $BBCodeMethod;
		}
		return $filters;
	}

	/**
	 * @param mixed $entries All entries.
	 * @param mixed $without Entries to be removed.
	 * @return array Remaining entries of {@code $value} after removing the entries of {@code $without}.
	 */
	public function BBCode($value) {
		if (!is_string($value)) {
			throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
		}

		$code = new Decoda($value);
        $code->setLocale($this->locale);
        
        if (true === $this->xhtml) {
			$code->setXhtml(true);
		}

        if ($this->default=='enabled')
            $code->addFilter(new \DefaultFilter());
        if ($this->block=='enabled')
            $code->addFilter(new \BlockFilter());
        if ($this->code=='enabled')
            $code->addFilter(new \CodeFilter());
        if ($this->email=='enabled')
            $code->addFilter(new \EmailFilter());
        if ($this->image=='enabled')
            $code->addFilter(new \ImageFilter());
        if ($this->list=='enabled')
            $code->addFilter(new \ListFilter());
        if ($this->quote=='enabled')
            $code->addFilter(new \QuoteFilter());
        if ($this->text=='enabled')
            $code->addFilter(new \TextFilter());
        if ($this->url=='enabled')
            $code->addFilter(new \UrlFilter());
        if ($this->video=='enabled')
            $code->addFilter(new \VideoFilter());

		return $code->parse(true);
	}

} 