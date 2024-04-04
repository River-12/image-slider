<?php

namespace Riverstone\ImageSlider\Model\ResourceModel\Slider;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Riverstone\ImageSlider\Model\Slider as Model;
use Riverstone\ImageSlider\Model\ResourceModel\Slider as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Model and resource Model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(Model::class, ResourceModel::class);
    }
}
