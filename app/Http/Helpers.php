<?php

use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\OTPVerificationController;
use App\Notifications\OrderNotification;
use App\Models\AccCoa;
use App\Models\HappyHour;
use App\Models\HappyHourProduct;
use App\Models\AccMonthlyBalance;
use App\Models\AccOpeningBalance;
use App\Models\AccPredefineAccount;
use App\Models\AccSubcode;
use App\Models\AccSubtype;
use App\Models\AccTransaction;
use App\Models\AccVoucher;
use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\SubSubCategory;
use App\Models\FlashDealProduct;
use App\Models\FlashDeal;
use App\Models\Transfer;
use App\Models\OtpConfiguration;
use App\Models\Upload;
use App\Models\Translation;
use App\Models\City;
use App\Models\CommissionHistory;
use App\Models\Customer_ledger;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Referr_code;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Warehouse;
use App\Models\Staff;
use App\Models\FirebaseNotification;
use App\Models\Customer;
use App\Models\FinancialYear;
use App\Models\Timezones;
use App\Models\Group_product;
use App\Models\OrderDetail;
use App\Models\Pos_ledger;
use App\Models\Purchase;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\CashTransfer;
use App\Models\Wishlist;
use App\Utility\CategoryUtility;
use App\Utility\MimoUtility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;


//highlights the selected navigation on admin panel
if (! function_exists('sendSMS')) {
    function sendSMS($to, $from, $text)
    {
        if (OtpConfiguration::where('type', 'nexmo')->first()->value == 1) {
            $api_key = env("NEXMO_KEY"); //put ssl provided api_token here
            $api_secret = env("NEXMO_SECRET"); // put ssl provided sid here

            $params = [
                "api_key" => $api_key,
                "api_secret" => $api_secret,
                "from" => $from,
                "text" => $text,
                "to" => $to
            ];

            $url = "https://rest.nexmo.com/sms/json";
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } elseif (OtpConfiguration::where('type', 'twillo')->first()->value == 1) {
            $sid = env("TWILIO_SID"); // Your Account SID from www.twilio.com/console
            $token = env("TWILIO_AUTH_TOKEN"); // Your Auth Token from www.twilio.com/console

            $client = new Client($sid, $token);
            try {
                $message = $client->messages->create(
                    $to, // Text this number
                    array(
                        'from' => env('VALID_TWILLO_NUMBER'), // From a valid Twilio number
                        'body' => $text
                    )
                );
            } catch (\Exception $e) {
            }
        } elseif (OtpConfiguration::where('type', 'ssl_wireless')->first()->value == 1) {
            $token = env("SSL_SMS_API_TOKEN", "Bazar Nao-9791e512-b538-45ab-9009-32eb3fefbe5e"); //put ssl provided api_token here
            $sid = env("SSL_SMS_SID", "BAZARNAOAPI"); // put ssl provided sid here

            $params = [
                "api_token" => $token,
                "sid" => $sid,
                "msisdn" => $to,
                "sms" => $text,
                "csms_id" => date('dmYhhmi') . rand(10000, 99999)
            ];

            $url = env("SSL_SMS_URL", "https://smsplus.sslwireless.com/api/v3/send-sms");
            //   echo $url;
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            // echo '<pre>';print_r($response);exit;
            //  print_r($response);exit;
            return $response;
        } elseif (OtpConfiguration::where('type', 'fast2sms')->first()->value == 1) {

            if (strpos($to, '+91') !== false) {
                $to = substr($to, 3);
            }

            $fields = array(
                "sender_id" => env("SENDER_ID"),
                "message" => $text,
                "language" => env("LANGUAGE"),
                "route" => env("ROUTE"),
                "numbers" => $to,
            );

            $auth_key = env('AUTH_KEY');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: $auth_key",
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return $response;
        } elseif (OtpConfiguration::where('type', 'mimo')->first()->value == 1) {
            $token = MimoUtility::getToken();

            MimoUtility::sendMessage($text, $to, $token);
            MimoUtility::logout($token);
        }
    }
}

//highlights the selected navigation on admin panel
if (! function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (! function_exists('areActiveRoutesHome')) {
    function areActiveRoutesHome(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

//highlights the selected navigation on frontend
if (! function_exists('default_language')) {
    function default_language()
    {
        return env("DEFAULT_LANGUAGE");
    }
}

/**
 * Save JSON File
 * @return Response
 */
if (! function_exists('convert_to_usd')) {
    function convert_to_usd($amount)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'USD')->first()->exchange_rate;
        }
    }
}

if (! function_exists('convert_to_kes')) {
    function convert_to_kes($amount)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'KES')->first()->exchange_rate;
        }
    }
}

//filter products based on vendor activation system
if (! function_exists('filter_products')) {
    function filter_products($products)
    {
        $verified_sellers = verified_sellers_id();
        if (BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1) {
            return $products->where('published', '1')->orderBy('created_at', 'desc')->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            return $products->where('published', '1')->where('added_by', 'admin');
        }
    }
}

//cache products based on category
if (! function_exists('get_cached_products')) {
    function get_cached_products($category_id = null)
    {
        $products = \App\Models\Product::where('published', 1);
        $verified_sellers = verified_sellers_id();
        if (BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1) {
            $products =  $products->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            $products = $products->where('added_by', 'admin');
        }

        if ($category_id != null) {
            return Cache::remember('products-category-' . $category_id, 86400, function () use ($category_id, $products) {
                $category_ids = CategoryUtility::children_ids($category_id);
                $category_ids[] = $category_id;
                return $products->whereIn('category_id', $category_ids)->latest()->take(12)->get();
            });
        } else {
            return Cache::remember('products', 86400, function () use ($products) {
                return $products->latest()->get();
            });
        }
    }
}

if (! function_exists('verified_sellers_id')) {
    function verified_sellers_id()
    {
        return App\Models\Seller::where('verification_status', 1)->get()->pluck('user_id')->toArray();
    }
}

//converts currency to home default currency
if (! function_exists('convert_price')) {
    function convert_price($price)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            $price = floatval($price) / floatval($currency->exchange_rate);
        }

        $code = \App\Models\Currency::findOrFail(\App\Models\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if (Session::has('currency_code')) {
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        } else {
            $currency = Currency::where('code', $code)->first();
        }

        $price = floatval($price) * floatval($currency->exchange_rate);

        return $price;
    }
}

//formats currency
if (! function_exists('format_price')) {
    function format_price($price)
    {
        if (BusinessSetting::where('type', 'decimal_separator')->first()->value == 1) {
            $fomated_price = number_format($price, BusinessSetting::where('type', 'no_of_decimals')->first()->value);
        } else {
            $fomated_price = number_format($price, BusinessSetting::where('type', 'no_of_decimals')->first()->value, ',', ' ');
        }

        if (BusinessSetting::where('type', 'symbol_format')->first()->value == 1) {
            return currency_symbol() . ' ' . $fomated_price;
        }
        return $fomated_price . ' ' . currency_symbol();
    }
}

//formats price to home default price with convertion
if (! function_exists('single_price')) {
    function single_price($price)
    {
        return format_price(convert_price($price));
    }
}

//Shows Price on page based on low to high
if (! function_exists('main_home_price')) {
    function main_home_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);

        if ($lowest_price == $highest_price) {
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}

//Shows Price on page based on low to high
if (! function_exists('home_price')) {
    function home_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);

        if ($lowest_price == $highest_price) {
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}

//Shows Price on page based on low to high with discount
if (! function_exists('home_discounted_price')) {
    function home_discounted_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if ($product->app_discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->app_discount) / 100;
                $highest_price -= ($highest_price * $product->app_discount) / 100;
            } elseif ($product->app_discount_type == 'amount') {
                $lowest_price -= $product->app_discount;
                $highest_price -= $product->app_discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);

        if ($lowest_price == $highest_price) {
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}

//Shows Price on page based on low to high with discount
if (! function_exists('main_home_discounted_price')) {
    function main_home_discounted_price($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        // Check for variant products
        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        // Check for active Flash Deals
        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if (
                $flash_deal != null
                && $flash_deal->status == 1
                && strtotime(date('d-m-Y')) >= $flash_deal->start_date
                && strtotime(date('d-m-Y')) <= $flash_deal->end_date
                && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null
            ) {

                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        // Check for active Happy Hours
        $happy_hours = \App\Models\HappyHour::where('status', 1)
            ->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))
            ->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))
            ->get();
        $inHappyHour = false;
        foreach ($happy_hours as $happy_hour) {
            if (HappyHourProduct::where('happy_hour_id', $happy_hour->id)->where('product_id', $id)->first() != null) {
                $happy_hour_product = HappyHourProduct::where('happy_hour_id', $happy_hour->id)->where('product_id', $id)->first();
                if ($happy_hour_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $happy_hour_product->discount) / 100;
                    $highest_price -= ($highest_price * $happy_hour_product->discount) / 100;
                } elseif ($happy_hour_product->discount_type == 'amount') {
                    $lowest_price -= $happy_hour_product->discount;
                    $highest_price -= $happy_hour_product->discount;
                }
                $inHappyHour = true;
                break;
            }
        }

        // Apply product-specific discounts if not in Flash Deal or Happy Hour
        if (!$inFlashDeal && !$inHappyHour) {
            if ($product->discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }

        // Apply taxes
        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        // Convert and format prices
        $lowest_price = convert_price($lowest_price);
        $highest_price = convert_price($highest_price);

        if ($lowest_price == $highest_price) {
            return format_price($lowest_price);
        } else {
            return format_price($lowest_price) . ' - ' . format_price($highest_price);
        }
    }
}


//Shows Base Price
if (! function_exists('home_base_price')) {
    function home_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return format_price(convert_price($price));
    }
}

//Shows Base Price
if (! function_exists('main_home_base_price')) {
    function main_home_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return format_price(convert_price($price));
    }
}

//Shows Base Price with discount
if (! function_exists('home_discounted_base_price')) {
    function home_discounted_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if ($product->app_discount_type == 'percent') {
                $price -= ($price * $product->app_discount) / 100;
            } elseif ($product->app_discount_type == 'amount') {
                $price -= $product->app_discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}

if (! function_exists('main_home_discounted_base_price')) {
    function main_home_discounted_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;
        $inFlashDeal = false;

        if (!$inFlashDeal) {
            if ($product->is_group_product) {
                $price = Group_product::where('group_product_id', $product->id)->sum('price');
            } else {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}


//Shows Base Price with Weekend offer
if (! function_exists('home_weekend_base_price')) {
    function home_weekend_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        if ($product->weekend_offer_type == 'percent') {
            $price -= ($price * $product->weekend_offer) / 100;
        } elseif ($product->weekend_offer_type == 'amount') {
            $price -= $product->weekend_offer;
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}

//Shows Base Price with Weekend offer
if (! function_exists('main_home_weekend_base_price')) {
    function main_home_weekend_base_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        if ($product->weekend_offer_type == 'percent') {
            $price -= ($price * $product->weekend_offer) / 100;
        } elseif ($product->weekend_offer_type == 'amount') {
            $price -= $product->weekend_offer;
        }


        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}

//Shows Base Price with flash deal
if (!function_exists('home_flash_deal_price')) {
    function home_flash_deal_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', '!=', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();

                $price -= ($price * $flash_deal->discount_percent) / 100;

                $inFlashDeal = true;
                break;
            }
        }

        return format_price(convert_price($price));
    }
}

//Shows Base Price with flash deal
if (!function_exists('main_home_flash_deal_price')) {
    function main_home_flash_deal_price($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', '!=', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();

                $price -= ($price * $flash_deal->discount_percent) / 100;

                $inFlashDeal = true;
                break;
            }
        }

        return format_price(convert_price($price));
    }
}

if (! function_exists('currency_symbol')) {
    function currency_symbol()
    {
        $code = \App\Models\Currency::findOrFail(\App\Models\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if (Session::has('currency_code')) {
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        } else {
            $currency = Currency::where('code', $code)->first();
        }
        return $currency->symbol;
    }
}

if (! function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'las la-star active'></i>";
        $halfStar = "<i class = 'las la-star half'></i>";
        $emptyStar = "<i class = 'las la-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        // echo $html;
        echo '';
    }
}


//Api
if (! function_exists('homeBasePrice')) {
    function homeBasePrice($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        if ($product->is_group_product) {
            $group_total_price = 0;
            $group_products = Group_product::where('group_product_id', $product->id)->get();
            foreach ($group_products as $group_product) {
                $item = Product::where('id', $group_product->product_id)->first();
                $group_total_price += $item->unit_price * $group_product->app_qty;
            }
            $price = $group_total_price;
        }
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return $price;
    }
}

if (! function_exists('homeDiscountedBasePrice')) {
    // function homeDiscountedBasePrice($id)
    // {
    //     $product = Product::findOrFail($id);
    //     $price = $product->unit_price;

    //     // $flash_deals = FlashDeal::where('status', 1)->get();
    //     $flash_deals = FlashDeal::where('status', 1)->get();
    //     $inFlashDeal = false;


    //     foreach ($flash_deals as $flash_deal) {
    //         if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
    //             $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
    //             if ($flash_deal_product->discount_type == 'percent') {
    //                 $price -= ($price * $flash_deal_product->discount) / 100;
    //             } elseif ($flash_deal_product->discount_type == 'amount') {
    //                 $price -= $flash_deal_product->discount;
    //             }
    //             $inFlashDeal = true;
    //             break;
    //         }
    //     }

    //     if (!$inFlashDeal) {
    //         if ($product->app_discount_type == 'percent') {
    //             $price -= ($price * $product->app_discount) / 100;
    //         } elseif ($product->app_discount_type == 'amount') {
    //             $price -= $product->app_discount;
    //         }
    //     }

    //     if ($product->tax_type == 'percent') {
    //         $price += ($price * $product->tax) / 100;
    //     } elseif ($product->tax_type == 'amount') {
    //         $price += $product->tax;
    //     }
    //     return format_price(convert_price($price));
    // }
    function homeDiscountedBasePrice($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        $happy_hours = HappyHour::where('status', 1)->get();
        $inHappyHour = false;
        foreach ($happy_hours as $happy_hour) {
            if ($happy_hour != null && $happy_hour->status == 1 && strtotime(date('d-m-Y')) >= $happy_hour->start_date && strtotime(date('d-m-Y')) <= $happy_hour->end_date && HappyHourProduct::where('happy_hour_id', $happy_hour->id)->where('product_id', $id)->first() != null) {
                $happy_hour_product = HappyHourProduct::where('happy_hour_id', $happy_hour->id)->where('product_id', $id)->first();
                if ($happy_hour_product->discount_type == 'percent') {
                    $price -= ($price * $happy_hour_product->discount) / 100;
                } elseif ($happy_hour_product->discount_type == 'amount') {
                    $price -= $happy_hour_product->discount;
                }
                $inHappyHour = true;
                break;
            }
        }

        if (!$inFlashDeal && !$inHappyHour) {
            if ($product->app_discount_type == 'percent') {
                $price -= ($price * $product->app_discount) / 100;
            } elseif ($product->app_discount_type == 'amount') {
                $price -= $product->app_discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }

        return format_price(convert_price($price));
    }
}

if (! function_exists('MainhomeDiscountedBasePrice')) {
    function MainhomeDiscountedBasePrice($id)
    {
        $product = Product::findOrFail($id);
        $price = $product->unit_price;

        // $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        if (!$inFlashDeal) {
            if ($product->is_group_product) {
                $group_total_price = Group_product::where('group_product_id', $product->id)->sum('app_price');
                $price = $group_total_price;
            } else {
                if ($product->app_discount_type == 'percent') {
                    $price -= ($price * $product->app_discount) / 100;
                } elseif ($product->app_discount_type == 'amount') {
                    $price -= $product->app_discount;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return format_price(convert_price($price));
    }
}

if (! function_exists('homePrice')) {
    function homePrice($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}

if (! function_exists('homeDiscountedPrice')) {
    function homeDiscountedPrice($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if ($product->app_discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->app_discount) / 100;
                $highest_price -= ($highest_price * $product->app_discount) / 100;
            } elseif ($product->app_discount_type == 'amount') {
                $lowest_price -= $product->app_discount;
                $highest_price -= $product->app_discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}

if (! function_exists('mainhomeDiscountedPrice')) {
    function mainhomeDiscountedPrice($id)
    {
        $product = Product::findOrFail($id);
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }


        $inFlashDeal = false;

        if (!$inFlashDeal) {
            if ($product->app_discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->app_discount) / 100;
                $highest_price -= ($highest_price * $product->app_discount) / 100;
            } elseif ($product->app_discount_type == 'amount') {
                $lowest_price -= $product->app_discount;
                $highest_price -= $product->app_discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}

if (! function_exists('brandsOfCategory')) {
    function brandsOfCategory($category_id)
    {
        $brands = [];
        $subCategories = SubCategory::where('category_id', $category_id)->get();
        foreach ($subCategories as $subCategory) {
            $subSubCategories = SubSubCategory::where('sub_category_id', $subCategory->id)->get();
            foreach ($subSubCategories as $subSubCategory) {
                $brand = json_decode($subSubCategory->brands);
                foreach ($brand as $b) {
                    if (in_array($b, $brands)) continue;
                    array_push($brands, $b);
                }
            }
        }
        return $brands;
    }
}

if (! function_exists('convertPrice')) {
    function convertPrice($price)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            $price = floatval($price) / floatval($currency->exchange_rate);
        }
        $code = Currency::findOrFail(BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if (Session::has('currency_code')) {
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        } else {
            $currency = Currency::where('code', $code)->first();
        }
        $price = floatval($price) * floatval($currency->exchange_rate);
        return $price;
    }
}


function translate($key, $lang = null)
{
    if ($lang == null) {
        $lang = App::getLocale();
    }

    $translation_def = Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->where('lang_key', $key)->first();
    if ($translation_def == null) {
        $translation_def = new Translation;
        $translation_def->lang = env('DEFAULT_LANGUAGE', 'en');
        $translation_def->lang_key = $key;
        $translation_def->lang_value = $key;
        $translation_def->save();
    }

    //Check for session lang
    $translation_locale = Translation::where('lang_key', $key)->where('lang', $lang)->first();
    if ($translation_locale != null && $translation_locale->lang_value != null) {
        return $translation_locale->lang_value;
    } elseif ($translation_def->lang_value != null) {
        return $translation_def->lang_value;
    } else {
        return $key;
    }
}

function remove_invalid_charcaters($str)
{
    $str = str_ireplace(array("\\"), '', $str);
    return str_ireplace(array('"'), '\"', $str);
}
function getShippingCost($index)
{
    $admin_products = array();
    $seller_products = array();
    $calculate_shipping = 0;
    $calculate_total = 0;

    foreach (Session::get('cart') as $key => $cartItem) {

        $product = \App\Models\Product::find($cartItem['id']);
        $calculate_total += ($product->unit_price * $cartItem['quantity']);
        if ($product->added_by == 'admin') {
            array_push($admin_products, $cartItem['id']);
        } else {
            $product_ids = array();
            if (array_key_exists($product->user_id, $seller_products)) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem['id']);
            $seller_products[$product->user_id] = $product_ids;
        }
    }
    $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
    if ($shipping_skip_total <= $calculate_total) {
        return 0;
    }

    //Calculate Shipping Cost
    if (get_setting('shipping_type') == 'flat_rate') {
        $calculate_shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if (!empty($admin_products)) {
            $calculate_shipping = \App\Models\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
        }
        if (!empty($seller_products)) {
            foreach ($seller_products as $key => $seller_product) {
                $calculate_shipping += \App\Models\Shop::where('user_id', $key)->first()->shipping_cost;
            }
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        $city = City::where('name', Session::get('shipping_info')['city'])->first();
        if ($city != null) {
            $calculate_shipping = $city->cost;
        }
    }

    $cartItem = Session::get('cart')[$index];
    $product = \App\Models\Product::find($cartItem['id']);

    if (get_setting('shipping_type') == 'flat_rate') {
        return $calculate_shipping / count(Session::get('cart'));
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if ($product->added_by == 'admin') {
            return \App\Models\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
        } else {
            return \App\Models\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        if ($product->added_by == 'admin') {
            return $calculate_shipping / count($admin_products);
        } else {
            return $calculate_shipping / count($seller_products[$product->user_id]);
        }
    } else {
        return \App\Models\Product::find($cartItem['id'])->shipping_cost;
    }
}
function getShippingCost__($index)
{
    $admin_products = array();
    $seller_products = array();
    $calculate_shipping = 0;

    foreach (Session::get('cart')->where('owner_id', Session::get('owner_id')) as $key => $cartItem) {
        $product = \App\Models\Product::find($cartItem['id']);
        if ($product->added_by == 'admin') {
            array_push($admin_products, $cartItem['id']);
        } else {
            $product_ids = array();
            if (array_key_exists($product->user_id, $seller_products)) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem['id']);
            $seller_products[$product->user_id] = $product_ids;
        }
    }

    //Calculate Shipping Cost
    if (get_setting('shipping_type') == 'flat_rate') {
        $calculate_shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if (!empty($admin_products)) {
            $calculate_shipping = \App\Models\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
        }
        if (!empty($seller_products)) {
            foreach ($seller_products as $key => $seller_product) {
                $calculate_shipping += \App\Models\Shop::where('user_id', $key)->first()->shipping_cost;
            }
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        $city = City::where('name', Session::get('shipping_info')['city'])->first();
        if ($city != null) {
            $calculate_shipping = $city->cost;
        }
    }

    $cartItem = Session::get('cart')[$index];
    $product = \App\Models\Product::find($cartItem['id']);

    if (get_setting('shipping_type') == 'flat_rate') {
        return $calculate_shipping / count(Session::get('cart'));
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if ($product->added_by == 'admin') {
            return \App\Models\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
        } else {
            return \App\Models\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        if ($product->added_by == 'admin') {
            return $calculate_shipping / count($admin_products);
        } else {
            return $calculate_shipping / count($seller_products[$product->user_id]);
        }
    } else {
        return \App\Models\Product::find($cartItem['id'])->shipping_cost;
    }
}

function timezones()
{
    return Timezones::timezonesToArray();
}

if (!function_exists('app_timezone')) {
    function app_timezone()
    {
        return config('app.timezone');
    }
}

if (!function_exists('api_asset')) {
    function api_asset($id)
    {
        if (($asset = \App\Models\Upload::find($id)) != null) {
            return $asset->file_name;
        }
        return "";
    }
}

//return file uploaded via uploader
if (!function_exists('uploaded_asset')) {
    function uploaded_asset($id)
    {
        if (($asset = \App\Models\Upload::find($id)) != null) {
            return my_asset($asset->file_name);
        }
        return null;
    }
}

if (! function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function my_asset($path, $secure = null)
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return Storage::disk('s3')->url($path);
        } else {
            return app('url')->asset('public/' . $path, $secure);
        }
    }
}

if (! function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        return app('url')->asset('public/' . $path, $secure);
    }
}



if (!function_exists('isHttps')) {
    function isHttps()
    {
        return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
    }
}

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = (isHttps() ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return env('AWS_URL') . '/';
        } else {
            return getBaseURL() . 'public/';
        }
    }
}


if (! function_exists('isUnique')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function isUnique($email)
    {
        $user = \App\Models\User::where('email', $email)->first();

        if ($user == null) {
            return '1'; // $user = null means we did not get any match with the email provided by the user inside the database
        } else {
            return '0';
        }
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        $setting = BusinessSetting::where('type', $key)->first();
        return $setting == null ? $default : $setting->value;
    }
}



if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isSeller')) {
    function isSeller()
    {
        if (Auth::check() && Auth::user()->user_type == 'seller') {
            return true;
        }
        return false;
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer()
    {
        if (Auth::check() && Auth::user()->user_type == 'customer') {
            return true;
        }
        return false;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// duplicates m$ excel's ceiling function
if (!function_exists('ceiling')) {
    function ceiling($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
    }
}

if (!function_exists('get_images')) {
    function get_images($given_ids, $with_trashed = false)
    {
        $givenids = is_null($given_ids) ? [] : explode(",", $given_ids);
        $ids = is_array($given_ids)
            ? $given_ids
            : $givenids;

        return $with_trashed
            ? Upload::withTrashed()->whereIn('id', $ids)->get()
            : Upload::whereIn('id', $ids)->get();
    }
}

//for api
if (!function_exists('get_images_path')) {
    function get_images_path($given_ids, $with_trashed = false)
    {
        $paths = [];
        $images = get_images($given_ids, $with_trashed);
        if (!$images->isEmpty()) {
            foreach ($images as $image) {
                $paths[] = !is_null($image) ? $image->file_name : "";
            }
        }

        return $paths;
    }
}
function generate_po_id($bill_id)
{

    $generated_bill_id = null;
    $date = date('Y');
    $temp_data = DB::table('purchases')
        ->where('date', 'like', $date . '%')
        ->where('branch', '=', Session::get('branch'))
        ->get();

    if ($temp_data) {

        $sl = DB::table('purchases')
            ->where('date', 'like', $date . '%')
            ->max('sl');
        $sl = $sl + 1;
        $generated_bill_id = 'PO/' . date('Y') . '/' . $sl;
    } else {
        $sl = 1;
        $generated_bill_id = 'PO/' . date('Y') . '/' . $sl;
    }

    $data[] = $sl;
    $data[] = $generated_bill_id;
    return $data;
}

function get_total_suppliers()
{

    $total = DB::table('suppliers')
        ->where('status', '=', 1)
        ->count();


    if (!$total) {
        return null;
    } else {
        return $total;
    }
}

function get_formated_date($date)
{

    if ($date) {

        $timestamp = strtotime($date);
        return $timestamp ? date('M d, Y', strtotime($date)) : null;
    } else {
        return NULL;
    }
}

function get_formated_time($date)
{
    if ($date) {
        return date('h:i', strtotime($date));
    } else {
        return NULL;
    }
}

function get_formated_amount($amount)
{
    if ($amount) {
        return number_format($amount, 2);
    } else {
        return '0.00';
    }
}

function get_random_number($length = 10)
{
    $characters = '0123456789';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $string;
}



//for api 
if (!function_exists('checkout_done')) {
    function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if (Auth::check()) {
                $clubpointController = new ClubPointController;
                $clubpointController->processClubPoints($order);
            }
        }
        if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            } else {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if ($orderDetail->product->user->user_type == 'seller') {
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();
    }
}

//for api
if (!function_exists('wallet_payment_done')) {
    function wallet_payment_done($user_id, $amount, $payment_method, $payment_details)
    {
        $user = \App\Models\User::find($user_id);
        $user->balance = $user->balance + $amount;
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $amount;
        $wallet->payment_method = $payment_method;
        $wallet->payment_details = $payment_details;
        $wallet->save();
    }
}

//Commission Calculation
if (!function_exists('commission_calculation')) {
    function commission_calculation($order)
    {
        if (
            \App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() == null ||
            !\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated
        ) {

            if ($order->payment_type == 'cash_on_delivery') {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    $commission_percentage = 0;
                    if (get_setting('category_wise_commission') != 1) {
                        $commission_percentage = get_setting('vendor_commission');
                    } else if ($orderDetail->product->user->user_type == 'seller') {
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                    }
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $admin_commission = ($orderDetail->price * $commission_percentage) / 100;

                        if (get_setting('product_manage_by_admin') == 1) {
                            $seller_earning = ($orderDetail->tax + $orderDetail->price) - $admin_commission;
                            $seller->admin_to_pay += $seller_earning;
                        } else {
                            $seller_earning = ($orderDetail->tax + $orderDetail->shipping_cost + $orderDetail->price) - $admin_commission;
                            $seller->admin_to_pay = $seller->admin_to_pay - $admin_commission;
                        }

                        $seller->save();

                        $commission_history = new CommissionHistory;
                        $commission_history->order_id = $order->id;
                        $commission_history->order_detail_id = $orderDetail->id;
                        $commission_history->seller_id = $orderDetail->seller_id;
                        $commission_history->admin_commission = $admin_commission;
                        $commission_history->seller_earning = $seller_earning;

                        $commission_history->save();
                    }
                }
            } elseif ($order->manual_payment) {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    $commission_percentage = 0;
                    if (get_setting('category_wise_commission') != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                    } else if ($orderDetail->product->user->user_type == 'seller') {
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                    }
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $admin_commission = ($orderDetail->price * $commission_percentage) / 100;

                        if (get_setting('product_manage_by_admin') == 1) {
                            $seller_earning = ($orderDetail->tax + $orderDetail->price) - $admin_commission;
                            $seller->admin_to_pay += $seller_earning;
                        } else {
                            $seller_earning = ($orderDetail->tax + $orderDetail->shipping_cost + $orderDetail->price) - $admin_commission;
                            $seller->admin_to_pay += $seller_earning;
                        }

                        $seller->save();

                        $commission_history = new CommissionHistory();
                        $commission_history->order_id = $order->id;
                        $commission_history->order_detail_id = $orderDetail->id;
                        $commission_history->seller_id = $orderDetail->seller_id;
                        $commission_history->admin_commission = $admin_commission;
                        $commission_history->seller_earning = $seller_earning;

                        $commission_history->save();
                    }
                }
            }
        }

        if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if ($order->user != null) {
                $clubpointController = new ClubPointController;
                $clubpointController->processClubPoints($order);
            }
        }
    }
}

function save_customer_ledger($data)
{
    $customer_ledger = new Customer_ledger();
    $customer_ledger->customer_id = $data['customer_id'];
    $customer_ledger->order_id = $data['order_id'];
    $customer_ledger->descriptions = $data['descriptions'];
    $customer_ledger->type = $data['type'];
    $customer_ledger->debit = $data['debit'];
    $customer_ledger->credit = $data['credit'];
    $customer_ledger->date = date('Y-m-d', strtotime($data['date']));
    $customer_ledger->save();
}

function calculate_discount($total)
{
    $offer_arr = array();
    $offers = Offer::get();
    // dd($offers);
    $dis = 0;
    Session::forget('offer_discount');



    $products = array();
    foreach ($offers as $offer) {

        if (time() >= $offer->start_date || time() <= $offer->end_date) {
            $d = json_decode($offer->details);

            if (strpos($offer->title, 'Bkash') !== false) {
                continue;
            }

            if (strpos($offer->title, '2nd') !== false) {
                if (isCustomer()) {
                    $uid = Auth::user()->id;
                    $orderCount = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('orders.user_id', $uid)->whereNotIn('order_details.delivery_status', ['cancel'])->whereBetween('orders.created_at', [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')])->groupBy('orders.id')->count();
                    if ($orderCount == 1) {
                        if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                            foreach ($d as $row) {
                                addToDiscountProduct($row->product_id);
                            }
                        }
                    }
                }
            } else {
                if ($offer->type == 'cart_base') {
                    if (!empty($d->product_id)) {
                        // echo $total;
                        foreach ($d->product_id as $pd_id) {
                            foreach (Session()->get('cart') as $key => $cartItem) {
                                if ($pd_id == $cartItem['id']) {
                                    $total -= ($cartItem['price'] * $cartItem['quantity']);
                                    // dd($d);
                                }
                            }
                        }
                        // dd($total);
                    }

                    if (($total >= $d->min_buy && $total <= $d->max_discount)) {
                        $dis = $offer->discount;
                        $type = $offer->discount_type;
                        if ($type == 'percent') {
                            $dis = ($total * $dis) / 100;
                        }
                        $dd = Session::get('offer_discount');
                        Session::put('offer_discount', $dis + $dd);
                    }
                } else {
                    if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                        $dis = $offer->discount;

                        foreach ($d as $row) {

                            if ($offer->full_discount == 0) {
                                $cart = collect();
                                foreach (Session()->get('cart') as $key => $cartItem) {
                                    if ($row->product_id == $cartItem['id']) {

                                        $max_qty = $offer->max_qty;
                                        $itm_disc = $offer->disc_per_qty;
                                        if ($cartItem['quantity'] > $max_qty) {
                                            $qqty = $max_qty;
                                        } else {
                                            $qqty = $cartItem['quantity'];
                                        }
                                        $cartItem['discount'] = $itm_disc * $qqty;
                                        $dis += $itm_disc * $qqty;
                                        $dd = Session::get('offer_discount');
                                        Session::put('offer_discount', $dis + $dd);
                                    }
                                    $cart->push($cartItem);
                                }
                                Session::put('cart', $cart);
                            } else {
                                addToDiscountProduct($row->product_id);
                            }
                        }
                    }
                }
            }
        }
    }
    return $total - $dis;
}

function addToDiscountProduct($p_id)
{
    $product = Product::find($p_id);
    $data = array();
    $data['id'] = $product->id;
    $data['owner_id'] = $product->user_id;
    $data['quantity'] = 1;
    $data['variant'] = null;
    $data['price'] = 0;
    $data['tax'] = 0;
    $data['offer'] = 1;
    $data['shipping'] = 0;
    $data['product_referral_code'] = null;
    $cart = collect();
    $foundInCart = false;
    $cart = collect();
    foreach (Session()->get('cart') as $key => $cartItem) {
        if (($cartItem['id'] == $p_id) && ($cartItem['offer'] == 1)) {
            $foundInCart = true;
        }
        $cart->push($cartItem);
    }
    if (!$foundInCart)
        $cart->push($data);
    Session::put('cart', $cart);
}

function offerCount()
{
    $conditions = ['published' => 1];
    $products = Product::where($conditions);
    $products->whereRaw('discount > 0');
    return $products->count();
}

function checkForFirstOrder($user_id)
{
    $order = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.delivery_status', ['cancel'])->groupBy('orders.id')->get();

    if (!empty($order) && count($order) == 1) {
        $order = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('orders.user_id', $user_id)->whereNotIn('order_details.delivery_status', ['cancel'])->groupBy('orders.id')->orderBy('orders.id', 'asc')->first();
        if ($order->grand_total >= 1000) {
            $r = Referr_code::where('used_by', $user_id)->first();
            if (!empty($r)) {
                $amount = \App\Models\AffiliateOption::where('type', 'download_app')->first()->percentage;
                $us = User::findOrFail($user_id);
                $wallet = new Wallet();
                $wallet->user_id = $r->user_id;
                $wallet->payment_method = 'Referral Rewards for first purchase-' . $us->customer->customer_id;
                $wallet->amount = $amount;
                $wallet->order_id = $order->id;
                $wallet->payment_details = json_encode(array('code' => $r->referr_code, 'user_id' => $user_id));
                $wallet->save();
                $user = User::findOrFail($r->user_id);

                $user->balance = $user->balance + $amount;
                $user->save();
            }
        }
    }
}


//Send Notification
if (!function_exists('send_notification')) {
    function send_notification($order, $order_status)
    {
        if ($order->seller_id == \App\Models\User::where('user_type', 'admin')->first()->id) {
            $users = User::findMany([$order->user->id, $order->seller_id]);
        } else {
            $users = User::findMany([$order->user->id, $order->seller_id, \App\Models\User::where('user_type', 'admin')->first()->id]);
        }

        $order_notification = array();
        $order_notification['order_id'] = $order->id;
        $order_notification['order_code'] = $order->code;
        $order_notification['user_id'] = $order->user_id;
        $order_notification['seller_id'] = $order->seller_id;
        $order_notification['status'] = $order_status;

        Notification::send($users, new OrderNotification($order_notification));
    }
}

if (!function_exists('send_firebase_notification')) {
    function send_firebase_notification($req)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';


        $fields = array(
            'to' => $req->device_token,
            'notification' => [
                'body' => $req->text,
                'title' => $req->title,
                'sound' => 'default' /*Default sound*/
            ],
            'data' => [
                'item_type' => $req->type,
                'item_type_id' => $req->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );

        //$fields = json_encode($arrayToSend);
        $headers = array(
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        //        var_dump($result);
        curl_close($ch);
        //        return $result;

        $firebase_notification = new FirebaseNotification;
        $firebase_notification->title = $req->title;
        $firebase_notification->text = $req->text;
        $firebase_notification->item_type = $req->type;
        $firebase_notification->item_type_id = $req->id;
        $firebase_notification->receiver_id = $req->user_id;

        $firebase_notification->save();
    }
}

function get_customer_area_name($customer_id)
{

    $customers = Customer::where('customers.user_id', $customer_id)
        ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
        ->select('areas.name')
        ->pluck('areas.name');

    return $customers;
}
function daysDiff($date)
{
    $date1 = new DateTime($date);
    $date2 = new DateTime(date('Y-m-d'));
    return $date1->diff($date2)->format('%a') . ' Days';
}

function getWearhouseId($order_id)
{

    $order = Order::findOrFail($order_id);
    // if($order->guest_id){
    //     $add = json_decode($order->shipping_address);
    //     $area = Area::where('name',$add->area)->first();
    //     return $area->wearhouse_id;

    // }else{
    //     $customers = Customer::where('customers.user_id', $order->user_id)
    //     ->leftjoin('areas', 'areas.code', '=', 'customers.area_code')
    //     ->select('areas.wearhouse_id')
    //     ->pluck('areas.wearhouse_id');
    //     return $customers[0];
    // }
    if ($order->warehouse) {
        return $order->warehouse;
    } else {
        return 1;
    }
}
function getWearhouseName($id)
{

    $wearhouse = Warehouse::findOrFail($id);
    if ($wearhouse) {
        return $wearhouse->name;
    } else {
        return '';
    }
}
function getWearhouseBuUserId($id)
{

    $staff = Staff::where('user_id', $id)->first();
    if ($staff) {
        return unserialize($staff->warehouse_id);
    } else {
        return array();
    }
}

function getCustomerNameByOrderno($order_no)
{
    if ($order_no) {
        $order = Order::where('code', $order_no)->first();
        if (!empty($order->user_id)) {
            return $order->user->name;
        } else {
            return 'Guest';
        }
    } else {
        return '';
    }
}

function getUserNameByuserID($user_id)
{
    if (is_numeric($user_id)) {
        $user = user::where('id', $user_id)->first();
        return $user->name ?? null;
    } else {
        return '';
    }
}

function getUsernameBycustomerstaffId($user_id)
{
    if (is_numeric($user_id)) {
        $user = user::where('id', $user_id)->first();
        if (!empty($user->name))
            return $user->name;
        else
            return '';
    } else {
        return '';
    }
}

function total_order($customer_id)
{
    if (!empty($customer_id)) {
        $customer = Customer::where('customer_id', $customer_id)->first();
        if (!empty($customer)) {
            $total_order =  count($customer->orders());
        } else {
            $total_order = 0;
        }
        if (!empty($total_order)) {
            return $total_order;
        } else {
            return 0;
        }
    }
}

function executive_name($user_id)
{
    if (!empty($user_id)) {
        $user = User::where('id', $user_id)
            ->select('name')->first();
        if (!empty($user)) {
            $executive_name = $user->name;
        }
        if (!empty($executive_name)) {
            return $executive_name;
        } else {
            return Null;
        }
    }
}

if (!function_exists('checkWishlist')) {
    function checkWishlist($product_id)
    {
        if (Auth::user()) {
            $wishlist = Wishlist::where('user_id', Auth::user()->id)->where('product_id', $product_id)->first();
            if ($wishlist) {
                return $wishlist->id;
            }
        }
        return false;
    }
}

// Start Accounting
if (!function_exists('defult_currency_symbol')) {
    function defult_currency_symbol()
    {
        return '';
    }
}

if (!function_exists('add_activity_log')) {
    function add_activity_log($type, $action_name, $id, $table, $route_name, $status = 0, $data = null)
    {
        $postData = (empty($_POST)) ? array() : $_POST;

        $activity_log = new ActivityLog();
        $activity_log->user_id = Auth::user()->id;
        $activity_log->type = $type;
        $activity_log->action = $action_name;
        $activity_log->action_id = $id;
        $activity_log->table_name = $table;
        $activity_log->route_name = $route_name;
        $activity_log->form_data = ($data == null) ? json_encode($postData) : json_encode($data);
        $activity_log->status = $status;
        $activity_log->save();
    }
}

if (!function_exists('dfs')) {
    function dfs($HeadName, $HeadCode, $oResult, &$visit, $d)
    {
        if ($d == 0) {
            echo "<li class=\"jstree-open \" id='" . $HeadCode . "'>$HeadName";
        } else if ($d == 1) {
            echo "<li class=\"jstree-open\"  id='" . $HeadCode . "'><a href='javascript:' onclick=\"loadData('" . $HeadCode . "')\">$HeadName</a>";
        } else {
            echo "<li id='" . $HeadCode . "'><a href='javascript:' onclick=\"loadData( this.id,'" . $HeadCode . "')\">$HeadName</a>";
        }

        $p = 0;
        for ($i = 0; $i < count($oResult); $i++) {
            if (!$visit[$i]) {
                if ($HeadCode == $oResult[$i]->pre_head_code) {
                    $visit[$i] = true;
                    if ($p == 0) {
                        echo "<ul>";
                    }
                    $p++;
                    dfs($oResult[$i]->head_name, $oResult[$i]->head_code, $oResult, $visit, $d + 1);
                }
            }
        }

        if ($p == 0) {
            echo "</li>";
        } else {
            echo "</ul>";
        }
    }
}

if (!function_exists('getsubTypeData')) {
    function getsubTypeData($id = null)
    {
        $query = AccSubtype::orderBy('id', 'asc');
        if ($id != null) {
            return $query->where('id', $id);
        } else {
            return $query->get();
        }
        return false;
    }
}

if (!function_exists('checkIsTransationAccount')) {
    function checkIsTransationAccount($id)
    {
        $rs = AccTransaction::select('id')->where('coa_id', $id)->get();
        if ($rs->isNotEmpty()) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('checkChildAccount')) {
    function checkChildAccount($id)
    {
        $coas = AccCoa::select("head_code", "head_level")->where('pre_head_code', $id)->get();

        if ($coas->isNotEmpty()) {
            return $coas;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_financial_year')) {
    function get_financial_year()
    {
        $financial_year = FinancialYear::where('start_date', '<=', date("Y-m-d"))
            ->where('end_date', '>=', date("Y-m-d"))
            ->where("status", 1)
            ->first();

        return $financial_year->id;
    }
}

if (!function_exists('get_financial_years')) {
    function get_financial_years($id = null)
    {
        $query = FinancialYear::orderBy('end_date', 'desc');

        if ($id !== null) {
            return $query->where('id', $id)->first();
        } else {
            return $query->get();
        }

        return false;
    }
}

if (!function_exists('get_previous_financial_year')) {
    function get_previous_financial_year($numYear)
    {
        $fyearStartDate = Session::get('fyearStartDate');
        $fyearEndDate = Session::get('fyearEndDate');
        $yearArray = array();
        for ($i = 1; $i <= $numYear; $i++) {
            $previousStartDate = date('Y-m-d', strtotime($fyearStartDate . ' -' . $i . ' year'));
            $previousEnddate = date('Y-m-d', strtotime($fyearEndDate . ' -' . $i . ' year'));
            $fyear =  FinancialYear::where('start_date', $previousStartDate)->where('end_date', $previousEnddate)->first();
            if ($fyear) {
                array_push($yearArray, $fyear->year_name);
            }
        }
        return $yearArray;
    }
}

if (!function_exists('get_current_financial_year')) {
    function get_current_financial_year()
    {
        $financial_year = FinancialYear::where('status', 1)
            ->where('is_close', 0)
            ->orderBy('id', "ASC")
            ->first();

        if ($financial_year) {
            return $financial_year;
        }
        return false;
    }
}

if (!function_exists('get_coa_heads')) {
    function get_coa_heads()
    {
        $result = AccCoa::where('is_active', 1)->get();
        $list = array('' => translate('Select Account'));
        if (!empty($result)) {
            foreach ($result as $value) {
                $list[$value->head_code] = $value->head_name;
            }
        }
        return $list;
    }
}

if (!function_exists('get_predefined_head')) {
    function get_predefined_head($field)
    {
        $query = AccPredefineAccount::value($field);
        return $query;
    }
}

if (!function_exists('get_predefine_code')) {
    function get_predefine_code()
    {
        $tableName = (new AccPredefineAccount())->getTable();
        $columns = Schema::getColumnListing($tableName);

        $excludedColumns = ['id', 'created_at', 'updated_at'];
        $filteredColumns = array_filter($columns, function ($column) use ($excludedColumns) {
            return !in_array($column, $excludedColumns);
        });

        return array_values($filteredColumns);
    }
}

if (!function_exists('get_predefine_code_values')) {
    function get_predefine_code_values()
    {
        return $result = AccPredefineAccount::first();
    }
}

if (!function_exists('get_max_field_number')) {
    function get_max_field_number($field, $table, $where = null, $type = null, $field2 = null)
    {
        $query = DB::table($table)->select($field, $field2);

        if ($where !== null) {
            $query->where($where, $type);
        }

        $record = $query->orderByRaw("LENGTH(voucher_no) DESC")
            ->orderByRaw("voucher_no DESC")->first();

        if ($record) {
            if ($field2 !== null) {
                $num = $record->{$field2};
                if (strpos($num, '-') !== false) {
                    list($txt, $intval) = explode('-', $num, 2);
                    return (int) $intval;
                } else {
                    return 0;
                }
            } else {
                return $record->{$field};
            }
        }

        return 0;
    }
}

if (!function_exists('get_referance_no')) {
    function get_referance_no($id)
    {
        $data = AccSubcode::select('reference_no')
            ->where('id', $id)
            ->first();

        if ($data) {
            return $data->reference_no;
        }

        return null;
    }
}

if (!function_exists('store_transaction_summary')) {
    function store_transaction_summary($coaid, $warehouse_id, $date)
    {
        $curentmonth = date('n',  strtotime($date));

        $fyear = get_financial_year();
        $summary =  get_closing_balance($coaid, $warehouse_id, $date);
        $oldrecord = get_monthly_balance($coaid, $warehouse_id, $fyear);

        if (!$oldrecord) {
            $monthly_balance = new AccMonthlyBalance();
            $monthly_balance->fyear = $fyear;
            $monthly_balance->coa_id = $coaid;
            $monthly_balance->warehouse_id = $warehouse_id;
            $monthly_balance->{'balance' . $curentmonth} = $summary;
            $monthly_balance->created_at = date('Y-m-d h:i:s');
        } else {
            $monthly_balance = AccMonthlyBalance::where('fyear', $fyear)->where('coa_id', $coaid)->where('warehouse_id', $warehouse_id)->first();
            $monthly_balance->fyear = $fyear;
            $monthly_balance->coa_id = $coaid;
            $monthly_balance->warehouse_id = $warehouse_id;
            $monthly_balance->{'balance' . $curentmonth} = $summary;
            $monthly_balance->created_at = date('Y-m-d h:i:s');
        }

        if ($monthly_balance->save()) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_closing_balance')) {
    function get_closing_balance($hCode, $warehouse_id, $dtpFromDate = null, $dtpToDate = null, $subtype = 1, $subcode = null, $hType = null)
    {
        if ($dtpToDate != null) {
            $toDate = $dtpToDate;
        } else {
            $toDate = $dtpFromDate;
        }

        $coaHead = general_led_report_head_name($hCode);
        $opening = get_opening_balance($hCode, $warehouse_id, $dtpFromDate, $toDate);
        $current = get_general_ledger_report($hCode, $warehouse_id, $toDate, $toDate, 0, 0);

        if ($current) {
            $balance = 0;
            foreach ($current as $cur) {
                if ($coaHead->head_type == 'A' || $coaHead->head_type == 'E') {
                    $balance += ($cur->debit - $cur->credit);
                } else {
                    $balance += ($cur->credit - $cur->debit);
                }
            }
        } else {
            $balance = 0;
        }

        return $closingbalance = $opening +  $balance;
    }
}

if (!function_exists('get_monthly_balance')) {
    function get_monthly_balance($coaid, $warehouse_id, $fyear)
    {
        $result = AccMonthlyBalance::where('warehouse_id', $warehouse_id)
            ->where('coa_id', $coaid)
            ->where('fyear', $fyear)
            ->first();

        if ($result) {
            return $result;
        }

        return false;
    }
}

if (!function_exists('get_opening_balance')) {
    function get_opening_balance($hCode, $warehouse_id, $dtpFromDate, $dtpToDate)
    {
        $coaHead = general_led_report_head_name($hCode);

        $fyearStartDate = Session::get('fyearStartDate');
        $fyearEndDate = Session::get('fyearEndDate');

        $oldDate = date('Y-m-d', strtotime($dtpFromDate . ' -1 year'));
        $prevDate = date('Y-m-d', strtotime($dtpFromDate . ' - 1day'));
        $oldBalance = 0;

        if ($coaHead && ($coaHead->head_type == 'L' || $coaHead->head_type == 'A')) {
            if ($dtpFromDate >= $fyearStartDate && $dtpFromDate <= $fyearEndDate) {
                $fyear = FinancialYear::where('start_date', '<=', $oldDate)->where('end_date', '>=', $oldDate)->first();
            } else {
                $fyear = FinancialYear::where('start_date', '<=', $oldDate)->where('end_date', '>=', $oldDate)->first();
            }
            if ($fyear) {
                $oldBalance = get_old_year_closing_balance($hCode, $warehouse_id, $fyear->id, $coaHead->head_type, $coaHead->sub_type);
            }
        } else {
            $oldBalance = 0;
        }

        $opening =  get_general_ledger_report($hCode, $warehouse_id, $fyearStartDate, $prevDate, 0, 0);
        if ($opening) {
            $balance = 0;
            foreach ($opening as $open) {
                if ($coaHead->head_type == 'A' || $coaHead->head_type == 'E') {
                    $balance += ($open->debit - $open->credit);
                } else {
                    $balance += ($open->credit - $open->debit);
                }
            }
        } else {
            $balance = 0;
        }

        return $oldBalance + $balance;
    }
}

if (!function_exists('get_old_year_closing_balance')) {
    function get_old_year_closing_balance($hCode, $warehouse_id, $year, $hType = null, $subtype = 1, $subcode = null)
    {
        $query = AccOpeningBalance::where('coa_id', $hCode)->where('fyear', $year);

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $query->whereIn('warehouse_id', $warehousearray);
        }

        if ($subtype != 1 && $subcode != null) {
            $query->where('sub_code', $subcode)->where('sub_type', $subtype);
        }

        if ($warehouse_id != null) {
            $query->where('warehouse_id', $warehouse_id);
        }

        $closings = $query->get();

        if ($closings->isNotEmpty()) {
            $balance = 0;
            foreach ($closings as $closing) {
                if ($hType == 'A') {
                    $balance += ($closing->debit - $closing->credit);
                } else {
                    $balance += ($closing->credit - $closing->debit);
                }
            }
            return $balance;
        }

        return false;
    }
}

if (!function_exists('get_opening_balance_subtype')) {
    function get_opening_balance_subtype($hCode, $warehouse_id, $dtpFromDate, $dtpToDate, $subtype = 1, $subcode = null)
    {
        $coa = general_led_report_head_name($hCode);
        $fyearStartDate = Session::get('fyearStartDate');
        $fyearEndDate = Session::get('fyearEndDate');

        $oldDate = date('Y-m-d', strtotime($dtpFromDate . ' -1 year'));
        $prevDate = date('Y-m-d', strtotime($dtpFromDate . ' -1 day'));
        $oldBalance = 0;

        if ($coa->head_type == 'L' || $coa->head_type == 'A') {
            if ($dtpFromDate >= $fyearStartDate && $dtpFromDate <= $fyearEndDate) {
                $fyear = FinancialYear::where('start_date', '<=', $oldDate)->where('end_date', '>=', $oldDate)->first();
            } else {
                $fyear = FinancialYear::where('start_date', '<=', $oldDate)->where('end_date', '>=', $oldDate)->first();
            }

            if ($fyear) {
                $oldBalance = get_old_year_closing_balance($hCode, $warehouse_id, $fyear->id, $coa->head_type, $subtype, $subcode);
            }
        } else {
            $oldBalance = 0;
        }

        $opening =  get_general_ledger_report($hCode, $warehouse_id, $fyearStartDate, $prevDate, 1, 0, $subtype, $subcode);
        if ($opening) {
            $balance = 0;
            foreach ($opening as $open) {
                if ($coa->head_type == 'A' || $coa->head_type == 'E') {
                    $balance += ($open->debit - $open->credit);
                } else {
                    $balance += ($open->credit - $open->debit);
                }
            }
        } else {
            $balance = 0;
        }

        return $oldBalance + $balance;
    }
}

if (!function_exists('get_general_ledger_report')) {
    function get_general_ledger_report($cmbCode, $warehouse_id, $dtpFromDate, $dtpToDate, $chkIsTransction, $isfyear = 0, $subtype = 1, $subcode = null)
    {
        if ($chkIsTransction == 1) {
            $query = AccTransaction::where('coa_id', $cmbCode)
                ->where('is_approved', 1)
                ->whereBetween('voucher_date', [$dtpFromDate, $dtpToDate]);


            if (Auth::user()->user_type != 'admin') {
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $query->whereIn('warehouse_id', $warehousearray);
            }

            if ($subtype != 1 && $subcode != null) {
                $query->where('sub_type', $subtype)->where('sub_code', $subcode);
            }

            if ($warehouse_id != null) {
                $query->where('warehouse_id', $warehouse_id);
            }

            if ($isfyear != 0) {
                $query->where('fyear', session('fyear'));
            }

            $query->orderBy('voucher_date', 'asc')
                ->orderBy('voucher_type', 'asc');

            return $query->get();
        } else {
            $query = AccTransaction::select(
                'coa_id',
                'voucher_type',
                DB::raw('SUM(debit) as debit'),
                DB::raw('SUM(credit) as credit')
            )
                ->where('is_approved', 1)
                ->whereBetween('voucher_date', [$dtpFromDate, $dtpToDate])
                ->where('coa_id', $cmbCode);

            if (Auth::user()->user_type != 'admin') {
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $query->whereIn('warehouse_id', $warehousearray);
            }


            if ($warehouse_id != null) {
                $query->where('warehouse_id', $warehouse_id);
            }

            if ($isfyear != 0) {
                $query->where('fyear', session('fyear'));
            }

            return $query->groupBy('coa_id')->get();
        }
    }
}

if (!function_exists('general_led_report_head_name')) {
    function general_led_report_head_name($cmbGLCode)
    {
        $coa = AccCoa::where('head_code', $cmbGLCode)->first();
        if ($coa) {
            return $coa;
        }
        return false;
    }
}

if (!function_exists('get_voucher_by_date')) {
    function get_voucher_by_date($warehouse_id, $party_id, $head_code, $start, $end, $status = 0)
    {
        $vouchers = AccVoucher::whereBetween('voucher_date', [$start, $end])->where('is_approved', $status);

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $vouchers->whereIn('warehouse_id', $warehousearray);
        }

        if ($warehouse_id != null) {
            $vouchers->where('warehouse_id', $warehouse_id);
        }

        if ($party_id != null) {
            $vouchers = $vouchers->where('relational_value', $party_id);
        }

        if ($head_code != null) {
            $vouchers = $vouchers->where(function ($query) use ($head_code) {
                $query->where('coa_id', $head_code)
                    ->orWhere('rev_code', $head_code);
            });
        }

        $vouchers = $vouchers->orderBy('id', 'desc')->get();
        return $vouchers;
    }
}

if (!function_exists('get_fixed_asset_report')) {
    function get_fixed_asset_report($type, $hCode, $warehouse_id, $fyear)
    {
        $fiscalyear = get_financial_years($fyear);

        $phead = get_general_ledger_head_name($hCode);
        $secondArray = array();
        $subTotal1 = 0;
        $subTotal2 = 0;
        $subTotal3 = 0;
        $subTotal4 = 0;
        $subTotal5 = 0;

        $subTotal6 = 0;
        $subTotal7 = 0;
        $subTotal8 = 0;
        $subTotal9 = 0;
        $subTotal10 = 0;
        $thirdLevel = get_charter_accounts_by_head_name($type, $phead->head_code);
        if ($thirdLevel) {
            $innerArray = array();
            foreach ($thirdLevel as $tdl) {
                $openig        = 0;
                $curentDebit   = 0;
                $curentCredit  = 0;
                $curentValue   = 0;
                $depAmount     = 0;
                $revOpening    = 0;
                $revCredit     = 0;
                $revDebit      = 0;
                $revBalance    = 0;
                $famount       = 0;
                $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);
                $transArray = array();
                if ($transationLevel) {
                    foreach ($transationLevel as $trans) {
                        $abalence = 0;
                        $bbalence = 0;
                        $cbalence = 0;
                        $dbalence = 0;
                        $ebalence = 0;
                        $fbalence = 0;
                        $gbalence = 0;
                        $hbalence = 0;
                        $ibalence = 0;
                        $jbalence = 0;
                        $deprate = 0;
                        $depreciatio = get_charter_accounts_by_asset_code($type, $trans->asset_code);
                        $lastbalance = get_last_year_balance($type, $trans->head_code, $warehouse_id, $fiscalyear->start_date, $fiscalyear->end_date);
                        if ($lastbalance) {
                            $abalence += $lastbalance;
                        }
                        $tval  = get_general_ledger_report($trans->head_code, $warehouse_id, $fiscalyear->start_date, $fiscalyear->end_date, 0, 0);
                        if ($tval) {
                            foreach ($tval as $amounts) {
                                $bbalence += $amounts->debit;
                                $cbalence += $amounts->credit;
                            }
                        }
                        if ($depreciatio) {
                            $lastrevbalance = get_last_year_balance($type, $depreciatio->head_code, $warehouse_id, $fiscalyear->start_date, $fiscalyear->end_date);
                            if ($lastrevbalance) {
                                $fbalence += $lastrevbalance;
                            }
                            $rtval  = get_general_ledger_report($trans->head_code, $warehouse_id, $fiscalyear->start_date, $fiscalyear->end_date, 0, 0);
                            if ($rtval) {
                                foreach ($rtval as $revamount) {
                                    $hbalence += $revamount->debit;
                                    $gbalence += $revamount->credit;
                                }
                            }
                        } else {
                        }

                        $dbalence += $abalence + ($bbalence - $cbalence);
                        if ($trans->depreciation_rate != 0) {
                            $ebalence += $dbalence * ($trans->depreciation_rate / 100);
                            $deprate = $trans->depreciation_rate;
                        }
                        $ibalence += $fbalence + ($gbalence - $hbalence);
                        $jbalence += ($dbalence - $ibalence);

                        $openig         += $abalence;
                        $curentDebit    += $bbalence;
                        $curentCredit   += $cbalence;
                        $curentValue    += $dbalence;
                        $depAmount      += $ebalence;
                        $revOpening     += $fbalence;
                        $revCredit      += $gbalence;
                        $revDebit       += $hbalence;
                        $revBalance     += $ibalence;
                        $famount        += $jbalence;

                        $trdata = array(
                            'headCode'      => $trans->head_code,
                            'headName'      => $trans->head_name,
                            'openig'        => $abalence,
                            'curentDebit'   => $bbalence,
                            'curentCredit'  => $cbalence,
                            'curentValue'   => $dbalence,
                            'depRate'       => $deprate,
                            'depAmount'     => $ebalence,
                            'revOpening'    => $fbalence,
                            'revCredit'     => $gbalence,
                            'revDebit'      => $hbalence,
                            'revBalance'    => $ibalence,
                            'famount'       => $jbalence
                        );
                        array_push($transArray,  $trdata);
                    }

                    $subTotal1   +=  $openig;
                    $subTotal2   +=  $curentDebit;
                    $subTotal3   +=  $curentCredit;
                    $subTotal4   +=  $curentValue;
                    $subTotal5   +=  $depAmount;
                    $subTotal6   +=  $revOpening;
                    $subTotal7   +=  $revCredit;
                    $subTotal8   +=  $revDebit;
                    $subTotal9   +=  $revBalance;
                    $subTotal10  +=  $famount;
                }
                $cdata = array(
                    'headCode'      => $tdl->head_code,
                    'headName'      => $tdl->head_name,
                    'openig'        => $openig,
                    'curentDebit'   => $curentDebit,
                    'curentCredit'  => $curentCredit,
                    'curentValue'   => $curentValue,
                    'depAmount'     => $depAmount,
                    'revOpening'    => $revOpening,
                    'revCredit'     => $revCredit,
                    'revDebit'      => $revDebit,
                    'revBalance'    => $revBalance,
                    'famount'       => $famount,
                    'innerHead'       => $transArray,

                );
                array_push($innerArray,  $cdata);
            }
        }
        $data = array(
            'headCode'  => $hCode,
            'headName'   => $phead->head_name,
            'subtotal1'  => $subTotal1,
            'subtotal2'  => $subTotal2,
            'subtotal3'  => $subTotal3,
            'subtotal4'  => $subTotal4,
            'subtotal5'  => $subTotal5,
            'subtotal6'  => $subTotal6,
            'subtotal7'  => $subTotal7,
            'subtotal8'  => $subTotal8,
            'subtotal9'  => $subTotal9,
            'subtotal10' => $subTotal10,
            'nextlevel'  => $innerArray
        );
        array_push($secondArray,  $data);
        return $secondArray;
    }
}

if (!function_exists('get_general_ledger_head_name')) {
    function get_general_ledger_head_name($cmbGLCode)
    {
        $query = AccCoa::select('head_code', 'head_name')->where('head_code', $cmbGLCode)->first();
        return $query;
    }
}

if (!function_exists('get_charter_accounts_by_head_name2')) {
    function get_charter_accounts_by_head_name2($type, $phead)
    {
        $CharterAccounts = AccCoa::select('head_code', 'head_name')
            ->where('head_type', $type)
            ->where('pre_head_code', $phead)->get();
        return $CharterAccounts;
    }
}

if (!function_exists('get_charter_accounts_by_head_name')) {
    function get_charter_accounts_by_head_name($type, $phead, $except = null)
    {
        $coas = AccCoa::where('head_type', $type)->where('pre_head_code', $phead);
        if ($except != null) {
            $coas->where('head_code', '!=', $except);
        }
        $coas = $coas->get();

        if ($coas->isNotEmpty()) {
            return $coas;
        }
        return false;
    }
}

if (!function_exists('get_charter_accounts_by_asset_code')) {
    function get_charter_accounts_by_asset_code($type, $assetCode)
    {
        $query = AccCoa::select('head_code', 'head_name', 'pre_head_code', 'asset_code', 'dep_code', 'depreciation_rate')
            ->where('dep_code', $assetCode)
            ->where('head_type', $type);

        $coa = $query->first();

        return $coa ? $coa : false;
    }
}

if (!function_exists('get_last_year_balance')) {
    function get_last_year_balance($type, $HeadCode, $warehouse_id, $fstartDate, $fendDate)
    {
        $previousStartDate = date('Y-m-d', strtotime($fstartDate . ' -1 year'));
        $previousEnddate = date('Y-m-d', strtotime($fendDate . ' -1 year'));

        $fyear =  FinancialYear::select('id')->where('start_date', $previousStartDate)->where('end_date', $previousEnddate)->first();

        if (isset($fyear->id)) {
            $query = AccOpeningBalance::select('debit', 'credit')
                ->where('coa_id', $HeadCode)
                ->where('fyear', $fyear->id);

            if (Auth::user()->user_type != 'admin') {
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $query->whereIn('warehouse_id', $warehousearray);
            }

            if ($warehouse_id != null) {
                $query->where('warehouse_id', $warehouse_id);
            }
            $balances = $query->get();

            if ($balances->isNotEmpty()) {
                $result = 0;
                foreach ($balances as $balance) {
                    if ($type == 'A') {
                        if ($balance->debit > $balance->credit) {
                            $result += $balance->debit;
                        } else if ($balance->credit > $balance->debit) {
                            $result -= $balance->credit;
                        }
                    } else {
                        if ($balance->debit > $balance->credit) {
                            $result -= $balance->debit;
                        } else if ($balance->credit > $balance->debit) {
                            $result += $balance->credit;
                        }
                    }
                }

                return $result;
            } else {
                return 0;
            }
        }
        return 0;
    }
}

if (!function_exists('get_head_summary')) {
    function get_head_summary($type, $phead, $warehouse_id, $dtpFromDate, $dtpToDate, $resultType)
    {
        $secondLevel = get_charter_accounts_by_pre_head_name($type, $phead);
        $mainHead = array();
        $sumTotal = 0;
        if ($secondLevel) {
            $secondArray = array();
            foreach ($secondLevel as $chac) {
                $subTotal = 0;
                $innerArray = array();
                $thirdLevel = get_charter_accounts_by_head_name($type, $chac->head_code);
                if ($thirdLevel) {
                    $thirdLevelArray = array();
                    foreach ($thirdLevel as $tdl) {
                        $balance = 0;
                        $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);

                        if ($transationLevel) {
                            $tDebit = 0;
                            $tCredit = 0;
                            foreach ($transationLevel as $trans) {
                                $tval  = get_general_ledger_report($trans->head_code, $warehouse_id, $dtpFromDate, $dtpToDate, 0, 0);
                                if ($tval) {
                                    foreach ($tval as $amounts) {
                                        $tDebit += $amounts->debit;
                                        $tCredit += $amounts->credit;
                                    }
                                }
                            }
                            if ($type == 'A' || $type == 'E') {
                                $balance = $tDebit - $tCredit;
                            } else {
                                $balance = $tCredit - $tDebit;
                            }
                            $sumTotal +=  $balance;
                            $subTotal += $balance;
                        }
                        $cdata = array(
                            'headCode' => $tdl->head_code,
                            'headName' => $tdl->head_name,
                            'amount' => $balance
                        );
                        array_push($innerArray,  $cdata);
                    }
                }
                $data = array(
                    'headCode' => $chac->head_code,
                    'headName' => $chac->head_name,
                    'subtotal' => $subTotal,
                    'innerHead' => $innerArray

                );
                array_push($secondArray,  $data);
            }
        }
        $maina = array(
            'head' => $phead,
            'gtotal' =>  $sumTotal,
            'nextlevel' => $secondArray
        );

        array_push($mainHead,  $maina);

        if ($resultType == 0) {
            return $mainHead;
        } else if ($resultType == 1) {
            return $sumTotal;
        }
    }
}

if (!function_exists('get_charter_accounts_by_pre_head_name')) {
    function get_charter_accounts_by_pre_head_name($type, $phead, $except = null)
    {
        $coas = AccCoa::where('head_type', $type)->where('pre_head_name', $phead);
        if ($except !== null) {
            $coas->where('head_code', '!=', $except);
        }
        $coas = $coas->get();

        if ($coas->isNotEmpty()) {
            return $coas;
        }
        return false;
    }
}

if (!function_exists('get_transational_accounts')) {
    function get_transational_accounts()
    {
        $coas = AccCoa::where('head_level', 4)->orderBy('head_type', 'asc')->get();
        return $coas;
    }
}

if (!function_exists('get_monthly_summary')) {
    function get_monthly_summary($head, $warehouse_id, $fyear)
    {
        $query = AccMonthlyBalance::where('coa_id', $head)
            ->where('fyear', $fyear);

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $query->whereIn('warehouse_id', $warehousearray);
        }

        if ($warehouse_id !== null) {
            $query->where('warehouse_id', $warehouse_id);
        }
        $statements = $query->get();

        return $statements;
    }
}

if (!function_exists('get_monthly_income')) {
    function get_monthly_income($type, $phead, $warehouse_id, $fyear)
    {
        $secondLevel = get_charter_accounts_by_pre_head_name($type, $phead, 401);
        $mainHead = array();
        $sumTotal1  = 0;
        $sumTotal2  = 0;
        $sumTotal3  = 0;
        $sumTotal4  = 0;
        $sumTotal5  = 0;
        $sumTotal6  = 0;
        $sumTotal7  = 0;
        $sumTotal8  = 0;
        $sumTotal9  = 0;
        $sumTotal10 = 0;
        $sumTotal11 = 0;
        $sumTotal12 = 0;
        if ($secondLevel) {
            $secondArray = array();
            foreach ($secondLevel as $chac) {
                $subTotal1  = 0;
                $subTotal2  = 0;
                $subTotal3  = 0;
                $subTotal4  = 0;
                $subTotal5  = 0;
                $subTotal6  = 0;
                $subTotal7  = 0;
                $subTotal8  = 0;
                $subTotal9  = 0;
                $subTotal10 = 0;
                $subTotal11 = 0;
                $subTotal12 = 0;
                $innerArray = array();
                $thirdLevel = get_charter_accounts_by_head_name($type, $chac->head_code);
                if ($thirdLevel) {
                    $thirdLevelArray = array();
                    foreach ($thirdLevel as $tdl) {
                        $balance1  = 0;
                        $balance2  = 0;
                        $balance3  = 0;
                        $balance4  = 0;
                        $balance5  = 0;
                        $balance6  = 0;
                        $balance7  = 0;
                        $balance8  = 0;
                        $balance9  = 0;
                        $balance10 = 0;
                        $balance11 = 0;
                        $balance12 = 0;
                        $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);

                        if ($transationLevel) {

                            foreach ($transationLevel as $trans) {
                                $tval  = get_monthly_summary($trans->head_code, $warehouse_id, $fyear);
                                if ($tval->isNotEmpty()) {
                                    foreach ($tval as $tv) {
                                        $balance1  += $tv->balance1;
                                        $balance2  += $tv->balance2;
                                        $balance3  += $tv->balance3;
                                        $balance4  += $tv->balance4;
                                        $balance5  += $tv->balance5;
                                        $balance6  += $tv->balance6;
                                        $balance7  += $tv->balance7;
                                        $balance8  += $tv->balance8;
                                        $balance9  += $tv->balance9;
                                        $balance10 += $tv->balance10;
                                        $balance11 += $tv->balance11;
                                        $balance12 += $tv->balance12;
                                    }
                                }
                            }

                            $sumTotal1  +=  $balance1;
                            $sumTotal2  +=  $balance2;
                            $sumTotal3  +=  $balance3;
                            $sumTotal4  +=  $balance4;
                            $sumTotal5  +=  $balance5;
                            $sumTotal6  +=  $balance6;
                            $sumTotal7  +=  $balance7;
                            $sumTotal8  +=  $balance8;
                            $sumTotal9  +=  $balance9;
                            $sumTotal10 +=  $balance10;
                            $sumTotal11 +=  $balance11;
                            $sumTotal12 +=  $balance12;
                            $subTotal1  += $balance1;
                            $subTotal2  += $balance2;
                            $subTotal3  += $balance3;
                            $subTotal4  += $balance4;
                            $subTotal5  += $balance5;
                            $subTotal6  += $balance6;
                            $subTotal7  += $balance7;
                            $subTotal8  += $balance8;
                            $subTotal9  += $balance9;
                            $subTotal10 += $balance10;
                            $subTotal11 += $balance11;
                            $subTotal12 += $balance12;
                        }
                        $cdata = array(
                            'headCode' => $tdl->head_code,
                            'headName' => $tdl->head_name,
                            'amount1'  => $balance1,
                            'amount2'  => $balance2,
                            'amount3'  => $balance3,
                            'amount4'  => $balance4,
                            'amount5'  => $balance5,
                            'amount6'  => $balance6,
                            'amount7'  => $balance7,
                            'amount8'  => $balance8,
                            'amount9'  => $balance9,
                            'amount10' => $balance10,
                            'amount11' => $balance11,
                            'amount12' => $balance12,
                        );
                        array_push($innerArray,  $cdata);
                    }
                }
                $data = array(
                    'headCode' => $chac->head_code,
                    'headName'  => $chac->head_name,
                    'subtotal'  => $subTotal1,
                    'subtota2'  => $subTotal2,
                    'subtota3'  => $subTotal3,
                    'subtota4'  => $subTotal4,
                    'subtota5'  => $subTotal5,
                    'subtota6'  => $subTotal6,
                    'subtota7'  => $subTotal7,
                    'subtota8'  => $subTotal8,
                    'subtota9'  => $subTotal9,
                    'subtotal0' => $subTotal10,
                    'subtotal1' => $subTotal11,
                    'subtotal2' => $subTotal12,
                    'innerHead' => $innerArray
                );
                array_push($secondArray,  $data);
            }
        }
        $maina = array(
            'head'         =>  $phead,
            'gtotal1'      =>  $sumTotal1,
            'gtotal2'      =>  $sumTotal2,
            'gtotal3'      =>  $sumTotal3,
            'gtotal4'      =>  $sumTotal4,
            'gtotal5'      =>  $sumTotal5,
            'gtotal6'      =>  $sumTotal6,
            'gtotal7'      =>  $sumTotal7,
            'gtotal8'      =>  $sumTotal8,
            'gtotal9'      =>  $sumTotal9,
            'gtotal10'     =>  $sumTotal10,
            'gtotal11'     =>  $sumTotal11,
            'gtotal12'     =>  $sumTotal12,
            'nextlevel'    =>  $secondArray
        );

        array_push($mainHead,  $maina);
        return $mainHead;
    }
}

if (!function_exists('get_from_second_level_expenses')) {
    function get_from_second_level_expenses($type, $hCode, $warehouse_id, $fyear)
    {
        $phead = get_general_ledger_head_name($hCode);
        $secondArray = array();

        $subTotal1  = 0;
        $subTotal2  = 0;
        $subTotal3  = 0;
        $subTotal4  = 0;
        $subTotal5  = 0;
        $subTotal6  = 0;
        $subTotal7  = 0;
        $subTotal8  = 0;
        $subTotal9  = 0;
        $subTotal10 = 0;
        $subTotal11 = 0;
        $subTotal12 = 0;
        $thirdLevel = get_charter_accounts_by_head_name($type, $phead->head_code);
        $innerArray = array();
        if ($thirdLevel) {
            foreach ($thirdLevel as $tdl) {
                $balance1  = 0;
                $balance2  = 0;
                $balance3  = 0;
                $balance4  = 0;
                $balance5  = 0;
                $balance6  = 0;
                $balance7  = 0;
                $balance8  = 0;
                $balance9  = 0;
                $balance10 = 0;
                $balance11 = 0;
                $balance12 = 0;
                $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);

                if ($transationLevel) {

                    foreach ($transationLevel as $trans) {
                        $tval = get_monthly_summary($trans->head_code, $warehouse_id, $fyear);
                        if ($tval->isNotEmpty()) {
                            foreach ($tval as $tv) {
                                $balance1  += $tv->balance1;
                                $balance2  += $tv->balance2;
                                $balance3  += $tv->balance3;
                                $balance4  += $tv->balance4;
                                $balance5  += $tv->balance5;
                                $balance6  += $tv->balance6;
                                $balance7  += $tv->balance7;
                                $balance8  += $tv->balance8;
                                $balance9  += $tv->balance9;
                                $balance10 += $tv->balance10;
                                $balance11 += $tv->balance11;
                                $balance12 += $tv->balance12;
                            }
                        }
                    }

                    $subTotal1  += $balance1;
                    $subTotal2  += $balance2;
                    $subTotal3  += $balance3;
                    $subTotal4  += $balance4;
                    $subTotal5  += $balance5;
                    $subTotal6  += $balance6;
                    $subTotal7  += $balance7;
                    $subTotal8  += $balance8;
                    $subTotal9  += $balance9;
                    $subTotal10 += $balance10;
                    $subTotal11 += $balance11;
                    $subTotal12 += $balance12;
                }
                $cdata = array(
                    'headCode' => $tdl->head_code,
                    'headName' => $tdl->head_name,
                    'amount1'  => $balance1,
                    'amount2'  => $balance2,
                    'amount3'  => $balance3,
                    'amount4'  => $balance4,
                    'amount5'  => $balance5,
                    'amount6'  => $balance6,
                    'amount7'  => $balance7,
                    'amount8'  => $balance8,
                    'amount9'  => $balance9,
                    'amount10' => $balance10,
                    'amount11' => $balance11,
                    'amount12' => $balance12,
                );
                array_push($innerArray,  $cdata);
            }
        }
        $data = array(
            'headCode' => $hCode,
            'headName'  => $phead->head_name,
            'subtota1'  => $subTotal1,
            'subtota2'  => $subTotal2,
            'subtota3'  => $subTotal3,
            'subtota4'  => $subTotal4,
            'subtota5'  => $subTotal5,
            'subtota6'  => $subTotal6,
            'subtota7'  => $subTotal7,
            'subtota8'  => $subTotal8,
            'subtota9'  => $subTotal9,
            'subtota10' => $subTotal10,
            'subtota11' => $subTotal11,
            'subtota12' => $subTotal12,
            'innerHead' => $innerArray
        );
        array_push($secondArray,  $data);

        return $secondArray;
    }
}

if (!function_exists('get_previous_year_balance_sheet')) {
    function get_previous_year_balance_sheet($hCode, $warehouse_id, $type, $numYear)
    {
        $fyearStartDate = Session::get('fyearStartDate');
        $fyearEndDate = Session::get('fyearEndDate');

        $previousStartDate = date('Y-m-d', strtotime($fyearStartDate . ' -' . $numYear . ' year'));
        $previousEnddate = date('Y-m-d', strtotime($fyearEndDate . ' -' . $numYear . ' year'));

        $fyear =  FinancialYear::where('start_date', $previousStartDate)->where('end_date', $previousEnddate)->first();
        if (isset($fyear->id)) {
            $query = AccOpeningBalance::select('debit', 'credit')
                ->where('coa_id', $hCode)
                ->where('fyear', $fyear->id);

            if (Auth::user()->user_type != 'admin') {
                $warehousearray = getWearhouseBuUserId(auth()->user()->id);
                $query->whereIn('warehouse_id', $warehousearray);
            }

            if ($warehouse_id != null) {
                $query->where('warehouse_id', $warehouse_id);
            }
            $oldbalanced = $query->get();

            if ($oldbalanced->isNotEmpty()) {
                $result = 0;
                foreach ($oldbalanced as $balance) {
                    if ($type == 'A') {
                        if ($balance->debit > $balance->credit) {
                            $result += $balance->debit;
                        } else if ($balance->credit > $balance->debit) {
                            $result -= $balance->credit;
                        }
                    } else {
                        if ($balance->debit > $balance->credit) {
                            $result -= $balance->debit;
                        } else if ($balance->credit > $balance->debit) {
                            $result += $balance->credit;
                        }
                    }
                }
                return $result;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_balance_sheet_summary')) {
    function get_balance_sheet_summary($type, $phead, $warehouse_id, $dtpFromDate, $dtpToDate)
    {
        $returnarning = AccPredefineAccount::select('cpl_code')->first();
        $secondLevel = get_charter_accounts_by_pre_head_name($type, $phead);
        $mainHead = array();
        $sumTotal = 0;
        $sumTotal1 = 0;
        $sumTotal2 = 0;
        $sumTotal3 = 0;
        if ($secondLevel) {
            $secondArray = array();
            foreach ($secondLevel as $chac) {
                $subTotal = 0;
                $subTotal1 = 0;
                $subTotal2 = 0;
                $subTotal3 = 0;
                $innerArray = array();
                $thirdLevel = get_charter_accounts_by_head_name($type, $chac->head_code);
                if ($thirdLevel) {
                    $thirdLevelArray = array();
                    foreach ($thirdLevel as $tdl) {
                        $balance = 0;
                        $returnern = 0;
                        $secondbalance = 0;
                        $thirdbalance = 0;
                        $fourthbalance = 0;
                        $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);

                        if ($transationLevel) {
                            $tbalence = 0;
                            $tDebit = 0;
                            $tCredit = 0;
                            $scyear = 0;
                            $tdyear = 0;
                            $fryear = 0;
                            foreach ($transationLevel as $trans) {
                                $tval  = get_closing_balance($trans->head_code, $warehouse_id, $dtpToDate);
                                if ($tval) {
                                    $tbalence += $tval;
                                }
                                $scyear += get_previous_year_balance_sheet($trans->head_code, $warehouse_id, $type, 1);
                                $tdyear += get_previous_year_balance_sheet($trans->head_code, $warehouse_id, $type, 2);
                                $fryear += get_previous_year_balance_sheet($trans->head_code, $warehouse_id, $type, 3);
                                if ($returnarning->cpl_code == $trans->head_code) {
                                    $income = get_head_summary('I', 'Income', $warehouse_id, $dtpFromDate, $dtpToDate, 1);
                                    $expense = get_head_summary('E', 'Expenses', $warehouse_id, $dtpFromDate, $dtpToDate, 1);
                                    $returnern += ($income - $expense);
                                }
                            }
                            if ($returnern != 0) {
                                $balance = $tbalence + $returnern;
                            } else {
                                $balance = $tbalence;
                            }

                            $secondbalance = $scyear;
                            $thirdbalance  = $tdyear;
                            $fourthbalance = $fryear;
                            $sumTotal   +=  $balance;
                            $sumTotal1  +=  $secondbalance;
                            $sumTotal2  +=  $thirdbalance;
                            $sumTotal3  +=  $fourthbalance;
                            $subTotal   +=  $balance;
                            $subTotal1  += $secondbalance;
                            $subTotal2  += $thirdbalance;
                            $subTotal3  += $fourthbalance;
                        }
                        $cdata = array(
                            'headCode' => $tdl->head_code,
                            'headName' => $tdl->head_name,
                            'amount' => $balance,
                            'secondyear' => $secondbalance,
                            'thirdyear' =>  $thirdbalance,
                            'fourthyear' => $fourthbalance,
                        );
                        array_push($innerArray,  $cdata);
                    }
                }
                $data = array(
                    'headCode' => $chac->head_code,
                    'headName'  => $chac->head_name,
                    'subtotal'  => $subTotal,
                    'ssubtotal' => $subTotal1,
                    'tsubtotal' => $subTotal2,
                    'fsubtotal' => $subTotal3,
                    'innerHead' => $innerArray

                );
                array_push($secondArray,  $data);
            }
        }
        $maina = array(
            'head'      =>  $phead,
            'gtotal'    =>  $sumTotal,
            'sgtotal'   =>  $sumTotal1,
            'tgtotal'   =>  $sumTotal2,
            'fgtotal'   =>  $sumTotal3,
            'nextlevel' =>  $secondArray
        );

        array_push($mainHead,  $maina);
        return $mainHead;
    }
}


if (!function_exists('reconciliation_voucher')) {
    function reconciliation_voucher($dtpFromDate, $dtpToDate, $bankCode = null, $warehouse_id = null, $status = 0)
    {
        $values = ['DV'];
        $query = DB::table('acc_vouchers as v')
            ->select(
                'v.*',
                DB::raw('SUM(v.credit) as credit'),
                DB::raw('SUM(v.debit) as debit'),
                'a.head_name as bank_name',
                'ac.head_name as account_name'
            )
            ->join('acc_coas as a', 'v.rev_code', '=', 'a.head_code', 'left')
            ->join('acc_coas as ac', 'v.coa_id', '=', 'ac.head_code', 'left')
            ->whereIn('voucher_type', $values)
            ->where('v.is_approved', 1)
            ->whereNotNull('v.cheque_no')
            ->where('a.is_bank_nature', 1);

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $query->whereIn('v.warehouse_id', $warehousearray);
        }


        if ($bankCode !== null) {
            $query->where('v.rev_code', $bankCode);
        }

        if ($warehouse_id !== null) {
            $query->where('v.warehouse_id', $warehouse_id);
        }

        if ($status == 1) {
            $query->where('v.is_honour', 1);
        } elseif ($status == 2) {
            $query->where('v.is_honour', 0);
        }

        $query->whereBetween('v.voucher_date', [$dtpFromDate, $dtpToDate])
            ->groupBy('v.voucher_no');

        $approveInfo = $query->get();

        return $approveInfo->isNotEmpty() ? $approveInfo : false;
    }
}

if (!function_exists('get_transational_head_by_id')) {
    function get_transational_head_by_id($HeadCode)
    {
        $coas = AccCoa::where('head_level', 4)
            ->where('pre_head_code', $HeadCode)
            ->where('is_active', 1)
            ->orderBy('head_name', 'asc')
            ->get();

        return $coas;
    }
}

if (!function_exists('general_led_report_headname')) {
    function general_led_report_headname($cmbGLCode)
    {
        $query = AccCoa::where('head_code', $cmbGLCode)->get();
        return $query;
    }
}

if (!function_exists('get_opening_summary')) {
    function get_opening_summary($HeadCode, $warehouse_id, $startdate, $enddate)
    {
        $sumval = 0;
        $headItem = get_transational_head_by_id($HeadCode);
        if ($headItem) {
            foreach ($headItem as $row) {
                $balance = get_opening_balance($row->head_code, $warehouse_id, $startdate, $enddate);
                if ($balance) {
                    $sumval += $balance;
                }
            }
        }
        return $sumval;
    }
}

if (!function_exists('get_closing_summary')) {
    function get_closing_summary($id, $warehouse_id, $reportDate)
    {
        $sumval = 0;
        $headItem = get_transational_head_by_id($id);
        if ($headItem) {
            foreach ($headItem as $row) {
                $balance = get_closing_balance($row->head_code, $warehouse_id, $reportDate);
                if ($balance) {
                    $sumval += $balance;
                }
            }
        }
        return $sumval;
    }
}

if (!function_exists('get_receipt_payment_head')) {
    function get_receipt_payment_head($phead, $accountID)
    {
        $query = AccCoa::where('pre_head_code', $phead)->where('is_active', 1);
        if (!empty($accountID)) {
            $query->whereIn('head_code', $accountID);
        }
        $results = $query->get();

        return $results;
    }
}

if (!function_exists('get_all_transational_voucher')) {
    function get_all_transational_voucher($reportType, $warehouse_id, $startDate, $endDate, $vType)
    {
        $query = AccTransaction::distinct()->select('coa_id')->whereBetween('voucher_date', [$startDate, $endDate]);

        if (Auth::user()->user_type != 'admin') {
            $warehousearray = getWearhouseBuUserId(auth()->user()->id);
            $query->whereIn('warehouse_id', $warehousearray);
        }

        if ($vType == 'DV') {
            $query->where('debit', '!=', 0.00);
        } else {
            $query->where('credit', ' !=', 0.00);
        }

        if ($warehouse_id != null) {
            $query->where('warehouse_id', $warehouse_id);
        }

        if ($reportType == 'Accrual Basis') {
            $query->where(function ($q) use ($vType) {
                $q->where('voucher_type', $vType)->orWhere('voucher_type', 'JV');
            });
        } else {
            $query->where('voucher_type', $vType);
        }

        $results = $query->get();

        if ($results->isNotEmpty()) {
            $transationarray = array();
            foreach ($results as $value) {
                array_push($transationarray, $value->coa_id);
            }
            return $transationarray;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_chartered_account_by_level')) {
    function get_chartered_account_by_level($level)
    {
        $results = AccCoa::where('head_level', $level)->where('is_active', 1)->get();
        if ($results->isNotEmpty() > 0) {
            return $results;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_item_ledger_receipt_payment')) {
    function get_item_ledger_receipt_payment($reportType, $warehouse_id, $startDate, $endDate, $vType)
    {
        $vaucherhead = get_all_transational_voucher($reportType, $warehouse_id, $startDate, $endDate, $vType);
        $thirdhead = get_chartered_account_by_level(3);
        if ($thirdhead) {
            $finalarray = array();
            foreach ($thirdhead as $third) {
                $paymentHead = get_receipt_payment_head($third->head_code, $vaucherhead);
                if ($paymentHead->isNotEmpty()) {
                    $subtotal = 0;
                    $paymentheadarray = array();
                    foreach ($paymentHead as $payhead) {
                        $dbalance = 0;
                        $cbalance = 0;
                        $headBalance = get_general_ledger_report($payhead->head_code, $warehouse_id, $startDate, $endDate, 0, 0);
                        if ($headBalance) {
                            foreach ($headBalance as $balance) {
                                if ($vType == 'DV') {
                                    $dbalance += $balance->debit;
                                    $subtotal += $balance->debit;
                                } else if ($vType == 'CV') {
                                    $cbalance += $balance->credit;
                                    $subtotal += $balance->credit;
                                }
                            }
                            $darray = array(
                                'code' => $payhead->head_code,
                                'headName' => $payhead->head_name,
                                'debit' => $dbalance,
                                'credit' => $cbalance
                            );
                            array_push($paymentheadarray, $darray);
                        }
                    }
                    if (count($paymentheadarray) > 0) {
                        $parray = array(
                            'hcode' => $third->head_code,
                            'headName' => $third->head_name,
                            'innerHead' => $paymentheadarray,
                            'subtotal' => $subtotal,
                        );
                        array_push($finalarray, $parray);
                    }
                }
            }
        }
        return $finalarray;
    }
}

if (!function_exists('year_closing_summary')) {
    function year_closing_summary($type, $phead, $warehouse_id, $dtpFromDate, $dtpToDate, $fyear)
    {
        $returnarning = AccPredefineAccount::select('lpl_code')->first();
        $route_name = Route::currentRouteName();
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');
        $open_date = date('Y-m-d', strtotime($dtpToDate . ' +1 day'));
        $secondLevel = get_charter_accounts_by_pre_head_name($type, $phead);

        $sta = array();
        if ($secondLevel) {
            foreach ($secondLevel as $chac) {
                $thirdLevel = get_charter_accounts_by_head_name($type, $chac->head_code);
                if ($thirdLevel) {
                    foreach ($thirdLevel as $tdl) {
                        $transationLevel = get_charter_accounts_by_head_name($type, $tdl->head_code);
                        if ($transationLevel) {
                            foreach ($transationLevel as $trans) {
                                $debit_amount = 0;
                                $credit_amount = 0;
                                if ($trans->sub_type == 1) {
                                    $tval = get_closing_balance($trans->head_code, $warehouse_id, $dtpToDate);
                                    if ($tval) {
                                        if ($type == 'A') {
                                            $credit_amount = 0;
                                            $debit_amount = $tval;
                                        } else {
                                            $debit_amount = 0;
                                            $credit_amount = $tval;
                                        }
                                    }

                                    $opening_balance = new AccOpeningBalance();
                                    $opening_balance->fyear = $fyear;
                                    $opening_balance->coa_id = $trans->head_code;
                                    $opening_balance->sub_type = 1;
                                    $opening_balance->sub_code = null;
                                    $opening_balance->open_date = $open_date;
                                    $opening_balance->debit = $debit_amount;
                                    $opening_balance->credit = $credit_amount;
                                    $opening_balance->warehouse_id = $warehouse_id;
                                    $opening_balance->created_by = $created_by;
                                    $opening_balance->created_at = $created_at;
                                    $opening_balance->save();

                                    add_activity_log("financial_year_closing", "create", $opening_balance->id, "acc_opening_balances", $route_name, 1, $opening_balance);
                                } else {
                                    $subcodes = get_subcode($trans->sub_type);
                                    if ($subcodes) {
                                        foreach ($subcodes as $subcode) {
                                            $tval = get_closing_balance($trans->head_code, $warehouse_id, $dtpToDate, $dtpToDate, $trans->sub_type, $subcode->id);
                                            if ($tval) {
                                                if ($type == 'A') {
                                                    $credit_amount =  0;
                                                    $debit_amount = $tval;
                                                } else {
                                                    $debit_amount = 0;
                                                    $credit_amount = $tval;
                                                }
                                            }

                                            $opening_balance = new AccOpeningBalance();
                                            $opening_balance->fyear = $fyear;
                                            $opening_balance->coa_id = $trans->head_code;
                                            $opening_balance->sub_type = $trans->sub_type;
                                            $opening_balance->sub_code = $subcode->id;
                                            $opening_balance->open_date = $open_date;
                                            $opening_balance->debit = $debit_amount;
                                            $opening_balance->credit = $credit_amount;
                                            $opening_balance->warehouse_id = $warehouse_id;
                                            $opening_balance->created_by = $created_by;
                                            $opening_balance->created_at = $created_at;
                                            $opening_balance->save();

                                            add_activity_log("financial_year_closing", "create", $opening_balance->id, "acc_opening_balances", $route_name, 1, $opening_balance);
                                        }
                                    }
                                }

                                if ($returnarning->lpl_code == $trans->head_code) {
                                    $income = get_head_summary('I', 'Income', $warehouse_id, $dtpFromDate, $dtpToDate, 1);
                                    $expense = get_head_summary('E', 'Expenses', $warehouse_id, $dtpFromDate, $dtpToDate, 1);
                                    if ($income > $expense) {
                                        $credit_amount = $income - $expense;
                                        $debit_amount = 0;
                                    } else {
                                        $credit_amount = $income - $expense;
                                        $debit_amount = 0;
                                    }

                                    $opening_balance = new AccOpeningBalance();
                                    $opening_balance->fyear = $fyear;
                                    $opening_balance->coa_id = $returnarning->lpl_code;
                                    $opening_balance->sub_type = 1;
                                    $opening_balance->sub_code = null;
                                    $opening_balance->open_date = $open_date;
                                    $opening_balance->debit = $debit_amount;
                                    $opening_balance->credit = $credit_amount;
                                    $opening_balance->warehouse_id = $warehouse_id;
                                    $opening_balance->created_by = $created_by;
                                    $opening_balance->created_at = $created_at;
                                    $opening_balance->save();

                                    add_activity_log("year_closing", "create", $opening_balance->id, "acc_opening_balances", $route_name, 1, $opening_balance);
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}

if (!function_exists('get_subcode')) {
    function get_subcode($id)
    {
        $subcodes = AccSubcode::where('sub_type_id', $id)->get();
        if ($subcodes->isNotEmpty()) {
            return $subcodes;
        }
        return false;
    }
}

if (!function_exists('check_financial_year')) {
    function check_financial_year($sdate, $edate, $yn)
    {
        $data = FinancialYear::where('start_date', $sdate)->where('end_date', $edate)->where('year_name', $yn)->count();
        if ($data > 0) {
            return false;
        }
        return true;
    }
}

if (!function_exists('last_closed_financial_year')) {
    function last_closed_financial_year()
    {
        $fyear = FinancialYear::where('is_close', 1)->orderBy('id', 'desc')->first();
        if ($fyear) {
            return $fyear->id;
        }
        return false;
    }
}

if (!function_exists('get_subcode_by_id')) {
    function get_subcode_by_id($id)
    {
        $subcode = AccSubcode::where('id', $id)->first();
        if ($subcode) {
            return $subcode;
        }
        return false;
    }
}

if (!function_exists('get_account_head_by_subtype')) {
    function get_account_head_by_subtype($id)
    {
        $coas = AccCoa::where('sub_type', $id)->get();
        if ($coas->isNotEmpty()) {
            return $coas;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_sub_type_items')) {
    function get_sub_type_items($id)
    {
        $subcodes = AccSubcode::where('sub_type_id', $id)->orderBy('id', 'asc')->get();

        if ($subcodes->isNotEmpty()) {
            return $subcodes;
        }
        return 0;
    }
}

if (!function_exists('invoice_method_wise_balance')) {
    function invoice_method_wise_balance($order_id)
    {
        $financialYearId = get_financial_year();

        $query = DB::table('acc_vouchers')
            ->select('acc_vouchers.debit', 'acc_vouchers.coa_id', 'acc_coas.head_name')
            ->leftJoin('acc_coas', 'acc_coas.head_code', '=', 'acc_vouchers.coa_id')
            ->where('acc_vouchers.reference_no', $order_id)
            ->where('acc_vouchers.voucher_type', 'CV')
            ->where('acc_vouchers.fyear', $financialYearId);

        return $query->get();
    }
}

if (!function_exists('getCoaIdForOrder')) {
    function getCoaIdForOrder($order)
    {
        switch ($order->payment_method) {
            case 'cash_on_delivery':
                return 1001;
            case 'bank_transfer':
                return 1002;
            case 'sslcommerz':
                return 1003;
            case 'wallet':
                return 1004;
            case 'bkash':
                return 1005;
            case 'nagad':
                return 1006;
            default:
                return 11100101; // Default COA ID
        }

        if ($order->type == 'retail') {
            return 2001;
        } elseif ($order->type == 'wholesale') {
            return 2002;
        }
    }
}

if (!function_exists('order_payment_method')) {
    function order_payment_method($order)
    {
        switch ($order->payment_method) {
            case 'cash_on_delivery':
                return 1020101;
            case 'sslcommerz':
                return 1020503;
            case 'bkash':
                return 1020506;
            case 'nagad':
                return 1020505;
            case 'wallet':
                return 1020525;
            default:
                return 1020101;
        }
    }
}

if (!function_exists('insert_sale_journal')) {
    function insert_sale_journal($order_id, $purchase_amount)
    {
        try{
            $order = Order::findOrFail($order_id);

            $cash_bank_headcode = order_payment_method($order);
    
            $order_id       = $order->id;
            $customer_id    = User::where('id', $order->user_id)->value('id');
            $grand_total    = $order->grand_total;
            // $paid_amount    = $order->paid_amount;
            // $due_amount     = $order->due_amount;
    
            $payments = $order->payment_details;
    
            if ($payments) {
                $payments = json_decode($payments);
                $paid_amount = $payments->amount;
                $due_amount = $grand_total - $paid_amount;
            } else {
                $paid_amount    = 0;
                $due_amount     = $grand_total;
            }
    
            $order->paid_amount = $paid_amount;
            $order->due_amount = $due_amount;
            $order->save();
    
            $wearhouse_id = getWearhouseId($order_id);
            $predefine_account  = AccPredefineAccount::first();
    
            $narration          = "Sales Voucher";
            $comment            = "Sales Voucher for customer";
            $rev_id              = $predefine_account->sales_code;
    
            if ($due_amount > 0) {
                $is_credit  = 1;
                $amnt_type  = 'debit';
                $coa_id     = $predefine_account->customer_code;
                $subcode    = AccSubcode::where('reference_no', $customer_id)->where('sub_type_id', 3)->value('id');
    
                insert_sale_credit_voucher($is_credit, $order_id, $wearhouse_id, $coa_id, $amnt_type, $due_amount, $narration, $comment, $rev_id, $subcode);
            }
    
            if ($paid_amount > 0) {
                $is_credit  = 0;
                $amnt_type  = 'credit';
                $coa_id     = $cash_bank_headcode;
    
                insert_sale_credit_voucher($is_credit, $order_id, $wearhouse_id, $coa_id, $amnt_type, $paid_amount, $narration, $comment, $rev_id);
            }
    
            // for inventory & cost of goods sold start
            $goodsCOAID     = $predefine_account->costs_of_good_solds;
            $purchasevalue  = $purchase_amount;
            $goodsNarration = "Sales cost of goods voucher";
            $username       = $order->user ? $order->user->name : 'Guest';
            $customer_id    = Customer::where('user_id', $order->user_id)->value('customer_id');
            $goodsComment = sprintf("Sales cost of goods voucher for customer (%s), %s and order code %s",$customer_id,$username,$order->code);
            $goodsreVID     = $predefine_account->inventory_code;
    
            insert_sale_inventory_voucher($order_id, $wearhouse_id, $goodsCOAID, $purchasevalue, $goodsNarration, $goodsComment, $goodsreVID);
            // for inventory & cost of goods sold end
    
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            // return false;
        }
    }
}

if (!function_exists('insert_sale_credit_voucher')) {
    function insert_sale_credit_voucher($is_credit = null, $order_id = null, $warehouse_id,  $coa_id = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $order          = Order::findOrFail($order_id);
        $customer_id    = User::where('id', $order->user_id)->value('id');
        $relvalue       = AccSubcode::where('reference_no', $customer_id)->where('sub_type_id', 3)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $order_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $coa_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $rev_id,
                'sub_type'          => 3,
                'sub_code'          => $subcode,
                'relational_type'   => 3,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CV', 'voucher_no');
            $voucher_no = "CV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'CV',
                'reference_no'      => $order_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $coa_id,
                'relational_type'   => 3,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit'] = $amnt;
            $debitinsert['credit'] = 0.00;
        } else {
            $debitinsert['debit'] = 0.00;
            $debitinsert['credit'] = $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}

if (!function_exists('insert_sale_inventory_voucher')) {
    function insert_sale_inventory_voucher($order_id = null, $warehouse_id, $dbtid = null, $amnt = null, $narration = null, $comment = null, $rev_id = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $order          = Order::findOrFail($order_id);
        $customer_id    = User::where('id', $order->user_id)->value('id');
        $relvalue       = AccSubcode::where('reference_no', $customer_id)->where('sub_type_id', 3)->value('id');

        $maxidforgoods = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
        $voucher_no = "JV-" . ($maxidforgoods + 1);

        $debitinsert = [
            'fyear'             => $fyear,
            'voucher_no'        => $voucher_no,
            'voucher_type'      => 'JV',
            'reference_no'      => $order_id,
            'voucher_date'      => $voucher_date,
            'coa_id'            => $dbtid,
            'narration'         => $narration,
            'ledger_comment'    => $comment,
            'debit'             => $amnt,
            'rev_code'          => $rev_id,
            'relational_type'   => 3,
            'relational_value'  => $relvalue,
            'is_approved'       => 0,
            'created_by'        => $created_by,
            'created_at'        => $created_at,
            'status'            => 0,
            'warehouse_id'      => $warehouse_id,
        ];

        AccVoucher::insert($debitinsert);
        return true;
    }
}

if (!function_exists('insert_sale_tax_voucher')) {
    function insert_sale_tax_voucher($order_id = null, $warehouse_id, $dbtid = null, $amnt = null, $narration = null, $comment = null, $rev_id = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $maxidtax = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
        $voucher_no = "JV-" . ($maxidtax + 1);

        $debitinsert = [
            'fyear'         => $fyear,
            'voucher_no'    => $voucher_no,
            'voucher_type'  => 'JV',
            'reference_no'  => $order_id,
            'voucher_date'  => $voucher_date,
            'coa_id'        => $dbtid,
            'anrration'     => $narration,
            'ledger_comment' => $comment,
            'debit'         => $amnt,
            'rev_code'      => $rev_id,
            'is_approved'   => 0,
            'created_by'    => $created_by,
            'created_at'    => $created_at,
            'status'        => 0,
            'warehouse_id'  => $warehouse_id,
        ];

        DB::table('acc_vouchers')->insert($debitinsert);

        return true;
    }
}

if (!function_exists('autoapprove')) {
    function autoapprove($invoice_id)
    {
        $vouchers = AccVoucher::select('reference_no', 'voucher_no')
            ->where('reference_no', $invoice_id)
            ->where('status', 0)
            ->get();

        foreach ($vouchers as $voucher) {
            approved_voucher($voucher->voucher_no, 'active');
        }

        return true;
    }
}

if (!function_exists('approve_voucher_by_voucher_no')) {
    function approve_voucher_by_voucher_no($voucherNo)
    {
        DB::table('acc_vouchers')
            ->where('voucher_no', $voucherNo)
            ->update([
                'status' => 1,
                'is_approved' => 1,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
    }
}

if (!function_exists('approve_voucher')) {
    function approve_voucher()
    {
        $values = ["DV", "CV", "JV", "CT"];

        $approveInfo = DB::table('acc_vouchers')
            ->select(
                'acc_vouchers.id as vid',
                'acc_vouchers.voucher_no',
                'acc_vouchers.reference_no',
                'acc_vouchers.narration',
                DB::raw('SUM(credit) as credit'),
                DB::raw('SUM(debit) as debit'),
                'acc_coas.head_name'
            )
            ->leftJoin('acc_coas', 'acc_vouchers.coa_id', '=', 'acc_coas.head_code')
            ->whereIn('voucher_type', $values)
            ->where('is_approved', 0)
            ->groupBy('voucher_no')
            ->orderBy('reference_no')
            ->get()
            ->toArray();

        return $approveInfo;
    }
}

if (!function_exists('approved')) {
    function approved(array $data)
    {
        return DB::table('acc_transactions')
            ->where('voucher_no', $data['voucher_no'])
            ->update($data);
    }
}

if (!function_exists('approved_voucher')) {
    function approved_voucher($id, $action)
    {
        $vauchers = AccVoucher::where('voucher_no', $id)->get();
        $approved_by = Auth::user()->id;
        $approved_at = date('Y-m-d H:i:s');

        if ($vauchers->isNotEmpty()) {
            foreach ($vauchers as $vaucher) {
                $transationinsert = array(
                    'voucher_id'        => $vaucher->id,
                    'fyear'             => $vaucher->fyear,
                    'voucher_no'        => $vaucher->voucher_no,
                    'voucher_type'      => $vaucher->voucher_type,
                    'reference_no'      => $vaucher->reference_no,
                    'voucher_date'      => $vaucher->voucher_date,
                    'coa_id'            => $vaucher->coa_id,
                    'narration'         => $vaucher->narration,
                    'cheque_no'         => !empty($vaucher->cheque_no) ? $vaucher->cheque_no : '',
                    'cheque_date'       => $vaucher->cheque_date,
                    'is_honour'         => $vaucher->is_honour,
                    'ledger_comment'    => $vaucher->ledger_comment,
                    'debit'             => $vaucher->debit,
                    'credit'            => $vaucher->credit,
                    'store_id'          => 0,
                    'is_posted'         => 1,
                    'rev_code'          => $vaucher->rev_code,
                    'sub_type'          => $vaucher->sub_type,
                    'sub_code'          => $vaucher->sub_code,
                    'is_approved'         => 1,
                    'created_by'        => $approved_by,
                    'created_at'        => $approved_at,
                    'warehouse_id'      => $vaucher->warehouse_id
                );
                $instran = AccTransaction::insert($transationinsert);
                // addActivityLog("approved_vaucher_transation", "create", $this->db->insert_id(), "acc_transaction", 1, $transationinsert);

                // Update Monthly Record
                if ($instran) {
                    store_transaction_summary($vaucher->coa_id, $vaucher->warehouse_id, $vaucher->voucher_date);
                    $revercetransationinsert = array(
                        'voucher_id'        => $vaucher->id,
                        'fyear'             => $vaucher->fyear,
                        'voucher_no'        => $vaucher->voucher_no,
                        'voucher_type'      => $vaucher->voucher_type,
                        'reference_no'      => $vaucher->reference_no,
                        'voucher_date'      => $vaucher->voucher_date,
                        'coa_id'            => $vaucher->rev_code,
                        'narration'         => $vaucher->narration,
                        'cheque_no'         => !empty($vaucher->cheque_no) ? $vaucher->cheque_no : '',
                        'cheque_date'       => $vaucher->cheque_date,
                        'is_honour'         => $vaucher->is_honour,
                        'ledger_comment'    => $vaucher->ledger_comment,
                        'debit'             => $vaucher->credit,
                        'credit'            => $vaucher->debit,
                        'store_id'          => 0,
                        'is_posted'         => 1,
                        'rev_code'          => $vaucher->coa_id,
                        'sub_type'          => $vaucher->sub_type,
                        'sub_code'          => $vaucher->sub_code,
                        'is_approved'         => 1,
                        'created_by'        => $approved_by,
                        'created_at'        => $approved_at,
                        'warehouse_id'      => $vaucher->warehouse_id
                    );
                    AccTransaction::insert($revercetransationinsert);
                    // addActivityLog("approved_vaucher_reversetransation", "create", $this->db->insert_id(), "acc_transaction", 1, $revercetransationinsert);

                    // update Monthly record
                    store_transaction_summary($vaucher->rev_code, $vaucher->warehouse_id, $vaucher->voucher_date);
                }
            }
        }
        $action = ($action == 'active' ? 1 : 0);
        $updatedata = array(
            'voucher_no'    => $id,
            'is_approved'   => $action,
            'approved_by'   => $approved_by,
            'approved_at'   => $approved_at,
            'status'        => $action
        );

        return AccVoucher::where('voucher_no', $id)->update($updatedata);
    }
}

if (!function_exists('insert_purchase_journal')) {
    function insert_purchase_journal($purchase_id)
    {
        $purchase = Purchase::findOrFail($purchase_id);

        $supplier_id = $purchase->supplier_id;
        $maxid = number_generator('id', 'acc_vouchers', 'reference_no', 'PO', 'reference_no');
        $purchase_no = "PO-" . ($maxid + 1);

        $predefined = AccPredefineAccount::first();

        $coa_id     = $predefined->purchase_code;
        $narration  = "Purchase Voucher";
        $comment    = "Purchase Voucher for supplier";

        $total_amount   = $purchase->total_value;
        $warehouse_id   = $purchase->wearhouse_id;
        $advance_payment = advance_payment($warehouse_id, 4, $supplier_id);

        if ($advance_payment > 0 && $advance_payment >= $total_amount) {
            $is_credit = 0;
            $amnt_type      = 'credit';
            $advcode        = $predefined->advance;
            $coa            = AccCoa::where('pre_head_code', $advcode)->where('sub_type', 4)->first();
            $rev_id         = $coa->head_code;
            $subcode        = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier_id)->value('id');
            $amount_pay     = $total_amount;

            $purchase->paid_amount = $amount_pay;
            $purchase->payment_amount = $amount_pay;
            $purchase->due_amount = 0.00;
            $purchase->payment_status = 3;

            $insert_voucher = insert_purchase_debit_voucher($is_credit, $purchase_id, $warehouse_id, $coa_id, $amnt_type, $amount_pay, $narration, $comment, $rev_id, $subcode);
        } else if ($advance_payment != 0 && $advance_payment < $total_amount) {
            $is_credit = 0;
            $amnt_type      = 'credit';
            $advcode        = $predefined->advance;
            $coa            = AccCoa::where('pre_head_code', $advcode)->where('sub_type', 4)->first();
            $rev_id         = $coa->head_code;
            $subcode        = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier_id)->value('id');
            $amount_pay     = $advance_payment;
            $insert_voucher = insert_purchase_debit_voucher($is_credit, $purchase_id, $warehouse_id, $coa_id, $amnt_type, $amount_pay, $narration, $comment, $rev_id, $subcode);

            $is_credit = 1;
            $amnt_type      = 'credit';
            $rev_id         = $predefined->supplier_code;
            $subcode        = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier_id)->value('id');
            $amount_pay     = $total_amount - $advance_payment;
            $insert_voucher = insert_purchase_debit_voucher($is_credit, $purchase_id, $warehouse_id, $coa_id, $amnt_type, $amount_pay, $narration, $comment, $rev_id, $subcode);

            $purchase->paid_amount = $advance_payment;
            $purchase->payment_amount = $advance_payment;
            $purchase->due_amount = $amount_pay;
            $purchase->payment_status = 2;
        } else {
            $is_credit = 1;
            $amnt_type      = 'credit';
            $rev_id         = $predefined->supplier_code;
            $subcode        = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier_id)->value('id');
            $amount_pay     = $total_amount;

            $insert_voucher = insert_purchase_debit_voucher($is_credit, $purchase_id, $warehouse_id, $coa_id, $amnt_type, $amount_pay, $narration, $comment, $rev_id, $subcode);

            $purchase->paid_amount = 0;
            $purchase->payment_amount = 0;
            $purchase->due_amount = $amount_pay;
            $purchase->payment_status = 2;
        }
        $purchase->save();
        return true;

        $autoapprove_voucher = get_setting('autoapprove_voucher');
        if ($autoapprove_voucher == 1) {
            autoapprove($purchase_no);
        }
    }
}

// Insert Purchase Debit Voucher
if (!function_exists('insert_purchase_debit_voucher')) {
    function insert_purchase_debit_voucher($is_credit = null, $purchase_id = null, $warehouse_id, $dbtid = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $purchase       = Purchase::findOrFail($purchase_id);
        $supplier_id    = $purchase->supplier_id;
        $relvalue       = AccSubcode::where('sub_type_id', 4)->where('reference_no', $supplier_id)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $purchase_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 4,
                'sub_code'          => $subcode,
                'relational_type'   => 4,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);
            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $purchase_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 4,
                'sub_code'          => $subcode,
                'relational_type'   => 4,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );

            // $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'DV', 'voucher_no');            
            // $voucher_no = "DV-". ($maxid +1);
            // $debitinsert = array(
            //     'fyear'             => $fyear,
            //     'voucher_no'        => $voucher_no,
            //     'voucher_type'      => 'DV',
            //     'reference_no'      => $purchase_no,
            //     'voucher_date'      => $voucher_date,
            //     'coa_id'            => $dbtid,     
            //     'narration'         => $narration,     
            //     'ledger_comment'    => $comment,   
            //     'rev_code'          => $rev_id,    
            //     'is_approved'       => 0,                      
            //     'created_by'        => $created_by,
            //     'created_at'        => $created_at,      
            //     'status'            => 0,      
            // );
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit']  = $amnt;
            $debitinsert['credit'] =  0.00;
        } else {
            $debitinsert['debit']  = 0.00;
            $debitinsert['credit'] =  $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}

if (!function_exists('number_generator')) {
    function number_generator($field, $table, $where = null, $type = null, $field2 = null)
    {
        $query = DB::table($table)->select($field, $field2);

        if ($where !== null) {
            $query->where($where, 'like', "{$type}%");
        }

        $record = $query->orderByRaw("LENGTH(reference_no) DESC")
            ->orderByRaw("reference_no DESC")->first();

        if ($record) {
            if ($field2 !== null) {
                $num = $record->{$field2};
                if (strpos($num, '-') !== false) {
                    list($txt, $intval) = explode('-', $num, 2);
                    return (int) $intval;
                } else {
                    return 0;
                }
            } else {
                return $record->{$field};
            }
        }

        return 0;
    }
}

if (!function_exists('payment_methods')) {
    function payment_methods()
    {
        $payment_types = AccCoa::where('pre_head_name', 'Cash')
            ->orWhere('pre_head_name', 'Cash at Bank')
            ->where('is_active', 1)
            ->get();

        if ($payment_types->isNotEmpty()) {
            $list[''] = 'Select Payment Method';
            foreach ($payment_types as $payment_type) {
                $list[$payment_type->head_code] = $payment_type->head_name;
            }
            return $list;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_supplier')) {
    function get_supplier()
    {
        $suppliers = Supplier::where('status', 1)->get();
        return $suppliers;
    }
}

if (!function_exists('supplier_payment')) {
    function supplier_payment()
    {
        return $data = AccTransaction::selectRaw('voucher_no as voucher')
            ->where('voucher_no', 'like', 'PM-%')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }
}

if (!function_exists('insert_supplier_payment')) {
    function insert_supplier_payment($request)
    {
        $voucher_no         = addslashes(trim($request->voucher_no));
        $voucher_type       = "Purchase";
        $cAID               = $request->cmbDebit;
        $dAID               = $request->txtCode;
        $Debit              = $request->txtAmount;
        $Credit             = 0;
        $VDate              = $request->dtpDate;
        $Narration          = addslashes(trim($request->txtRemarks));
        $IsPosted           = 1;
        $IsAppove           = 1;
        $sup_id             = $request->supplier_id;
        $CreateBy           = Auth::user()->id;
        $createdate         = date('Y-m-d H:i:s');
        $dbtid              = $dAID;
        $Damnt              = $Debit;
        $supplier_id        = $sup_id;
        $multipayamount     = $request->pamount_by_method;
        $multipaytype       = $request->multipaytype;
        $supinfo            = Supplier::where('supplier_id', $supplier_id)->first();
        $voucher_details    = Purchase::where('id', $voucher_no)->first();
        $paid_amount        = ($voucher_details ? $voucher_details->paid_amount : 0) + ($Debit ? $Debit : 0);
        $due_amount         = ($voucher_details ? $voucher_details->due_amount : 0) - ($Debit ? $Debit : 0);
        $warehouse_id       = $voucher_details->wearhouse_id;

        $purchase_info = array(
            'paid_amount'       => $paid_amount,
            'payment_amount'    => $paid_amount,
            'due_amount'        => $due_amount,
            'payment_status'    => ($due_amount == 0) ? 3 : 2,
        );

        Purchase::where('id', $voucher_no)->update($purchase_info);

        $predefine_account  = AccPredefineAccount::first();
        $Narration          = "Purchase Due Voucher";
        $Comment            = "Purchase Due Voucher for supplier";
        $COAID              = $predefine_account->supplier_code;
        $subcode            = AccSubcode::where('reference_no', $supplier_id)->where('sub_type_id', 4)->value('id');

        if ($multipaytype && $multipayamount) {
            $amnt_type = 'debit';
            for ($i = 0; $i < count($multipaytype); $i++) {
                $reVID = $multipaytype[$i];
                $amount_pay = $multipayamount[$i];
                $insrt_pay_amnt_vcher = insert_purchase_due($voucher_no, $warehouse_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID, $subcode);
            }
        }

        $autoapprove_voucher = get_setting('autoapprove_voucher');
        if ($autoapprove_voucher == 1) {
            $vouchers = AccVoucher::where('reference_no', $voucher_no)->where('status', 0)->get();
            foreach ($vouchers as $value) {
                $data = approved_voucher($value->voucher_no, 'active');
            }
        }

        return  $insrt_pay_amnt_vcher;
    }
}

if (!function_exists('insert_purchase_due')) {
    function insert_purchase_due($purchase_id = null, $warehouse_id = null, $dbtid = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'DV', 'voucher_no');
        $voucher_no = "DV-" . ($maxid + 1);
        $debitinsert = array(
            'fyear'             => $fyear,
            'voucher_no'        => $voucher_no,
            'voucher_type'      => 'DV',
            'reference_no'      => $purchase_id,
            'voucher_date'      => $voucher_date,
            'coa_id'            => $dbtid,
            'narration'         => $narration,
            'ledger_comment'    => $comment,
            'rev_code'          => $rev_id,
            'sub_type'          => 4,
            'sub_code'          => $subcode,
            'is_approved'       => 0,
            'created_by'        => $created_by,
            'created_at'        => $created_at,
            'status'            => 0,
            'warehouse_id'      => $warehouse_id,
        );

        if ($amnt_type == 'debit') {
            $debitinsert['debit']  = $amnt;
            $debitinsert['credit'] =  0.00;
        } else {
            $debitinsert['debit']  = 0.00;
            $debitinsert['credit'] =  $amnt;
        }
        AccVoucher::insert($debitinsert);
        return $created_at;
    }
}

if (!function_exists('get_supplier_info')) {
    function get_supplier_info($supplier_id)
    {
        return Supplier::where('supplier_id', $supplier_id)
            ->get()
            ->toArray();
    }
}

if (!function_exists('get_supplier_payment_info')) {
    function get_supplier_payment_info($voucher_no, $purchase_date)
    {
        return AccVoucher::select('acc_vouchers.*', DB::raw('SUM(acc_vouchers.debit) as sumDebit'), 'purchases.chalan_no')
            ->leftJoin('purchases', 'acc_vouchers.reference_no', '=', 'purchases.id')
            ->where('acc_vouchers.reference_no', $voucher_no)
            ->where('acc_vouchers.created_at', $purchase_date)
            ->get()
            ->toArray();
    }
}

if (!function_exists('get_company_info')) {
    function get_company_info()
    {
        $company_info = [
            'company_name' => 'Bazarnao Ltd',
            'address' => 'Dhaka, Bangladesh',
            'mobile' => '01759724410',
            'email' => 'info@bazarnao.com',
            'website' => 'https://bazarnao.com',
        ];

        return $company_info;
    }
}

if (!function_exists('get_customer')) {
    function get_customer($customer_id = null)
    {
        $customers = Customer::where('user_id', '!=', null);
        if ($customer_id != null) {
            $customers->where('user_id', $customer_id);
        }
        $customers = $customers->get();
        return $customers;
    }
}

if (!function_exists('customer_receive')) {
    function customer_receive()
    {
        return  $data = AccTransaction::selectRaw('voucher_no as voucher')
            ->where('voucher_no', 'like', 'CR-%')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }
}

if (!function_exists('get_customer_info')) {
    function get_customer_info($customer_id)
    {
        return User::where('id', $customer_id)
            ->get()
            ->toArray();
    }
}

if (!function_exists('get_customer_receive_info')) {
    function get_customer_receive_info($voucher_no, $receive_date)
    {
        return AccVoucher::select('acc_vouchers.*', DB::raw('SUM(acc_vouchers.credit) as sumcredit'), 'orders.code')
            ->leftJoin('orders', 'acc_vouchers.reference_no', '=', 'orders.id')
            ->where('acc_vouchers.reference_no', $voucher_no)
            ->where('acc_vouchers.created_at', $receive_date)
            ->get()
            ->toArray();
    }
}

if (!function_exists('insert_customer_receive')) {
    function insert_customer_receive($request)
    {
        $voucher_no     = addslashes(trim($request->voucher_no));
        $Vtype          = "INV";
        $cAID           = $request->cmbDebit;
        $dAID           = $request->txtCode;
        $Debit          = 0;
        $Credit         = $request->txtAmount;
        $VDate          = $request->dtpDate;
        $customer_id    = $request->customer_id;
        $Narration      = addslashes(trim($request->txtRemarks));
        $IsPosted       = 1;
        $IsAppove       = 1;
        $CreateBy       = Auth::user()->id;
        $createdate     = date('Y-m-d H:i:s');
        $dbtid          = $dAID;
        $Credit         = $Credit;
        $multipayamount = $request->pamount_by_method;
        $multipaytype   = $request->multipaytype;
        $customerid     = $customer_id;
        $customerinfo   = User::where('id', $customer_id)->first();
        $voucher_details = Order::where('id', $voucher_no)->first();
        $paid_amount    = ($voucher_details ? $voucher_details->paid_amount : 0) + ($Credit ? $Credit : 0);
        $due_amount     = ($voucher_details ? $voucher_details->due_amount : 0) - ($Credit ? $Credit : 0);
        $warehouse_id   = $voucher_details->warehouse;
        $payment_status = ($due_amount == 0) ? 'paid' : 'partial';

        $invoice_info   = array(
            'paid_amount'   => $paid_amount,
            'due_amount'    => $due_amount,
            'payment_status' => ($due_amount == 0) ? 'paid' : 'partial',
        );

        $predefine_account  = AccPredefineAccount::first();
        $Narration          = "Sales Due Voucher";
        $Comment            = "Sales Due Voucher for Customer";
        $COAID              = $predefine_account->customer_code;
        $subcode            = AccSubcode::where('reference_no', $customerid)->where('sub_type_id', 3)->value('id');

        if ($multipaytype && $multipayamount) {
            $amnt_type = 'credit';
            for ($i = 0; $i < count($multipaytype); $i++) {
                $reVID = $multipaytype[$i];
                $amount_pay = $multipayamount[$i];

                $insrt_pay_amnt_vcher = insert_sales_due($voucher_no, $warehouse_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID, $subcode);
            }
        }
        Order::where('id', $voucher_no)->update($invoice_info);
        OrderDetail::where('order_id', $voucher_no)->update($invoice_info);

        $autoapprove_voucher = get_setting('autoapprove_voucher');
        if ($autoapprove_voucher == 1) {
            $vouchers = AccVoucher::where('reference_no', $voucher_no)->where('status', 0)->get();
            foreach ($vouchers as $value) {
                $data = approved_voucher($value->voucher_no, 'active');
            }
        }

        // Update Payment Status Order & Other
        update_payment_status($voucher_no, $VDate, $Credit, $payment_status);

        return $insrt_pay_amnt_vcher;
    }
}

if (!function_exists('insert_sales_due')) {
    function insert_sales_due($invoice_id = null, $warehouse_id = null, $dbtid = null, $amnt_type = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CV', 'voucher_no');
        $voucher_no = "CV-" . ($maxid + 1);
        $debitinsert = array(
            'fyear'         => $fyear,
            'voucher_no'    => $voucher_no,
            'voucher_type'  => 'CV',
            'reference_no'  => $invoice_id,
            'voucher_date'  => $voucher_date,
            'coa_id'        => $dbtid,
            'narration'     => $Narration,
            'ledger_comment' => $Comment,
            'rev_code'      => $reVID,
            'sub_type'      => 3,
            'sub_code'      => $subcode,
            'is_approved'   => 0,
            'created_by'    => $created_by,
            'created_at'    => $created_at,
            'status'        => 0,
            'warehouse_id'  => $warehouse_id,
        );

        if ($amnt_type == 'debit') {
            $debitinsert['debit']  = $amnt;
            $debitinsert['credit'] =  0.00;
        } else {
            $debitinsert['debit']  = 0.00;
            $debitinsert['credit'] =  $amnt;
        }
        AccVoucher::insert($debitinsert);
        return $created_at;
    }
}

if (!function_exists('update_payment_status')) {
    function update_payment_status($order_id, $payment_date, $payment_amount, $payment_status)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (!empty($order->payment_details)) {
            $orderpayment = json_decode($order->payment_details);
            if (!empty($orderpayment)) {
                $total = $orderpayment->amount + $payment_amount;
                if ($total <= $order->grand_total && $payment_amount != 0) {
                    $paid = $orderpayment->amount + $payment_amount;
                } else {
                    flash(translate('Already Paid Full Amount'))->error();
                    return 0;
                }
            }
        } else {
            $paid = $payment_amount;
        }

        if ($paid >= $order->grand_total) {
            $status = 'paid';
        } else {
            $status = $payment_status;
        }

        if ($order->order_from == 'POS') {
            $posledgerorder = Pos_ledger::where('order_id', $order_id)->first();
            $posledgerorder->decrement('due', $payment_amount);
            $posledgerorder->save();
            $warehousearray = getWearhouseBuUserId(Auth::user()->id);
            DB::table('pos_ledger')->insert([
                'order_id' => $order->id,
                'warehouse_id' => $warehousearray[0],
                'type' => 'Order',
                'order_amount' => 0,
                'due' => 0,
                'debit' => $payment_amount,
                'date' => date('Y-m-d', strtotime($order->created_at)),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
        }

        foreach ($order->orderDetails as $key => $orderDetail) {
            $orderDetail->payment_status = $status;
            $orderDetail->save();
        }

        $status = $status;
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status == 'unpaid') {
                $status = 'unpaid';
            }
        }

        $order->payment_status = $status;
        $oVal = (object)[
            'amount' => $paid,
            'status' => 'VALID',
            'error' => null
        ];
        $order->payment_details = json_encode($oVal);
        $order->save();

        if ($order->payment_status == 'paid' || $order->payment_status == 'partial') {

            $array['view'] = 'emails.payment';
            $array['subject'] = translate('Your order payment has been paid') . ' - ' . $order->code;
            $array['from'] = 'sales@bazarnao.com';
            $array['order'] = $order;
            $shipping_address = json_decode($order->shipping_address);
            if (!empty($shipping_address->email)) {
                //Mail::to($shipping_address->email)->queue(new InvoiceEmailManager($array));
            }

            if (!empty($order->user_id)) {
                $customer_id = $order->user_id;
            } else {
                $customer_id = $order->guest_id;
            }
            $cust_ledger = array();
            $cust_ledger['customer_id'] = $customer_id;
            $cust_ledger['order_id'] = $order_id;
            $cust_ledger['descriptions'] = 'Cash Payment';
            $cust_ledger['type'] = 'Payment';
            $cust_ledger['debit'] = 0;
            $cust_ledger['credit'] = $payment_amount;
            $cust_ledger['date'] = date('Y-m-d', strtotime($payment_date));
            save_customer_ledger($cust_ledger);
        }

        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
                if ($order->payment_type == 'cash_on_delivery') {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay - ($orderDetail->price * $commission_percentage) / 100;
                                $seller->save();
                            }
                        }
                    }
                } elseif ($order->manual_payment) {
                    if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                        $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    } else {
                        foreach ($order->orderDetails as $key => $orderDetail) {
                            $orderDetail->payment_status = 'paid';
                            $orderDetail->save();
                            if ($orderDetail->product->user->user_type == 'seller') {
                                $commission_percentage = $orderDetail->product->category->commision_rate;
                                $seller = $orderDetail->product->user->seller;
                                $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                                $seller->save();
                            }
                        }
                    }
                }
            }

            if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliatePoints($order);
            }

            if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
                if ($order->user != null) {
                    $clubpointController = new ClubPointController;
                    $clubpointController->processClubPoints($order);
                }
            }

            $order->commission_calculated = 1;
            $order->save();
        }

        if (\App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated && \App\Models\OtpConfiguration::where('type', 'otp_for_paid_status')->first()->value) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_payment_status($order, $payment_amount);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }
}

if (!function_exists('advance_payment')) {
    function advance_payment($warehouse_id, $subtype, $reference_no, $dtpFromDate = null, $dtpToDate = null)
    {
        $advcode = AccPredefineAccount::first()->value('advance');
        $coa = AccCoa::where('pre_head_code', $advcode)->where('sub_type', $subtype)->first();
        $subcode = AccSubcode::where('sub_type_id', $subtype)->where('reference_no', $reference_no)->first();

        $dtpFromDate = $dtpFromDate ? date('Y-m-d', strtotime($dtpFromDate)) : date('Y-m-01');
        $dtpToDate = $dtpToDate ? date('Y-m-d', strtotime($dtpToDate)) : date('Y-m-d');

        $ledger = general_led_report_head_name($coa->head_code);
        $prebalance = get_opening_balance_subtype($coa->head_code, $warehouse_id, $dtpFromDate, $dtpToDate, $subtype, $subcode->id);
        $HeadName2 = get_general_ledger_report($coa->head_code, $warehouse_id, $dtpFromDate, $dtpToDate, 1, 0, $subtype, $subcode->id);

        $TotalCredit = 0;
        $TotalDebit = 0;
        $CurBalance = $prebalance;
        foreach ($HeadName2 as $data) {
            $TotalDebit += $data->debit;
            $TotalCredit += $data->credit;
            if ($ledger->head_type == 'A' || $ledger->head_type == 'E') {
                if ($data->debit > 0) {
                    $CurBalance += $data->debit;
                }
                if ($data->credit > 0) {
                    $CurBalance -= $data->credit;
                }
            } else {
                if ($data->debit > 0) {
                    $CurBalance -= $data->debit;
                }
                if ($data->credit > 0) {
                    $CurBalance += $data->credit;
                }
            }
        }

        if ($CurBalance > 0) {
            return $CurBalance;
        } else {
            return 0;
        }
    }
}

if (!function_exists('insert_transfer_journal')) {
    function insert_transfer_journal($transfer_id)
    {
        $transfer = Transfer::findOrFail($transfer_id);

        $from_warehouse_id = $transfer->from_wearhouse_id;
        $to_warehouse_id = $transfer->to_wearhouse_id;
        
        $maxid = number_generator('id', 'acc_vouchers', 'reference_no', 'TR', 'reference_no');
        $transfer_no = "TR-" . ($maxid + 1);

        $predefined = AccPredefineAccount::first();

        $coa_id     = $predefined->purchase_code;
        $narration  = "Transfer Voucher";
        $comment    = "Transfer Voucher for warehouse";

        $amount_pay   = $transfer->qty * $transfer->unit_price;

        if ($amount_pay) {
            $is_credit = 1;
            $amnt_type      = 'credit';
            $payable_rev_id = $predefined->payable_warehouse_code;
            $subcode        = AccSubcode::where('sub_type_id', 6)->where('reference_no', $from_warehouse_id)->value('id');
            insert_transfer_debit_voucher($is_credit, $transfer_id, $to_warehouse_id, $coa_id, $amnt_type, $amount_pay, $narration, $comment, $payable_rev_id, $subcode);
              
            $is_credit      = 1;
            $amnt_type      = 'debit';
            $receivable_rev_id = $predefined->receivable_warehouse_code;
            $subcode        = AccSubcode::where('sub_type_id', 6)->where('reference_no', $to_warehouse_id)->value('id');
            insert_transfer_credit_voucher($is_credit, $transfer_id, $from_warehouse_id, $receivable_rev_id, $amnt_type, $amount_pay, $narration, $comment, $coa_id, $subcode);

            $transfer->paid_amount = 0;
            $transfer->due_amount = $amount_pay;
            $transfer->payment_status = 0; // Unpaid
        }
        $transfer->save();

        $autoapprove_voucher = get_setting('autoapprove_voucher');
        if ($autoapprove_voucher == 1) {
            autoapprove($transfer_no);
        }
        return true;
    }
}

if (!function_exists('insert_transfer_debit_voucher')) {
    function insert_transfer_debit_voucher($is_credit = null, $transfer_id = null, $warehouse_id, $dbtid = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $transfer          = Transfer::findOrFail($transfer_id);
        $from_warehouse_id = $transfer->from_wearhouse_id;
        $relvalue          = AccSubcode::where('reference_no', $from_warehouse_id)->where('sub_type_id', 6)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 6,
                'sub_code'          => $subcode,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);
            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 6,
                'sub_code'          => $subcode,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit']  = $amnt;
            $debitinsert['credit'] =  0.00;
        } else {
            $debitinsert['debit']  = 0.00;
            $debitinsert['credit'] =  $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}

if (!function_exists('insert_transfer_credit_voucher')) {
    function insert_transfer_credit_voucher($is_credit = null, $transfer_id = null, $warehouse_id,  $coa_id = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $transfer          = Transfer::findOrFail($transfer_id);
        $to_warehouse_id = $transfer->to_wearhouse_id;
        $relvalue          = AccSubcode::where('reference_no', $to_warehouse_id)->where('sub_type_id', 6)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $coa_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $rev_id,
                'sub_type'          => 6,
                'sub_code'          => $subcode,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CV', 'voucher_no');
            $voucher_no = "CV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'CV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $coa_id,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit'] = $amnt;
            $debitinsert['credit'] = 0.00;
        } else {
            $debitinsert['debit'] = 0.00;
            $debitinsert['credit'] = $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}


if (!function_exists('insert_cash_transfer_journal')) {
    function insert_cash_transfer_journal($transfer_id)
    {
        $transfer = CashTransfer::findOrFail($transfer_id);

        $from_warehouse_id = $transfer->from_warehouse_id;
        $to_warehouse_id = $transfer->to_warehouse_id;
        
        $maxid = number_generator('id', 'acc_vouchers', 'reference_no', 'CTR', 'reference_no');
        $transfer_no = "CTR-" . ($maxid + 1);

        $predefined = AccPredefineAccount::first();
        
        // Use user-selected COA IDs from transfer table
        // from_coa_id = Account Head for From Warehouse (always cash)
        // to_coa_id = Account Head for To Warehouse (cash or bank based on voucher type)
        $from_coa_id = $transfer->from_coa_id; // From Warehouse account (will be credited)
        $to_coa_id = $transfer->to_coa_id;     // To Warehouse account (will be debited)
        
        $narration = $transfer->narration ?? "Cash Transfer Voucher";
        $comment = $transfer->ledger_comment ?? "Cash Transfer Voucher for warehouse";

        $amount_pay = $transfer->amount;

        if ($amount_pay) {
            $is_credit = 1;
            $amnt_type = 'credit';
            $payable_rev_id = $predefined->payable_warehouse_code;
            $subcode = AccSubcode::where('sub_type_id', 6)->where('reference_no', $from_warehouse_id)->value('id');
            
            // Credit entry using from_coa_id (money going out from From Warehouse)
            insert_cash_transfer_debit_voucher($is_credit, $transfer_id, $to_warehouse_id, $from_coa_id, $amnt_type, $amount_pay, $narration, $comment, $payable_rev_id, $subcode);
              
            $is_credit = 1;
            $amnt_type = 'debit';
            $receivable_rev_id = $predefined->receivable_warehouse_code;
            $subcode = AccSubcode::where('sub_type_id', 6)->where('reference_no', $to_warehouse_id)->value('id');
            
            // Debit entry using to_coa_id (money coming in to To Warehouse)
            insert_cash_transfer_credit_voucher($is_credit, $transfer_id, $from_warehouse_id, $to_coa_id, $amnt_type, $amount_pay, $narration, $comment, $receivable_rev_id, $subcode);
        }

        $autoapprove_voucher = get_setting('autoapprove_voucher');
        if ($autoapprove_voucher == 1) {
            autoapprove($transfer_no);
        }
        return true;
    }
}

if (!function_exists('insert_cash_transfer_debit_voucher')) {
    function insert_cash_transfer_debit_voucher($is_credit = null, $transfer_id = null, $warehouse_id, $dbtid = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $transfer          = CashTransfer::findOrFail($transfer_id);
        $from_warehouse_id = $transfer->from_wearhouse_id;
        $relvalue          = AccSubcode::where('reference_no', $from_warehouse_id)->where('sub_type_id', 6)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 6,
                'sub_code'          => $subcode,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);
            $debitinsert = array(
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $dbtid,
                'sub_type'          => 4,
                'sub_code'          => $subcode,
                'relational_type'   => 4,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            );
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit']  = $amnt;
            $debitinsert['credit'] =  0.00;
        } else {
            $debitinsert['debit']  = 0.00;
            $debitinsert['credit'] =  $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}

if (!function_exists('insert_cash_transfer_credit_voucher')) {
    function insert_cash_transfer_credit_voucher($is_credit = null, $transfer_id = null, $warehouse_id,  $coa_id = null, $amnt_type = null, $amnt = null, $narration = null, $comment = null, $rev_id = null, $subcode = null)
    {
        $fyear = get_financial_year();
        $voucher_date = date('Y-m-d');
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $transfer          = CashTransfer::findOrFail($transfer_id);
        $to_warehouse_id = $transfer->to_wearhouse_id;
        $relvalue          = AccSubcode::where('reference_no', $to_warehouse_id)->where('sub_type_id', 6)->value('id');

        if ($is_credit == 1) {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'JV', 'voucher_no');
            $voucher_no = "JV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'JV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $coa_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $rev_id,
                'sub_type'          => 6,
                'sub_code'          => $subcode,
                'relational_type'   => 6,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        } else {
            $maxid = get_max_field_number('id', 'acc_vouchers', 'voucher_type', 'CV', 'voucher_no');
            $voucher_no = "CV-" . ($maxid + 1);

            $debitinsert = [
                'fyear'             => $fyear,
                'voucher_no'        => $voucher_no,
                'voucher_type'      => 'CV',
                'reference_no'      => $transfer_id,
                'voucher_date'      => $voucher_date,
                'coa_id'            => $rev_id,
                'narration'         => $narration,
                'ledger_comment'    => $comment,
                'rev_code'          => $coa_id,
                'relational_type'   => 3,
                'relational_value'  => $relvalue,
                'is_approved'       => 0,
                'created_by'        => $created_by,
                'created_at'        => $created_at,
                'status'            => 0,
                'warehouse_id'      => $warehouse_id,
            ];
        }

        if ($amnt_type == 'debit') {
            $debitinsert['debit'] = $amnt;
            $debitinsert['credit'] = 0.00;
        } else {
            $debitinsert['debit'] = 0.00;
            $debitinsert['credit'] = $amnt;
        }

        AccVoucher::insert($debitinsert);
        return true;
    }
}