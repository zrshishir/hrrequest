<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;
use DB;
use Illuminate\Support\Facades\File;
use Storage;
use Log;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index()
    {  
       
        // set_time_limit(10);
        // ini_set('memory_limit', '10M');
     
       
        if (Auth::check()) {
            // get all user limit 5
            $allUsers = User::get()->take(5);
            $allUsersNum = $allUsers->count();
            // get all banned user
            // $bannedUsers = User::where('status', 'Banned')->get();
           
            //dashboard loaded with active and banned users
            $activeUsers = User::where('status', 'Active')->orderBy('created_at', 'desc')->get()->take(5);
            $activeUsersNum = $activeUsers->count();
            $bannedUsers = User::where('status', 'Banned')->orderBy('created_at', 'desc')->get()->take(5);
            $bannedUsersNum = $bannedUsers->count();
           
            return view('home', get_defined_vars());
        } else {
            
            //if no user registration first.
            $user = User::first();
            if (!$user) {
                return redirect('register');
            }
            
            return view('getting-start', get_defined_vars());
        }

    }

//privacy poilicy page
    public function privacyPolicy()
    {
        $privacy = configValue('privacy');
        return view('terms.view_privacy', get_defined_vars());
    }

// terms and codition
    public function termsCondition()
    {
        $termsCondition = configValue('termCondition');
        return view('terms.view_privacy', get_defined_vars());
    }

//delete data in demo version
    public function clearDemoDatabaseDaily()
    {
        if (env('APP_ENV') == 'demo') {

            // migrate all table
            Artisan::call('migrate:refresh', [
                '--force' => true,
            ]);

            //seed all table
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
            Log::info('Demo data deleted at', [date('d-m-Y h:i:s:A')]);
        }
    }

    public function download_sample()
    {
        return Response::download(base_path('sample_email_list.csv'));
    }

    public function clearLog()
    {
       $f = @fopen(storage_path('logs/laravel.log'), "r+");
       if ($f !== false) {
           ftruncate($f, 0);
           fclose($f);
       }
        Log::error('Clear log');
    }
}
