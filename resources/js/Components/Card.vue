<template>
    <div class="block rounded-lg shadow-lg bg-white">
        <div class="py-3 px-6 border-b border-gray-300" v-if="$slots.header">
            <slot name="header"/>
        </div>
        <slot/>
        <div class="py-3 px-6 border-t border-gray-300 text-gray-600" v-if="$slots.footer">
            <slot name="footer"/>
        </div>
    </div>
</template>

<script>
export default {
    name: "Card"
}
</script>

<style scoped>

</style>
