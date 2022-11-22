<template>
    <Head title="Галерея"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Галерея
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <Card class="grid p-3 gap-4 grid-cols-1"
                      :class="[$page.props.auth.user.is_admin?'md:grid-cols-3':'md:grid-cols-2']">
                    <TailwindPagination :data="images" :limit="1" @pagination-change-page="getResults"
                                        :item-classes="['hover:bg-blue-300','hover:border-blue-300']"
                                        :active-classes="['hover:bg-blue-300','bg-blue-700','text-white','border-blue-700']"
                    />
                    <div class="flex">
                        <litepie-datepicker
                            i18n="ru"
                            :options="options"
                            :use-range="true"
                            :formatter="formatter"
                            v-model="created_between"
                        ></litepie-datepicker>
                    </div>
                    <select v-if="$page.props.auth.user.is_admin"
                            class="form-select appearance-none block w-full px-3 py-1.5 text-base font-normal text-gray-700
                            bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition
                            ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                            v-model="filter.user_id">
                        <option selected="selected" :value="null">Все пользователи</option>
                        <option v-for="user in users" :value="user.id">{{ user.login }}</option>
                    </select>
                </Card>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 pt-5">
                    <div v-for="image in images.data" class="rounded-lg bg-white max-w-sm relative">
                        <div class="h-64 relative mb-[2.875rem]">
                            <a :href="route('images.show',image.code)" target="_blank">
                                <div class="absolute inset-0 bg-cover bg-center rounded-t-lg"
                                     :style="'background-image: url('+route('images.download',image.code)+')'"></div>
                                <div
                                    class="opacity-0 hover:opacity-70 duration-300 absolute inset-0 flex
                                 justify-center bg-white items-center text-black font-semibold">
                                <span class="opacity-100">
                                    {{ image.user.login }}<br>
                                    {{ image.created_at }}
                                </span>
                                </div>
                            </a>
                        </div>
                        <div class="flex absolute bottom-0 w-full">
                            <input type="text" :value="image.link"
                                   class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-bl-md text-sm
                                    focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                            <button type="button" @click="copy(image.link)"
                                    :class="!$page.props.auth.user.is_admin?'rounded-br-md':''"
                                    class="inline-flex flex-shrink-0 justify-center items-center h-[2.875rem] w-[2.875rem]
                                     border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                </svg>
                            </button>
                            <button type="button" v-if="$page.props.auth.user.is_admin" @click="destroy(image.code)"
                                    class="inline-flex flex-shrink-0 justify-center items-center h-[2.875rem] w-[2.875rem]
                                      rounded-br-md border border-transparent font-semibold bg-red-500 text-white hover:bg-red-600
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {TailwindPagination} from 'laravel-vue-pagination';
import TextInput from '@/Components/TextInput.vue';
import Card from "@/Components/Card.vue";
import {Head} from "@inertiajs/inertia-vue3";
import LitepieDatepicker from 'litepie-datepicker';
import {ref} from "vue";

export default {
    name: "Listing",
    components: {
        AuthenticatedLayout, TailwindPagination,
        Card, TextInput,
        Head,
        LitepieDatepicker
    },
    props: {
        images: Object,
        users: Object,
        filter: Object
    },
    methods: {
        getResults(page) {
            this.$inertia.visit(route('images.index'), {
                data: {
                    filter: this.filter,
                    page
                }
            })
        },
        destroy(image) {
            if (confirm('Удалить фото?'))
                this.$inertia.delete(route('images.destroy', image), {
                    only: ['images'],
                    preserveState: true
                })
        },
        copy(data) {
            navigator.clipboard.writeText(data)
                .then(() => {
                    this.$toast.info(`Ссылка скопирована!`)
                })
                .catch(err => {
                    this.$toast.error('Не получилось, скопируйте вручную.')
                });
        },
        searchMethod: _.debounce(function () {
            this.$inertia.visit(
                route('images.index'),
                {
                    method: 'get',
                    data: {filter: this.filter},
                    only: ['images'],
                    preserveState: true
                }
            );
        }, 500),
    },
    watch: {
        'created_between'(newVal) {
            this.filter.created_between = newVal.start + ',' + newVal.end;
        },
        'filter.created_between'() {
            this.searchMethod();
        },
        'filter.user_id'() {
            this.searchMethod();
        },
    },
    beforeMount() {
        let a = this.$props.filter.created_between.split(',');
        this.created_between.start = a[0]
        this.created_between.end = a[1]
    },
    data() {
        return {
            created_between: {start: '', end: ''},
            options: {
                shortcuts: {
                    today: 'Сегодня',
                    yesterday: 'Вчера',
                    past: period => period + ' дн',
                    currentMonth: 'Этот месяц',
                    pastMonth: 'Пред. месяц'
                },
                footer: {
                    apply: 'Применить',
                    cancel: 'Отмена'
                }
            }
        }
    },
    setup() {
        const formatter = ref({
            date: 'DD.MM.YYYY',
            month: 'MMM'
        });
        const navigator = window.navigator;
        // const filter = ref({
        //     created_between: '',
        //     user_id: ''
        // })

        return {
            formatter,
            navigator
        };
    }
}
</script>

<style scoped>

</style>
