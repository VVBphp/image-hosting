<template>

    <Head :title="image.code"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Фото {{ image.code }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <Card>
                    <template #header>
                        {{ image.user.login }}
                        {{ image.created_at }}
                        <div class="float-right" v-if="$page.props.auth.user.is_admin && !image.deleted_at">
                            <button type="button" @click="destroy(image.code)"
                                    class="inline-flex items-center -mt-2 -mr-5 px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight
                                uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg
                                 focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                                Удалить
                            </button>
                        </div>
                        <div class="float-right" v-else-if="image.deleted_at">
                            <span class="text-red-600 pr-4"> Удалено {{ image.deleted_at }}</span>
                            <button type="button" @click="restore(image.code)"
                                    class="inline-flex items-center -mt-2 -mr-5 px-6 py-2.5 bg-sky-600 text-white font-medium text-xs leading-tight
                                uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg
                                 focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                                Восстановить
                            </button>
                        </div>
                    </template>
                    <img :src="route('images.download',image.code)" alt="{{image.code}}">
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from "@inertiajs/inertia-vue3";
import Card from "@/Components/Card.vue";

export default {
    name: "Show",
    components: {
        Card,
        Head, AuthenticatedLayout
    },
    props: ['image'],
    methods: {
        destroy(image) {
            if (confirm('Удалить фото?'))
                this.$inertia.delete(route('images.destroy', image), {
                    only: ['images'],
                    preserveState: true
                })
        },
        restore(image) {
            if (confirm('Восстановить фото?'))
                this.$inertia.post(route('images.restore', image))
        },
    }
}
</script>

<style scoped>

</style>
