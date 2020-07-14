<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Client;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected $defaultRole;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->defaultRole = setting('app.default_role');

        $this->middleware(['permission:clients_create'])->only(['create','store']);
        $this->middleware(['permission:clients_show'])->only('show');
        $this->middleware(['permission:clients_edit'])->only(['edit','update']);
        $this->middleware(['permission:clients_delete'])->only('destroy');
        $this->middleware(['permission:clients_ban'])->only(['banClient','activateClient']);
        $this->middleware(['permission:clients_activity'])->only('activityLog');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'registration_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:' . Client::getTableName() . ',email'],
            'secondary_email'   => ['string', 'email', 'max:255', 'unique:' . Client::getTableName() . ',secondary_email'],
            'dob'               => ['date_format:Y-m-d'],
            'contact'           => ['string'],
            'passport_number'   => ['string'],
            'process_address'   => ['string'],
            'nationality'       => ['string'],
            'work_status'       => ['string', 'in:0,1,2'],
            'photo'             => ['string'],
            'banned'            => ['integer', 'in:0,1'],
            'assign_date'       => ['date_format:Y-m-d H:i:s'],
            'assign_to'         => ['integer', 'exists:' . Client::getTableName() . ',id'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'is_superadmin'     => ['in:0']
        ]);
    }

    /**
     * Create a new client instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Client
     */
    protected function create(array $data)
    {
        $client = Clinet::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::find($this->defaultRole);
        if ($role) {
            $client->assignRole($role);
        }



        return;
    }
}
