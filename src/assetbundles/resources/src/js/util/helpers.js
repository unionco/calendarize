import moment from 'moment';

export default {};

export const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
export const prefixes = ['First', 'Second', 'Third', 'Fourth', 'Last'];

export const getDayName = (date) => {
    const d = new Date(date);
    return days[d.getDay()];
};

export const weekOfMonth = (date) => {
    const m = moment(date);
    let w = m.isoWeekday(7).week() - moment(m).startOf('month').isoWeekday(7).week() - 1;

    if (w < 0) {
        w = prefixes.length - 1;
    }
    return w;
}

export const weekAndDay = (date) => {
    const d = new Date(date);
    return prefixes[weekOfMonth(d)] + ' ' + days[d.getDay()];
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
    const date = new Date(value);
    const d = date.getDate();
    const e = nth(d);

    return `${d}${e}`;
};