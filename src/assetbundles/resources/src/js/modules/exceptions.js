import { getLocalizeMoment, getLocalizedDayName } from '../util/helpers';

// helper method
const removeChild = (item, child) => {
    item.removeChild(child);
};

class CalendarException {
    constructor(node, attr, withTime = false) {
        this.node = node;
        this.withTime = withTime;

        // dom setup
        this.dateField = node.querySelector('.hasDatepicker');
        this.hiddenName = this.dateField.getAttribute('name');
        this.dateField.removeAttribute('name');
        node.querySelector('input[type="hidden"]').removeAttribute('name');

        this.listing = node.querySelector('.exceptions-list');
        this.listingItems = this.listing.querySelectorAll('li');

        if (withTime) {
            this.timeField = node.querySelector('.ui-timepicker-input');
            this.trigger = node.querySelector('[data-trigger]');
            
            const datetimewrapper = node.querySelector('.datetimewrapper');
            datetimewrapper.querySelectorAll('input').forEach(node => node.removeAttribute('name'));

            this.addEventListener(this.trigger, 'click');
        } else {
            this.addEventListener(this.dateField, 'custom');
        }

        if (this.listingItems.length) {
            this.listingItems.forEach((item) => {
                const close = item.querySelector('[data-icon]');
                close.addEventListener('click', () => {
                    removeChild(this.listing, item);
                })
            })
        }
    }

    addEventListener(node, action = 'click') {
        node.addEventListener(action, (e) => {
            const li = document.createElement('li');
            const p = document.createElement('p');
            const length = this.listing.querySelectorAll('li').length;

            // remove icon
            const close = document.createElement('div');
            close.setAttribute('data-icon', 'remove');
            close.addEventListener('click', () => {
                removeChild(this.listing, li);
            });

            // create base inputs
            const localizedMoment = getLocalizeMoment(this.dateField.value);
            let newValue = localizedMoment.format('D/M/Y');

            const name = this.hiddenName;
            const position = name.indexOf('[date]');
            const newFieldName = [name.slice(0, position), `[${length || 0}]`, name.slice(position)].join('');

            const hidden = this.createInput(newFieldName, 'hidden', newValue);
            const timezone = this.createInput(newFieldName.replace('date', 'timezone'), 'hidden', Craft.timezone);

            p.innerHTML = getLocalizedDayName(localizedMoment) + ', ' + localizedMoment.format('L');

            li.appendChild(close);
            li.appendChild(hidden);
            li.appendChild(timezone);

            this.dateField.value = '';

            // add time input
            if (this.withTime) {
                const newTimeValue = this.timeField.value;
                const hiddenTime = this.createInput(newFieldName.replace('date', 'time'), 'hidden', newTimeValue);
                p.innerHTML += ' ' + newTimeValue;
                li.appendChild(hiddenTime);
                this.timeField.value = '';
            }

            li.appendChild(p);

            this.listing.appendChild(li);
        })
    }

    createInput(name, type, value) {
        const input = document.createElement('input');
        input.setAttribute('type', type);
        input.setAttribute('name', name);
        input.value = value;

        return input;
    }
}

export function dateExceptionInit(context) {
    context.querySelectorAll('[data-exceptions]').forEach((node) => new CalendarException(node, 'data-exceptions'));
}

export function timeExceptionInit(context) {
    context.querySelectorAll('[data-time-exceptions]').forEach((node) => new CalendarException(node, 'data-time-exceptions', true));
}