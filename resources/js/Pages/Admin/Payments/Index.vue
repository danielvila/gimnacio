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
import { nextTick, onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    payments: {type:Object},
    date_start: {type:String},
    date_end: {type:String},
    memberships: {type:Object},
    payment_types: {type:Object},
    users: {type:Object},    
    autorized: {type:String}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const title = ref('');
const id_payment = ref(0);
const price_membership = ref('');

const form = useForm({ type_receptor: 2, type_contrib: 1, amount: '', date_buys: '', user_id: '0', name: '0', email: '', ruc: '', dv: '', membership_id: '0', membership: '', payment_type_id: '0', payment_name: ''});
const search = useForm({ date_start: props.date_start, date_end: props.date_end });

const submit = ()=>{
    search.get(route('payments.index'));
}
onMounted(() => {
    search.date_start = currentDate(props.date_start);
    search.date_end = currentDate(props.date_end);
    props.memberships.unshift({ "id": 0, "name": "Seleccione una membresia" });
    props.payment_types.unshift({ "id": 0, "name": "Seleccione un tipo de pago" });
    props.users.unshift({ "id": 0, "name": "Seleccione un cliente" });
});
const currentDate = (lafecha) =>{
    const fechaActual = new Date(lafecha);
    const formatoFecha = fechaActual.toISOString().split('T')[0]; // Formato YYYY-MM-DD 
    return formatoFecha;
}

const openModal = (id, payment)=>{  
    ventanamodal.value = true;    
    id_payment.value = id;
    if(id==0){
        title.value = 'Crear Pago';        

        const fechaActual = new Date();
        const formatoFecha = fechaActual.toISOString().split('T')[0]; // Formato YYYY-MM-DD      
        form.date_buys =  formatoFecha;
    }else{
        nextTick(()=> nameInput.value.focus());    
        title.value = 'Editar Pago';
        form.amount = payment.amount.toString();
        form.date_buys = payment.date_buys;
        form.user_id = payment.user_id.toString();
        form.name = payment.user.name;
        form.email = payment.user.email;
        form.membership_id = payment.membership.id.toString();
        form.membership = payment.membership.name;
        form.payment_type_id = payment.payment_type_id.toString();
        form.payment_name = payment.payment_type.name;
        costMembership();
    }
}

const save = ()=>{    
    if(id_payment.value==0){
        form.post(route('payments.store'), {
            onSuccess: () => ok('Pago creado'),
            onError: (errors) => {
                console.error(errors);
            },
        });     
    }else{
        form.put(route('payments.update', id_payment.value), {
            onSuccess: () => ok('Pago actualizado'), 
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }
}

const nameUser = (event) => {
  const valorSeleccionado = event.target.value;
  const opcionSeleccionada = props.users.find(c => c.id === parseInt(valorSeleccionado));
  if (opcionSeleccionada.id > 0 ) {
    form.name = opcionSeleccionada.name;
  }else{
    form.name = '';
  }
}

const namePayment = (event) => {
  const valorSeleccionado = event.target.value;
  const opcionSeleccionada = props.payment_types.find(p => p.id === parseInt(valorSeleccionado));
  if (opcionSeleccionada.id > 0 ) {
    form.payment_name = opcionSeleccionada.name;
  }else{
    form.payment_name = '';
  }
}

const closeModal = ()=>{
    ventanamodal.value = false;
    id_payment.value = 0;
    price_membership.value = '';
    form.reset();
}

const ok = (msj) => {
    form.reset();
    closeModal();
    Swal.fire({title:msj, icon:'success'});
}
const deletePayment = (id, name) => {
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
            form.delete(route('payments.destroy', id), {
                onSuccess: ()=>{ok('Membresia eliminada')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
}

const costMembership = ()=>{
    const membership = props.memberships.find(m => m.id === parseInt(form.membership_id, 10));
 
    if (membership.id > 0) {
        price_membership.value = membership.price.toString();
        form.membership = membership.name;
    }else{
        price_membership.value = '';
        form.membership = '';
    }
}

const membreciavencida = (duration, date_buys)=> {
    const fechaServidor = new Date(date_buys);
    const fechaUsuario = new Date();
    const diferenciaTiempo = fechaUsuario.getTime() - fechaServidor.getTime();
    const diferenciaDias = Math.floor(diferenciaTiempo / (1000 * 60 * 60 * 24));
    const diasrestantes = parseInt(duration, 10) - diferenciaDias;
    if(diasrestantes < 0){
        return 'bg-red-600 text-white';
    } else{
        return '';
    }
}
</script>

<template>
    <AuthenticatedLayout title="Lista de Pagos" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Pagos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex items-center justify-between border-b border-gray-200">                        
                        <div class=" flex items-center p-3 basis-1/4">
                            <InputLabel for="date_start" value="Desde:" />
                            <TextInput 
                                id="date_start"
                                v-model="search.date_start"
                                type="date"
                                class="ml-1 block w-full" 
                            />
                        </div>  
                        <div class=" flex items-center p-3 basis-1/4">
                            <InputLabel for="date_end" value="Hasta:" />
                            <TextInput 
                                id="date_end"
                                v-model="search.date_end"
                                type="date"
                                class="ml-1 block w-full"  
                            />
                        </div>
                        <div class=" basis-1/4">
                            <PrimaryButton @click="submit" title="Buscar cliente">
                                <i class="fa-solid fa-search"></i> Buscar
                            </PrimaryButton>
                        </div>
                        <div class="grid justify-items-end basis-1/4">
                            <PrimaryButton 
                                @click="$event => openModal(0)">
                                <i class="fa-solid fa-plus-circle"></i> Agregar pago
                            </PrimaryButton> 
                        </div>                   
                    </div>
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Cliente</th>
                                    <th class="border border-gray-400 px-2 py-2">Membresia</th>
                                    <th class="border border-gray-400 px-2 py-2">Pago</th>
                                    <th class="border border-gray-400 px-2 py-2">Fecha de compra</th>
                                    <th class="border border-gray-400 px-2 py-2">Vence compra</th>
                                    <th class="border border-gray-400 px-2 py-2">Tipo de pago</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment, i in payments.data" :key="payment.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{payment.user.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{payment.membership.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{payment.amount}}</td>                                   
                                    <td class="border border-gray-400 px-2 py-2">{{payment.date_buys}}</td>
                                    <td class="border border-gray-400 px-2 py-2" :class="membreciavencida(payment.membership.duration, payment.date_buys)">{{payment.date_buys_end}}</td>                                    
                                    <td class="border border-gray-400 px-2 py-2">{{payment.payment_type.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(payment.id, payment)">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deletePayment(payment.id, payment.amount)">
                                            <i class="fa-solid fa-trash"></i>
                                        </DangerButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-white grid v-screen place-items-center py-3">
                        <Paginator :paginator="payments" />
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
                    <div class="p-3 basis-2/4">
                        <InputLabel for="type_receptor" value="Tipo de receptor:" />
                        <select v-model="form.type_receptor" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                            <option value="1">Contribuyente</option>
                            <option value="2">Consumidor final</option>
                            <option value="3">Gobierno</option>
                            <option value="3">Extranjero</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.type_receptor" />
                    </div>
                    <div v-show="form.type_receptor == 1" class="p-3 basis-2/4">
                        <InputLabel for="type_contrib" value="Tipo de Contribuyente:" />
                        <select v-model="form.type_contrib" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                            <option value="1">Natural</option>
                            <option value="2">Jurídico</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.type_contrib" />
                    </div>                       
                </div>
                <div class="sm:flex">
                    <div class="p-3 basis-2/4">
                        <InputLabel for="user_id" value="Cliente:" />
                        <SelectInput :options="users" v-model="form.user_id" class="mt-1 block w-full" @change="nameUser"/>                    
                        <InputError class="mt-2" :message="form.errors.user_id" />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="membership_id" value="Membresia:" />
                        <SelectInput :options="memberships" v-model="form.membership_id" @change="costMembership" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.membership_id" />
                    </div>                              
                </div>
                <div class="sm:flex" v-show="form.type_receptor == 1" >
                    <div class="p-3 basis-2/4">
                        <InputLabel for="contrib_name" value="Nombre del contribuyente:" />
                        <TextInput 
                            v-model="form.contrib_name"
                            type="text"
                            class="mt-1 block w-full"      
                            placeholder="Nombre del contribuyente"
                        />
                        <InputError class="mt-2" :message="form.errors.contrib_name" />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="contrib_email" value="Email del contribuyente:" />
                        <TextInput 
                            v-model="form.contrib_email"
                            type="text"
                            class="mt-1 block w-full"      
                            placeholder="Email del contribuyente"
                        />
                        <InputError class="mt-2" :message="form.errors.contrib_email" />
                    </div>
                </div>
                <div class="sm:flex" v-show="form.type_receptor == 1" >
                    <div class="p-3 basis-2/4">
                        <InputLabel for="contrib_address" value="Dirección del contribuyente:" />
                        <TextInput 
                            v-model="form.contrib_address"
                            type="text"
                            class="mt-1 block w-full"      
                            placeholder="Dirección del contribuyente"
                        />
                        <InputError class="mt-2" :message="form.errors.contrib_address" />
                    </div>
                    <div class="p-3 basis-1/4">
                        <InputLabel for="contrib_ruc" value="RUC del contribuyente:" />
                        <TextInput 
                            v-model="form.contrib_ruc"
                            type="text"
                            class="mt-1 block w-full"      
                            placeholder="RUC del contribuyente"
                        />
                        <InputError class="mt-2" :message="form.errors.contrib_ruc" />
                    </div>
                    <div class="p-3 basis-1/4">
                        <InputLabel for="dv" value="DV del contribuyente:" />
                        <TextInput 
                            v-model="form.dv"
                            type="text"
                            class="mt-1 block w-full"      
                            placeholder="DV del contribuyente"
                        />
                        <InputError class="mt-2" :message="form.errors.dv" />
                    </div>
                </div>
                <div class="sm:flex">
                    <div class="p-3 basis-2/4">
                        <span class="block font-medium text-sm text-gray-700">Costo de la membresia:</span>
                        <TextInput 
                            v-model="price_membership" 
                            type="text"
                            class="mt-1 block w-full"                   
                            placeholder="Costo de la membresia"
                            readonly
                        />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="amount" value="Monto a pagar:" />
                        <TextInput 
                            id="amount"
                            v-model="form.amount"  ref="nameInput"
                            type="text"
                            class="mt-1 block w-full"                   
                            placeholder="Monto a pagar"
                            required
                            autofocus
                            autocomplete="amount"
                        />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>
                </div>
           
                <div class="sm:flex">
                    <div class="p-3 basis-2/4">
                        <InputLabel for="payment_type_id" value="Tipo de pago:" />
                        <SelectInput :options="payment_types" v-model="form.payment_type_id" @change="namePayment" class="mt-1 block w-full"/>                    
                        <InputError class="mt-2" :message="form.errors.payment_type_id" />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="date_buys" value="Fecha de compra:" />
                        <TextInput 
                            id="date_buys"
                            v-model="form.date_buys"
                            type="date"
                            class="mt-1 block w-full"                        
                            required
                            placeholder=""
                        />
                        <InputError class="mt-2" :message="form.errors.date_buys" />
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
