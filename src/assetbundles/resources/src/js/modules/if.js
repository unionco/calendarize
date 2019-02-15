
export default {};
export function onInit(context) {
    context.querySelectorAll('[data-if]').forEach((node) => {
        const selector = node.getAttribute('data-if');
        const operator = node.getAttribute('data-if-value');
        const or = node.getAttribute('data-or-value') || operator;
        const trigger = context.querySelector(`#${selector}`);

        const initialValue = trigger.value || "0";

        if (initialValue != operator) {
            node.classList.add('hidden');
            if (or && (initialValue != or)) {
                node.classList.add('hidden');
            }
        }

        trigger.addEventListener('change', (e) => {
            const newValue = e.target.value;
            if (newValue == operator || newValue == or) {
                node.classList.remove('hidden');
            } else {
                node.classList.add('hidden');
            }
        })
    });
}