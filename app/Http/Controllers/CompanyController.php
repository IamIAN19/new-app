<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
   public function index(){
      $data = Company::with('user', 'updatedBy')->paginate(20);

      return view('company.index', compact('data'));
   }

   public function store( Request $request )
   {
      if( $request->ajax() ){

         $request->validate([
           'name' => 'required|unique:companies,name'
         ]);

         try{
            Company::create([
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
               Rule::unique('companies', 'name')->ignore($request->id),
           ],
            'id'  => 'required|numeric'
          ]);

         try{
            $request->merge(['updated_by' => Auth::user()->id]);
            $company = Company::find($request->id);

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
            $company = Company::find($request->id);

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
        
            $data = Company::with('user', 'updatedBy')->paginate(20);

            $html = view('company.table', compact('data'))->render();
   
            return response()->json(['html' => $html], 200); 
         }
         catch( Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
         }
      }

      return abort(403, 'Unauthorized!');
   }
}
