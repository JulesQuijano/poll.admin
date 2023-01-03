<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Inertia } from "@inertiajs/inertia";
import  Modal from "@/Components/Modal.vue";
import { reactive } from "vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    'voters': Object,
    'flash' : Object,
    'errors' : Object,
    'totalVoters' : Number,
})

const modal = reactive({
    open: false,
    messages: [],
    withOK: true,
    icon: ''
})

const formModal = reactive({
    show: false,
    message: ''
})

const purgeModal = reactive({
    show: false,
})

const form = useForm({
    csv: null
})

function updateVoterPasswordReset(id) {
    const result = props.voters.data.find(voter => voter.id === id)
    if (result) {
        result.has_password_request=false
        modal.messages.push(result.student_number)
    }
}

function showPasswordResetModal(message) {
    modal.messages.unshift(message);
    modal.withOK=true
    modal.open = true
}

function hideModal() {
    modal.open=false
    modal.messages.splice(0, modal.messages.length)
    modal.icon=''
    modal.withOK=true
}

function resetPassword(id) {
    Inertia.post(route('voters.reset-password'),{ voter: id }, {
        onSuccess: (page) => {
            if (page.props.flash.status==='ok') {
                updateVoterPasswordReset(id)
                showPasswordResetModal(page.props.flash.message)
            }
        },
    })
}

function importVoterList() {
    formModal.show=false

    form.post(route('voters.import-list'), {
        onStart: (visit) => {
            hideModal()
            modal.withOK=false
            modal.messages.push('Uploading voters list')
            modal.icon='fa-solid fa-upload'
            modal.open=true
        },
        onSuccess: (page) => {
            hideModal()
            if (page.props.flash.status==='ok') {
                modal.messages.push(page.props.flash.message)
                modal.open = true
            }
        },
        onError: (errors) => {
            hideModal()
            formModal.show=true
        },
    })
}

function purgeVoterList() {
    purgeModal.show=false
    Inertia.post(route('voters.purge-list'), {}, {
        onStart: (visit) => {
            hideModal()
            modal.withOK=false
            modal.messages.push('Purging voters list')
            modal.icon='fa-solid fa-trash-alt'
            modal.open=true
        },
        onSuccess: (page) => {
            hideModal()
            if (page.props.flash.status==='ok') {
                modal.messages.push(page.props.flash.message)
                modal.open = true
            }
        },
    })
}

</script>

<template>
    <Head><title>Voters</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">Voters: {{totalVoters}}</div>
                <div class="space-x-2">
                    <PrimaryButton v-if="voters.data.length===0"  @click="formModal.show=true">
                        <div class="flex space-x-1.5 items-center">
                            <font-awesome-icon icon="fa-solid fa-upload" size="lg" class="text-amber-300"/>
                            <span>Import Voters List</span>
                        </div>
                    </PrimaryButton>
                    <PrimaryButton v-if="voters.data.length>0" @click="purgeModal.show=true">
                        <div class="flex space-x-1.5 items-center">
                            <font-awesome-icon icon="fa-solid fa-trash-alt" size="lg" class="text-amber-300"/>
                            <span>Purge Voters List</span>
                        </div>
                    </PrimaryButton>
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
                                    <table class="min-w-full divide-y divide-amber-100">
                                        <thead class="bg-rose-800 text-amber-200">
                                        <tr>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Student Number
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Email
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                College
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="voter in voters.data" :key="voter.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-2 whitespace-nowrap" v-text="voter.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="voter.student_number"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="voter.email"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="voter.college"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap">
                                                <form @submit.prevent="resetPassword(voter.id)">
                                                    <PrimaryButton v-if="voter.has_password_request" class="h-6">
                                                        <div class="flex space-x-1.5 items-center">
                                                        <font-awesome-icon icon="fa-solid fa-fingerprint" size="lg" class="text-amber-300"/>
                                                        <span>Reset Password</span>
                                                        </div>
                                                    </PrimaryButton>
                                                </form>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination :next="voters.next_page_url"
                                                :prev="voters.prev_page_url"
                                                class="flex justify-center my-6"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal max-width="sm" :show="modal.open">
            <div class="py-6 flex flex-col justify-center text-center">
                <div class="text-center text-lg text-gray-600 pb-4">
                    <font-awesome-icon v-if="modal.icon" :icon="modal.icon" size="lg" bounce class="text-amber-300"/>
                    <div v-for="message in modal.messages">{{message}}</div>
                </div>
                <progress v-if="form.progress" :value="form.progress.percentage" max="100"
                          class="rounded-md mx-4 text-black bg-amber-400"
                          v-text="form.progress.percentage">
                </progress>
                <div v-if="modal.withOK" class="flex justify-center h-8">
                    <PrimaryButton @click="hideModal()">OK</PrimaryButton>
                </div>
            </div>
        </Modal>
        <Modal max-width="sm" :show="purgeModal.show">
            <div class="py-6 flex flex-col justify-center text-center">
                <font-awesome-icon icon="fa-solid fa-trash-alt" size="3x" bounce class="text-amber-300"/>
                <div class="text-center text-lg text-gray-600 pb-4">
                    This action will purge the voters list.<br/>
                    Do you wish to continue?
                </div>
                <div class="flex justify-center h-8 space-x-2">
                    <PrimaryButton @click="purgeVoterList()">Yes</PrimaryButton>
                    <PrimaryButton @click="purgeModal.show=false">No</PrimaryButton>
                </div>
            </div>
        </Modal>
        <Modal max-width="md" :show="formModal.show" @close="formModal.show=false">
            <div class="py-6 flex flex-col justify-center text-center">
                <form @submit.prevent="importVoterList()">
                    <label for="csv-file" class="cursor-pointer space-y-3">
                        <font-awesome-icon v-if="form.csv" icon="fa-solid fa-file-csv" size="3x" class="text-rose-700"/>
                        <font-awesome-icon v-else icon="fa-solid fa-file-csv" size="3x" flip class="text-rose-700"/>
                        <div v-if="errors.csv" class="text-xs text-red-800 mt-1">{{ errors.csv }}</div>
                        <div class="text-center text-md text-gray-600 border rounded-md py-1 mx-6 flex justify-center items-center">
                            <span v-if="form.csv" v-text="form.csv.name"></span>
                            <span v-else>Select Voters CSV file</span>
                        </div>
                        <input id="csv-file" type="file" @input="form.csv = $event.target.files[0]" class="hidden" accept="text/csv"/>
                    </label>
                    <div v-if="form.csv" class="flex justify-center h-8 mt-4">
                        <PrimaryButton>
                            <div class="flex space-x-1.5 items-center">
                                <font-awesome-icon v-if="form.csv" icon="fa-solid fa-upload" size="xl" bounce class="text-amber-300"/>
                                <font-awesome-icon v-else icon="fa-solid fa-upload" class="text-lg text-amber-300"/>
                                <span>Submit</span>
                            </div>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
progress { color: darkred; }
progress::-webkit-progress-value { background: darkred; }
progress::-moz-progress-bar { background: darkred; }
</style>
