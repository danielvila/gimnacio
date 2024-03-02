<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Paginator from "@/Components/Paginator.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import WarningButton from '@/Components/WarningButton.vue';
import { Head, useForm } from '@inertiajs/vue3';


const props = defineProps({
    schedules: {type:Object},
    coach: {type:Object},
    autorized: {type:String}
});
</script>

<template>
    <AuthenticatedLayout title="Lista de Horario" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Horario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">                        
                       {{coach.name}}                   
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Día</th>
                                    <th class="border border-gray-400 px-2 py-2">Hora</th>
                                    <th class="border border-gray-400 px-2 py-2">Rutina</th>
                                    <th class="border border-gray-400 px-2 py-2">Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="schedule, i in schedules" :key="schedule.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.day_of_week}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.hour}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.routine.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.description}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
