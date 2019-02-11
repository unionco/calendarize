import { onInit as IfInit } from './modules/if';
import { onInit as MonthSelectInit } from './modules/month-select';
import { onInit as ListSwitchInit } from './modules/light-switch';
import { dateExceptionInit, timeExceptionInit } from './modules/exceptions';

class Calendarize {
    constructor(namespaceId) {
        const context = document.getElementById(`${namespaceId}-field`);
        
        this.context = context;
        
        ListSwitchInit(context);
        IfInit(context);
        MonthSelectInit(context);
        
        dateExceptionInit(context);
        timeExceptionInit(context);
    }
}

;(function(window) {
    window.Calendarize = Calendarize;
})(window);