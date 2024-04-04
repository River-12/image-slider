<?php

namespace Riverstone\ImageSlider\Ui\Component\Form\Slider\Modifier;

use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Slider implements ModifierInterface
{
    /**
     * Get Configuration for ui component

     * @return $meta
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'instruct' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('How to Render'),
                                'componentType' => Fieldset::NAME,
                                'collapsible' => true,
                                'sortOrder' => 100
                            ],
                        ],
                    ],
                    'children' => [
                        'instruct' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Different ways to render'),
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'elementTmpl' => 'Riverstone_ImageSlider/instruction.html',
                                        'dataScope' => 'identifier',
                                        'dataType' => 'text',
                                        'sortOrder' => 10,
                                        'visible' => true
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        );
        return $meta;
    }
    /**
     * Get data

     * @return $data
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
