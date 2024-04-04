<?php

namespace Riverstone\ImageSlider\Model\ResourceModel\SliderImage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Riverstone\ImageSlider\Model\SliderImage as Model;
use Riverstone\ImageSlider\Model\ResourceModel\SliderImage as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Model and Resource Model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(Model::class, ResourceModel::class);
    }
}
