@props([
    'action' => '',
    'method' => 'DELETE',
    'id' => '',
    'name' => 'item',
    'size' => 'sm',
    'class' => 'btn-danger',
    'icon' => 'fas fa-trash',
    'title' => 'Are you sure?',
    'text' => 'This action cannot be undone.',
    'confirmText' => 'Yes, delete it!',
    'cancelText' => 'Cancel',
])

<button type="button" 
        class="btn {{ $size }} {{ $class }} delete-btn"
        data-action="{{ $action }}"
        data-method="{{ $method }}"
        data-id="{{ $id }}"
        data-name="{{ $name }}"
        data-title="{{ $title }}"
        data-text="{{ $text }}"
        data-confirm-text="{{ $confirmText }}"
        data-cancel-text="{{ $cancelText }}">
    <i class="{{ $icon }}"></i> Delete
</button>