<?php

namespace Riverstone\ImageSlider\Model;

use Magento\Framework\Model\AbstractModel;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage as ResourceModel;

class SliderImage extends AbstractModel
{
    /**
     * Resource Model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
