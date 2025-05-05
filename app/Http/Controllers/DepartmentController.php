<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(){
        $data = Department::with('user', 'updatedBy')->orderByDesc('id')->paginate(20);

        return view('department.index', compact('data'));
    }

    public function store( Request $request )
    {
       if( $request->ajax() ){
 
          $request->validate([
            'name' => 'required|unique:departments,name'
          ]);
 
          try{
             Department::create([
                'name' => $request->name,
                'created_by' => Auth::user()->id
             ]);
 
             return response()->json(['message' => 'Successfully added the company'], 200);
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }

    public function update( Request $request )
    {
       if( $request->ajax() ){
 
          $request->validate([
             'name' => [
                'required',
                Rule::unique('departments', 'name')->ignore($request->id),
            ],
             'id'  => 'required|numeric'
           ]);
 
          try{
             $request->merge(['updated_by' => Auth::user()->id]);
             $company = Department::find($request->id);
 
             $company->update($request->all());
 
             return response()->json(['message' => 'Successfully updated!'], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }

    public function updateStatus( Request $request )
    {
       if( $request->ajax() ){
          
          $request->validate([
             'id'  => 'required|numeric',
             'status' => 'required'
           ]);
 
          try{
             $company = Department::find($request->id);
 
             $company->update($request->all());
 
             return response()->json(['message' => 'Successfully updated!'], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }

    public function fetchContent( Request $request)
    {
       if( $request->ajax() ){
          
          try{
         
             $data = Department::with('user', 'updatedBy')->orderByDesc('id')->paginate(20);
 
             $html = view('department.table', compact('data'))->render();
    
             return response()->json(['html' => $html], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }
}
