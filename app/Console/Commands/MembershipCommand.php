<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactanosMailable;
use App\Models\Payment;
use Carbon\Carbon;

class MembershipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se envia correo con aviso que la membresia esta proxima a vencerse.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = Payment::with(['membership','user'])->where('date_buys_end', '>', now())->get();
        $hoy = now();
        $clients = [];
        foreach($payments as $key => $payment){
            $fechaEvento = Carbon::createFromFormat('Y-m-d', $payment->date_buys_end);
            $diasFaltantes = $hoy->diffInDays($fechaEvento);
            if ($payment->membership->duration == '365' && $diasFaltantes <= 30) {
                if($diasFaltantes == 30 || $diasFaltantes == 23 || $diasFaltantes == 15 || $diasFaltantes == 8 || $diasFaltantes == 1){
                    $clients[] = ['name'=>$payment->user->name, 'email'=>$payment->user->email, 'days'=>$diasFaltantes, 'subject'=>'Vencimiento de membresia']; 
                }                              
            }else if ($payment->membership->duration == '90' && $diasFaltantes <= 30) {
                if($diasFaltantes == 30 || $diasFaltantes == 23 || $diasFaltantes == 15 || $diasFaltantes == 8 || $diasFaltantes == 1){
                    $clients[] = ['name'=>$payment->user->name, 'email'=>$payment->user->email, 'days'=>$diasFaltantes, 'subject'=>'Vencimiento de membresia']; 
                }
            }else if ($payment->membership->duration == '30' && $diasFaltantes <= 15) {
                if($diasFaltantes == 15 || $diasFaltantes == 8 || $diasFaltantes == 1){
                    $clients[] = ['name'=>$payment->user->name, 'email'=>$payment->user->email, 'days'=>$diasFaltantes, 'subject'=>'Vencimiento de membresia']; 
                }
            }
        }

        foreach ($clients as $key => $client) {
            Mail::to($client['email'])
                ->send(new ContactanosMailable($client));
        }
    }
}
