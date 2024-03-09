<script setup>
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    autorized: {type:String}
});
</script>

<template>    
    <NavLink v-if="autorized=='Client'" :href="route('horary.index')" :active="route().current('horary.index')">
        Horario
    </NavLink>
    <NavLink v-if="autorized=='Client'" :href="route('payments')" :active="route().current('payments')">
        Pagos
    </NavLink>
    <NavLink v-if="autorized=='Client'" :href="route('concurrences')" :active="route().current('concurrences')">
        Visitas
    </NavLink>     
</template>
