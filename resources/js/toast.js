/**
 * Global toast store (Alpine.store('toasts')).
 *
 * Add a toast from anywhere:
 *   - Alpine:   $store.toasts.add({ type: 'success', message: '...' })
 *   - Browser:  window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '...' } }))
 *   - Livewire: $this->dispatch('toast', type: 'success', message: '...')
 */
let nextId = 1;

export default {
    items: [],

    add({ type = 'info', message, duration = 5000 }) {
        if (!message) {
            return;
        }

        const id = nextId++;
        this.items.push({ id, type, message });

        if (duration > 0) {
            setTimeout(() => this.remove(id), duration);
        }
    },

    remove(id) {
        this.items = this.items.filter((item) => item.id !== id);
    },
};
