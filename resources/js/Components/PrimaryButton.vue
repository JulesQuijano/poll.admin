<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2
        bg-rose-700 border border-transparent
        rounded-md font-semibold text-xs
        text-white uppercase
        tracking-widest shadow-sm
        hover:bg-rose-400 hover:text-gray-800
        active:bg-rose-200
        transition ease-in-out duration-150">
        <slot />
    </button>
</template>
