import { createObserver } from '../util/create-observer';

export default {};
export function onInit(context) {
    context.querySelectorAll('[data-toggle-listener]').forEach((node) => {
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
}