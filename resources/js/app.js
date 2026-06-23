import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
// SweetAlert2 Configuration
document.addEventListener('DOMContentLoaded', function() {
    // Handle all delete buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const action = this.dataset.action;
            const method = this.dataset.method || 'DELETE';
            const name = this.dataset.name || 'item';
            const title = this.dataset.title || 'Are you sure?';
            const text = this.dataset.text || `This will permanently delete this ${name}. This action cannot be undone.`;
            const confirmText = this.dataset.confirmText || 'Yes, delete it!';
            const cancelText = this.dataset.cancelText || 'Cancel';
            const id = this.dataset.id;

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;

                    // Add CSRF token
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    form.appendChild(csrf);

                    // Add method spoofing (for DELETE, PUT, etc.)
                    if (method !== 'POST') {
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = method;
                        form.appendChild(methodField);
                    }

                    // Add ID if present
                    if (id) {
                        const idField = document.createElement('input');
                        idField.type = 'hidden';
                        idField.name = 'id';
                        idField.value = id;
                        form.appendChild(idField);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Success toast
    window.showSuccess = function(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        });
    };

    // Error toast
    window.showError = function(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            timer: 4000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        });
    };

    // Info toast
    window.showInfo = function(message) {
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
        });
    };
});