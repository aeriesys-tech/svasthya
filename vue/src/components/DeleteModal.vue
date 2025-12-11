<template>
    <!-- Delete Modal Starts -->
    <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Delete {{ name }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this {{ name }} record? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button v-if="!isLoading" type="button" class="btn btn-danger" @click="destroy()">Delete</button>
                    <button v-else type="button" class="btn btn-danger">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal Ends -->
</template>

<script setup>
    const props = defineProps({
        name: {
            type: String,
            required: true
        },
        isLoading: {
            type: Boolean,
            required: true
        },
        destroy: Function
    });

    const show = () => {
        const modal = new window.bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    const destroy = () => {
        props.destroy();
    }

    const hide = () => {
        const modal = window.bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        modal.hide();
    }

    defineExpose({
        show,
        hide,
    })
</script>