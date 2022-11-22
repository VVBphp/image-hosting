<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm, usePage} from '@inertiajs/inertia-vue3';

defineProps({
    telegram: Object
});

const t = usePage().props.value.telegram;
console.log(t);

const form = useForm({
    api_token: t ? t.api_token : '',
});

const updateTelegram = () => {
    form.put(route('telegram.credentials'), {
        preserveScroll: true,
        onSuccess: () => {
            // form.reset()
        },
        onError: () => {
            // if (form.errors.username) {
            //     form.reset('password', 'password_confirmation');
            //     passwordInput.value.focus();
            // }
            // if (form.errors.current_password) {
            //     form.reset('current_password');
            //     currentPasswordInput.value.focus();
            // }
        },
    });
};
</script>

<template>
    <section>
        <form @submit.prevent="updateTelegram" class="mt-6 space-y-6">

            <div>
                <InputLabel for="api_token" value="Token"/>
                <TextInput
                    id="api_token"
                    ref="api_token"
                    v-model="form.api_token"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.api_token" class="mt-2"/>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Сохранить</PrimaryButton>
                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Сохранено.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
