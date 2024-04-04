require([
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'mage/translate'
], function (validator, $) {
    validator.addRule(
        'validate-slider-identifier',
        function (value) {
            var regexp = /^[a-z0-9-_]+$/;
            return value.search(regexp) !== -1;
        },
        $.mage.__('Only lower case letters, numbers and hyphen are allowed')
    );
});
