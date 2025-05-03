import './bootstrap';

// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import DataTables
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';

// Import Font Awesome
import '@fortawesome/fontawesome-free/js/all';

// Initialize DataTables when Livewire loads
document.addEventListener('livewire:init', () => {
    Livewire.on('initializeDataTable', () => {
        $('.datatable').DataTable({
            responsive: true
        });
    });
});
