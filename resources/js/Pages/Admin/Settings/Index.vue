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
import { Head, useForm, router } from '@inertiajs/vue3';
import { nextTick, onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    setting: {type:Object},
    autorized: {type:String}
});
const nameInput = ref(null);
const form = useForm({_method: 'put', id: '', name: '', ruc: '', dv: '', logo: '', description: '', email: '', phone: ''});

onMounted(() => {
    nextTick(()=> nameInput.value.focus());
    form.id = props.setting.id;
    form.name = props.setting.name;
    form.ruc = props.setting.ruc;
    form.dv = props.setting.dv;
    form.logo = props.setting.logo;
    form.description = props.setting.description;
    form.email = props.setting.email;
    form.phone = props.setting.phone;

    document.getElementById("picture").setAttribute('src', props.setting.logo);
});

const save = ()=>{
    form.post(route(`settings.update`, form.id), {
        onSuccess: () => ok('Datos actualizados'),
        onError: (errors) => {
            console.error(errors);
        },
    }); 
}

const deleteLogo = ()=>{
    form._method = 'delete';
    form.delete(route(`settings.destroy`, form.id), {
        onSuccess: () => {
            ok('Logo eliminado');            
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
    document.getElementById("picture").setAttribute('src', '');
    document.getElementById("logo").value = null;
}

const ok = (msj) => {
    Swal.fire({title:msj, icon:'success'});
}

const cambiarImagen = (event) => {
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = (event) => {
        document.getElementById("picture").setAttribute('src', event.target.result);
    };
    reader.readAsDataURL(file);
}
</script>

<template>
    <AuthenticatedLayout title="Datos de la empresa" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Datos de la empresa
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                        <h2 class="px-3 text-lg font.medium text-gray-900">
                            Actualizar datos de la empresa                   
                        </h2> 
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">                        
                        <form @submit.prevent="save">
                            <div class="sm:flex">
                                <div class="p-3 basis-2/4">
                                    <InputLabel for="name" value="Nombre de la empresa:" />
                                    <TextInput 
                                        id="name"
                                        v-model="form.name"  ref="nameInput"
                                        type="text"
                                        class="mt-1 block w-full"                   
                                        placeholder="Nombre de la empresa"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                                <div class="p-3 basis-2/4">
                                    <InputLabel for="ruc" value="RUC:" />
                                    <TextInput 
                                        id="ruc"
                                        v-model="form.ruc"
                                        type="text"
                                        class="mt-1 block w-full" 
                                        placeholder="RUC"
                                    />
                                    <InputError class="mt-2" :message="form.errors.ruc" />
                                </div>                                                                
                            </div>
                            <div class="sm:flex"> 
                                <div class="p-3 basis-1/4">
                                    <InputLabel for="dv" value="DV:" />
                                    <TextInput 
                                        id="dv"
                                        v-model="form.dv"
                                        type="text"
                                        class="mt-1 block w-full"  
                                        placeholder="DV"
                                    />
                                    <InputError class="mt-2" :message="form.errors.dv" />
                                </div>
                                <div class="p-3 basis-1/4">
                                    <InputLabel for="phone" value="Telefono:" />
                                    <TextInput 
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full" 
                                        placeholder="Telefono"
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone" />
                                </div>
                                <div class="p-3 basis-2/4">
                                    <InputLabel for="email" value="E-mail:" />
                                    <TextInput 
                                        id="email"
                                        v-model="form.email"  
                                        type="text"
                                        class="mt-1 block w-full"                   
                                        placeholder="E-mail"
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>                            
                            </div>
                            <div class="sm:flex">                    
                                <div class="p-3 basis-2/4">
                                    <InputLabel for="logo" value="Logo:" />
                                    <input type="file" @input="form.logo = $event.target.files[0]" @change="cambiarImagen" accept="image/*" id="logo" class="mt-1 block w-full" />
                                    <InputError class="mt-2" :message="form.errors.logo" />
                                </div>
                                <div class="p-3 basis-2/4">
                                    <img src="" id="picture" width="200" />
                                    <DangerButton v-if="form.logo" @click="$event => deleteLogo()" title="Eliminar Logo" >
                                        <i class="fa-solid fa-trash"></i> Eliminar Logo
                                    </DangerButton>                                     
                                </div>                             
                            </div> 
                            <div class="p-3">
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
                            </div>
                        </form>                       
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
