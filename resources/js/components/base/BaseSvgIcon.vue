<script setup>
defineProps({
    path: {
        type: String,
        required: true,
    },
    sizeClass: {
        type: String,
        default: 'size-5',
    },
});
</script>

<template>
    <svg :class="sizeClass" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor">
        <path :d="path" />
    </svg>
</template>
