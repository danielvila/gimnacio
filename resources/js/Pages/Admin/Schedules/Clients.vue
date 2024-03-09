<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    schedules: {type:Object},
    clients: {type:Object},
    autorized: {type:String}
});

const daysofweek = ref('');
const hoursday = ref('');
const ventanamodal = ref(false);
const studentsmodal = ref(false);
const schedule_id = ref('');
const form = useForm({ schedule_id: '', rutine: '', description: '' });

onMounted(() => {
    const diasUnicos = new Set(props.schedules.map(objeto => objeto.day_of_week));
    daysofweek.value = [...diasUnicos];

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

const openModal = (id, rutine, description)=>{   
    ventanamodal.value = true;
    form.schedule_id = id;
    form.rutine = rutine;
    form.description = description;
}

const openstudentModal = (id)=>{   
    studentsmodal.value = true;
    schedule_id.value = id;
}

const closeModal = ()=>{
    ventanamodal.value = false;
    studentsmodal.value = false;
    schedule_id.value = '';
    form.reset();
}

const students = (id)=>{
    var student = '';
    var x = 0;
    for(x; x < props.clients.length; x++){
        if(props.clients[x]['schedule_id'] == id){
           student += props.clients[x]['name'];
        }  
    }
    if (student != '') {
        return true;
    }else{
        return false;
    }    
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
                                    <td class="border border-gray-400" v-for="hourday, i in hoursday" :key="i">{{hourday.clients}}
                                        <ul>
                                            <li v-for="(day, j) in hourday" :key="j" class="border border-gray-400 cursor-pointer" style="height: 74px;">
                                                <p @click="$event => openModal(day.id, day.routine.name, day.description)" class="p-2" title="Ver detalles del horario">{{ day.hour }}</p>
                                                <p v-if="students(day.id)" @click="$event => openstudentModal(day.id)" class="p-2 bg-gray-300" title="Ver lista de estudiantes">ver estudiantes</p>
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
            <div class="px-6 pt-6">
                <p class="pb-2">
                    Rutina: {{form.rutine}}
                </p>
                <p>
                    Descripción:<br/>
                    {{form.description}}
                </p>
            </div>
            <div class="p-6 mt-6 flex justify-between">                
                <SecondaryButton :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing" @click="closeModal">
                    <i class="fa-regular fa-rectangle-xmark  mr-2"></i> Cerrar
                </SecondaryButton>
            </div>
        </Modal>
         <Modal :show="studentsmodal" @close="closeModal">           
            <div class="px-6 pt-6">
                <h3 class="pb-3">
                    Lista de estudiantes
                </h3>                             
               <div v-for="(client, j) in clients" :key="j">
                    <span v-if="client.schedule_id==schedule_id">{{ client.name }}</span>
                </div>                
            </div>
            <div class="p-6 mt-6 flex justify-between">                
                <SecondaryButton :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing" @click="closeModal">
                    <i class="fa-regular fa-rectangle-xmark  mr-2"></i> Cerrar
                </SecondaryButton>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
