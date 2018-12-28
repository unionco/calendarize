import moment from 'moment';

function createObserver(cb) {
    const mutationObserver = new MutationObserver(function (mutations) {
        mutations.forEach((mutation) => {
            cb(mutation);
        });
    });

    return mutationObserver;
}

window.onload = () => {
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const prefixes = ['First', 'Second', 'Third', 'Fourth', 'Last'];

    const getDayName = (date) => {
        const d = new Date(date);
        return days[d.getDay()];
    };
    
    const weekOfMonth = (date) => {
        const m = moment(date);
        let w = m.isoWeekday(7).week() - moment(m).startOf('month').isoWeekday(7).week() - 1;
        
        if (w < 0) {
            w = prefixes.length - 1;
        }
        return w;
    }

    const weekAndDay = (date) => {
        const d = new Date(date);
        return prefixes[weekOfMonth(d)] + ' ' + days[d.getDay()];
    }

    const nth = (d) => {
        if (d > 3 && d < 21) return 'th';
        switch (d % 10) {
            case 1: return "st";
            case 2: return "nd";
            case 3: return "rd";
            default: return "th";
        }
    }

    const getDateDay = (value) => {
        const date = new Date(value);
        const d = date.getDate();
        const e = nth(d);

        return `${d}${e}`;
    };

    document.querySelectorAll('[data-toggle-listener]').forEach((node) => {
        let oldValue = node.getAttribute('aria-checked');
        const checkbox = node.querySelector('input[type="hidden"]');
        const observer = createObserver((mutation) => {
            if (mutation.type === 'attributes') {
                const newValue = node.getAttribute('aria-checked');
                if (newValue !== oldValue) {
                    // fireevent
                    checkbox.value = newValue === 'false' ? 0 : 1;
                    checkbox.dispatchEvent(new Event('change'));
                    oldValue = newValue;
                }
            }
        });
        observer.observe(node, {
            attributes: true
        });
    });

    document.querySelectorAll('[data-if]').forEach((node) => {
        const selector = node.getAttribute('data-if');
        const operator = node.getAttribute('data-if-value');
        const trigger = document.querySelector(`[name="${selector}"]`);
        const initialValue = trigger.value || "0";
        
        if (initialValue != operator) {
            node.classList.add('hidden');
        }

        trigger.addEventListener('change', (e) => {
            const newValue = e.target.value;
            if (newValue == operator) {
                node.classList.remove('hidden');
            } else {
                node.classList.add('hidden');
            }
        })
    });

    document.querySelectorAll('[data-use]').forEach((node) => {
        const selector = node.getAttribute('data-use');
        const input = document.querySelector(`input[name="${selector}"]`);
        
        const initialValue = input.value;
        node.innerHTML = getDateDay(initialValue);

        input.addEventListener('custom', () => {
            const newValue = input.value;
            node.innerHTML = getDateDay(newValue);
        });
    });

    document.querySelectorAll('[data-monthly-select]').forEach((node) => {
        const selector = node.getAttribute('data-monthly-select');
        const dateField = document.querySelector(`[name="${selector}"]`);
        const select = node.querySelector('select');

        dateField.addEventListener('custom', (e) => {
            const dateValue = dateField.value;
            const optOneValue = getDateDay(dateValue);
            const optTwoValue = weekAndDay(dateValue);

            select.querySelectorAll('option')[0].innerHTML = optOneValue;
            select.querySelectorAll('option')[1].innerHTML = optTwoValue;
        });
    });

    document.querySelectorAll('[data-exceptions]').forEach((node) => {
        const dateField = node.querySelector('.hasDatepicker');
        const hiddenName = node.getAttribute('data-exceptions');
        const listing = node.querySelector('.exceptions-list');
        const listingItems = listing.querySelectorAll('li');

        const removeChild = (item, child) => {            
            item.removeChild(child);
        };

        if (listingItems.length) {
            listingItems.forEach((item) => {
                const close = item.querySelector('[data-icon]');
                close.addEventListener('click', () => {
                    removeChild(listing, item);
                })
            })
        }

        dateField.addEventListener('custom', (e) => {
            const newValue = dateField.value;
            const li = document.createElement('li');
            const close = document.createElement('div');
            const p = document.createElement('p');
            const hidden = document.createElement('input');
            const timezone = document.createElement('input');
            const length = listing.querySelectorAll('li').length;

            hidden.setAttribute('type', 'hidden');
            hidden.setAttribute('name', hiddenName.replace('[]', `[${length || 0}]`));
            hidden.value = newValue;

            timezone.setAttribute('type', 'hidden');
            timezone.setAttribute('name', hiddenName.replace('[]', `[${length || 0}]`).replace('date', 'timezone'));
            timezone.value = Craft.timezone;

            close.setAttribute('data-icon', 'remove');
            close.addEventListener('click', () => {
                removeChild(listing, li);
            })

            p.innerHTML = getDayName(newValue) + ', ' + newValue;
            
            li.appendChild(close);
            li.appendChild(p);
            li.appendChild(hidden);
            li.appendChild(timezone);

            listing.appendChild(li);
        })
    });
};