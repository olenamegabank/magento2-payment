define(
    [
        'ko',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'MegaBank_Payment/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/model/messageList',
        'jquery/jquery.cookie'
    ],
    function (ko,
              customerData,
              Component,
              $,
              quote,
              resourceUrlManager,
              storage,
              errorProcessor,
              url,
              fullScreenLoader,
              globalMessageList
    ) {
        'use strict';

        var cacheKey = 'megabank-payment-data',
            saveData = function (data) {
                customerData.set(cacheKey, data);
            },
            getData = function () {
                return customerData.get(cacheKey)();
            };

        return Component.extend({
            frameHtml: ko.observable(''),
            isPlaceOrderActionVisible: ko.observable(false),
            isPayButtonVisible: ko.observable(true),
            isFrameVisible: ko.observable(false),
            isSuccessVisible: ko.observable(false),
            cookieName: 'mb_result',
            cookieChecker: null,
            defaults: {
                template: 'MegaBank_Payment/payment/checkout'
            },

            initialize: function () {
                this._super();
                if (getData() && getData().transaction) {
                    this.hideAllBlocks();
                    this.isSuccessVisible(true);
                    this.isPlaceOrderActionVisible(true);
                }
            },
            getPlaceOrderDeferredObject: function () {
                var self = this;

                return this._super().fail(function (e) {
                    fullScreenLoader.stopLoader();
                    saveData({transaction: false});
                    self.hideAllBlocks();
                    self.isPayButtonVisible(true);
                });
            },

            placeOrder: function () {
                if (!getData() || !getData().transaction) {
                    this.initIframe();
                    return;
                }
                var self = this;
                var superFunc = self._super.bind(this);
                fullScreenLoader.startLoader();

                $.ajax({
                    url: url.build(resourceUrlManager.getUrlForMegaBankCheck(quote)),
                    type: 'POST',
                    context: this,
                    contentType: 'application/json'
                }).done(
                    function (response) {
                        if (response) {
                            response = JSON.parse(response);
                            if (response.success) {
                                saveData({transaction: false});
                                superFunc();
                            } else {
                                saveData({transaction: false});
                                self.showError(response.message, true);
                            }
                        } else {
                            self.showError('', true);
                        }
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        fullScreenLoader.stopLoader();
                    }
                );
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },
            initIframe: function () {
                var self = this;
                fullScreenLoader.startLoader();
                storage.post(
                    resourceUrlManager.getUrlForMegaBankWidget(quote)
                ).done(
                    function (response) {
                        if (response) {
                            response = JSON.parse(response);
                            if (response.widget) {
                                self.frameHtml(response.widget);
                                self.hideAllBlocks();
                                self.isFrameVisible(true);
                                self.checkCookie(self.onCookieChange.bind(self));
                            } else if (response.message) {
                                self.showError(response.message, true);
                            }
                        } else {
                            self.showError();
                        }
                    }
                ).fail(
                    function (response) {

                        errorProcessor.process(response);
                    }
                ).always(
                    function () {
                        fullScreenLoader.stopLoader();
                    }
                );
            },
            onCookieChange: function () {
                var data = $.cookie(this.cookieName);
                if (data) {
                    data = JSON.parse(decodeURIComponent(decodeURI(data)));
                    clearInterval(this.cookieChecker);
                    this.delCookie(this.cookieName);
                    if (data.error) {
                        this.showError(data.error, true);
                    } else if (data.transaction) {
                        saveData({transaction: data.transaction});
                        this.hideAllBlocks();
                        this.isSuccessVisible(true);
                        this.isPlaceOrderActionVisible(true);
                        this.placeOrder();
                    }
                }
            },
            delCookie: function (name) {
                var date = new Date(0);
                document.cookie = name + '=' + '; path=/; expires=' + date.toUTCString();
            },
            checkCookie: function (callback, interval = 1000) {
                let lastCookie = document.cookie;
                this.cookieChecker = setInterval(function () {
                    let cookie = document.cookie;
                    if (cookie !== lastCookie) {
                        try {
                            callback(cookie);
                        } finally {
                            lastCookie = cookie;
                        }
                    }
                }, interval);
            },
            showError: function (text, isPayButtonVisible) {
                var messageContainer = this.messageContainer || globalMessageList;
                messageContainer.addErrorMessage({
                    message: text ? text : $.mage.__('Unexpected payment error. Please try pay again')
                });

                this.hideAllBlocks();
                if (isPayButtonVisible) {
                    this.isPayButtonVisible(isPayButtonVisible);
                }
            },
            hideAllBlocks: function () {
                this.isPlaceOrderActionVisible(false);
                this.isPayButtonVisible(false);
                this.isFrameVisible(false);
                this.isSuccessVisible(false);
            }
        });
    }
);
