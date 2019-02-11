import {
    weekAndDay,
    getDateDay
} from '../util/helpers';

export default {};
export function onInit(context) {
    context.querySelectorAll('[data-monthly-select]').forEach((node) => {
        const selector = node.getAttribute('data-monthly-select');
        const dateField = context.querySelector(`#${selector}`);
        const select = node.querySelector('select');

        dateField.addEventListener('custom', (e) => {
            const dateValue = dateField.value;
            const optOneValue = getDateDay(dateValue);
            const optTwoValue = weekAndDay(dateValue);

            select.querySelectorAll('option')[0].innerHTML = optOneValue;
            select.querySelectorAll('option')[1].innerHTML = optTwoValue;
        });
    });
}