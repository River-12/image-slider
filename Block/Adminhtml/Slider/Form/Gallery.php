<?php

namespace Riverstone\ImageSlider\Block\Adminhtml\Slider\Form;

use Magento\Framework\View\Element\AbstractBlock;
use Riverstone\ImageSlider\Block\Adminhtml\Slider\Form\Gallery\Content;

class Gallery extends AbstractBlock
{
    protected $htmlId = 'slider_gallery';

    protected $formName = 'slider_form';

    protected $name = "slider";

    /**
     * Media Gallery
     *
     * @return string
     */
    public function toHtml()
    {
        /* @var $content Content */
        $content = $this->getChildBlock('content');
        $content->setId($this->htmlId . '_content')->setElement($this);
        $content->setFormName($this->formName);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMediaGallery($galleryJs);
        return $content->toHtml();
    }

    /**
     * Get Name
     *
     * @return mixed|string
     */
    public function getName()
    {
        return $this->name;
    }
}
