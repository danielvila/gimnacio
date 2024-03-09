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
    payment: {type:Object},
    horaries: {type:Object},
    autorized: {type:String}
});

const daysofweek = ref('');
const hoursday = ref('');
const ventanamodal = ref(false);
const form = useForm({ schedule_id: '', coach: '', rutine: '', description: '' });

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

const openModal = (id, coach, rutine, description)=>{   
    ventanamodal.value = true;
    form.schedule_id = id;
    form.coach = coach;
    form.rutine = rutine;
    form.description = description;
}

const closeModal = ()=>{
    ventanamodal.value = false;
    form.reset();
}

const save = ()=>{
    if(form.schedule_id > 0){
        form.post(route('horary.store'), {
            onSuccess: () => ok('Inscripción realizada'),
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }
}

const deleteRegister = () => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
    alerta.fire({
        title:'Seguro que quiere eliminar: Esta clase?',
        icon:'question', showCancelButton: true,
        confirmButtonText:'<i class="fa-solid fa-check"></i> Si, eliminar',
        cancelButtonText:'<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if(result.isConfirmed){
            form.delete(route('horary.destroy', form.schedule_id), {
                onSuccess: ()=>{ok('Inscripción eliminada')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
}

const ok = (msj) => {    
    closeModal();
    Swal.fire({title:msj, icon:'success'});
}

const yesHorary = (id)=>{
    console.log(id);
    const schedule = props.horaries.find(m => m.id === id);

    if (schedule) {
        return true;
    }else{
        return  false;
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
                                    <td class="border border-gray-400" v-for="hourday, i in hoursday" :key="i">
                                        <ul>
                                            <li v-for="(day, j) in hourday" :key="j" :class="yesHorary(day.id)?'bg-red-600 text-white':''" class="border border-gray-400 px-2 py-3 cursor-pointer">
                                                <p @click="$event => openModal(day.id, day.user.name, day.routine.name, day.description)">{{ day.hour }}</p>
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
                <h2 class="p-6 text-lg font.medium text-gray-900">
                    Entrenador: {{form.coach}}                
                </h2>           
            </div>
            <div class="px-6">
                <p class="pb-2">
                    Rutina: {{form.rutine}}
                </p>
                <p>
                    Descripción:<br/>
                    {{form.description}}
                </p>
            </div>
            <div class="p-6 mt-6 flex justify-between">
                <div v-if="payment !=''">                
                    <DangerButton v-if="yesHorary(form.schedule_id)" :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing" @click="deleteRegister">
                        <i class="fa-regular fa-trash-can mr-2"></i> Cancelar
                    </DangerButton>

                    <PrimaryButton v-else: :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing" @click="save">
                        <i class="fa-regular fa-square-check mr-2"></i> inscribirse
                    </PrimaryButton>
                </div>
                <div v-else: class="bg-red-600 text-white p-2 rounded-md">
                    Debe comprar una membresia.
                </div>
                <SecondaryButton :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing" @click="closeModal">
                    <i class="fa-regular fa-rectangle-xmark  mr-2"></i> Cerrar
                </SecondaryButton>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
