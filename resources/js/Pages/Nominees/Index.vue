<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue"
import Pagination from "@/Components/Pagination.vue";
import {computed, reactive, ref, watch} from "vue";
import {debounce} from "lodash";
import {Inertia} from "@inertiajs/inertia";
import Modal from "@/Components/Modal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    'poll': Object,
    'nominees' : Object,
    'positions': Object,
    'totalCount': Number,
    'filters': Object,
    'flash' : Object,
    'errors' : Object,
})

const ongoing = computed(()=>{
    return props.poll.status === 2
})

const search = ref(props.filters.search)

watch(search, debounce(function (value) {
    Inertia.get(route('poll.nominees',props.poll), {search: value},{
        preserveState: true,
        replace: true,
    })
}, 500))

const modal = reactive({
    show: false,
    messages: [],
})

function showModal(messages) {
    messages.forEach((message) => {
        modal.messages.push(message)
    })
    modal.show=true
}

function closeModal() {
    modal.show=false
    modal.messages.splice(0,modal.messages.length)
}

const form = useForm({
    name: null,
    position_id: 1,
    affiliation: null
})

const formModal=reactive({
    show: false,
    create: true,
    data: Object(null)
})

function formModalClose() {
    formModal.show=false
    formModal.data=Object(null)

    form.name=null
    form.affiliation=null
    form.position_id=1
}

function showEditModal(nominee) {
    formModal.create=false
    formModal.data=nominee

    form.name=nominee.name
    form.position_id=nominee.position_id
    form.affiliation=nominee.affiliation

    formModal.show=true
}

function editNominee(nominee) {
    closeModal()
    form.transform((data) => ({
        ...data,
        id: nominee.id,
    })).post(route('nominee.update'),{
        onSuccess: (page) => {
            formModalClose()
            if (page.props.flash.status==='ok') {
                showModal(page.props.flash.message)
            }
        },
    });
}

function showCreateModal() {
    formModal.create=true
    formModal.data=props.poll
    formModal.show=true
}

function createNominee(poll) {
    closeModal()
    form.transform((data) => ({
        ...data,
        poll_id: poll.id,
    })).post(route('nominee.store'), {
        onSuccess: (page) => {
            formModalClose()
            if (page.props.flash.status==='ok') {
                showModal(page.props.flash.message)
            }
        },
    })
}

const deleteModal=reactive({
    show: false,
    nominee: Object(null)
})

function closeDeleteModal() {
    deleteModal.show=false
    deleteModal.nominee=Object(null)
}

function showDeleteModal(nominee) {
    deleteModal.nominee=nominee
    deleteModal.show=true
}

function deletePollEvent(nominee) {
    closeModal()
    Inertia.post(route('nominee.destroy'),{'id': nominee.id},{
        onStart: (visit)=> {
            closeDeleteModal()
        },
        onSuccess: (page) => {
            if (page.props.flash.status==='ok' || page.props.flash.status==='failed') {
                showModal(page.props.flash.message)
            }
        },
        onError: (errors) => {
        }
    })
}
</script>

<template>
    <Head><title>Poll Nominees</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="text-rose-700 hover:text-indigo-800">{{poll.title}}</span>
                </div>
                <div v-if="ongoing">
                    <PrimaryButton @click="showCreateModal()">
                        <div class="flex space-x-1 items-center">
                            <font-awesome-icon icon="fa-solid fa-calendar-plus" size="lg" class="text-amber-300"/>
                            <span>New Nominee</span>
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
                                    <div class="pb-2 flex justify-between items-center">
                                        <div class="text-indigo-800 font-bold">Total Nominees: {{ totalCount }}</div>
                                        <div class="flex items-center w-full sm:w-1/4 rounded p-1 border h-9">
                                            <input v-model="search" type="text" placeholder="Nominee..."
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
                                                Name
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Position
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Affiliation
                                            </th>
                                            <th v-if="ongoing" scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="nominee in nominees.data" :key="nominee.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-2 whitespace-nowrap" v-text="nominee.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="nominee.name"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="nominee.position"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="nominee.affiliation"></td>
                                            <td v-if="ongoing" class="text-sm text-gray-900 font-light py-2">
                                                <div class="space-x-1">
                                                    <PrimaryButton class="w-24 h-6" @click="showEditModal(nominee)">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-pen" size="lg" class="text-amber-300"/>
                                                            <span>Edit</span>
                                                        </div>
                                                    </PrimaryButton>
                                                    <PrimaryButton v-if="nominee.canModify" class="w-24 h-6" @click="showDeleteModal(nominee)">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-trash-alt" size="lg" class="text-amber-300"/>
                                                            <span>Delete</span>
                                                        </div>
                                                    </PrimaryButton>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <Pagination :next="nominees.next_page_url"
                                                :prev="nominees.prev_page_url"
                                                class="flex justify-center my-6"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MessageBox -->
        <Modal max-width="sm" :show="modal.show">
            <div class="py-6 flex flex-col justify-center text-center">
                <div class="text-center text-lg text-gray-600 pb-4">
                    <div v-for="message in modal.messages">{{message}}</div>
                </div>
                <div class="flex justify-center h-8">
                    <PrimaryButton @click="closeModal()">OK</PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal max-width="sm" :show="deleteModal.show">
            <div class="py-6 flex flex-col justify-center text-center">
                <div class="text-center text-lg text-gray-600 pb-4">
                    <div>Please confirm to delete nominee</div>
                    <div>{{ deleteModal.nominee.name }}.</div>
                </div>
                <div class="flex justify-center space-x-1 h-8">
                    <PrimaryButton class="w-20 flex justify-center" @click="deletePollEvent(deleteModal.nominee)">Yes</PrimaryButton>
                    <SecondaryButton class="w-20 flex justify-center" @click="closeDeleteModal()">Cancel</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- form Modal -->
        <Modal max-width="xl" :show="formModal.show" @close="formModalClose()">
            <div class="bg-rose-800 text-amber-200 py-2 px-2 flex justify-between">
                <span class="ml-2" v-text="formModal.create ? 'Create New Nominee' : 'Edit Nominee'" ></span>
                <button @click="formModalClose()">
                    <font-awesome-icon icon="fa-solid fa-rectangle-xmark" size="lg"/>
                </button>
            </div>
            <div class="flex flex-col mt-4 mx-4">
                <form @submit.prevent="formModal.create ? createNominee(formModal.data) : editNominee(formModal.data)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="name">
                            Name
                        </label>
                        <div v-if="errors.name" class="text-xs text-red-800 mt-1">{{ errors.name }}</div>
                        <div class="border-b">
                            <input v-model="form.name" class="form-input w-full focus:ring-transparent border-none"
                                   id="name" type="text" placeholder="Name..."
                                   :disabled="formModal.create ? false : !formModal.data.canModify">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="affiliation">
                            Affiliation
                        </label>
                        <div v-if="errors.affiliation" class="text-xs text-red-800 mt-1">{{ errors.affiliation }}</div>
                        <div class="border-b">
                            <textarea v-model="form.affiliation" class="form-textarea w-full focus:ring-transparent border-none"
                                      id="affiliation" placeholder="Affiliation..."></textarea>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="position">
                            Position
                        </label>
                        <div class="border-b">
                            <select v-model="form.position_id" class="form-select w-full focus:ring-transparent border-none"
                                    id="position" :disabled="formModal.create ? false : !formModal.data.canModify">
                                <option v-for="position in positions" v-text="position.name" :value="position.id"></option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 mb-6 flex justify-center">
                        <PrimaryButton :disable="form.processing">
                            <div class="flex space-x-1.5 items-center">
                                <font-awesome-icon icon="fa-solid fa-upload" size="lg" class="text-amber-300"/>
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
.form-input {
    outline: none;
    border: none;
}
</style>
