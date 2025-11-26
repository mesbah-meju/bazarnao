<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\CustomerResource;
use App\Http\Resources\V2\CustomerReviewCollection;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function show($id)
    {
        return new CustomerResource(Customer::find($id));
    }
    
    public function customer_review()
    {
       $reviews = Customer::join('users','users.id','=','customers.user_id')->where('customers.testimonial','!=', 'NULL')->select('users.name','customers.testimonial','customers.testimonial_bangla','users.avatar_original')->get();
       return new CustomerReviewCollection($reviews);
    }

}
