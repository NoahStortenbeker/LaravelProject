import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Import components - de data wordt automatisch geïmporteerd in scripts.js
import './components/scripts';
import './components/transitions';
import './components/userlogin';
import './components/homeButton';

// Import category data
import './data/menData';
import './data/womenData';
import './data/kidsData';
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (csrf) {
    fetch('/heartbeat', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'include',
    }).catch(() => {});
    setInterval(() => {
      fetch('/heartbeat', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'include',
      }).catch(() => {});
    }, 60000);
  }
});
