define([
    'jquery',
    'mage/utils/wrapper'
],function ($, Wrapper) {
    "use strict";

    return function (origRules)
    {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction)
            {
                var fields = originalAction();
                fields.push('shipping_amount');
                return fields;
            }
        );

        return origRules;
    };
});