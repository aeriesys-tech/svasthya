<template>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="myLiveToast" class="toast border-0" data-bs-delay="2000" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-light" :class="toastClass">
                <strong class="me-auto">{{ toastHeader }}</strong>
            </div>
            <div class="toast-body">{{ message }}</div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';

    const type = ref('');
    const message = ref('');

    const toastClass = computed(() => {
        return {
            'bg-success': type.value === 'success',
            'bg-danger': type.value === 'error',
            'bg-warning': type.value === 'warning',
            'bg-info': type.value === 'info',
            'bg-theme-1': !['success', 'error', 'warning', 'info'].includes(type.value),
        };
    });

    const toastHeader = computed(() => {
        switch (type.value) {
            case 'success':
                return 'Success';
            case 'error':
                return 'Error';
            case 'warning':
                return 'Warning';
            case 'info':
                return 'Info';
            default:
                return 'General';
        }
    });

    const show = (msg, option) => {
        type.value = option.type;
        message.value = msg;
        const toastBootstrap = window.bootstrap.Toast.getOrCreateInstance(document.getElementById('myLiveToast'))
        toastBootstrap.show()
    }

    defineExpose({
        show
    })
</script>