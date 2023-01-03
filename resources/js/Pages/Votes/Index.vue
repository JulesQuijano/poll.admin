<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import Pagination from "@/Components/Pagination.vue";
import {debounce} from "lodash";
import {ref, watch} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    'poll': Object,
    'votes' : Object,
    'totalVotes': Number,
    'filters': Object,
})

const search = ref(props.filters.search)

watch(search, debounce(function (value) {
    Inertia.get(route('poll.votes',props.poll), {search: value},{
        preserveState: true,
        replace: true,
    })
}, 500))
</script>

<template>
    <Head><title>Poll Nominees</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="text-rose-700 hover:text-indigo-800 mr-2">{{poll.title}}</span>
                </div>
            </div>
        </template>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col mx-4 my-2">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <div class="pb-2 flex justify-between items-center">
                                        <div class="text-indigo-800 font-bold">Total Votes: {{ totalCount }}</div>
                                        <div class="flex items-center w-full sm:w-1/4 rounded p-1 border h-9">
                                            <input v-model="search" type="text" placeholder="Student Number..."
                                                   class="form-input w-full h-8 focus:ring-0 text-sm">
                                            <button v-if="search" @click="search=''" class="mx-1">
                                                <font-awesome-icon icon="fa-solid fa-rectangle-xmark"/>
                                            </button>
                                        </div>
                                    </div>
                                    <table class="min-w-full divide-y divide-amber-100 table-auto">
                                        <thead class="bg-rose-800 text-amber-200">
                                        <tr>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                College
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Student Number
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Nominee
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Position
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="vote in votes.data" :key="vote.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-2 whitespace-nowrap" v-text="vote.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="vote.college"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="vote.student_number"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="vote.nominee"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="vote.position"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination :next="votes.next_page_url"
                                                :prev="votes.prev_page_url"
                                                class="flex justify-center my-6"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-input {
    outline: none;
    border: none;
}
</style>
