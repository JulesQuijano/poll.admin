<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2
        bg-amber-500 border border-transparent
        rounded-md font-semibold text-xs
        text-gray-700 uppercase
        tracking-widest shadow-sm hover:bg-amber-700
        hover:text-white
        transition ease-in-out duration-150">
        <slot />
    </button>
</template>
