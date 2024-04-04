<?php

namespace Riverstone\ImageSlider\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Riverstone\ImageSlider\Model\ResourceModel\Slider\CollectionFactory;

class SliderLists implements OptionSourceInterface
{
    /**
     * Slider Factory
     *
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $sliders = [];
        $lists = [];
        $sliderCollections = $this->collectionFactory->create();
        foreach ($sliderCollections as $collection) {
            $collectionData = $collection->getData();
            if (isset($collectionData['id']) && isset($collectionData['name'])) {
                $lists['value'] = $collectionData['identifier'];
                $lists['label'] = $collectionData['name'];
                array_push($sliders, $lists);
            }
        }
        return $sliders;
    }
}
