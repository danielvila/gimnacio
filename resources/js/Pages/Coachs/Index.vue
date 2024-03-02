<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WarningButton from '@/Components/WarningButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    coachs: {type:Object},
    autorized: {type:String}
});

const getWhatsAppLink = (phone) => {
  const phoneNumber = String(phone).trim();
  return 'https://api.whatsapp.com/send/?phone=507' + phoneNumber;
}
const getPhone = (phone) => {
  const phoneNumber = String(phone).trim();
  return 'tel:' + phoneNumber;
}
</script>

<template>
    <AuthenticatedLayout title="Lista de entrenadores" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de entrenadores
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Nombre</th>
                                    <th class="border border-gray-400 px-2 py-2">Telefono</th>
                                    <th class="border border-gray-400 px-2 py-2">Cumpleaños</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" >Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="coach, i in coachs" :key="coach.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{coach.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <a :href="getWhatsAppLink(coach.profile.phone)" target="_blank">
                                            <i class="fa-brands fa-whatsapp"></i> {{coach.profile.phone}}
                                        </a> / 
                                        <a :href="getPhone(coach.profile.phone)">
                                            <i class="fa-solid fa-phone-volume"></i> {{coach.profile.phone}}
                                        </a>                                        
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">{{coach.profile.birthday}}</td>
                                    <td class="border border-gray-400 px-2 py-2 text-center">
                                        <a :href="'/coachs/' + coach.id" title="Ver horario">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </td>                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>
