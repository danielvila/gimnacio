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
    users: {type:Object},
    roles: {type:Object},
    q: {type:String},
    autorized: {type:String}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const updatepassword = ref(true);
const title = ref('');
const id_client = ref(0);
const form = useForm({
    name: '', email: '', password: '', password_confirmation: '', roles: []
});

const search = useForm({
    q: props.q
});

const submit = ()=>{
    search.get(route('users.index'));
}

const openModal = (id, name, email, roles)=>{    
    ventanamodal.value = true;
    nextTick(()=> nameInput.value.focus());
    id_client.value = id;
    if(id==0){
        title.value = 'Crear usuario';
    }else{        
        title.value = 'Editar usuario';
        form.name = name;
        form.email = email;       
        form.roles = roles.map(rol => rol.id);
        updatepassword.value = false;
    }
}

const save = ()=>{
    if(id_client.value==0){
        form.post(route('users.store'), {
            onSuccess: () => ok('Usuario creado'),
            //onFinish: () => closeModal(),    
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else{
        form.put(route('users.update', id_client.value), {
            onSuccess: () => ok('Usuario actualizado'),
            onError: (errors) => {
                console.error(errors);
            },
        });     
    }
}

const closeModal = ()=>{
    ventanamodal.value = false;
    id_client.value = 0;
    updatepassword.value = true;
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
            form.delete(route('users.destroy', id), {
                onSuccess: ()=>{ok('Cliente eliminado')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
}
</script>

<template>
    <AuthenticatedLayout title="Lista de Usuarios" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                        <div class="flex mr-3 w-full">
                            <TextInput id="q"
                                type="text"
                                class="mt-1 w-full mr-3"
                                v-model="search.q"
                                placeholder="Buscar por Nombre"
                            />
                            <PrimaryButton @click="submit">
                                <i class="fa-solid fa-search"></i> Buscar
                            </PrimaryButton> 
                        </div>
                        <PrimaryButton 
                            @click="$event => openModal(0)">
                            <i class="fa-solid fa-plus-circle"></i> Agregar Usuario
                        </PrimaryButton>                    
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Nombre</th>
                                    <th class="border border-gray-400 px-2 py-2">Email</th>
                                    <th class="border border-gray-400 px-2 py-2">Rol</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user, i in users.data" :key="user.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{user.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{user.email}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <span v-for="rol, i in user.roles" :key="rol.id" class="mr-2 bg-purple-100 p-2 rounded-md">{{rol.name}}</span>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(user.id, user.name, user.email, user.roles)">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deleteClient(user.id, user.name)">
                                            <i class="fa-solid fa-trash"></i>
                                        </DangerButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>                   
                    <div class="bg-white grid v-screen place-items-center py-3">
                        <Paginator :paginator="users" />                        
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
                <div class="p-3">
                    <InputLabel for="name" value="Nombre del usuario:" />
                    <TextInput 
                        id="name"
                        v-model="form.name"  ref="nameInput"
                        type="text"
                        class="mt-1 block w-full"                   
                        placeholder="Nombre del usuario"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="sm:flex"> 
                    <div class="p-3 basis-1/2">
                        <InputLabel for="email" value="Email:" />
                        <TextInput 
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"                        
                            required
                            placeholder="Email"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>                              
                </div> 
                <div v-if="updatepassword" class="sm:flex">
                    <div class="p-3 basis-1/2">
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div class="p-3 basis-1/2">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>
                <div v-else class="p-3">
                    <SecondaryButton @click="updatepassword = !updatepassword">
                        <i class="fa-solid fa-edit"></i> Actualizar password
                    </SecondaryButton>
                </div>
                <div class="sm:flex">
                    <div  v-for="rol, i in roles" :key="rol.id" class="p-3 basis-1/4">
                        <label>
                            <input type="checkbox" v-model="form.roles" :value="rol.id" />
                            <span class="ml-2">{{rol.name}}</span>
                        </label> 
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
