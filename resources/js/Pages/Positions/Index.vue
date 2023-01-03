<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import Modal from "@/Components/Modal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue"
import {reactive} from "vue";

const props = defineProps({
    'positions': Object,
    'flash' : Object,
    'errors' : Object,
})

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
    id: -1,
    name: null
})

const formModal=reactive({
    show: false,
    create: true,
    position: Object(null)
})

function formModalClose() {
    formModal.show=false
    formModal.position=Object(null)

    form.id=-1
    form.name=null
}

function showEditModal(position) {
    form.id=position.id
    form.name=position.name

    formModal.position=position
    formModal.create=false
    formModal.show=true
}

function editPosition(position) {
    closeModal()
    form.post(route('position.update'),{
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
    formModal.show=true
}

function createPosition() {
    closeModal()
    form.post(route('position.store'), {
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
    position: Object(null)
})

function closeDeleteModal() {
    deleteModal.show=false
    deleteModal.position=Object(null)
}

function showDeleteModal(position) {
    deleteModal.position=position
    deleteModal.show=true
}

function deletePosition(position) {
    closeModal()
    Inertia.post(route('position.destroy'),{'id': position.id},{
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
    <Head><title>Positions</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">
                    <span class="text-rose-700 hover:text-indigo-800">Positions</span>
                </div>
                <PrimaryButton @click="showCreateModal()">
                    <div class="flex space-x-1 items-center">
                        <font-awesome-icon icon="fa-solid fa-calendar-plus" size="lg" class="text-amber-300"/>
                        <span>New Position</span>
                    </div>
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-col mx-4 my-2">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-amber-100 table-fixed">
                                        <thead class="bg-rose-800 text-amber-200">
                                        <tr>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center">
                                                Name
                                            </th>
                                            <th scope="col" class="text-sm font-medium py-2 text-left tracking-wider text-center w-3/12">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="position in positions" :key="position.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-2 whitespace-nowrap" v-text="position.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="position.name"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 max-w-fit">
                                                <div class="flex flex-col justify-center items-center space-y-1">
                                                    <PrimaryButton v-if="position.canModify" class="w-24 h-6" @click="showEditModal(position)">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-pen" size="lg" class="text-amber-300"/>
                                                            <span>Edit</span>
                                                        </div>
                                                    </PrimaryButton>
                                                    <PrimaryButton v-if="position.canModify" class="w-24 h-6" @click="showDeleteModal(position)">
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
                    <div>{{ deleteModal.position.name }}.</div>
                </div>
                <div class="flex justify-center space-x-1 h-8">
                    <PrimaryButton class="w-20 flex justify-center" @click="deletePosition(deleteModal.position)">Yes</PrimaryButton>
                    <SecondaryButton class="w-20 flex justify-center" @click="closeDeleteModal()">Cancel</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- form Modal -->
        <Modal max-width="xl" :show="formModal.show" @close="formModalClose()">
            <div class="bg-rose-800 text-amber-200 py-2 px-2 flex justify-between">
                <span class="ml-2" v-text="formModal.create ? 'Create New Position' : 'Edit Position'" ></span>
                <button @click="formModalClose()">
                    <font-awesome-icon icon="fa-solid fa-rectangle-xmark" size="lg"/>
                </button>
            </div>
            <div class="flex flex-col mt-4 mx-4">
                <form @submit.prevent="formModal.create ? createPosition() : editPosition(formModal.position)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="name">
                            Name
                        </label>
                        <div v-if="errors.name" class="text-xs text-red-800 mt-1">{{ errors.name }}</div>
                        <div class="border-b">
                            <input v-model="form.name" class="form-input w-full focus:ring-transparent border-none"
                                   id="name" type="text" placeholder="Name...">
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
