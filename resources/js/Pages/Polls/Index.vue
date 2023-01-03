<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/inertia-vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue"
import ActionButton from "@/Components/ActionButton.vue";
import  Modal from "@/Components/Modal.vue";
import {computed, reactive} from "vue";
import {Inertia} from "@inertiajs/inertia";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
    'polls': Array,
    'status': Object,
    'categories': Object,
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

const formModal=reactive({
    show: false,
    create: true,
    poll: null
})

function currentDate() {
    const current = new Date()
    const year = current.getFullYear()
    const m = (current.getMonth()+1).toString()
    const month = m.length === 1 ? '0'+m : m
    const d = current.getDate().toString()
    const day = d.length === 1 ? '0'+d : d
    const date = `${year}-${month}-${day}`;
    return date;
}

const eventForm = useForm({
    title: null,
    description: null,
    start: currentDate(),
    duration: 1,
    status: 1,
    category: 1
})


function formModalClose() {
    formModal.show=false
    formModal.poll=null

    eventForm.title=null
    eventForm.description=null
    eventForm.start=currentDate()
    eventForm.duration=1
    eventForm.status=1
    eventForm.category=1
}

function showEditModal(poll) {
    formModal.create=false
    formModal.poll=poll
    eventForm.title=poll.title
    eventForm.description=poll.description
    eventForm.start=poll.start
    eventForm.duration=poll.duration
    eventForm.status=poll.status_id
    eventForm.category=poll.category_id
    formModal.show=true
}

function editPollEvent(poll) {
    closeModal()
    eventForm.transform((data) => ({
        ...data,
        id: poll.id,
    })).post(route('poll.update'), {
        onSuccess: (page) => {
            if (page.props.flash.status==='ok') {
                formModalClose()
                showModal(page.props.flash.message)
            }
        },
    })
}

function showCreateModal() {
    formModal.create=true
    formModal.show=true
}

function createPollEvent() {
    closeModal()
    eventForm.post(route('poll.store'), {
        onSuccess: (page) => {
            if (page.props.flash.status==='ok') {
                formModalClose()
                showModal(page.props.flash.message)
            }
        },
    })
}

const deleteModal=reactive({
    show: false,
    poll: null
})

function closeDeleteModal() {
    deleteModal.show=false
    deleteModal.poll=null
}

function showDeleteModal(poll) {
    deleteModal.poll=poll
    deleteModal.show=true
}

function deletePollEvent(poll) {
    closeModal()
    Inertia.post(route('poll.destroy'),{'id': poll.id},{
        onStart: (visit)=> {
            hideDeleteModal()
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
    <Head><title>Poll Events</title></Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="font-semibold text-xl text-gray-800 leading-tight">Polls</div>
                <div class="space-x-2">
                    <PrimaryButton @click="showCreateModal()">
                        <div class="flex space-x-1 items-center">
                            <font-awesome-icon icon="fa-solid fa-calendar-plus" size="lg" class="text-amber-300"/>
                            <span>New Poll Event</span>
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
                                                Title - Description
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Start
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Duration
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Status
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Category
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Links
                                            </th>
                                            <th scope="col" class="text-sm font-medium text-left py-2 tracking-wider text-center">
                                                Actions
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-amber-100 text-center">
                                        <tr class="border-b" v-for="poll in polls" :key="poll.id">
                                            <td class="text-sm text-gray-900 font-medium px-4 py-4" v-text="poll.id"></td>
                                            <td class="text-sm text-gray-900 font-light py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="font-bold">{{poll.title}}</span>
                                                    <span class="text-xs">{{poll.description}}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="poll.start"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="poll.duration"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="poll.status"></td>
                                            <td class="text-sm text-gray-900 font-light py-2 whitespace-nowrap" v-text="poll.category"></td>
                                            <td class="text-sm text-gray-900 font-light py-2">
                                                <div class="space-y-1 flex flex-col mx-auto w-32">
                                                    <ActionButton :url="route('poll.nominees',poll)" class="h-6">
                                                        <div class="flex space-x-1.5 items-center justify-center">
                                                            <font-awesome-icon icon="fa-solid fa-people-group" size="lg" class="text-amber-300"/>
                                                            <span>Nominees</span>
                                                        </div>
                                                    </ActionButton>
                                                    <ActionButton :url="route('poll.votes',poll)" class="h-6">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-list-check" size="lg" class="text-amber-300"/>
                                                            <span>Votes</span>
                                                        </div>
                                                    </ActionButton>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-900 font-light py-2">
                                                <div class="space-y-1 flex flex-col mx-auto w-32">
                                                    <PrimaryButton v-if="poll.canEdit" @click="showEditModal(poll)" class="h-6">
                                                        <div class="flex space-x-1.5 items-center">
                                                            <font-awesome-icon icon="fa-solid fa-pen" size="lg" class="text-amber-300"/>
                                                            <span>Edit</span>
                                                        </div>
                                                    </PrimaryButton>
                                                    <PrimaryButton v-if="poll.canDelete" @click="showDeleteModal(poll)" class="h-6">
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
                    <div>Please confirm to delete poll event</div>
                    <div>{{ deleteModal.poll.title }}.</div>
                </div>
                <div class="flex justify-center space-x-1 h-8">
                    <PrimaryButton class="w-20 flex justify-center" @click="deletePollEvent(deleteModal.poll)">Yes</PrimaryButton>
                    <SecondaryButton class="w-20 flex justify-center" @click="closeDeleteModal()">Cancel</SecondaryButton>
                </div>
            </div>
        </Modal>

        <!-- form Modal -->
        <Modal max-width="xl" :show="formModal.show" @close="formModalClose()">
            <div class="bg-rose-800 text-amber-200 py-2 px-2 flex justify-between">
                <span class="ml-2" v-text="formModal.create ? 'Create New Poll Event' : 'Edit Poll Event'" ></span>
                <button @click="formModalClose()">
                    <font-awesome-icon icon="fa-solid fa-rectangle-xmark" size="lg"/>
                </button>
            </div>
            <div class="flex flex-col mt-4 mx-4">
                <form @submit.prevent="formModal.create ? createPollEvent() : editPollEvent(formModal.poll)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="title">
                            Title
                        </label>
                        <div v-if="errors.title" class="text-xs text-red-800 mt-1">{{ errors.title }}</div>
                        <div class="border-b">
                        <input v-model="eventForm.title" class="form-input w-full focus:ring-transparent border-none"
                               id="title" type="text" placeholder="Event Title">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="description">
                            Description
                        </label>
                        <div v-if="errors.description" class="text-xs text-red-800 mt-1">{{ errors.description }}</div>
                        <div class="border-b">
                            <textarea v-model="eventForm.description" class="form-textarea w-full focus:ring-transparent border-none"
                            id="description" placeholder="Event Description"></textarea>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="start">
                            Start Date
                        </label>
                        <div class="border-b">
                            <input v-model="eventForm.start" class="form-input w-full focus:ring-transparent border-none"
                                   id="start" type="date">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="duration">
                            Duration (Days)
                        </label>
                        <div class="border-b">
                            <input v-model="eventForm.duration" class="form-input w-full focus:ring-transparent border-none"
                                   id="duration" type="number" placeholder="Event Duration">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="status">
                            Status
                        </label>
                        <div class="border-b">
                            <select v-model="eventForm.status" class="form-select w-full focus:ring-transparent border-none"
                                    id="status">
                                <option v-for="item in status" v-text="item[0]" :value="item[1]"></option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold" for="category">
                            Category
                        </label>
                        <div class="border-b">
                            <select v-model="eventForm.category" class="form-select w-full focus:ring-transparent border-none"
                                    id="category">
                                <option v-for="item in categories" v-text="item[0]" :value="item[1]"></option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 mb-6 flex justify-center">
                        <PrimaryButton :disabled="eventForm.processing">
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
