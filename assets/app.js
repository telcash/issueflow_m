import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Busco en el documento html el elemento con id = 'delete-ticket-modal' que es la ventana de confirmación
const deleteModal = document.getElementById('delete-ticket-modal');

// Busco en el documento html el formulario de eliminación
const deleteForm = document.getElementById('delete-ticket-form');

// Busco en el modal el código del ticket a eliminar
const deleteCode = deleteModal?.querySelector('[data-delete-ticket-code]');

// Busco en el modal el título del ticket a eliminar
const deleteTitle = deleteModal?.querySelector('[data-delete-ticket-title]');

// Agrego un listener a la página que escuche si el botón de eliminar es presionado
document.addEventListener('click', (event) => {
    // ¿Se ha pulsado un botón Eliminar?
    const deleteButton = event.target.closest('[data-delete-ticket]');

    if (deleteButton) {
        // Indicamos al formulario que envíe la petición al servidor
        deleteForm.action = deleteButton.dataset.deleteUrl;

        deleteCode.textContent = deleteButton.dataset.deleteCode;
        deleteTitle.textContent = deleteButton.dataset.deleteTitle;

        deleteModal.showModal();

        return ;
    }

    const closeButton = event.target.closest('[data-modal-close]');

    if (closeButton) {
        deleteModal.close();
    }
})
