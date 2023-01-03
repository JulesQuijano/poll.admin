<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';

</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-no-repeat bg-center"
    style="background-image: url('/bg.jpg')">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-32 h-32 fill-current"/>
            </Link>
        </div>
        <div class="w-11/12 sm:max-w-md mt-6 px-6 py-4 bg-rose-800 text-black shadow-md overflow-hidden sm:rounded-lg opacity-90">
            <slot />
        </div>
    </div>
</template>
