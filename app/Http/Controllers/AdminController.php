<?php

namespace App\Http\Controllers;

use App\Address;
use App\Client;
use App\SensorBrand;
use App\SensorType;
use App\User;
use App\Sensor;
use App\Site;
use App\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\Feature\TestClass;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.index', compact('user'));
    }

    public function users(){
        $users = User::all();
        $customers = Client::all();
        $usersTypes = UserType::all();
        return view ('admin.usersadmin', compact('users', 'customers', 'usersTypes'));
    }

    public function client(){
        $customers = Client::all();
        return view ('admin.clientsadmin', compact('customers'));
    }

    public function sites(){
        $sites = Site::all();
        $users = User::all();
        $association =  Site::with('users')->get(); //ritorna tutti i record della tabella molti a molti
        return view ('admin.sitesadmin', compact('sites', 'users', 'association'));
    }

    public function sensors(){
        $sites = Site::all();
        $sensors = Sensor::all();
        $types = SensorType::all();
        $brands = SensorBrand::all();
        return view ('admin.sensoradmin', compact('sites', 'sensors', 'types', 'brands'));
    }

    public function notAccesible(){
        return view('error');
    }

    public function showAdd($type)
    {
        if($type == "user") {
            $clients = Client::all();
            $usertypes = UserType::all();
            return view('admin.insertuser', compact('clients', 'usertypes'));
        } else if($type == "client") {
            return view('admin.insertclient');
        } else if($type == "site") {
            $users = User::all();
            return view('admin.insertsite', compact('users'));
        } else if($type == "sensor") {
            $sites = Site::all();
            $brands = SensorBrand::all();
            $types = SensorType::all();
            return view('admin.insertsensor', compact('sites', 'brands', 'types'));
        }
    }

    public function add(Request $request, $type)
    {
        if($type == "user") {
            $cryptPassword = Hash::make($request->password);
            $request->merge(array('password' => $cryptPassword));

            $rules = array(
                'CF' => 'regex:^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$^',
                'Name' => 'regex:^[a-zA-Z]{2,}$^',
                'Surname' => 'regex:^[a-zA-Z]{2,}$^',
                'Phone' => 'digits_between:9,10'
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return redirect('/admin/user/showAdd')->withErrors($validator);
            } else {
                $user = new User($request->all());
                $user->save();
            }

            return redirect(route('adminUsers'));
        } else if($type == "client") {
            $rules = array(
                'PI' => 'regex:^[0-9]{11}$^',
                'Province' => 'regex:^[A-Z]{2}$^',
                'City' => 'regex:^[a-zA-Z]+$^',
                'StreetNumber' => 'regex:^[0-9]+[a-zA-Z]?$^',
                'ZipCode' => 'regex:^[0-9]{5}$^'
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return redirect('/admin/client/showAdd')->withErrors($validator);
            } else {
                $client = new Client();
                $client->PI = $request->PI;
                $client->BusinessName = $request->BusinessName;


                $address = new Address();
                $address->Country = $request->Country;
                $address->Province = $request->Province;
                $address->City = $request->City;
                $address->Street = $request->Street;
                $address->StreetNumber = $request->StreetNumber;
                $address->ZipCode = $request->ZipCode;

                if(Address::where('Street', '=', $request->Street)->exists()) {
                    $errors = array(
                        'Address' => "Attenzione: L'indirizzo è già utilizzato"
                    );

                    return redirect(route('showAdd', 'client'))->withErrors($errors);
                } else {
                    $address->save();

                    $client->address_id = $address->id;
                    $client->save();

                    return redirect(route('adminClients'));
                }
            }
        } else if($type == "site") {
            $rules = array(
                'Province' => 'regex:^[A-Z]{2}$^',
                'City' => 'regex:^[a-zA-Z]+$^',
                'StreetNumber' => 'regex:^[0-9]+[a-zA-Z]?$^',
                'ZipCode' => 'regex:^[0-9]{5}$^'
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return redirect('/admin/site/showAdd')->withErrors($validator);
            } else {
                $user = User::find($request->user_id);

                $site = new Site();
                $site->Name = $request->Name;
                $site->Description = $request->Description;

                $address = new Address();
                $address->Country = $request->Country;
                $address->Province = $request->Province;
                $address->City = $request->City;
                $address->Street = $request->Street;
                $address->StreetNumber = $request->StreetNumber;
                $address->ZipCode = $request->ZipCode;

                if(Address::where('StreetNumber', '=', $request->StreetNumber)->exists()) {
                    $errors = array(
                        'Address' => "Attenzione: L'indirizzo è già utilizzato"
                    );

                    return redirect('/admin/site/showAdd')->withErrors($errors);
                } else {
                    $address->save();
                    $site->address_id = $address->id;
                    $site->save();

                    $user->sites()->attach($site->id);

                    return redirect(route('adminSites'));
                }
            }
        } else if($type == "sensor") {
            $rules = array(
                'Latitude' => 'regex:^-?[0-9]{1,2}\.\d{6,}$^',
                'Longitude' => 'regex:^-?[0-9]{1,2}\.\d{6,}$^',
                'MaxValue' => 'integer',
                'MinValue' => 'integer'
            );

            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return redirect('/admin/sensor/showAdd')->withErrors($validator);
            } else {
                $sensor = new Sensor($request->all());
                $sensor->save();

                return redirect(route('adminSensors'));
            }
        }
    }

    public function edit(Request $request, $type)
    {
        $response = array();

        if($type == "user") {
            $user = User::find($request->id);
            if($request->password != "") {
                $cryptPassword = Hash::make($request->password);
                $request->merge(array('password' => $cryptPassword));
            } else {
                unset($request['password']);
            }

            $user->update($request->all());

            $response = array(
                'success' => 'true'
            );
        } else if($type == "client") {
            $client = Client::find($request->id);
            $client->update($request->all());

            $response = array(
                'success' => 'true'
            );
        } else if($type == "site") {
            $site = Site::find($request->id);
            $address = Address::find($request->AddressID);

            if(count($request->users_id) > 0) {
                foreach ($request->users_id as $id) {
                    $user = User::find($id);

                    $exist = DB::table('site_user')
                            ->where('user_id', $id)
                            ->where('site_id', $request->id)
                            ->first();

                    if(is_null($exist)) {
                        $user->sites()->attach($request->id);
                    }
                }
            }

            $address->Street = $request->Street;
            $address->Province = $request->Province;
            $address->StreetNumber = $request->StreetNumber;

            $address->save();

            $site->Name = $request->Name;
            $site->Description = $request->Description;
            $site->save();

            $response = array(
                'success' => 'true'
            );
        } else if($type == "sensor") {
            $sensor = Sensor::find($request->id);

            $test = new TestClass($request);
            $test->testModelSensor();

            $sensor->update($request->all());
        }

        return $response;
    }

    public function delete(Request $request, $type)
    {
        if($type == "user") {
            User::destroy($request->id);

            $response = array(
                'success' => 'true'
            );
        } else if($type == "client") {
            $client = Client::find($request->id);
            $client->address()->delete();
            $client->delete();

            $response = array(
                'success' => 'true'
            );
        } else if($type == "site") {
            $site = Site::find($request->id);
            $site->address()->delete();
            $site->delete();
            //Site::destroy($request->id);

            $response = array(
                'success' => 'true'
            );
        } else if($type == "sensor") {
            Sensor::destroy($request->id);

            $response = array(
                'success' => 'true'
            );
        }
        return $response;
    }
}
