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
    clients: {type:Object},
    memberships: {type:Object},
    payment_types: {type:Object},
    q: {type:String},
    autorized: {type:String}
});
const nameInput = ref(null);
const ventanamodal = ref(false);
const modalpay = ref(false);
const updatepassword = ref(true);
const name_user = ref('');
const price_membership = ref('');
const title = ref('');
const id_client = ref(0);
const id_userpayment = ref(0);

const form = useForm({
    name: '', email: '', password: '', password_confirmation: '', cedula: '', phone: '', address: '', birthday: ''
});
const form_pay = useForm({ amount: '', date_buys: '', user_id: '', membership_id: '', payment_type_id: ''});
const form_concurrence = useForm({ user_id: '', to_redirect: 'clients.index' });
const search = useForm({ q: props.q });

const submit = ()=>{
    search.get(route('clients.index'));
}

const openModal = (id, name, email, cedula, phone, address, birthday)=>{    
    ventanamodal.value = true;
    nextTick(()=> nameInput.value.focus());
    id_client.value = id;
    if(id==0){
        title.value = 'Crear cliente';
        form.birthday = currentDate();
    }else{        
        title.value = 'Editar cliente';
        form.name = name;
        form.email = email;
        form.cedula = cedula;
        form.phone = phone;
        form.address = address;
        form.birthday = birthday;
        updatepassword.value = false;
    }
}

const openModalpay = (id, name)=>{    
    modalpay.value = true;
    id_userpayment.value = id;
    if(id > 0){
        name_user.value = name;
        form_pay.user_id = id.toString();             
        form_pay.date_buys =  currentDate();
    }
}

const saveConcurrence = (id,) => {
    form_concurrence.user_id = id;
    form_concurrence.post(route('concurrences.store'), {
        onSuccess: () => ok('Entrada agregada'),
        onError: (errors) => {
            console.error(errors);
        },
    });
}

const save = ()=>{
    if(id_userpayment.value > 0){
        form_pay.post(route('payments.store'), {
            onSuccess: () => ok('Pago creado'),
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else if(id_client.value==0){
        form.post(route('clients.store'), {
            onSuccess: () => ok('Cliente creado'),
            //onFinish: () => closeModal(),    
            onError: (errors) => {
                console.error(errors);
            },
        });        
    }else if(id_client.value > 0){
        form.put(route('clients.update', id_client.value), {
            onSuccess: () => ok('Cliente actualizado'),
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

    modalpay.value = false;
    id_userpayment.value = 0;
    form_pay.reset();
    form_concurrence.reset();
}

const ok = (msj) => {
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
            form.delete(route('clients.destroy', id), {
                onSuccess: ()=>{ok('Cliente eliminado')},
                onError: (errors) => {
                    console.error(errors);
                },
            });
        }
    });
}

const currentDate = () =>{
    const fechaActual = new Date();
    const formatoFecha = fechaActual.toISOString().split('T')[0]; // Formato YYYY-MM-DD 
    return formatoFecha;
}

const costMembership = ()=>{
    const membership = props.memberships.find(m => m.id === parseInt(form_pay.membership_id, 10));

    if (membership) {
        price_membership.value = membership.price.toString();
    }
}
</script>

<template>
    <AuthenticatedLayout title="Lista de Clientes" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lista de Clientes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                        <div class="flex mr-3 w-full">
                            <TextInput id="q" title="Buscar por Nombre"
                                type="text"
                                class="mt-1 w-full mr-3"
                                v-model="search.q"
                                placeholder="Buscar por Nombre"
                            />
                            <PrimaryButton @click="submit" title="Buscar cliente">
                                <i class="fa-solid fa-search"></i> Buscar
                            </PrimaryButton> 
                        </div>
                        <PrimaryButton @click="$event => openModal(0)" title="Agregar cliente">
                            <i class="fa-solid fa-plus-circle"></i> Agregar cliente
                        </PrimaryButton>                    
                    </div>                    
                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 py-2">Nombre</th>
                                    <th class="border border-gray-400 px-2 py-2">Email</th>
                                    <th class="border border-gray-400 px-2 py-2">Telefono</th>
                                    <th class="border border-gray-400 px-2 py-2">Fecha de cumpleaños</th>
                                    <th class="border border-gray-400 px-2 py-2 text-center" colspan="4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="client, i in clients.data" :key="client.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{client.name}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{client.email}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{client.profile.phone}}</td>
                                    <td class="border border-gray-400 px-2 py-2">{{client.profile.birthday}}</td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModal(client.id, client.name, 
                                            client.email, client.profile.cedula, client.profile.phone, 
                                            client.profile.address, client.profile.birthday)" title="Editar cliente">
                                            <i class="fa-solid fa-edit"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => openModalpay(client.id, client.name)" title="Pagar membresia" class="bg-sky-500">
                                            <i class="fa-regular fa-address-card"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <WarningButton @click="$event => saveConcurrence(client.id)" title="Agregar asistencia" class="bg-sky-500">
                                            <i class="fa-regular fa-clock"></i>
                                        </WarningButton>
                                    </td>
                                    <td class="border border-gray-400 px-2 py-2">
                                        <DangerButton @click="$event => deleteClient(client.id, client.name)" title="Eliminar cliente">
                                            <i class="fa-solid fa-trash"></i>
                                        </DangerButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-white grid v-screen place-items-center py-3">
                        <Paginator :paginator="clients" />
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
                <InputLabel for="name" value="Nombre del cliente:" />
                <TextInput 
                    id="name"
                    v-model="form.name"  ref="nameInput"
                    type="text"
                    class="mt-1 block w-full"                   
                    placeholder="Nombre del cliente"
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
                <div class="p-3 basis-1/2">
                    <InputLabel for="cedula" value="Numero de cedula:" />
                    <TextInput id="cedula"  type="text"
                        class="mt-1 block w-full"
                        v-model="form.cedula"
                        required
                        placeholder="Numero de cedula"
                    />
                    <InputError class="mt-2" :message="form.errors.cedula" />
                </div>               
            </div>
            <div class="sm:flex">                
                <div class="p-3 basis-1/2">
                    <InputLabel for="phone" value="Numero de telefono:" />
                    <TextInput id="phone"  type="tel"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                        required
                        placeholder="Numero de telefono"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
                <div class="p-3 basis-1/2">
                    <InputLabel for="birthday" value="Fecha de nacimiento:" />
                    <TextInput 
                        id="birthday"
                        v-model="form.birthday"
                        type="date"
                        class="mt-1 block w-full"                        
                        required
                        placeholder="Fecha de nacimiento"
                    />
                    <InputError class="mt-2" :message="form.errors.birthday" />
                </div>              
            </div>           
            <div class="p-3">
                <InputLabel for="address" value="Dirección:" />
                <textarea id="address" v-model="form.address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"                   
                    placeholder="Dirección"
                    required rows="5"></textarea>               
                <InputError class="mt-2" :message="form.errors.address" />
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
        <Modal :show="modalpay" @close="closeModal">
            <div class="sm:flex">                
                <h2 class="p-3 text-lg font.medium text-gray-900">
                    Crear pago                   
                </h2>               
            </div>
            <form @submit.prevent="save">
                <div class="sm:flex">
                    <div class="p-3 basis-2/4">
                        <InputLabel for="user_id" value="Cliente:" />
                        <TextInput 
                            v-model="name_user" 
                            type="text"
                            class="mt-1 block w-full" 
                            readonly
                        />
                        <TextInput 
                            v-model="form_pay.user_id" 
                            type="hidden"
                        />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="membership_id" value="Membresia:" />
                        <SelectInput :options="memberships" v-model="form_pay.membership_id" @change="costMembership" class="mt-1 block w-full"/>                    
                        <InputError class="mt-2" :message="form_pay.errors.membership_id" />
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
                            v-model="form_pay.amount"  ref="nameInput"
                            type="text"
                            class="mt-1 block w-full"                   
                            placeholder="Monto a pagar"
                            required
                            autofocus
                            autocomplete="amount"
                        />
                        <InputError class="mt-2" :message="form_pay.errors.amount" />
                    </div>
                </div>
           
                <div class="sm:flex">
                    <div class="p-3 basis-2/4">
                        <InputLabel for="payment_type_id" value="Tipo de pago:" />
                        <SelectInput :options="payment_types" v-model="form_pay.payment_type_id" class="mt-1 block w-full"/>                    
                        <InputError class="mt-2" :message="form_pay.errors.payment_type_id" />
                    </div>
                    <div class="p-3 basis-2/4">
                        <InputLabel for="date_buys" value="Fecha de compra:" />
                        <TextInput 
                            id="date_buys"
                            v-model="form_pay.date_buys"
                            type="date"
                            class="mt-1 block w-full"                        
                            required
                            placeholder=""
                        />
                        <InputError class="mt-2" :message="form_pay.errors.date_buys" />
                    </div>                                        
                </div>            
                <div class="p-3 mt-6 flex justify-between">
                    <PrimaryButton :class="{ 'opacity-25': form_pay.processing }" 
                        :disabled="form_pay.processing" @click="save">
                        <i class="fa-solid fa-save"></i> Guardar
                    </PrimaryButton>            
               
                    <SecondaryButton :class="{ 'opacity-25': form_pay.processing }" 
                        :disabled="form_pay.processing" @click="closeModal">
                        <i class="fa-solid fa-ban"></i> Cancelar
                    </SecondaryButton>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
