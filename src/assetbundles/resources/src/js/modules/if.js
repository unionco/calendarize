
export default function onInit() {
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
}