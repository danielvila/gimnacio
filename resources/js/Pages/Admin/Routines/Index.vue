<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import Paginator from "@/Components/Paginator.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import WarningButton from '@/Components/WarningButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    routines: {type:Object},
    autorized: {type:String}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const title = ref('');
const id_routine = ref(0);
const form = useForm({ name: '', description: '' });

const openModal = (id, name, description)=>{    
    ventanamodal.value = true;
    nextTick(()=> nameInput.value.focus());
    id_routine.value = id;
    if(id==0){
        title.value = 'Crear Rutina';
    }else{        
        title.value = 'Editar Rutina';
        form.name = name;
        form.description = description;
    }
}

const save = ()=>{
    if(id_routine.value==0){
        form.post(route('routines.store'), {
            onSuccess: () => ok('Rutina creada'),
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else{
        form.put(route('routines.update', id_routine.value), {
            onSuccess: () => ok('Rutina actualizada'), 
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }
}

const closeModal = ()=>{
    ventanamodal.value = false;
    id_routine.value = 0;
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
            form.delete(route('routines.destroy', id), {
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
    <AuthenticatedLayout title="Lista de rutinas" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Rutinas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">                        
                        <PrimaryButton 
                            @click="$event => openModal(0)">
                            <i class="fa-solid fa-plus-circle"></i> Agregar rutina
                        </PrimaryButton>                    
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Nombre</th>
                                    <th class="border border-gray-400 px-2 py-2">Descripción</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="routine, i in routines" :key="routine.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{routine.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{routine.description}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(routine.id, routine.name, routine.description)">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deleteClient(routine.id, routine.name)">
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
                    <InputLabel for="name" value="Nombre de la membresia:" />
                    <TextInput 
                        id="name"
                        v-model="form.name"  ref="nameInput"
                        type="text"
                        class="mt-1 block w-full"                   
                        placeholder="Nombre de la membresia"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
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
