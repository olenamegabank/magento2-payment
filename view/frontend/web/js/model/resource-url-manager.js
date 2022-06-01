define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager'
    ],
    function ($, resourceUrlManager) {
        "use strict";

        return $.extend({
            getUrlForMegaBankWidget: function (quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/guest-megabank-payment/:cartId/widget',
                    'customer': '/megabank-payment/mine/widget'
                };
                return this.getUrl(urls, params);
            },
            getUrlForMegaBankCheck: function (quote) {
                var params = (this.getCheckoutMethod() == 'guest') ? {cartId: quote.getQuoteId()} : {};
                var urls = {
                    'guest': '/guest-megabank-payment/:cartId/check',
                    'customer': '/megabank-payment/mine/check'
                };
                return this.getUrl(urls, params);
            }
        }, resourceUrlManager);
    }
);
