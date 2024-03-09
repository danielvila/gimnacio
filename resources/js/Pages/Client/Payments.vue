<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Paginator from "@/Components/Paginator.vue";

const props = defineProps({
    payments: {type:Object},
    autorized: {type:String}
});

const currentDate = (duration, date_buys) =>{
    const fechaServidor = new Date(date_buys);
    const fechaUsuario = new Date();
    const diferenciaTiempo = fechaUsuario.getTime() - fechaServidor.getTime();
    const diferenciaDias = Math.floor(diferenciaTiempo / (1000 * 60 * 60 * 24));
    const diasrestantes = parseInt(duration, 10) - diferenciaDias; 

    return diasrestantes;
}

const calcularDiferencia = (duration, date_buys)=> {
    const diasrestantes = currentDate(duration, date_buys);
    if(diasrestantes < 0){
        return 'Membresia vencida';
    } else{
        return diasrestantes;
    }
}

const membreciavencida = (duration, date_buys)=> {
    const diasrestantes = currentDate(duration, date_buys);
    if(diasrestantes < 0){
        return 'bg-red-600 text-white';
    } else{
        return '';
    }
}
</script>
<template>
    <AuthenticatedLayout title="Lista de Clientes" :autorized="autorized">
        <template #header>           
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de pagos
            </h2>
        </template>

        <div class="py-12">                       
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-3 lg:p-8 flex justify-between border-b border-gray-200">
                    </div>

                    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">                                                         
                        <table class="w-full border text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="border-gray-100">
                                    <th class="border border-gray-400 px-2 py-2">#</th>
                                    <th class="border border-gray-400 px-2 ">Membresia</th>
                                    <th class="border border-gray-400 px-2 ">Pago</th>
                                    <th class="border border-gray-400 px-2 ">Fecha de compra</th>
                                    <th class="border border-gray-400 px-2 ">Fecha de vencimiento</th>                                   
                                    <th class="border border-gray-400 px-2 " width="150">Días disponibles</th>
                                    <th class="border border-gray-400 px-2 ">Tipo de pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payment, i in payments.data" :key="payment.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="border border-gray-400 px-2 py-2">{{i + 1}}</td>
                                    <td class="border border-gray-400 px-2 ">{{payment.membership.name}}</td>
                                    <td class="border border-gray-400 px-2 ">{{payment.amount}}</td>                                                
                                    <td class="border border-gray-400 px-2 ">{{payment.date_buys}}</td>
                                     <td class="border border-gray-400 px-2 ">{{payment.date_buys_end}}</td>
                                    <td class="border border-gray-400 px-2 " :class="membreciavencida(payment.membership.duration, payment.date_buys)">
                                        {{calcularDiferencia(payment.membership.duration, payment.date_buys)}}
                                    </td>
                                    <td class="border border-gray-400 px-2 ">{{payment.payment_type.name}}</td>
                                </tr>
                            </tbody>
                        </table>                           
                    </div>                    
                </div>
                <div class="bg-white grid v-screen place-items-center py-3">
                    <Paginator :paginator="payments" />
                </div> 
            </div>
        </div>
    </AuthenticatedLayout>
</template>