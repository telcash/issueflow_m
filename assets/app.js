import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

/**
 * Agrego un listener a la página que escuche eventos click
 * Si se determina que el click ocurrió en un botón de Eliminar ejecuta
 * la lógica correspondiente
 */
document.addEventListener('click', (event) => {
    // Si el evento no ocurrió en un elemento, no hacemos nada
    if (!(event.target instanceof Element)) {
        return;
    }

    // Turbo puede reemplazar el contenido de la página; consultar el DOM actual
    // evita conservar referencias a elementos que ya fueron retirados.
    const deleteModal = document.getElementById('delete-ticket-modal');

    // Verificamos si el click fue en uno de los botones de eliminar
    // Esto lo hacemos observando si el evento ocurrió en un elemento
    // con el atributo data-delete-ticket
    const deleteButton = event.target.closest('[data-delete-ticket]');

    /**
     * Si el click fue en uno de los botones de eliminar procedemos
     * con la lógica de eliminación
     */
    if (deleteButton) {
        // Obtenemos el formulario y los elementos del modal que necesitamos
        const deleteForm = document.getElementById('delete-ticket-form');
        const deleteCode = deleteModal?.querySelector('[data-delete-ticket-code]');
        const deleteTitle = deleteModal?.querySelector('[data-delete-ticket-title]');

        if (!deleteModal || !deleteForm || !deleteCode || !deleteTitle) {
            return;
        }
        /**
         * Agregamos la acción de petición al servidor al botón del formulario.
         * Lo hacemos asignando la url del botón de la tarjeta del ticket
        */
        deleteForm.action = deleteButton.dataset.deleteUrl;

        /**
         * Agregamos los textos correspondientes al código y título del ticket en el modal de confirmación
         */
        deleteCode.textContent = deleteButton.dataset.deleteCode;
        deleteTitle.textContent = deleteButton.dataset.deleteTitle;

        // El formulario navega al servidor; cerrar antes del envío evita que
        // el navegador restaure el diálogo como abierto pero no modal.
        if (deleteModal.open) {
            deleteModal.close();
        }

        // Doy la instrucción para que el modal se muestre
        deleteModal.showModal();

        return ;
    }

    /**
     * Si el evento click ocurrió en un botón de cerrar el modal,
     * entonces cierro el modal
     */
    const closeButton = event.target.closest('[data-modal-close]');

    if (closeButton && deleteModal?.open) {
        deleteModal.close();
    }
});

// El diálogo debe quedar cerrado mientras se envía la eliminación.
document.addEventListener('submit', (event) => {
    if (event.target instanceof HTMLFormElement && event.target.id === 'delete-ticket-form') {
        const deleteModal = document.getElementById('delete-ticket-modal');

        if (deleteModal?.open) {
            deleteModal.close();
        }
    }
});

// Turbo reutiliza la página al navegar atrás; guardarla con el modal abierto
// podría restaurarlo en un estado inconsistente.
document.addEventListener('turbo:before-cache', () => {
    const deleteModal = document.getElementById('delete-ticket-modal');

    if (deleteModal?.open) {
        deleteModal.close();
    }
});
