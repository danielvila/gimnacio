<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Paginator from "@/Components/Paginator.vue";
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    concurrences: {type:Object},
    date_concurrence: {type:String},
    autorized: {type:String}
});
const date_concurrences = [
  { id: 'local', name: 'En local' },
  { id: 'salieron', name: 'Ya salieron' },
  { id: 'all', name: 'Todos' }
];
const form = useForm({ to_redirect: 'concurrences.index'});
const search = useForm({ date_concurrence: props.date_concurrence });

const submit = ()=>{
    search.get(route('concurrences.index'));
}

const ok = (msj) => {   
    Swal.fire({title:msj, icon:'success'});
    form.reset();
}

const saveConcurrence = (id,) => {
    form.put(route('home.update', id), {
        onSuccess: () => ok('Salida agregada'),
        onError: (errors) => {
            console.error(errors);
        },
    });
}
</script>

<template>
    <AuthenticatedLayout title="Lista de Asistencia" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Asistencia
            </h2>
            <div v-if="$page.props.flash.message" class="alert">
                {{ ok($page.props.flash.message) }}
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                        <div class="p-3 basis-2/4">
                            <InputLabel for="date_concurrence" value="Cliente:" />
                            <SelectInput :options="date_concurrences" v-model="search.date_concurrence"  @change="submit" class="mt-1 block w-full"/>
                        </div>
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Cliente</th>
                                    <th class="border border-gray-400 px-2 py-2">Entrada</th>
                                    <th class="border border-gray-400 px-2 py-2">Salida</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="concurrence, i in concurrences.data" :key="concurrence.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{concurrence.user.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{concurrence.entry_time}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{concurrence.departure_time}}</td>                                    
                                    <td class="border border-gray-400 px-2 py-2 text-center">
                                        <DangerButton @click="$event => saveConcurrence(concurrence.id)" title="Agregar salida" >
                                            <i class="fa-regular fa-clock"></i>
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
    </AuthenticatedLayout>
</template>
