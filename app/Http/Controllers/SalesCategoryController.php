<?php

namespace App\Http\Controllers;

use App\Models\SalesCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesCategoryController extends Controller
{
    public function index(){

        $data = SalesCategory::with('user', 'updatedBy')->paginate(20);

        return view('sales.index', compact('data'));
    }

    public function store( Request $request )
    {
        if( $request->ajax() ){

            $request->validate([
                'name' => 'required|unique:sales_categories,name',
            ]);

            try{
                SalesCategory::create([
                    'name' => $request->name,
                    'created_by' => Auth::user()->id
                ]);

                return response()->json(['message' => 'Successfully added the sales category'], 200);
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
                Rule::unique('sales_categories', 'name')->ignore($request->id),
            ],
             'id'  => 'required|numeric'
           ]);
 
          try{
             $request->merge(['updated_by' => Auth::user()->id]);

             $sales = SalesCategory::find($request->id);
 
             $sales->update($request->all());
 
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
             $company = SalesCategory::find($request->id);
 
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
         
            $data = SalesCategory::with('user', 'updatedBy')->paginate(20);
 
            $html = view('sales.table', compact('data'))->render();
    
             return response()->json(['html' => $html], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }
}
