define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Customer/js/model/customer',
        'mage/url'
    ],
    function (
        ko,
        $,
        Component,
        _,
        quote,
        totalsDefaultProvider,
        rateReg,
        customer,
        url
    ) {
        'use strict';
        /**
         * check-login - is the name of the component's .html template
         */
        return Component.extend({
            defaults: {
                template: 'MageMango_CustomShippingAmount/amount'
            },

            /**
             *
             * @returns {*}
             */
            initialize: function () {
                this._super();
                return this;
            },

            shippingChange: function(obj, event){
                var address = quote.shippingAddress();
                var AjaxUrl = "http://202.131.107.107:8013/abm/pub/amount/amount/index";

                var data = {
                    'type' : event.target.value
                };
                $.ajax({
                    showLoader: true,
                    url: AjaxUrl,
                    data: data,
                    type: "POST",
                    dataType: 'json'
                }).done(function (response) {
                    rateReg.set(address.getKey(), null);
                    rateReg.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                    totalsDefaultProvider.estimateTotals(quote.shippingAddress());
                }).fail(function (response){
                });
            },
            /**
             * @return {String}
             */
            getCheckoutMethod: function () {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        });
    }
);