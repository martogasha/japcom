<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class Billing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Billing clients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cDate = date('d-m-Y');
        $requests=User::where('due_date',$cDate)->get();
        foreach ($requests as $request){
            $currentMonth = date('m');
            $getExistingInvoice = Invoice::where('user_id',$request->id)->where('status',0)->first();
            $getUser = User::find($request->id);
            $paymentDate = date("d-m-Y", strtotime($request->payment_date));
            $dueDate = date("d-m-Y", strtotime($request->due_date));
            $time = strtotime($getUser->due_date);
            $nextDate = date("d-m-Y", strtotime("+1 month", $time));
            if ($getExistingInvoice){
                if ($getUser->payment_date==0){
                    $nextDatePrototype =  date('d-m-Y', strtotime($getUser->due_date. ' - 1 month'));
                    $date1 =$nextDatePrototype;
                    $date2 =$getUser->due_date;
                }
                else{
                    $date1 = $getUser->payment_date;
                    $date2 =$getUser->due_date;
                }

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                if ($months>=1){
                    $usage_time = $days+30;
                }
                else{
                    $usage_time = $days;
                }
                $total = $request->amount + $request->package_amount;
                $createInvoice = Invoice::create([
                    'invoice_date'=>$dueDate,
                    'amount'=>$request->package_amount,
                    'user_id'=>$request->id,
                    'usage_time'=>$getExistingInvoice->usage_time + $usage_time,
                    'balance'=>$total,
                    'status'=>0,
                    'statas'=>0,
                ]);
                $terminateInvoice = Invoice::where('id',$getExistingInvoice->id)->update(['status'=>2]);
                $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$total]);
                $updateAmount = User::where('id',$getUser->id)->update(['amount'=>0]);
                $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>0]);
                $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
            }
            else{

                $date1 = $getUser->payment_date;
                $date2 =$getUser->due_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                if ($months==1){
                    $usage_time = $days+30;
                }
                else{
                    $usage_time = $days;
                }
                $total = $request->amount + $request->package_amount;
                if ($total<=0){
                    $createInvoice = Invoice::create([
                        'invoice_date' => $dueDate,
                        'amount' => $request->package_amount,
                        'user_id' => $request->id,
                        'usage_time' => $usage_time,
                        'balance' => $total,
                        'status' => 1,
                        'statas' => 0,
                    ]);
                    $createP = Payment::create([
                        'invoice_id'=>$createInvoice->id,
                        'user_id'=>$request->id,
                        'date'=>$dueDate,
                        'amount'=>$request->package_amount,
                        'status'=>1,
                        'payment_method'=>'Balance Carry Over',
                        'currentMonth'=>$currentMonth,
                    ]);
                    $updateInvoice = Invoice::where('id',$createInvoice->id)->update(['payment_id'=>$createP->id]);
                    $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$total]);
                    $updateAmount = User::where('id',$getUser->id)->update(['amount'=>$createP->amount]);
                    $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>$request->due_date]);
                    $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
                }
                else {
                    $createInvoice1 = Invoice::create([
                        'invoice_date' => $dueDate,
                        'amount' => $request->package_amount,
                        'user_id' => $request->id,
                        'usage_time' => $usage_time,
                        'balance' => $total,
                        'status' => 0,
                        'statas' => 0,
                    ]);
                    $createP1 = Payment::create([
                        'invoice_id'=>$createInvoice1->id,
                        'user_id'=>$request->id,
                        'date'=>$dueDate,
                        'amount'=>$request->amount*-1,
                        'status'=>1,
                        'payment_method'=>'Balance Carry Over',
                        'currentMonth'=>$currentMonth,
                    ]);
                    $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['payment_id'=>$createP1->id]);
                    $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['balance'=>$total]);
                    $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['balance'=>$total]);
                    $updateBalance = User::where('id',$request->id)->update(['balance'=>$total]);
                    $updateAmount = User::where('id',$request->id)->update(['amount'=>0]);
                    $updatePaymentDate = User::where('id',$request->id)->update(['payment_date'=>0]);
                    $updateDueDate = User::where('id',$request->id)->update(['due_date'=>$nextDate]);
                }
            }

        }
    }
}
