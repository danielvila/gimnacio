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
    full_client: {type:Object},
    payments: {type:Object},
    concurrences: {type:Object},
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



const calcSaldo = (price, pago)=> {
    const saldo = Math.round((price - pago) * 100) / 100
    return saldo;
}

const calcularDiferencia = (duration, date_buys)=> {
    const fechaServidor = new Date(date_buys);
    const fechaUsuario = new Date();
    const diferenciaTiempo = fechaUsuario.getTime() - fechaServidor.getTime();
    const diferenciaDias = Math.floor(diferenciaTiempo / (1000 * 60 * 60 * 24));
    const diasrestantes = parseInt(duration, 10) - diferenciaDias;
    if(diasrestantes < 0){
        return 'Membresia vencida';
    } else{
        return diasrestantes;
    }
}

const membreciavencida = (duration, date_buys)=> {
    const fechaServidor = new Date(date_buys);
    const fechaUsuario = new Date();
    const diferenciaTiempo = fechaUsuario.getTime() - fechaServidor.getTime();
    const diferenciaDias = Math.floor(diferenciaTiempo / (1000 * 60 * 60 * 24));
    const diasrestantes = parseInt(duration, 10) - diferenciaDias;
    if(diasrestantes < 0){
        return true;
    } else{
        return false;
    }
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
</script>
<script>
      /* Optional Javascript to close the radio button version by clicking it again */
      var myRadios = document.getElementsByName('tabs2');
      var setCheck;
      var x = 0;
      for(x = 0; x < myRadios.length; x++){
          myRadios[x].onclick = function(){
              if(setCheck != this){
                   setCheck = this;
              }else{
                  this.checked = false;
                  setCheck = null;
          }
          };
      }
   </script>

<template>
    <AuthenticatedLayout title="Lista de Clientes" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de {{full_client.name}}
            </h2>
        </template>

        <div class="py-12">
                       
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                    </div>

                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">                           
                        <div class="relative flex tabs-container">
                            <div class="tab w-full overflow-hidden border-t">
                               <input class="absolute opacity-0" id="tab-single-one" type="radio" name="tabs2" >
                               <label class="shadow-md block p-5 leading-normal cursor-pointer tab-single-one" for="tab-single-one">Pagos</label>
                                <div class="tab-content overflow-hidden border-l-2 bg-gray-100 border-indigo-500 leading-normal absolute l-0 w-full">
                                    
                                    <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr class="border-gray-100">
                                                <th class="border border-gray-400 px-2 py-2">#</th>
                                                <th class="border border-gray-400 px-2 py-2">Membresia</th>
                                                <th class="border border-gray-400 px-2 py-2">Precio</th>
                                                <th class="border border-gray-400 px-2 py-2">Pago</th>
                                                <th class="border border-gray-400 px-2 py-2">saldo</th>
                                                <th class="border border-gray-400 px-2 py-2">Fecha de compra</th>                                    
                                                <th class="border border-gray-400 px-2 py-2">Días disponibles</th>
                                                <th class="border border-gray-400 px-2 py-2">Tipo de pago</th>
                                                <th class="border border-gray-400 px-2 py-2 text-center" colspan="2">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="payment, i in payments.data" :key="payment.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{payment.membership.name}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{payment.membership.price}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{payment.amount}}</td>
                                                <td class="border border-gray-400 px-2 py-2" :class="calcSaldo(payment.membership.price, payment.amount) > 0 ?'bg-red-600 text-white':''">{{ calcSaldo(payment.membership.price, payment.amount) }}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{payment.date_buys}}</td>
                                                <td class="border border-gray-400 px-2 py-2" :class="membreciavencida(payment.membership.duration, payment.date_buys)==true?'bg-red-600 text-white':''">{{calcularDiferencia(payment.membership.duration, payment.date_buys)}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{payment.payment_type.name}}</td>
                                                <td class="border border-gray-400 px-2 py-2">
                                                    <WarningButton @click="$event => openModal(payment.id, payment.amount, payment.date_buys, payment.user_id, payment.membership_id, payment.payment_type_id)">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </WarningButton>
                                                </td>
                                                <td class="border border-gray-400 px-2 py-2">
                                                    <DangerButton @click="$event => deleteClient(payment.id, payment.amount)">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </DangerButton>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="bg-white grid v-screen place-items-center py-3">
                                        <Paginator :paginator="payments" />
                                    </div>                                  
                                </div>
                            </div>
                            <div class="tab w-full overflow-hidden border-t">
                               <input class="absolute opacity-0" id="tab-single-two" type="radio" name="tabs2">
                               <label class="shadow-md block p-5 leading-normal cursor-pointer tab-single-two" for="tab-single-two">Asistencias</label>
                               <div class="tab-content overflow-hidden border-l-2 bg-gray-100 border-indigo-500 leading-normal absolute l-0 w-full">
                                    <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr class="border-gray-100">
                                                <th class="border border-gray-400 px-2 py-2">#</th>
                                                <th class="border border-gray-400 px-2 py-2">Entrada</th>
                                                <th class="border border-gray-400 px-2 py-2">Salida</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="concurrence, i in concurrences.data" :key="concurrence.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" >
                                                <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{concurrence.entry_time}}</td>
                                                <td class="border border-gray-400 px-2 py-2">{{concurrence.departure_time}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="bg-white grid v-screen place-items-center py-3">
                                        <Paginator :paginator="concurrences" />
                                    </div>
                               </div>
                            </div>                   
                        </div>                        
                    </div>
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<style>
         /* Tab content - closed */
         .tabs-container{height: 400px}
         .tab-content {
         max-height: 0;
         -webkit-transition: max-height .35s;
         -o-transition: max-height .35s;
         transition: max-height .35s;left: 0;
         }
         .tab{
            width: 175px;
         }
         /* :checked - resize to full height */
         .tab input:checked ~ .tab-content {
         max-height: 100vh;
         height: 335px;
         }
         .tab-single-one{border-radius: 8px 0 0 0;}
         .tab-single-two{border-radius: 0 8px  0 0;}
         .tab input + label{
         /*@apply text-xl p-5 border-l-2 border-indigo-500 bg-gray-100 text-indigo*/
         font-size: 1.25rem; /*.text-xl*/
         padding: 1.25rem; /*.p-5*/
         border: 1px solid #4b5563;
         }
        
         /* Label formatting when open */
         .tab input:checked + label{
         /*@apply text-xl p-5 border-l-2 border-indigo-500 bg-gray-100 text-indigo*/
         border-color: #6574cd; /*.border-indigo*/
         background-color: #f8fafc; /*.bg-gray-100 */
         color: #6574cd; /*.text-indigo*/
         }
         /* Icon */
         .tab label::after {
         float:right;
         right: 0;
         top: 0;
         display: block;
         width: 1.5em;
         height: 1.5em;
         line-height: 1.5;
         font-size: 1.25rem;
         text-align: center;
         -webkit-transition: all .35s;
         -o-transition: all .35s;
         transition: all .35s;
         }
         /* Icon formatting - closed */
         .tab input[type=checkbox] + label::after {
         content: "+";
         font-weight:bold; /*.font-bold*/
         border-width: 1px; /*.border*/
         border-radius: 9999px; /*.rounded-full */
         border-color: #b8c2cc; /*.border-grey*/
         }
         .tab input[type=radio] + label::after {
         content: "\25BE";
         font-weight:bold; /*.font-bold*/
         border-width: 1px; /*.border*/
         border-radius: 9999px; /*.rounded-full */
         border-color: #b8c2cc; /*.border-grey*/
         }
         /* Icon formatting - open */
         .tab input[type=checkbox]:checked + label::after {
         transform: rotate(315deg);
         background-color: #6574cd; /*.bg-indigo*/
         color: #f8fafc; /*.text-grey-lightest*/
         }
         .tab input[type=radio]:checked + label::after {
         transform: rotateX(180deg);
         background-color: #6574cd; /*.bg-indigo*/
         color: #f8fafc; /*.text-grey-lightest*/
         }
      </style>