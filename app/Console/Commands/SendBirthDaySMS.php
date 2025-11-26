<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\User;
use App\Http\Controllers\OTPVerificationController;

class SendBirthDaySMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdaywish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For BirthDay Wish SMS Send';

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
     * @return mixed
     */
    public function handle()
    {
        $customer_info = User::join('customers','customers.user_id','users.id')
        ->select('users.name','users.phone','customers.dob')
        ->where('user_type','customer')
        ->where('customers.dob', '>','1970-01-01')->get();
            
      	foreach($customer_info as $customer ){
           	if(!empty(($customer->dob) && ($customer->shipping_address))){
              	$get_phone = json_decode($customer->shipping_address)->phone;
              	$date = date('m-d',strtotime($customer->dob));
              	$today =date('m-d');
                if($date === $today){
                	try {
                      	$otpController = new OTPVerificationController;
                   		$otpController->send_birth_day_wish_sms($get_phone,$customer);
                  	} catch (\Exception $e) {
            		}
       			}
     		}
  		}
        $this->info('Birthday wish SMS Command has been run successfully');
    }
 
}
