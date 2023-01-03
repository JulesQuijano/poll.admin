<script setup>

import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    next : String,
    prev : String
})
</script>

<template>
    <div class="space-x-4">
        <Component
            :href="prev"
            :is="prev ? 'Link' : 'span'"
            :class="prev ? 'bg-rose-700 text-white hover:bg-rose-400 hover:text-gray-900' : 'bg-amber-500 text-gray-500'"
            class="px-4 rounded-md p-1">
            <font-awesome-icon icon="fa-solid fa-arrow-left" class="pr-1"/> Prior
        </Component>
        <Component
            :href="next"
            :is="next ? 'Link' : 'span'"
            :class="next ? 'bg-rose-700 text-white hover:bg-rose-400 hover:text-gray-900' : 'bg-amber-500 text-gray-500'"
            class="px-4 rounded-md p-1">
            Next <font-awesome-icon icon="fa-solid fa-arrow-right" class="pl-1"/>
        </Component>
    </div>
</template>

