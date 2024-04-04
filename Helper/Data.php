<?php

namespace Riverstone\ImageSlider\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    // phpcs:ignore
    const SLIDER_EXTENSION_ENABLED = 'imageslider/general/enable';

    /**
     * Config
     *
     * @var ScopeConfigInterface
     */

    protected $scopeConfig;
    /**
     * UrlInterface
     *
     * @var UrlInterface
     */

    protected $url;

    /**
     * Constructor parameter
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeInterface
     * @param UrlInterface $url
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeInterface,
        UrlInterface $url
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeInterface;
        $this->url = $url;
    }

    /**
     * Module status
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool)$this->getConfigValue(self::SLIDER_EXTENSION_ENABLED);
    }
    // phpcs:ignore
    /**
     * Config value
     *
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
