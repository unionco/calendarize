
export default function onInit(context) {
    context.querySelectorAll('[data-use]').forEach((node) => {
        const selector = node.getAttribute('data-use');
        const input = context.querySelector(`input[name="${selector}"]`);

        const initialValue = input.value;
        node.innerHTML = getDateDay(initialValue);

        input.addEventListener('custom', () => {
            const newValue = input.value;
            node.innerHTML = getDateDay(newValue);
        });
    });
}