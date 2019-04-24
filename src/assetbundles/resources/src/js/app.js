import { onInit as IfInit } from './modules/if';
import { onInit as MonthSelectInit } from './modules/month-select';
import { onInit as ListSwitchInit } from './modules/light-switch';
import { dateExceptionInit, timeExceptionInit } from './modules/exceptions';
import { setLocale as momentSetLocale } from './util/helpers';

class Calendarize {
    constructor(namespaceId, locale) {
        const context = document.getElementById(`${namespaceId}-field`);
        this.context = context;

        Calendarize.setLocale(locale);

        ListSwitchInit(context);
        IfInit(context);
        MonthSelectInit(context);
        
        dateExceptionInit(context);
        timeExceptionInit(context);
    }

    static setLocale(locale) {
        this.locale = locale;
        momentSetLocale(this.locale);
    }

    static getLocale() {
        return this.locale;
    }
}

;(function(window) {
    window.Calendarize = Calendarize;
})(window);