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
    'users': Object,
    'roles': Object,
    'status': Object,
    'flash': Object,
    'errors': Object,
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

const deleteModal=reactive({
    show: false,
    position: Object(null)
})

function closeDeleteModal() {
    deleteModal.show=false
    deleteModal.user=Object(null)
}

function showDeleteModal(user) {
    deleteModal.user=user
    deleteModal.show=true
}

function deleteUser(user) {
    closeModal()
    Inertia.post(route('user.destroy'),{'id': user.id},{
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

const form = useForm({
   id: -1,
   name: null,
   email: null,
   role: 2,
   status: 1
});

const formModal=reactive({
    show: false,
    create: true,
    user: Object(null),
    canModify: true
})

function formModalClose() {
    formModal.show=false
    formModal.user=Object(null)
    formModal.canModify=true

    form.id=-1
    form.name=null
    form.email=null
    form.role=2
    form.status=1
}

function showEditModal(user) {
    form.id=user.id
    form.name=user.name
    form.email=user.email
    form.role=user.role
    form.status=user.status

    formModal.user=user
    formModal.create=false
    formModal.show=true
    formModal.canModify=user.canModify
}

function editUser(user) {
    closeModal()
    form.post(route('user.update'),{
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
    formModal.user = Object(null)
    formModal.canModify=true
    formModal.show=true
}

function createUser() {
    closeModal()
    form.post(route('user.store'), {
        onSuccess: (page) => {
            formModalClose()
            if (page.props.flash.status==='ok') {
                showModal(page.props.flash.message)
            }
        },
    })
}

function passwordReset(user) {
    closeModal()
    Inertia.post(route('user.reset-password'),{'id':user.id},{
        onSuccess: (page) => {
            if (page.props.flash.status==='ok') {
                showModal(page.props.flash.message)
            }
        }
    })
}

</script>

<template>
    <Head><title>Admin Users</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">Admin</div>
                <div class="space-x-2">
                    <PrimaryButton @click="showCreateModal()">
                        <div class="flex space-x-1 items-center">
                            <font-awesome-icon icon="fa-solid fa-user-alt" size="lg" class="text-amber-300"/>
                            <span>New Admin User</span>
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
                                    <table class="min-w-full divide-y divide-amber-100 table-auto">
                                        <thead class="bg-rose-800 text-amber-200">
                                        <tr>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                #
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Name
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Email
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Role
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Status
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Password
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="user in users" :key="user.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-4" v-text="user.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-4 whitespace-nowrap" v-text="user.name"></td>
                                            <td class="text-sm text-gray-900 font-light py-4 whitespace-nowrap" v-text="user.email"></td>
                                            <td class="text-sm text-gray-900 font-light py-4 whitespace-nowrap" v-text="user.roleStr"></td>
                                            <td class="text-sm text-gray-900 font-light py-4 whitespace-nowrap" v-text="user.statusStr"></td>
                                            <td class="text-sm text-gray-900 font-light py-2">
                                                <div class="flex justify-center items-center mx-auto">
                                                    <PrimaryButton v-if="!user.isSuper" @click="passwordReset(user)" class="h-6 w-24">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-fingerprint" size="lg" class="text-amber-300"/>
                                                            <span>Reset</span>
                                                        </div>
                                                    </PrimaryButton>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-900 font-light py-2">
                                                <div class="space-y-1 flex flex-col mx-auto items-center">
                                                    <PrimaryButton @click="showEditModal(user)" class="h-6 w-24">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-pen" size="lg" class="text-amber-300"/>
                                                            <span>Edit</span>
                                                        </div>
                                                    </PrimaryButton>
                                                    <PrimaryButton v-if="user.canModify" @click="showDeleteModal(user)" class="h-6 w-24">
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
                    <div>{{ deleteModal.user.name }}.</div>
                </div>
                <div class="flex justify-center space-x-1 h-8">
                    <PrimaryButton class="w-20 flex justify-center" @click="deleteUser(deleteModal.user)">Yes</PrimaryButton>
                    <SecondaryButton class="w-20 flex justify-center" @click="closeDeleteModal()">Cancel</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- form Modal -->
        <Modal max-width="xl" :show="formModal.show" @close="formModalClose()">
            <div class="bg-rose-800 text-amber-200 py-2 px-2 flex justify-between">
                <span class="ml-2" v-text="formModal.create ? 'Create New User' : 'Edit User'" ></span>
                <button @click="formModalClose()">
                    <font-awesome-icon icon="fa-solid fa-rectangle-xmark" size="lg"/>
                </button>
            </div>
            <div class="flex flex-col mt-4 mx-4">
                <form @submit.prevent="formModal.create ? createUser() : editUser(formModal.user)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="name">
                            Name
                        </label>
                        <div v-if="errors.name" class="text-xs text-red-800 mt-1">{{ errors.name }}</div>
                        <div class="border-b">
                            <input v-model="form.name" class="form-input w-full focus:ring-transparent border-none"
                                   id="name" type="text" placeholder="Name..." :disabled="!formModal.canModify">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="email">
                            Email
                        </label>
                        <div v-if="errors.email" class="text-xs text-red-800 mt-1">{{ errors.email }}</div>
                        <div class="border-b">
                            <input v-model="form.email" class="form-input w-full focus:ring-transparent border-none"
                                   id="email" type="email" placeholder="Email...">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="role">
                            Role
                        </label>
                        <div class="border-b">
                            <select v-model="form.role" class="form-select w-full focus:ring-transparent border-none"
                                    id="role" :disabled="!formModal.canModify">
                                <option v-for="role in roles" v-text="role[1]" :value="role[0]" :disabled="formModal.canModify"></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="status">
                            Status
                        </label>
                        <div class="border-b">
                            <select v-model="form.status" class="form-select w-full focus:ring-transparent border-none"
                                    id="status" :disabled="!formModal.canModify">
                                <option v-for="item in status" v-text="item[1]" :value="item[0]"></option>
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
