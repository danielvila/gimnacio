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
    memberships: {type:Object}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const title = ref('');
const id_membership = ref(0);
const form = useForm({ name: '', duration: '' });

const openModal = (id, name, duration)=>{    
    ventanamodal.value = true;
    nextTick(()=> nameInput.value.focus());
    id_membership.value = id;
    if(id==0){
        title.value = 'Crear Membresia';
    }else{        
        title.value = 'Editar Membresia';
        form.name = name;
        form.duration = duration;
    }
}

const save = ()=>{
    if(id_membership.value==0){
        form.post(route('memberships.store'), {
            onSuccess: () => ok('Membresia creada'),
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else{
        form.put(route('memberships.update', id_membership.value), {
            onSuccess: () => ok('Membresia actualizada'), 
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }
}

const closeModal = ()=>{
    ventanamodal.value = false;
    id_membership.value = 0;
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
            form.delete(route('memberships.destroy', id), {
                onSuccess: ()=>{ok('Membresia eliminada')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
}
</script>

<template>
    <AuthenticatedLayout title="Lista de Membresias">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Membresias
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">                        
                        <PrimaryButton 
                            @click="$event => openModal(0)">
                            <i class="fa-solid fa-plus-circle"></i> Agregar membresia
                        </PrimaryButton>                    
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Nombre</th>
                                    <th class="border border-gray-400 px-2 py-2">Duración</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="membership, i in memberships" :key="membership.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{membership.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{membership.duration}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(membership.id, membership.name, 
                                            membership.duration)">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deleteClient(membership.id, membership.name)">
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
                
            <div class="sm:flex">
                <div class="p-3 basis-3/4">
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
                    <InputLabel for="duration" value="Duración:" />
                    <TextInput 
                        id="duration"
                        v-model="form.duration"
                        type="text"
                        pattern="[0-9]"
                        class="mt-1 block w-full"                        
                        required
                        placeholder="Solo numeros"
                    />
                    <InputError class="mt-2" :message="form.errors.duration" />
                </div>                              
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
