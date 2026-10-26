import './bootstrap';

// Livewire ships its own Alpine build. Importing both would start Alpine twice,
// so we bundle Livewire's copy here and start it once.
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import toast from './toast';
import './dashboard';

window.Alpine = Alpine;

Alpine.store('toasts', toast);

// Lets plain scripts and Livewire (`$this->dispatch('toast', ...)`) raise toasts.
window.addEventListener('toast', (event) => Alpine.store('toasts').add(event.detail));

Livewire.start();
