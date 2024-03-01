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
import { nextTick, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    schedules: {type:Object},
    routines: {type:Object},
    autorized: {type:String}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const title = ref('');
const id_schedule = ref(0);
const form = useForm({ day_of_week: '', hour: '', description: '', routine_id: '' });

const day_of_week = [
  { id: 1, name: 'Lunes' },
  { id: 2, name: 'Martes' },
  { id: 3, name: 'Miercoles' },
  { id: 4, name: 'Jueves' },
  { id: 5, name: 'Viernes' },
  { id: 6, name: 'Sabado' },
  { id: 7, name: 'Domingo' },
];

const openModal = (id, day_of_week, hour, description, routine_id)=>{    
    ventanamodal.value = true;
    nextTick(()=> nameInput.value.focus());
    id_schedule.value = id;
    if(id==0){
        title.value = 'Crear horario';
    }else{        
        title.value = 'Editar horario';
        form.day_of_week = day_of_week;
        form.hour = hour;
        form.description = description;
        form.routine_id = routine_id;
    }
}

const save = ()=>{
    if(id_schedule.value==0){
        form.post(route('schedules.store'), {
            onSuccess: () => ok('Horario creado'),
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else{
        form.put(route('schedules.update', id_schedule.value), {
            onSuccess: () => ok('Horario actualizado'), 
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }
}

const closeModal = ()=>{
    ventanamodal.value = false;
    id_schedule.value = 0;
    form.reset();
}

const ok = (msj) => {
    form.reset();
    closeModal();
    Swal.fire({title:msj, icon:'success'});
}
const deleteClient = (id, name) => {
    const alerta = Swal.mixin({
        buttonsStyling:true
    });
    alerta.fire({
        title:'Seguro que quiere eliminar: '+name+'?',
        icon:'question', showCancelButton: true,
        confirmButtonText:'<i class="fa-solid fa-check"></i> Si, eliminar',
        cancelButtonText:'<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if(result.isConfirmed){
            form.delete(route('schedules.destroy', id), {
                onSuccess: ()=>{ok('Rutina eliminada')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
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
                        <PrimaryButton 
                            @click="$event => openModal(0)">
                            <i class="fa-solid fa-plus-circle"></i> Agregar horario
                        </PrimaryButton>                    
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Día</th>
                                    <th class="border border-gray-400 px-2 py-2">Hora</th>
                                    <th class="border border-gray-400 px-2 py-2">Rutina</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="schedule, i in schedules" :key="schedule.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.day_of_week}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.hour}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{schedule.routine.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(schedule.id, schedule.NumDayOfWeek, schedule.NumHour, schedule.description, schedule.routine_id)">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deleteClient(schedule.id, schedule.day_of_week)">
                                            <i class="fa-solid fa-trash"></i>
                                        </DangerButton>
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
                    {{title}}                    
                </h2>               
            </div>
            <form @submit.prevent="save">      
                <div class="p-3 basis-2/4">
                    <InputLabel for="day_of_week" value="Día:" />
                    <SelectInput :options="day_of_week" v-model="form.day_of_week" class="mt-1 block w-full" required/>                    
                    <InputError class="mt-2" :message="form.errors.day_of_week" />
                </div>
                 <div class="p-3 basis-2/4">
                    <InputLabel for="hour" value="Hora:" />
                    <TextInput 
                        id="hour"
                        v-model="form.hour" 
                        type="time"
                        class="mt-1 block w-full"                   
                        placeholder="Hora"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.hour" />
                </div>
                 <div class="p-3 basis-2/4">
                    <InputLabel for="routine_id" value="Rutina:" />
                    <SelectInput :options="routines" v-model="form.routine_id" class="mt-1 block w-full" required/>                     
                    <InputError class="mt-2" :message="form.errors.routine_id" />
                </div>
                <div class="p-3 basis-1/4">
                    <InputLabel for="description" value="Descripción:" />
                    <textarea id="description" v-model="form.description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"                   
                    placeholder="Descripción"
                    required rows="5"></textarea>                    
                    <InputError class="mt-2" :message="form.errors.description" />
                </div>                                                  
                <div class="p-3 mt-6 flex justify-between">
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing" @click="save">
                        <i class="fa-solid fa-save"></i> Guardar
                    </PrimaryButton>            
               
                    <SecondaryButton :class="{ 'opacity-25': form.processing }" 
                        :disabled="form.processing" @click="closeModal">
                        <i class="fa-solid fa-ban"></i> Cancelar
                    </SecondaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
