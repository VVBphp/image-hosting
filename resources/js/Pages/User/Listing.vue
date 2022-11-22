<template>

    <Head title="Пользователи"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Пользователи
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <Card>
                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead class="border-b">
                                        <tr>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                #
                                            </th>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Login
                                            </th>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Telegram
                                            </th>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Password
                                            </th>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Ban
                                            </th>
                                            <th scope="col"
                                                class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                Admin
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="user in users.data" class="border-b"
                                            :class="[!user.is_active?'bg-red-100 border-red-200':'',user.is_admin?'bg-green-100 border-green-200':'']"
                                        >
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ user.id }}
                                            </td>
                                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                {{ user.login }}
                                            </td>
                                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                {{ user.telegram.first_name }} {{ user.telegram.last_name }}
                                                (@{{ user.telegram.username }})
                                            </td>
                                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                <div class="flex w-full" v-if="$page.props.auth.user.id!==user.id">
                                                    <input type="text" :ref="'password_'+user.id"
                                                           class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-l-md text-sm
                                    focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                                                    <button type="button"
                                                            @click="updatePassword(user.id,$refs['password_'+user.id].value)"
                                                            class="inline-flex flex-shrink-0 justify-center items-center h-[2.875rem] w-[2.875rem]
                                      rounded-r-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24" stroke-width="1.5"
                                                             stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap text-right">
                                                <div class="form-check form-switch" v-if="$page.props.auth.user.id!==user.id">
                                                    <input @change="switchActive(user.id)"
                                                           class="form-check-input appearance-none w-9 -ml-10 rounded-full h-5 align-top
                                                         bg-white bg-no-repeat bg-contain bg-gray-300 focus:outline-none cursor-pointer shadow-sm"
                                                           type="checkbox" role="switch"
                                                           :checked="!user.is_active">
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-900 font-light px-6 py-4 whitespace-nowrap text-right">
                                                <div class="form-check form-switch" v-if="$page.props.auth.user.id!==user.id">
                                                    <input @change="switchAdmin(user.id)"
                                                           class="form-check-input appearance-none w-9 -ml-10 rounded-full h-5 align-top
                                                         bg-white bg-no-repeat bg-contain bg-gray-300 focus:outline-none cursor-pointer shadow-sm"
                                                           type="checkbox" role="switch"
                                                           :checked="user.is_admin">
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pb-2 pr-1">
                        <TailwindPagination :data="users" :limit="1" @pagination-change-page="getResults"
                                            :item-classes="['hover:bg-blue-300','hover:border-blue-300']"
                                            :active-classes="['hover:bg-blue-300','bg-blue-700','text-white','border-blue-700']"
                        />
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {TailwindPagination} from 'laravel-vue-pagination';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Card from "@/Components/Card.vue";
import {Head} from "@inertiajs/inertia-vue3";
import axios from "axios";

export default {
    name: "Listing",
    components: {
        AuthenticatedLayout, TailwindPagination,
        Card, TextInput, InputError,
        Head
    },
    props: ['users'],
    methods: {
        getResults(page) {
            this.$inertia.visit(route('user.index'), {
                data: {
                    page
                }
            })
        },
        updatePassword(id) {
            let password = this.$refs['password_' + id][0].value;
            if (!password || password.length < 8) {
                return this.$toast.error('Пароль должен иметь больше 8 символов!')
            }
            axios.post(route('user.update', id), {password})
                .then(() => {
                    this.$toast.success('Пароль пользователя изменен!')
                    this.$refs['password_' + id][0].value = '';
                    this.$inertia.reload();
                })
                .catch(() => this.$toast.warning('Произошла ошибка, пароль не изменен!'))
        },
        switchActive(id) {
            axios.post(route('user.update', id), {'active': ''})
                .then(() => {
                    this.$toast.success('Статус пользователя изменен!')
                    this.$inertia.reload();
                })
                .catch(() => this.$toast.warning('Произошла ошибка!'))
        },
        switchAdmin(id) {
            axios.post(route('user.update', id), {'admin': ''})
                .then(() => {
                    this.$toast.success('Статус пользователя изменен!')
                    this.$inertia.reload();
                })
                .catch(() => this.$toast.warning('Произошла ошибка!'))
        }
    }
}
</script>

<style scoped>

</style>
