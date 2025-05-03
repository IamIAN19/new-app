<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(){

        $data = Supplier::with('user', 'updatedBy')->paginate(20);

        return view('supplier.index', compact('data'));
    }

    public function store( Request $request )
    {
        if( $request->ajax() ){

            $request->validate([
                'name'      => 'required|unique:suppliers,name',
                'tin'       => 'required|unique:suppliers,tin',
                'address'   => 'required',
                'classification' => 'required'
            ]);

            try{
                Supplier::create([
                    'name' => $request->name,
                    'tin' => $request->tin,
                    'address' => $request->address,
                    'created_by' => Auth::user()->id,
                    'classification' => $request->classification
                ]);

                return response()->json(['message' => 'Successfully added the supplier'], 200);
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
                Rule::unique('suppliers', 'name')->ignore($request->id),
            ],
            'tin' => [
                'required',
                Rule::unique('suppliers', 'tin')->ignore($request->id),
            ],
            'address'   => 'required',
             'id'  => 'required|numeric'
           ]);
 
          try{
            $request->merge(['updated_by' => Auth::user()->id]);

             $supplier = Supplier::find($request->id);
 
             $supplier->update($request->all());
 
             return response()->json(['message' => 'Successfully updated!'], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }

    public function remove(Request $request)
    {
        if( $request->ajax() ){

            $request->validate([
                'id'  => 'required|numeric'
            ]);
            
            try{
           
             DB::transaction(function () use ($request){
                 Supplier::find($request->id)->delete();
             });

             return response()->json(['message' => 'Successfully Deleted!'], 200);

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
         
            $data = Supplier::with('user', 'updatedBy')->paginate(20);
 
            $html = view('supplier.table', compact('data'))->render();
    
             return response()->json(['html' => $html], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }

    public function getByTin($tin)
    {
        $supplier = Supplier::where('tin', $tin)->first();
    
        if ($supplier) {
            return response()->json([
                'name' => $supplier->name,
                'address' => $supplier->address,
                'classification' => $supplier->classification == "vat" ? "VAT" : "Non-VAT",
            ]);
        }
    
        return response()->json(['message' => 'Not found'], 404);
    }
}
