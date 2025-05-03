<?php

namespace App\Http\Controllers;

use App\Models\AccountTitle;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicesOtherExpenses;
use App\Models\InvoiceSub;
use App\Models\SalesCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(){
        $invoices = Invoice::with('company', 'supplier', 'user', 'updatedBy')->orderByDesc('id')->paginate(20);

        return view('invoice.index', compact('invoices'));
    }

    public function create(){

        $company        = Company::select('id', 'name')->get();
        $sales_category = SalesCategory::select('id', 'name')->get();
        $account_titles   = AccountTitle::with('subs')->get();

        return view('invoice.create', compact('company', 'sales_category', 'account_titles'));
    }

    public function store(Request $request){
        if($request->ajax()){

            $validator = Validator::make($request->all(), [
                'number' => 'required|numeric|unique:invoices,number',
                'tin' => 'required|numeric',
                'supplier_name' => 'required',
                'classification' => 'required',
                'voucher_no' => 'required',
                'address' => 'required',
                'sales_category' => 'required',
                'company_id' => 'required',
                'added_date' => 'required',
                'department_id' => 'required',
                'taxable_amount' => ['nullable', 'numeric'],
                'percentage' => ['nullable', 'numeric'],
                'zero_rated' => ['nullable', 'numeric'],
                'vat_exempt' => ['nullable', 'numeric'],
            ]);
            
            // Run the validation
            $validated = $validator->validate();

            try{
                DB::transaction(function () use ($request){
                    $invoice = new Invoice();
                    $invoice->number = $request->number;
                    $invoice->tin    = $request->tin;
                    $invoice->supplier = $request->supplier_name;
                    $invoice->address = $request->address;
                    $invoice->sales_category_id = $request->sales_category;
                    $invoice->number = $request->number;
                    $invoice->vat_tax_percentage = $request->percentage;
                    $invoice->vat_tax_amount = $request->taxable_amount;
                    $invoice->vat_zero_rated = $request->zero_rated;
                    $invoice->vat_exempt = $request->vat_exempt;
                    $invoice->voucher_no = $request->voucher_no;
                    $invoice->department_id = $request->department_id;
                    $invoice->classification = $request->classification;
                    $invoice->added_date = $request->added_date;

                    $totalComputation = 0;
                    
                    $inputOutputVat = 0;
                    // Net vat computation for tax
                    if($request->taxable_amount ){
                        $inputOutputVat = ($request->taxable_amount * .12);
                    }

                    $totalComputation = ($request->zero_rated + $request->vat_exempt + $inputOutputVat );
                    $invoice->total_amount = number_format($totalComputation,2);
                    $invoice->company_id = $request->company_id;
                    $invoice->user_id = Auth::user()->id;

                    $invoice->save();

                    $invoice = $invoice->fresh();

                    $additional_amount = 0;

                    if( $request->has('account') ){
                        foreach($request->account as $key => $val){
                            if( isset($val['sub']) ){

                                $otherExpense = InvoicesOtherExpenses::create([
                                    'invoice_id' => $invoice->id,
                                    'account_title_id' => $val['title_id'],
                                    'has_child' => 1,
                                ]);

                                foreach( $val['sub'] as $sub ){
                                    $debitCredit = ( ($val['debit'] ?? 0) + ($val['credit'] ?? 0) );
                                    $additional_amount += $debitCredit;
                                    InvoiceSub::create([
                                        'invoice_other_expenses_id' => $otherExpense->id,
                                        'account_sub_id' => $sub['id'],
                                        'amount' => 0,
                                        'debit' => $sub['debit'],
                                        'credit' => $sub['credit'],
                                        'particulars' => $sub['particulars'],
                                    ]);
                                }
                            }else{
                                if( $val['debit'] || $val['credit'] ){
                                    $additional_amount += ( $val['debit'] + $val['credit'] );
                                    InvoicesOtherExpenses::create([
                                        'invoice_id' => $invoice->id,
                                        'account_title_id' => $val['title_id'],
                                        'debit' => $val['debit'],
                                        'credit' => $val['credit'],
                                        'particulars' => $val['particulars'],
                                    ]);
                                }
                            }
                        }

                        if($additional_amount > 0){
                            $invoice->total_amount = ($invoice->total_amount + $additional_amount);
                            $invoice->save();
                        }
                    }
                });

                return response()->json(['message' => 'Successfully added an invoice'], 200);
            }
            catch(Exception $e){
                return response()->json(['message' => 'Failed: '.$e->getMessage()], 500);
            }

        }

        return abort(403, 'Unauthorized!');
    }

    public function fetchAccountSection( Request $request )
    { 
        if($request->ajax()){

            $request->validate([
                'id' => 'required',
            ]);

            $time = time();

            $account = AccountTitle::with('subs')->find($request->id);

            $html = view('invoice.create-section', compact('account', 'time'))->render();

            return response()->json(['html' => $html], 200);
        }

        return abort(403, 'Unauthorized!');
    }

    public function show(Invoice $invoice){

        $company        = Company::select('id', 'name')->get();
        $sales_category = SalesCategory::select('id', 'name')->get();
        $account_titles   = AccountTitle::with('subs')->get();

        return view('invoice.show', compact('company', 'sales_category', 'account_titles', 'invoice'));
    }

    public function update(Request $request){
        if($request->ajax()){

            $validator = Validator::make($request->all(), [
                'id'    => 'required',
                'number' => [
                    'required',
                    'numeric',
                    Rule::unique('invoices', 'number')->ignore($request->id),
                ],
                'tin' => 'required|numeric',
                'supplier_name' => 'required',
                'classification' => 'required',
                'voucher_no' => 'required',
                'address' => 'required',
                'sales_category' => 'required',
                'company_id' => 'required',
                'added_date' => 'required',
                'department_id' => 'required',
                'taxable_amount' => ['nullable', 'numeric'],
                'percentage' => ['nullable', 'numeric'],
                'zero_rated' => ['nullable', 'numeric'],
                'vat_exempt' => ['nullable', 'numeric'],
            ]);
            
            // Apply 'required' only if is_vatable is present
            $validator->sometimes(['taxable_amount', 'percentage'], 'required', function ($input) {
                return isset($input->is_vatable);
            });
            
            // Run the validation
            $validated = $validator->validate();

            try{
                DB::transaction(function () use ($request){
                    $invoice = Invoice::findOrFail($request->id);
                    $invoice->number = $request->number;
                    $invoice->tin    = $request->tin;
                    $invoice->supplier = $request->supplier_name;
                    $invoice->address = $request->address;
                    $invoice->sales_category_id = $request->sales_category;
                    $invoice->number = $request->number;
                    $invoice->updated_by = Auth::user()->id;

                    $invoice->vat_tax_percentage = $request->percentage;
                    $invoice->vat_tax_amount = $request->taxable_amount;
                    $invoice->vat_zero_rated = $request->zero_rated;
                    $invoice->vat_exempt = $request->vat_exempt;
                    $invoice->voucher_no = $request->voucher_no;
                    $invoice->department_id = $request->department_id;
                    $invoice->classification = $request->classification;
                    $invoice->added_date = $request->added_date;

                    // $vatComputation   = 0;
                    $totalComputation = 0;
                    
                    // if( $request->filled('is_vatable') ){
                    //     $invoice->vat_tax_amount = $request->taxable_amount;
                    //     $invoice->vat_tax_percentage = $request->percentage;

                    //     $vatComputation = ($request->taxable_amount * ($request->percentage / 100));
                    // }
                    
                    $inputOutputVat = 0;
                    // Net vat computation for tax
                    if($request->taxable_amount ){
                        $inputOutputVat = ($request->taxable_amount * .12);
                    }

                    $totalComputation = ($request->zero_rated + $request->vat_exempt + $inputOutputVat );

                    $invoice->total_amount = number_format($totalComputation,2);

                    $invoice->company_id = $request->company_id;
                    $invoice->user_id = Auth::user()->id;

                    $invoice->save();

                    $invoice = $invoice->fresh();
                 
                    $additional_amount = 0;

                    // Remove children data for quick insert
                    $ids = InvoicesOtherExpenses::where('invoice_id', $invoice->id)->select('id')->get()->pluck('id')->toArray();
                    InvoiceSub::whereIn('invoice_other_expenses_id', $ids)->delete();
                    InvoicesOtherExpenses::where('invoice_id', $invoice->id)->delete();

                    if( $request->has('account') ){
                        foreach($request->account as $key => $val){
                   
                            if( isset($val['sub']) ){

                                $otherExpense = InvoicesOtherExpenses::create([
                                    'invoice_id' => $invoice->id,
                                    'account_title_id' => $val['title_id'],
                                    'has_child' => 1,
                                ]);

                                foreach( $val['sub'] as $sub ){
                                    $debitCredit = ( ($val['debit'] ?? 0) + ($val['credit'] ?? 0) );
                                    $additional_amount += $debitCredit;
                                    InvoiceSub::create([
                                        'invoice_other_expenses_id' => $otherExpense->id,
                                        'account_sub_id' => $sub['id'],
                                        'amount' => 0,
                                        'debit' => $sub['debit'],
                                        'credit' => $sub['credit'],
                                        'particulars' => $sub['particulars'],
                                    ]);
                                }
                            }else{
                                if( $val['debit'] || $val['credit'] ){
                                    $additional_amount += ( $val['debit'] + $val['credit'] );
                                    InvoicesOtherExpenses::create([
                                        'invoice_id' => $invoice->id,
                                        'account_title_id' => $val['title_id'],
                                        'debit' => $val['debit'],
                                        'credit' => $val['credit'],
                                        'particulars' => $val['particulars'],
                                    ]);
                                }
                            }
                        }

                        if($additional_amount > 0){
                            $invoice->total_amount = ($invoice->total_amount + $additional_amount);
                            $invoice->save();
                        }
                    }
                });

                return response()->json(['message' => 'Successfully added an invoice'], 200);
            }
            catch(Exception $e){
                return response()->json(['message' => 'Failed: '.$e->getMessage()], 500);
            }

        }

        return abort(403, 'Unauthorized!');
    }

    public function delete( Request $request ){
        if( $request->ajax() ){

            $request->validate(['id' => 'required|numeric']);

            $result = DB::transaction(function () use ($request) {
                // Get related expense IDs
                $ids = InvoicesOtherExpenses::where('invoice_id', $request->id)
                            ->pluck('id')
                            ->toArray();
            
                // Delete related InvoiceSub entries
                InvoiceSub::whereIn('invoice_other_expenses_id', $ids)->delete();
            
                // Delete related InvoicesOtherExpenses entries
                InvoicesOtherExpenses::where('invoice_id', $request->id)->delete();
            
                // Delete the Invoice itself
                $invoiceDeleted = Invoice::where('id', $request->id)->delete();
            
                // Optionally check if the invoice was deleted
                return $invoiceDeleted > 0;
            });
            
            if ($result) {
                return response()->json(['message' => 'Successfully deleted the invoice.'], 200);
            }
            
            return response()->json(['message' => 'Failed!'], 500);
        }

        return abort(403, 'Unauthorized!');
    }

    public function fetchContent( Request $request)
    {
       if( $request->ajax() ){
          
          try{
             $invoices = Invoice::with('company', 'supplier', 'user', 'updatedBy')->orderByDesc('id');

             if($request->filled('code')){
                $invoices = $invoices->where('code', $request->code);
             }

             $invoices = $invoices->paginate(20);
 
             $html = view('invoice.table', compact('invoices'))->render();
    
             return response()->json(['html' => $html], 200); 
          }
          catch( Exception $e){
             return response()->json(['message' => $e->getMessage()], 500);
          }
       }
 
       return abort(403, 'Unauthorized!');
    }
}
