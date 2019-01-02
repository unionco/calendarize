import InitIf from './modules/if';
import InitUse from './modules/use';
import InitLightswitch from './modules/light-switch';
import InitMonthSelect from './modules/month-select';
import { dateExceptionInit, timeExceptionInit } from './modules/exceptions';

window.onload = () => {
    InitIf();
    InitUse();
    InitLightswitch();
    InitMonthSelect();
    
    dateExceptionInit();
    timeExceptionInit();
};