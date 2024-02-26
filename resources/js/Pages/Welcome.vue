<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Paginator from "@/Components/Paginator.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    concurrences: {type:Object},
    message: {type:String},
});

const form = useForm({ entry_time: '', departure_time: '', user_id: ''});
const form_concurrence = useForm({ username: '', password: '',revisa:''});

const ok = (msj) => {
    form.reset();
    form_concurrence.reset(); 
    Swal.fire({title:msj, icon:'success'});
    form_concurrence.errors.username = null;
    form_concurrence.errors.password = null;
    form_concurrence.errors.revisa = null;
}

const save = (id) => {
    if (id > 0) {
        form.put(route('home.update', id), {
            onSuccess: () => ok('Salida agregada'),
            onError: (errors) => {
                console.error(errors);
            },
        });
    } else {
        form_concurrence.post(route('home.store'), {
            onSuccess: () => {ok('Entrada agregada')},
            onError: (errors) => {
                console.error(errors);
            },
        });
    }        
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

        <div class="w-full mx-auto">
            <div class="max-w-3xl mx-auto bg-white mb-6 py-6 flex justify-center" style="height: 120px;">
                <ApplicationLogo />         
            </div>            

            <div class="max-w-3xl mx-auto bg-white overflow-hidden shadow-xl sm:rounded-lg px-6">             
                <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">                        
                    <form @submit.prevent="save">                       
                        <div class="sm:flex">                            
                            <div class="p-3 basis-2/4">
                                <InputLabel for="username" value="Nombre de usuario:" />
                                <TextInput 
                                    id="username"
                                    v-model="form_concurrence.username"
                                    type="text"
                                    class="mt-1 block w-full"                   
                                    placeholder="Nombre de usuario"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form_concurrence.errors.username" />
                            </div>
                            <div class="p-3 basis-2/4">
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput 
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form_concurrence.password"
                                    required
                                    placeholder="Contraseña"
                                    autocomplete="current-password"
                                />
                                <InputError class="mt-2" :message="form_concurrence.errors.password" />
                            </div>
                            <div class="p-3 mt-6 flex justify-between">
                                <PrimaryButton :class="{ 'opacity-25': form_concurrence.processing }" 
                                    :disabled="form_concurrence.processing" @click="save(0)">
                                    <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Entrar
                                </PrimaryButton>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form_concurrence.errors.revisa" />
                    </form>                    
                </div>                    
                <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                    <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr class="border-gray-100">
                                <th class="border border-gray-400 text-center">#</th>
                                <th class="border border-gray-400 px-2 py-2">Cliente</th>
                                <th class="border border-gray-400 text-center">Entrada</th>
                                <th class="border border-gray-400 text-center">Salida</th>
                                <th class="border border-gray-400 py-2 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="concurrence, i in concurrences.data" :key="concurrence.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                                <td class="border border-gray-400 text-center">{{i + 1}}</td>
                                <td class="border border-gray-400 px-2">{{concurrence.user.name}}</td>
                                <td class="border border-gray-400 text-center">{{concurrence.entry_time}}</td>
                                <td class="border border-gray-400 text-center">{{concurrence.departure_time}}</td>                                    
                                <td class="border border-gray-400 py-2 text-center">
                                    <DangerButton @click="$event => save(concurrence.id)" title="Salir" >
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Salir
                                    </DangerButton>
                                </td>                                    
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-white grid v-screen place-items-center py-3">
                    <Paginator :paginator="concurrences" />
                </div>
            </div>
        </div>       
    </div>
</template>