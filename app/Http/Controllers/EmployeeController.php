<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use GuzzleHttp\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeesPost;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employees.index',
            [
                'apikey' => ENV('API_KEY_GOOGLE_MAPS')
            ]
        );
    
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeesPost $request)
    {
        $employee = new Employee;
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->birthdate = $request->birthdate;
        $employee->position = $request->position;
        $employee->address = $request->address;
        $employee->save();

        $getskills = explode(",", $request->skills);

        foreach($getskills as $key => $val) {
            $skill = new Skill;
            $skill->skill = $val;
            $skill->qualification = 0;
            $skill->employee_id = $employee->id;
            $skill->save();
        }
        return response()->json([
            'statusCode'=>200,
            'message' =>'Datos guardados correctamente',
        ]);
    }

    public function getList()
    {
        $employees = Employee::with('skills')->orderBy('id', 'DESC')->get();
        $dataEmployees=[];
        foreach ($employees as $key => $employee) {
            $coordinatesEmployee = $this->getCoordinates($employee->address);
            $arrayAux = [
                "id" => $employee->id,
                "name" => $employee->name,
                "address" => $employee->address,
                "position" => $employee->position,
                "birthdate" => date("d/m/Y", strtotime($employee->birthdate)),
                "birthdate2" => $employee->birthdate,
                "latitude" => isset($coordinatesEmployee['error'])?$coordinatesEmployee['error']:$coordinatesEmployee['latitude'],
                "longitude" =>  isset($coordinatesEmployee['error'])?$coordinatesEmployee['error']:$coordinatesEmployee['longitude'],
                "skills" => $employee->skills,
            ];
            array_push($dataEmployees, $arrayAux);
        }
        return response()->json([
            'employees' => $dataEmployees,
        ],
        200);
    }

    public function getCoordinates($address)
    {
        $coordinates =[];
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => ENV('API_KEY_GOOGLE_MAPS'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if ($data['status'] === 'OK') {
            $latitude = $data['results'][0]['geometry']['location']['lat'];
            $longitude = $data['results'][0]['geometry']['location']['lng'];
            $coordinates =['latitude' => $latitude, 'longitude'=>$longitude];
            return $coordinates;
        }
        return ['error' => 'No se pudo obtener la información'];
    }
}
