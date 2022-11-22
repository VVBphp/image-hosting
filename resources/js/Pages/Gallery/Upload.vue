<template>
    <Head title="Загрузка фото"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Загрузка фото
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <main class="container mx-auto max-w-screen-lg h-full">
                    <!-- file upload modal -->
                    <article aria-label="File Upload Modal" ref="dropzone"
                             class="relative h-full flex flex-col bg-white shadow-xl rounded-md">
                        <!-- overlay -->
                        <div id="overlay" :class="{ 'draggedover': showOverlay }"
                             class="w-full h-full absolute top-0 left-0 pointer-events-none z-50 flex flex-col items-center justify-center rounded-md">
                            <i>
                                <svg class="fill-current w-12 h-12 mb-3 text-blue-700"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path
                                        d="M19.479 10.092c-.212-3.951-3.473-7.092-7.479-7.092-4.005 0-7.267 3.141-7.479 7.092-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h13c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408zm-7.479-1.092l4 4h-3v4h-2v-4h-3l4-4z"/>
                                </svg>
                            </i>
                            <p class="text-lg text-blue-700">Отпустите файлы для загрузки</p>
                        </div>

                        <!-- scroll area -->
                        <section class="h-full overflow-auto p-8 w-full h-full flex flex-col">
                            <header
                                class="border-dashed border-2 border-gray-400 py-12 flex flex-col justify-center items-center">
                                <p class="mb-3 font-semibold text-gray-900 flex flex-wrap justify-center">
                                    <span>Перетащите сюда </span>&nbsp;<span>файлы или</span>
                                </p>
                                <input @input="addImages($event.target.files)" ref="hidden-input" id="hidden-input"
                                       type="file" multiple class="hidden" accept="image/*"/>
                                <button id="button" @click="$refs['hidden-input'].click()"
                                        class="mt-2 rounded-sm px-3 py-1 bg-gray-200 hover:bg-gray-300 focus:shadow-outline focus:outline-none">
                                    выберите в галерее
                                </button>
                            </header>

                            <h1 class="pt-8 pb-3 font-semibold sm:text-lg text-gray-900">
                                К загрузке
                            </h1>

                            <ul id="gallery" class="flex flex-1 flex-wrap -m-1">
                                <li v-if="images.length === 0"
                                    class="h-full w-full text-center flex flex-col items-center justify-center items-center">
                                    <img class="mx-auto w-32"
                                         src="https://user-images.githubusercontent.com/507615/54591670-ac0a0180-4a65-11e9-846c-e55ffce0fe7b.png"
                                         alt="no data"/>
                                    <span class="text-small text-gray-500">Файлы не выбраны</span>
                                </li>
                                <li v-else v-for="(image, index) in images" class="pb-3 h-full w-full ">
                                    <div class="flex flex-col md:flex-row rounded-lg bg-white shadow-lg">
                                        <img
                                            class="w-full h-96 md:h-auto object-cover md:w-48 rounded-t-lg md:rounded-none md:rounded-l-lg"
                                            :src="URL_.createObjectURL(image.file)" alt=""/>
                                        <div class="p-6 flex flex-col justify-start w-full">
                                            <h5 class="text-gray-900 text-xl font-medium mb-2">
                                                {{ image.file.name }}
                                                <button @click="images.splice(index,1)"
                                                        class="rounded-sm px-3 py-1 bg-red-600 hover:bg-red-400 text-white focus:shadow-outline focus:outline-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                         class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                    </svg>
                                                </button>
                                            </h5>
                                            <div class="w-full bg-gray-200 mb-2"
                                                 v-if="image.progress">
                                                <div
                                                    class="bg-blue-500 text-xs font-medium text-blue-100 text-center p-0.5 leading-none"
                                                    :style="'width: '+image.progress">
                                                    {{ image.progress }}%
                                                </div>
                                            </div>
                                            <div class="text-gray-700 text-base mb-4">
                                                <div v-if="image.error"
                                                     class="bg-red-100 rounded-lg py-5 px-6 mb-3 text-base text-red-700 inline-flex items-center w-full"
                                                     role="alert">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                         data-icon="times-circle" class="w-4 h-4 mr-2 fill-current"
                                                         role="img" xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 512 512">
                                                        <path fill="currentColor"
                                                              d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path>
                                                    </svg>
                                                    {{ image.error }}
                                                </div>
                                                <div v-if="image.link" class="flex rounded-md shadow-sm w-1/2">
                                                    <input type="text" :value="image.link"
                                                           class="py-3 px-4 block w-full border-gray-200 shadow-sm rounded-l-md text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                                                    <button type="button"
                                                            class="inline-flex flex-shrink-0 justify-center items-center h-[2.875rem] w-[2.875rem] rounded-r-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                             viewBox="0 0 24 24" stroke-width="1.5"
                                                             stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </section>

                        <!-- sticky footer -->
                        <footer class="flex justify-end px-8 pb-8 pt-4">
                            <button id="submit" @click="sendImages"
                                    class="rounded-sm px-3 py-1 bg-blue-700 hover:bg-blue-500 text-white focus:shadow-outline focus:outline-none">
                                Начать загрузку
                            </button>
                            <button id="cancel" @click="images = []"
                                    class="ml-3 rounded-sm px-3 py-1 hover:bg-gray-300 focus:shadow-outline focus:outline-none">
                                Отмена
                            </button>
                        </footer>
                    </article>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from "@/Components/Card.vue";
import {Head} from "@inertiajs/inertia-vue3";

export default {
    name: "Upload",
    components: {
        AuthenticatedLayout,
        Card,
        Head
    },
    data() {
        return {
            dragAndDropCapable: false,
            showOverlay: false,
            images: []
        }
    },
    mounted() {
        this.dragAndDropCapable = this.determineDragAndDropCapable();
        if (this.dragAndDropCapable) {
            ['drag', 'dragstart', 'dragenter', 'dragleave', 'dragover'].forEach(function (evt) {
                this.$refs.dropzone.addEventListener(evt, function (e) {
                    this.showOverlay = true;
                    e.preventDefault();
                    e.stopPropagation();
                }.bind(this), false);
            }.bind(this));
            ['dragend'].forEach(function (evt) {
                this.$refs.dropzone.addEventListener(evt, function (e) {
                    this.showOverlay = false;
                    e.preventDefault();
                    e.stopPropagation();
                }.bind(this), false);
            }.bind(this));
            this.$refs.dropzone.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                this.addImages(e.dataTransfer.files);
                this.showOverlay = false;
            }.bind(this));
        }
    },
    methods: {
        determineDragAndDropCapable() {
            let div = document.createElement('div');
            return (('draggable' in div)
                    || ('ondragstart' in div && 'ondrop' in div))
                && 'FormData' in window
                && 'FileReader' in window;
        },
        addImages(files) {
            for (let i = 0; i < files.length; i++) {
                this.images.unshift({file: files[i], progress: 0, link: '', error: ''})
            }
        },
        sendImages() {
            for (let i = 0; i < this.images.length; i++) {
                if (this.images[i].link !== '') continue;
                let formData = new FormData();
                let file = this.images[i].file;
                formData.append('image', file);
                axios.post(route('images.store'),
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.images[i].progress = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100));
                        }.bind(this)
                    }
                )
                    .then(function (response) {
                        this.images[i].link = response.data;
                    }.bind(this))
                    .catch(function (error) {
                        if (error.response.data.errors && error.response.data.message) {
                            this.images[i].error = error.response.data.message;
                        }
                    }.bind(this));
            }
        }
    },
    setup() {
        const URL_ = URL;

        return {URL_}
    },
}
</script>

<style scoped>

.hasImage:hover section {
    background-color: rgba(5, 5, 5, 0.4);
}

.hasImage:hover button:hover {
    background: rgba(5, 5, 5, 0.45);
}

#overlay p,
i {
    opacity: 0;
}

#overlay.draggedover {
    background-color: rgba(255, 255, 255, 0.7);
}

#overlay.draggedover p,
#overlay.draggedover i {
    opacity: 1;
}

.group:hover .group-hover\:text-blue-800 {
    color: #2b6cb0;
}
</style>
