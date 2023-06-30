<?php

namespace App\Http\Controllers;

use App\Models\Skill;
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
        return view('employees.index');
    
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

        return response()->json([
            'employees' => $employees
        ],
        200);
    }
}
