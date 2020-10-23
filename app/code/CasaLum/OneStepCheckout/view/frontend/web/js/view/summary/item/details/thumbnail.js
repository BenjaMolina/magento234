define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/item/details/thumbnail'
    ],
    function (ko, Component) {
        return Component.extend({
            defaults: {
                template: 'CasaLum_OneStepCheckout/summary/item/details/thumbnail'
            },
            isShowImage: ko.observable(window.checkoutConfig.enable_items_image)
        });
    }
);
