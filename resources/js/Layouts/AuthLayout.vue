<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    autorized: {type:String}
});

const showingNavigationDropdown = ref(false);

</script>

<template>
    <NavLink v-if="autorized=='Coach'" :href="route('schedules.index')" :active="route().current('schedules.index')">
        Horario
    </NavLink>
    <NavLink v-if="autorized=='Coach'" :href="route('students')" :active="route().current('students')">
        Estudiantes
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('clients.index')" :active="route().current('clients.index')">
        Clientes
    </NavLink>                                
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('payments.index')" :active="route().current('payments.index')">
        Pagos
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('concurrences.index')" :active="route().current('concurrences.index')">
        Asistencias
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('coachs.index')" 
        :active="route().current('coachs.index') || route().current('coachs.show')">
        Entrenadores
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('routines.index')" :active="route().current('routines.index')">
        Rutinas 
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('paymentypes.index')" :active="route().current('paymentypes.index')">
        Tipos de pago 
    </NavLink>
    <NavLink v-if="autorized=='Admin'" :href="route('memberships.index')" :active="route().current('memberships.index')">
        Membresias 
    </NavLink>
    <NavLink v-if="autorized=='Admin'" :href="route('users.index')" :active="route().current('users.index')">
        Usuarios
    </NavLink>
    <NavLink v-if="autorized=='Employee' || autorized=='Admin'" :href="route('settings.index')" :active="route().current('settings.index')">
        Configuración
    </NavLink>
</template>
