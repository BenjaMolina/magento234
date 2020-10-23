/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver'
], function (ko, checkoutDataResolver) {
    'use strict';

    var shippingRates = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),

        /**
         * Set shipping rates
         *
         * @param {*} ratesData
         */
        setShippingRates: function (ratesData) {
            shippingRates(ratesData);
            shippingRates.valueHasMutated();
            checkoutDataResolver.resolveShippingRates(ratesData);
            /**Forzamos a que los inputs no queden desactivados */
            this.forceEnabledShippingInputs(ratesData);
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getShippingRates: function () {
            return shippingRates;
        },

        /**
         * FIXED issue for inputs Shipping Methods Disabled when select 
         * State and Zip code at the same time
         * https://github.com/magento/magento2/issues/7497
         */
        forceEnabledShippingInputs: function(ratesData){
            var container = document.getElementById('co-shipping-method-form');
            if (container) {
                var inputs = container.getElementsByTagName('input');
        
                ratesData.forEach(function(rate) {
                    if (rate.available) {
                        var name = rate.carrier_code + '_' + rate.method_code;
                        for (var i = 0; i < inputs.length; i++) {
                            if (inputs[i].value === name) {
                                inputs[i].removeAttribute('disabled');
                            }
                        }
                    }
                });
            }
        }
    };
});
