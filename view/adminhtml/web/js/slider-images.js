define([
    'jquery',
    'underscore',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'baseImage',
    'prototype'
], function ($, _, mageTemplate, registry) {
    'use strict';

    function bytesToSize(bytes)
    {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'], i;

        if (bytes === 0) {
            return '0 Byte';
        }

        i = window.parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }

    $.widget('mage.sliderImages', {
        options: {
            imageSelector: '[data-role=image]',
            imageElementSelector: '[data-role=image-element]',
            template: '[data-template=image]',
            imageResolutionLabel: '[data-role=resolution]',
            imgTitleSelector: '[data-role=img-title]',
            imageSizeLabel: '[data-role=size]',
            initialized: false
        },

        _create: function () {
            this.options.images = this.options.images || this.element.data('images');
            this.options.parentComponent = this.options.parentComponent || this.element.data('parent-component');

            this.imgTmpl = mageTemplate(this.element.find(this.options.template).html().trim());

            this._bind();

            $.each(this.options.images, $.proxy(function (index, imageData) {
                this.element.trigger('addItem', imageData);
            }, this));

            this.options.initialized = true;
        },

        _bind: function () {
            this._on({
                addItem: '_addItem',
                removeItem: '_removeItem',

                'mouseup [data-role=delete-button]': function (event) {
                    var $imageContainer;

                    event.preventDefault();
                    $imageContainer = $(event.currentTarget).closest(this.options.imageSelector);
                    this.element.trigger('removeItem', $imageContainer.data('imageData'));
                },
            });
        },

        _contentUpdated: function () {
            if (this.options.initialized && this.options.parentComponent) {
                registry.async(this.options.parentComponent)(
                    function (parentComponent) {
                        parentComponent.bubble('update', true);
                    }
                );
            }
        },

        _addItem: function (event, imageData) {
            var count = this.element.find(this.options.imageSelector).length,
                element,
                imgElement;

            let isNew = true;
            if (imageData['image_id']) {
                isNew = false;
            }

            imageData = $.extend({
                'image_id': imageData['image_id'] ? imageData['image_id'] : Math.random().toString(33).substr(2, 18),
                'is_new': isNew,
                sizeLabel: bytesToSize(imageData.size)
            }, imageData);

            element = this.imgTmpl({
                data: imageData
            });

            element = $(element).data('imageData', imageData);

            if (count === 0) {
                element.prependTo(this.element);
            } else {
                element.insertAfter(this.element.find(this.options.imageSelector + ':last'));
            }

            imgElement = element.find(this.options.imageElementSelector);

            imgElement.on('load', this._updateImageDimesions.bind(this, element));

            this._contentUpdated();
        },

        _removeItem: function (event, imageData) {
            var $imageContainer = this.findElement(imageData);

            imageData.isRemoved = true;
            $imageContainer.addClass('removed').hide().find('.is-removed').val(1);

            this._contentUpdated();
        },

        _updateImageDimesions: function (imgContainer) {
            var $img = imgContainer.find(this.options.imageElementSelector)[0],
                $dimens = imgContainer.find('[data-role=image-dimens]');

            $dimens.text($img.naturalWidth + 'x' + $img.naturalHeight + ' px');
        },

        findElement: function (data) {
            return this.element.find(this.options.imageSelector).filter(function () {
                return $(this).data('imageData').image_id === data.image_id;
            }).first();
        },
    });

    return $.mage.sliderImages;
});
