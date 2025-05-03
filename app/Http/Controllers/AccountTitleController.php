<?php

namespace App\Http\Controllers;

use App\Models\AccountSub;
use App\Models\AccountTitle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountTitleController extends Controller
{
    public function index(){

        $accountTitles = AccountTitle::with('subs', 'user', 'updatedBy')->orderByDesc('id')->paginate(20);

        return view('account.index', compact('accountTitles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'code' => 'required|unique:account_titles,code',
            'subs.*.name' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $accountTitle = AccountTitle::create([
                'title' => $request->title,
                'code' => $request->code,
                'created_by' => Auth::user()->id
            ]);

            if ($request->subs) {
                foreach ($request->subs as $sub) {
                    $accountTitle->subs()->create([
                        'account_title_id' => $accountTitle->id,
                        'name' => $sub['name'],
                        'code' => $sub['code'],
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Account title created successfully']);
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $accountTitle = AccountTitle::findOrFail($id);
    
            // Validate main account title
            $validated = $request->validate([
                'title' => 'required',
                'code' => 'required|unique:account_titles,code,' . $accountTitle->id, // Ignore the current account title when checking code
                'subs.*.name' => 'required',
            ]);
    
            // Update the account title
            $accountTitle->update([
                'title' => $request->title,
                'code'  => $request->code,
                'updated_by' => Auth::user()->id
            ]);
    
            // Handle existing sub-accounts update
            if ($request->subs) {
                foreach ($request->subs as $subId => $subVal) {
                    $sub = AccountSub::where('account_title_id', $id)->find($subId);
                    if ( $sub <> null ) {
                        $sub->update([
                            'name' => $subVal['name'],
                            'code' => $subVal['code'],
                        ]);
                    }
                    else{
                        // Create new sub-account
                        AccountSub::create([
                            'account_title_id' => $accountTitle->id,
                            'name' => $subVal['name'],
                            'code' => $subVal['code'],
                        ]);
                    }
                }
            }
        });
    
        return response()->json(['message' => 'Account title updated successfully']);
    }

    public function updateStatus( Request $request )
    {
       if( $request->ajax() ){
          
          $request->validate([
             'id'  => 'required|numeric',
             'status' => 'required'
           ]);
 
          try{
             $accountTitle = AccountTitle::find($request->id);
             $accountTitle->update($request->all());
 
             return response()->json(['message' => 'Successfully updated!'], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }
    
    
    public function fetchModalBody( Request $request ){
        if( $request->ajax() ){
            try{
                $data = AccountTitle::with('subs')->findOrFail($request->id);

                $html = view('account.edit-account-modal', compact('data'))->render();

                return response()->json(['html' => $html], 200);
            }
            catch(Exception $e){
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }

        return abort(403, 'Unauthorized!');
    }
}
