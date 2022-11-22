<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TelegramForm from "./Partials/TelegramForm.vue";
import UpdateTelegramForm from "./Partials/UpdateTelegramForm.vue";
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import Card from "@/Components/Card.vue";
import {Head} from '@inertiajs/inertia-vue3';

defineProps({
    mustVerifyEmail: Boolean,
    status: Boolean,
    telegram: Object
});
</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Профиль
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <Card v-if="$page.props.auth.user.is_admin">
                    <template #header>
                        <h2 class="text-lg font-medium text-gray-900">Установки бота</h2>
                    </template>
                    <TelegramForm :telegram="telegram" class="p-6"/>
                </Card>
                <Card>
                    <template #header>
                        <h2 class="text-lg font-medium text-gray-900">Привязка телеграм</h2>
                    </template>
                    <UpdateTelegramForm :telegram="telegram" class="p-6"/>
                </Card>
                <Card>
                    <template #header>
                        <h2 class="text-lg font-medium text-gray-900">Изменение пароля</h2>
                        <p class="mt-1 text-sm text-gray-600">Убедитесь, что в вашей учетной записи используется длинный
                            случайный пароль, чтобы оставаться в безопасности.</p>
                    </template>
                    <UpdatePasswordForm class="p-6"/>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
