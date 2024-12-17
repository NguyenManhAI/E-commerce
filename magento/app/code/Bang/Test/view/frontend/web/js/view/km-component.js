define([
    'uiComponent'
], function (Component) {
    "use strict";
 
    return Component.extend({
        defaults: {
            title: 'KM Template',
            content: 'Lorem ipsum is a placeholder text.',
            btnText: 'Click',
            isPrimary: true
        }
    });
});