<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Paginator from "@/Components/Paginator.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    message: {type:String},
});
const nameInput = ref(null);
const form = useForm({ name: '', email: '', phone: '', subject: '', description: ''});

onMounted(() => {
    nextTick(()=> nameInput.value.focus());
});

const ok = (msj) => {
    form.reset();    
    Swal.fire({title:msj, icon:'success'});
    form.errors.name = null;
    form.errors.email = null;
    form.errors.phone = null;
    form.errors.subject = null;
    form.errors.description = null;
    form.errors.revisa = null;
}

const save = () => {   
    form.post(route('contact.store'), {
        onSuccess: () => {ok('Mensaje enviado.')},
        onError: (errors) => {
            console.error(errors);
        },
    });      
}
</script>

<template>
    <Head title="Bienvenido" />

    <div class="sm:flex sm:justify-center min-h-screen bg-center bg-gray-100 selection:bg-red-500 selection:text-white" >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Dashboard</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >
            </template>
        </div>

        <div class="w-full mx-auto mt-6">
            <div class="max-w-3xl mx-auto bg-white overflow-hidden shadow-xl sm:rounded-lg px-6">             
                <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">                        
                    <form @submit.prevent="save">                       
                        <div class="sm:flex">                            
                            <div class="p-3 basis-2/4">
                                <InputLabel for="username" value="Nombre:" />
                                <TextInput 
                                    id="name"
                                    v-model="form.name"  ref="nameInput"
                                    type="text"
                                    class="mt-1 block w-full"                   
                                    placeholder="Nombre"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>
                            <div class="p-3 basis-2/4">
                                <InputLabel for="email" value="Correo:" />
                                <TextInput 
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    placeholder="Correo"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>                            
                        </div>
                         <div class="sm:flex">                            
                            <div class="p-3 basis-2/4">
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
                                <InputLabel for="subject" value="Asunto:" />
                                <TextInput 
                                    id="subject"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.subject"
                                    required
                                    placeholder="Asunto"
                                />
                                <InputError class="mt-2" :message="form.errors.subject" />
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
                                :disabled="form.processing" @click="save()">
                                <i class="fa-regular fa-envelope mr-2"></i> Enviar
                            </PrimaryButton>
                        </div>
                        <InputError class="mt-2" :message="form.errors.revisa" />
                    </form>                    
                </div>
            </div>
        </div>       
    </div>
</template>