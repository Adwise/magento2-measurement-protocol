define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict'

    return function (payload) {
        return wrapper.wrap(payload, function (originalAction, payloadData) {
            payloadData = originalAction(payloadData);
            if (typeof (payloadData.addressInformation['extension_attributes']) !== 'object') {
                payloadData.addressInformation['extension_attributes'] = {};
            }

            if (typeof ga !== 'function') {
                return originalAction(payloadData);
            }

            if (!Object.prototype.hasOwnProperty.call(ga, 'getAll')) {
                return originalAction(payloadData);
            }

            var trackers = ga.getAll();
            var clientId;

            for (var i = 0; i < trackers.length; i++) {
                if (trackers[i].get('trackingId') === window.checkoutConfig.tracking_id) {
                    clientId = trackers[i].get('clientId');
                }
            }

            if (clientId) {
                var gaClientId = {
                    'ga_client_id': clientId
                }

                payloadData.addressInformation.extension_attributes = $.extend(payloadData.addressInformation.extension_attributes, gaClientId)
            }
            return payloadData
        });
    }
})
