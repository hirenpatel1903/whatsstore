<?php

namespace App\Models;

use App\Models\Mail\CommonEmailTemplate;
use App\Models\Mail\OrderMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Jenssegers\Date\Date;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use App\Models\Customer;
use Twilio\Rest\Client;

class Utility extends Model
{
    public function createSlug($table, $title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title, '-');
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($table, $slug, $id);
        // If we haven't used it before then we are all good.
        if(!$allSlugs->contains('slug', $slug))
        {
            return $slug;
        }
        // Just append numbers like a savage until we find not used.
        for($i = 1; $i <= 100; $i++)
        {
            $newSlug = $slug . '-' . $i;
            if(!$allSlugs->contains('slug', $newSlug))
            {
                return $newSlug;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($table, $slug, $id = 0)
    {
        return DB::table($table)->select()->where('slug', 'like', $slug . '%')->where('id', '<>', $id)->get();
    }



          public static function getDateFormated($date, $time = false)
    {
        if(!empty($date) && $date != '0000-00-00')
        {
            if($time == true)
            {
                return date("d M Y H:i A", strtotime($date));
            }
            else
            {
                return date("d M Y", strtotime($date));
            }
        }
        else
        {
            return '';
        }
    } 
    public static function settings()
    {
        $data = DB::table('settings');

        if(\Auth::check())
        {
            if(\Auth::user()->type == 'super admin')
            {
                $userId = \Auth::user()->creatorId();
                $data   = $data->where('created_by', '=', 1);
            }
            else
            {
                $userId = \Auth::user()->current_store;
                $data   = $data->where('created_by', '=', $userId);
            }
        }
        else
        {
            $data = $data->where('created_by', '=', 1);
        }
        $data = $data->get();

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "currency_symbol_position" => "pre",
            "logo" => "logo.png",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "invoice_color" => "ffffff",
            "quote_template" => "template1",
            "quote_color" => "ffffff",
            "salesorder_template" => "template1",
            "salesorder_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "fffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "fffff",
            "quote_prefix" => "#QUO",
            "salesorder_prefix" => "#SOP",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "VAT",
            "shipping_display" => "on",
            "footer_link_1" => "Support",
            "footer_value_1" => "#",
            "footer_link_2" => "Terms",
            "footer_value_2" => "#",
            "footer_link_3" => "Privacy",
            "footer_value_3" => "#",
            "company_logo" => "logo.png",
            "company_favicon" => "",
            "title_text" => "",
            "footer_text" => "",
            "default_language" => "en",
            "display_landing_page" => "on",
            "currency_symbol" => "",
            "currency" => "",
            "gdpr_cookie" => "",
            "cookie_text" =>"",
            "signup_button" =>"on",
            "color" => "theme-3",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_dark_logo" => "logo-dark.png",
            "company_light_logo" => "logo-light.png",

        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function getAdminPaymentSetting()
    {
        $data     = DB::table('admin_payment_settings');
        $settings = [];
        if(\Auth::check())
        {
            $user_id = 1;
            $data    = $data->where('created_by', '=', $user_id);

        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function themeOne()
    {
        $arr = [];

        $arr = [
            'theme1' => [
                'style-card-body.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home.png')),
                    'color' => '3bbc9c',
                ],
                'style-card-body-green.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-1.png')),
                    'color' => '3fcc71',
                ],
                'style-card-body-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-2.png')),
                    'color' => '3498db',
                ],
                'style-card-body-purple.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-3.png')),
                    'color' => '9b59b6',
                ],
                'style-card-body-dark-grey.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-4.png')),
                    'color' => '34495e',
                ],
            ],
            'theme2' => [
                'style-black-body.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home.png')),
                    'color' => '3bbc9c',
                ],
                'style-black-body-green.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-1.png')),
                    'color' => '3fcc71',
                ],
                'style-black-body-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-2.png')),
                    'color' => '3498db',
                ],
                'style-black-body-purple.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-3.png')),
                    'color' => '9b59b6',
                ],
                'style-black-body-dark-grey.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-4.png')),
                    'color' => '34495e',
                ],
            ],
            'theme3' => [
                'style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home.png')),
                    'color' => 'f1c40f',
                ],
                'style-orange.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-1.png')),
                    'color' => 'e67e22',
                ],
                'style-red.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-2.png')),
                    'color' => 'e74c3c',
                ],
                'style-grey.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-3.png')),
                    'color' => '95a5a6',
                ],
                'style-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-4.png')),
                    'color' => '2c3e50',
                ],
            ],
            'theme4' => [
                'style-light-body-orange.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home.png')),
                    'color' => 'f39c11',
                ],
                'style-light-body-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-1.png')),
                    'color' => '3498db',
                ],
                'style-light-body-purple.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-2.png')),
                    'color' => '9b59b6',
                ],
                'style-light-body-green.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-3.png')),
                    'color' => '3bbc9c',
                ],
                'style-light-body.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-4.png')),
                    'color' => '34495e',
                ],
            ],
            'theme5' => [
                'style-dark-body.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home.png')),
                    'color' => 'ee786c',
                ],
                'style-dark-body-green.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-1.png')),
                    'color' => '3fcc71',
                ],
                'style-dark-body-yellow.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-2.png')),
                    'color' => 'f1c40f',
                ],
                'style-dark-body-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-3.png')),
                    'color' => '2980b9',
                ],
                'style-dark-body-grey.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-4.png')),
                    'color' => '95a5a6',
                ],
            ],
            'theme6' => [
                'style-grey-body.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme6/Home.png')),
                    'color' => '8693ae',
                ],
                'style-grey-body-blue.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme6/Home-1.png')),
                    'color' => '3498db',
                ],
                'style-grey-body-orange.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme6/Home-2.png')),
                    'color' => 'e67e22',
                ],
                'style-grey-body-green.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme6/Home-3.png')),
                    'color' => '3fcc71',
                ],
                'style-grey-body-yellow.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme6/Home-4.png')),
                    'color' => 'f1c40f',
                ],
            ],
        ];

        return $arr;
    }

    public static function priceFormat($price)
    {
        $settings = Utility::settings();
        if(\Auth::check() && \Auth::User()->type == 'Owner')
        {
            $user     = Auth::user()->current_store;
            $settings = Store::where('id', $user)->first();

            if($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "with")
            {
                return $settings['currency'] . ' ' . number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2);
            }
            elseif($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "without")
            {
                return $settings['currency'] . number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2);
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "with")
            {
                return number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2) . ' ' . $settings['currency'];
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "without")
            {
                return number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2) . $settings['currency'];
            }
        }
        else
        {
            $slug = session()->get('slug');
            if(!empty($slug))
            {
                $store = Store::where('slug', $slug)->first();

                if($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "with")
                {
                    return $store['currency'] . ' ' . number_format($price, isset($store->decimal_number)?$store->decimal_number:2);
                }
                elseif($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "without")
                {
                    return $store['currency'] . number_format($price, isset($store->decimal_number)?$store->decimal_number:2);
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "with")
                {
                    return number_format($price, isset($store->decimal_number)?$store->decimal_number:2) . ' ' . $store['currency'];
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "without")
                {
                    return number_format($price, isset($store->decimal_number)?$store->decimal_number:2) . $store['currency'];
                }
            }

            //            return (($settings['currency_symbol_position'] == "pre") ? $settings['currency_symbol'] : '') . number_format($price, 2) . (($settings['currency_symbol_position'] == "post") ? $settings['currency_symbol'] : '');
            return (($settings['currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
        }
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_date_format'], strtotime($time));
    }

    public static function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = ProductTax::find($tax);
        }

        return $taxes;
    }

    public static function taxRate($taxRate, $price, $quantity)
    {

        return ($taxRate / 100) * ($price * $quantity);
    }

    public static function totalTaxRate($taxes)
    {

        $taxArr  = explode(',', $taxes);
        $taxRate = 0;

        foreach($taxArr as $tax)
        {

            $tax     = ProductTax::find($tax);
            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
        }

        return $taxRate;
    }

    public static function userBalance($users, $id, $amount, $type)
    {
        if($users == 'customer')
        {
            $user = Customer::find($id);
        }
        else
        {
            $user = Vender::find($id);
        }

        if(!empty($user))
        {
            if($type == 'credit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance + $amount;
                $user->save();
            }
            elseif($type == 'debit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance - $amount;
                $user->save();
            }
        }
    }

    public static function bankAccountBalance($id, $amount, $type)
    {
        $bankAccount = BankAccount::find($id);
        if($bankAccount)
        {
            if($type == 'credit')
            {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $amount;
                $bankAccount->save();
            }
            elseif($type == 'debit')
            {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $amount;
                $bankAccount->save();
            }
        }

    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }
        if(!is_dir($dir))
        {
            return unlink($dir);
        }
        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }
            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }
        }

        return rmdir($dir);
    }

    public static function getSuperAdminValByName($key)
    {
        $data = DB::table('settings');
        $data = $data->where('name', '=', $key);
        $data = $data->first();
        if(!empty($data))
        {
            $record = $data->value;
        }
        else
        {
            $record = '';
        }

        return $record;
    }

  

    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{store_name}',
            '{order_no}',
            '{customer_name}',
            '{phone}',
            '{billing_address}',
            '{shipping_address}',
            '{special_instruct}',
            '{item_variable}',
            '{qty_total}',
            '{sub_total}',
            '{discount_amount}',
            '{shipping_amount}',
            '{total_tax}',
            '{final_total}',
            '{sku}',
            '{quantity}',
            '{product_name}',
            '{variant_name}',
            '{item_tax}',
            '{item_total}',
        ];
        $arrValue    = [
            'store_name' => '',
            'order_no' => '',
            'customer_name' => '',
            'phone' => '',
            'billing_address' => '',
            'shipping_address' => '',
            'special_instruct' => '',
            'item_variable' => '',
            'qty_total' => '',
            'sub_total' => '',
            'discount_amount' => '',
            'shipping_amount' => '',
            'total_tax' => '',
            'final_total' => '',
            'sku' => '',
            'quantity' => '',
            'product_name' => '',
            'variant_name' => '',
            'item_tax' => '',
            'item_total' => '',
        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name'] = env('APP_NAME');
        $arrValue['app_url']  = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Email Template Modules Function START

    public static function userDefaultData()
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();
        foreach($allEmail as $email)
        {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => 1,
                    'is_active' => 1,
                ]
            );
        }
    }

    // Common Function That used to send mail with check all cases
    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj, $store, $order)
    {
        // find template is exist or not in our record
        $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

        if(isset($template) && !empty($template))
        {
          
            // check template is active or not by company
            $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->first();
          
            if($is_active->is_active == 1)
            {
              
                // get email content language base
                $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $store->lang)->first();

                $content->from = $template->from;
                if(!empty($content->content))
                {
                    $content->content = self::replaceVariables($content->content, $obj, $store, $order);

                    // send email
                      try
                      {
                        
                      
                        config(
                            [
                                'mail.driver' => $store->mail_driver,
                                'mail.host' => $store->mail_host,
                                'mail.port' => $store->mail_port,
                                'mail.encryption' => $store->mail_encryption,
                                'mail.username' => $store->mail_username,
                                'mail.password' => $store->mail_password,
                                'mail.from.address' => $store->mail_from_address,
                                'mail.from.name' => $store->mail_from_name,
                            ]
                        );
                        if($mailTo==$store->email)
                        {

                            Mail::to(
                            [
                                $store->email,
                            ]
                        )->send(new CommonEmailTemplate($content, $store));
                        }
                        else
                        {
                            Mail::to(
                            [
                                $mailTo,
                            ]
                        )->send(new CommonEmailTemplate($content, $store));
                        }   

                        

                     }


                    catch(\Exception $e)
                     {
                        $error = __('E-Mail has been not sent due to SMTP configuration');
                     }

                    if(isset($error))
                     {
                      $arReturn = [
                             'is_success' => false,
                            'error' => $error,
                         ];
                     }
                     else
                    {
                        $arReturn = [
                            'is_success' => true,
                            'error' => false,
                        ];
                     }
                }
                else
                {
                    $arReturn = [
                        'is_success' => false,
                        'error' => __('Mail not send, email is empty'),
                    ];
                }

                return $arReturn;
            }
            else
            {
                return [
                    'is_success' => true,
                    'error' => false,
                ];
            }
        }
        else
        {
            return [
                'is_success' => false,
                'error' => __('Mail not send, email not found'),
            ];
        }
        //        }
    }

    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    /*public static function replaceVariables($content, $obj, $store, $order)
    {
        $arrVariable = [
            '{app_name}',
            '{order_name}',
            '{order_status}',
            '{app_url}',
            '{order_url}',
            '{owner_name}',
            '{order_id}',
            '{order_date}',
        ];
        $arrValue    = [
            'app_name' => '-',
            'order_name' => '-',
            'order_status' => '-',
            'app_url' => '-',
            'order_url' => '-',
            'owner_name'=>'-',
            'order_id'=>'-',
            'order_date'=>'-',
        ];
        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name']  = $store->name;
        $arrValue['app_url']   = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';
        $arrValue['order_url'] = '<a href="' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '" target="_blank">' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '</a>';



         $ownername=User::where('email',$store->email)->first();
        $id    = Crypt::decrypt($order);
        $order=Order::where('id',$id)->first();
        $arrValue['owner_name']  = $ownername->name;
        $arrValue['order_id']      =$order->order_id;
        $arrValue['order_date']    =self::dateFormat($order->created_at);

        return str_replace($arrVariable, array_values($arrValue), $content);
    }*/
    public static function replaceVariables($content, $obj, $store, $order)
    {
        $arrVariable = [
            '{app_name}',
            '{order_name}',
            '{order_status}',
            '{app_url}',
            '{order_url}',
            '{order_id}',
            '{owner_name}',
            '{order_date}',
        ];
        $arrValue    = [
            'app_name' => '-',
            'order_name' => '-',
            'order_status' => '-',
            'app_url' => '-',
            'order_url' => '-',
            'order_id' =>'-',
            'owner_name' => '-',
            'order_date' => '-',
        ];
        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }
        $arrValue['app_name']  = $store->name;
        $arrValue['app_url']   = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';
        $arrValue['order_url'] = '<a href="' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '" target="_blank">' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '</a>';
        $user=User::where('id',$store->created_by)->first();
        $ownername=User::where('email',$user->email)->first();
        $id    = Crypt::decrypt($order);
        $order=Order::where('id',$id)->first();
        $arrValue['owner_name']  = $ownername->name;
        $arrValue['order_id']      =$order->order_id;
        $arrValue['order_date']    =self::dateFormat($order->created_at);
        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Make Entry in email_tempalte_lang table when create new language
    public static function makeEmailLang($lang)
    {
        $template = EmailTemplate::all();
        foreach($template as $t)
        {
            $default_lang                 = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
          
            $emailTemplateLang            = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang      = $lang;
            $emailTemplateLang->subject   = $default_lang->subject;
            $emailTemplateLang->content   = $default_lang->content;
            $emailTemplateLang->save();
        }
    }

    // For Email template Module
    public static function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'Order Created',
            'Status Change',
            "Order Created For Owner",
        ];

        foreach($emailTemplate as $eTemp)
        {
            EmailTemplate::create(
                [
                    'name' => $eTemp,
                    'from' => env('APP_NAME'),
                    'created_by' => 1,
                ]
            );
        }

        $defaultTemplate = [
            'Order Created' => [
                'subject' => 'Order Complete',
                'lang' => [
                    'ar' => '<p>مرحبا ،</p><p>مرحبا بك في {app_name}.</p><p>مرحبا ، {order_name} ، شكرا للتسوق</p><p>قمنا باستلام طلب الشراء الخاص بك ، سيتم الاتصال بك قريبا !</p><p>شكرا ،</p><p>{app_name}</p><p>{order_url}</p>',
                    'da' => '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Hej, {order_name}, tak for at Shopping</p><p>Vi har modtaget din købsanmodning.</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>',
                    'de' => '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Hi, {order_name}, Vielen Dank für Shopping</p><p>Wir haben Ihre Kaufanforderung erhalten, wir werden in Kürze in Kontakt sein!</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We received your purchase request, we\'ll be in touch shortly!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'es' => '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>Recibimos su solicitud de compra, ¡estaremos en contacto en breve!</p><p>Gracias,</p><p>{app_name}</p><p>{order_url}</p>',
                    'fr' => '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We reçus your purchase request, we \'ll be in touch incess!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'it' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Ciao, {order_name}, Grazie per Shopping</p><p>Abbiamo ricevuto la tua richiesta di acquisto, noi \ saremo in contatto a breve!</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ja' => '<p>こんにちは &nbsp;</p><p>{app_name}へようこそ。</p></p><p><p>こんにちは、 {order_name}、お客様の購買要求書をお受け取りいただき、すぐにご連絡いたします。</p><p>ありがとうございます。</p><p>{app_name}</p><p>{order_url}</p>',
                    'nl' => '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Hallo, {order_name}, Dank u voor Winkelen</p><p>We hebben uw aankoopaanvraag ontvangen, we zijn binnenkort in contact!</p><p>Bedankt,</p><p>{ app_name }</p><p>{order_url}</p>',
                    'pl' => '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Hi, {order_name}, Dziękujemy za zakupy</p><p>Otrzymamy Twój wniosek o zakup, wkrótce będziemy w kontakcie!</p><p>Dzięki,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ru' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Hi, {order_name}, Спасибо за Шоппинг</p><p>Мы получили ваш запрос на покупку, мы \ скоро свяжемся!</p><p>Спасибо,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pt' => '<p>Olá,&nbsp;</p><p><span style="font-size: 1rem;">Bem-vindo a {app_name}.</span></p><p><span style="font-size: 1rem;">Oi, {order_name}, Obrigado por Shopping</span></p><p><span style="font-size: 1rem;">Recebemos o seu pedido de compra, nós estaremos em contato em breve!</span><br></p><p><span style="font-size: 1rem;">Obrigado,</span></p><p><span style="font-size: 1rem;">{app_name}</span></p><p><span style="font-size: 1rem;">{order_url}</span><br></p>',
                ],
            ],
            'Status Change' => [
                'subject' => 'Order Status',
                'lang' => [
                    'ar' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'da' => '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Din ordre er {order_status}!</p><p>Hej {order_navn}, Tak for at Shopping</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>',
                    'de' => '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Ihre Bestellung lautet {order_status}!</p><p>Hi {order_name}, Danke für Shopping</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'es' => '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'fr' => '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Votre commande est {order_status} !</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'it' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ja' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'nl' => '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Uw bestelling is {order_status}!</p><p>Hi {order_name}, Dank u voor Winkelen</p><p>Bedankt,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pl' => '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Twoje zamówienie to {order_status}!</p><p>Hi {order_name}, Dziękujemy za zakupy</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ru' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pt' => '<p>Olá,&nbsp;</p><p><span style="font-size: 1rem;">Bem-vindo a {app_name}.</span></p><p><span style="font-size: 1rem;">Sua Ordem é {order_status}!</span><br></p><p><span style="font-size: 1rem;">Oi {order_name}, Obrigado por Shopping</span><br></p><p><span style="font-size: 1rem;">Obrigado,</span><br></p><p><span style="font-size: 1rem;">{app_name}</span><br></p><p><span style="font-size: 1rem;">{order_url}</span><br></p>',
                ],
            ],


             'Order Created For Owner' => [
                'subject' => 'Order Complete',
                 'lang' => [
                    'ar' => '<p>&lt;p&gt;مرحبا ،&lt;/p&gt;&lt;p&gt;عزيزي { wowner_name }.&lt;/p&gt;&lt;p&gt;هذا هو التأكيد لأمر التأكيد { order_id } في&lt;span style="font-size: 1rem;"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;شكرا&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>',
                    'da' => '<p>&lt;p&gt;Velkommen,&lt;/p&gt;&lt;p&gt;Kære { owner_name }.&lt;/p&gt;&lt;p&gt;Dette er bekræftelsen af bekræftelsen af bekræftelseskommandoen { order_id } i&lt;span style="font-size: 1rem;"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Tak,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>',
                    'de' => '<p>&lt;p&gt;Willkommen,&lt;/p&gt;&lt;p&gt;Liebe {owner_name}.&lt;/p&gt;&lt;p&gt;Dies ist die Bestätigung des Bestätigungsbefehls {order_id} in&lt;span style="font-size: 1rem;"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Danke,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Dear {owner_name}.</p><p>This is Confirmation Order {order_id} place on&nbsp;<span style="font-size: 1rem;">{order_date}.</span></p><p>Thanks,</p><p>{order_url}</p>',
                    'es' => '<p>&lt;p&gt;Bienvenido,&lt;/p&gt;&lt;p&gt;Estimado {owner_name}.&lt;/p&gt;&lt;p&gt;Esta es la confirmación del mandato de confirmación {order_id} en&lt;span style="font-size: 1rem;"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Gracias,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>',
                    'fr' => '<p>&lt;p&gt;Bienvenue,&lt;/p&gt;&lt;p&gt;Cher { owner_name }.&lt;/p&gt;&lt;p&gt;Voici la confirmation de la commande de confirmation { order_id } dans&lt;span style="font-size: 1rem;"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Merci,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>',
                    'it' => '<p>&lt;p&gt;Benvenuti,&lt;/p&gt;&lt;p&gt;Caro {owner_name}.&lt;/p&gt;&lt;p&gt;Questa è la conferma del comando di conferma {order_id} in&lt;span style="font-size: 1rem;"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Grazie,&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>',
                    'ja' => '<p>&lt;p&gt;ようこそ&lt;/p&gt;&lt;p&gt;Dear {owner_name}。&lt;/p&gt;&lt;p&gt;これは、&lt;span style="font-size:1rem;"&gt;{order_date}.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;{order_url}&lt;/span&gt;の確認コマンド {order_id} の確認です。&lt;/p&gt;&lt;p&gt;{order_url}&lt;/p&gt;<br></p>',
                    'nl' => '<p>&lt;p&gt;Welkom,&lt;/p&gt;&lt;p&gt;Beste { owner_name }.&lt;/p&gt;&lt;p&gt;Dit is de bevestiging van de bevestigingsopdracht { order_id } in&lt;span style="font-size: 1rem;"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Bedankt,&lt;/p&gt;&lt;p&gt;{ order_url }&lt;/p&gt;<br></p>',
                    'pl' => '<p>&lt;p&gt;Witamy,&lt;/p&gt;&lt;p&gt;Szanowny Panie {owner_name }.&lt;/p&gt;&lt;p&gt;To jest potwierdzenie komendy potwierdzenia {order_id } w&lt;span style="font-size: 1rem;"&gt;{order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Thanks,&lt;/p&gt;&lt;p&gt;{order_url }&lt;/p&gt;<br></p>',
                    'ru' => '<p>&lt;p&gt;Добро пожаловать,&lt;/p&gt;&lt;p&gt;Уважаемый { owner_name }.&lt;/p&gt;&lt;p&gt;Это подтверждение команды подтверждения { order_id } в&lt;span style="font-size: 1rem;"&gt;{ order_date }.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Спасибо,&lt;/p&gt;&lt;p&gt;{ urder_url }&lt;/p&gt;<br></p>',
                    'pt' => '<p>Olá,&nbsp;</p><p><span style="font-size: 1rem;">Querido {owner_name}.</span><br></p><p><span style="font-size: 1rem;">Este é o Confirmação Order {order_id} place on {order_date}.</span><br></p><p><span style="font-size: 1rem;">Obrigado,</span><br></p><p><span style="font-size: 1rem;">{order_url}</span><br></p>',
                ],
            ],
        ];

        $email = EmailTemplate::all();

        foreach($email as $e)
        {
            foreach($defaultTemplate[$e->name]['lang'] as $lang => $content)
            {
                EmailTemplateLang::create(
                    [
                        'parent_id' => $e->id,
                        'lang' => $lang,
                        'subject' => $defaultTemplate[$e->name]['subject'],
                        'content' => $content,
                    ]
                );
            }
        }
    }

    public static function CustomerAuthCheck($slug = null)
    {
        if($slug == null)
        {
            $slug = \Request::segment(1);
        }
        $auth_customer = Auth::guard('customers')->user();
        if(!empty($auth_customer))
        // 
        {
            $store_id = Store::where('slug', $slug)->pluck('id')->first();
            $customer  = Customer::where('store_id', $store_id)->where('email', $auth_customer->email)->count();
            if($customer > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }  
    public static function success_res($msg = "", $args = array())
    {   
        
        $msg       = $msg == "" ? "success" : $msg;
        $msg_id    = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    public static function error_res($msg = "", $args = array())
    {
        
        $msg       = $msg == "" ? "error" : $msg;
        $msg_id    = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }
    public static function getPaymentSetting($store_id = null)
    {
        $data     = DB::table('store_payment_settings');
        $settings = [];
        if(\Auth::check())
        {
            $store_id = \Auth::user()->current_store;
            $data     = $data->where('store_id', '=', $store_id);

        }
        else
        {
            $data = $data->where('store_id', '=', $store_id);
        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }
    public static function send_twilio_msg($to, $msg,$store)
        {   

          
            $account_sid    = $store->twilio_sid;

            $auth_token = $store->twilio_token;

            $twilio_number =$store->twilio_from;
            
            $client = new Client($account_sid, $auth_token);

            $client->messages->create($to, [
                'from' => $twilio_number,
                'body' => $msg]) ;

        }


public static function order_create_owner($order,$owner, $store)
    {
       
       $msg=__("Hello,
Dear".' '.$owner->name.' '.",
This is Confirmation Order ".' '.$order->order_id."place on".self::dateFormat($order->created_at)."
Thanks,");


                           
        Utility::send_twilio_msg($store->notification_number,$msg,$store);

        //  dd('SMS Sent Successfully.');

    }

    public static function order_create_customer($order,$customer, $store)
    {
        $msg=__("Hello,
            Welcome to".' '.$store->name.' '.",
            Thank you for your purchase from".' '.$store->name.".
            Order Number:".' '.$order->order_id.".
            Order Date:".' '.self::dateFormat($order->created_at));

        Utility::send_twilio_msg($customer->phone_number,$msg,$store);


    }

    public static function status_change_customer($order,$customer, $store)
    {
       $msg=__("Hello,
            Welcome to".' '.$store->name.' '.",
            Your Order is".' '.$order->status.".
            Hi".' '.$order->name.", Thank you for Shopping.
            Thanks,
            ".$store->name);

    Utility::send_twilio_msg($customer->phone_number,$msg,$store);

         
    }

    public static function colorset(){
        if(\Auth::user())
        {
            $user = \Auth::user();
            $setting = DB::table('settings')->where('created_by',$user->creatorId())->pluck('value','name')->toArray();

        }   
        else{
            $setting = DB::table('settings')->pluck('value','name')->toArray();
        }
        return $setting;
            // dd($setting);
    }

    public static function get_superadmin_logo(){
        $is_dark_mode = self::getValByName('cust_darklayout');
        if($is_dark_mode == 'on'){
            return 'logo-light.png';
        }else{
            return 'logo-dark.png';
        }
    }
      public static function get_company_logo(){
        $is_dark_mode = self::getValByName('cust_darklayout');
        
        if($is_dark_mode == 'on'){
            $logo = self::getValByName('cust_darklayout');
            return Utility::getValByName('company_light_logo');
        }else{
            return Utility::getValByName('company_dark_logo');
        }
    }

    
}

