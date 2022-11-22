<script setup>

defineProps({
    telegram: Object
});

</script>

<template>
    <form class="space-y-6">
        <div v-if="$page.props.auth.user.telegram" class=" border-b border-gray-300">
            Привязан
            {{ $page.props.auth.user.telegram.first_name }} {{ $page.props.auth.user.telegram.last_name }}
            (@{{ $page.props.auth.user.telegram.username }})
            <small class="text-gray-500">
                ID {{ $page.props.auth.user.bot_user_id }}
            </small>
        </div>
        <div class="flex items-center gap-4">
            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold
             text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none
              focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
               :href="telegram?'tg://resolve?domain='+telegram.username+'&start='+$page.props.auth.user.login:'javascript:'">Привязать
                {{ $page.props.auth.user.telegram ? 'другой' : '' }}
            </a>
        </div>
    </form>
</template>
