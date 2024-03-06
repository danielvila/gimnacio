<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
    schedules: {type:Object},
    coach: {type:Object},
    autorized: {type:String}
});

const daysofweek = ref('');
const hoursday = ref('');
const ventanamodal = ref(false);
const descriptionText = ref('');

onMounted(() => {
    const diasUnicos = new Set(props.schedules.map(objeto => objeto.day_of_week));
    daysofweek.value = [...diasUnicos];


    // Objeto temporal para organizar los elementos por día de la semana
    const datosPorDia = props.schedules.reduce((acumulador, elemento) => {        
        const { day_of_week } = elemento;

        if (!acumulador[day_of_week]) {
        acumulador[day_of_week] = [];
        }
        acumulador[day_of_week].push(elemento);

        return acumulador;
    }, {});

    hoursday.value = Object.values(datosPorDia);

});

const openModal = (description)=>{    
    ventanamodal.value = true;
    descriptionText.value = description;    
}

const closeModal = ()=>{
    ventanamodal.value = false;
    descriptionText.value = '';
}
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
                                    <th v-for="dayofweek, i in daysofweek" :key="i" class="border border-gray-400 px-2 py-2">{{dayofweek}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400" v-for="hourday, i in hoursday" :key="i">
                                        <ul>
                                            <li v-for="(day, j) in hourday" :key="j" class="border border-gray-400 px-2 py-3 cursor-pointer">
                                                <p @click="$event => openModal( day.description)">{{ day.hour }} - {{ day.routine.name }}</p>
                                            </li>
                                        </ul>
                                    </td>                                 
                                </tr>                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <Modal :show="ventanamodal" @close="closeModal">
            <div class="sm:flex">                
                <h2 class="p-3 text-lg font.medium text-gray-900">
                    Descripción                    
                </h2>               
            </div>
            <div class="p-3">
                {{descriptionText}}
            </div>
            <div class="p-3 mt-6 flex justify-between"> 
                <SecondaryButton @click="closeModal">
                    <i class="fa-solid fa-ban"></i> Cerrar
                </SecondaryButton>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
