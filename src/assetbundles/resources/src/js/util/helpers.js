import moment from 'moment';

export default {};

export const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
export const prefixes = ['First', 'Second', 'Third', 'Fourth', 'Last'];

export const setLocale = (locale) => {
    moment.locale(locale);
}

export const getDayName = (date) => {
    const d = new Date(date);
    return days[d.getDay()];
};

export const getLocalizedDayName = (mom) => {
    return mom.format('dddd');
};
export const weekOfMonth = (date) => {
    const m = moment(date);
    let w = m.isoWeekday(7).week() - moment(m).startOf('month').isoWeekday(7).week() - 1;

    if (w < 0) {
        w = prefixes.length - 1;
    }
    return w;
}

export const getLocalizeMoment = (dateString) => {
    if (dateString instanceof moment) {
        return dateString;
    }
    const currentLocaleData = moment.localeData();
    const localeFormat = currentLocaleData._longDateFormat.L;
    return moment(dateString, localeFormat);
}

export const weekIndex = (dateString) => {
    const date = getLocalizeMoment(dateString);
    const offsetDate = moment(date).date() - 1;
    const index = 1; // start index at 0 or 1, your choice
    const week = index + Math.floor(offsetDate / 7);
    
    return week;
}

export const weekAndDay = (dateString) => {
    const date = getLocalizeMoment(dateString);
    const week = weekIndex(date);
    const day = date.format('dddd');
    
    if (week === 1) {
        return `First ${day}`;
    }

    const isLastWeek = weekIndex(moment(date).add(1, 'week')) < week ? true : false;
    if (isLastWeek) {
        return `Last ${day}`;
    }

    return `${week}${nth(week)} ${day}`;
}

export const nth = (d) => {
    if (d > 3 && d < 21) return 'th';
    switch (d % 10) {
        case 1: return "st";
        case 2: return "nd";
        case 3: return "rd";
        default: return "th";
    }
}

export const getDateDay = (value) => {
    const currentLocaleData = moment.localeData();
    const localeFormat = currentLocaleData._longDateFormat.L;
    const mdate = moment(value, localeFormat);
    const d = mdate.date();
    const e = mdate.format('Do');

    return `${e}`;
};