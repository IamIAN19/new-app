<x-app-layout>  
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            select.form-select{
                color: #000000 !important;
                height: 38px;
            }

            input{
                padding: .7rem !important;
            }

            .custom-close-btn {
                width: 30px;
                height: 30px;
                background-color: red;
                color: white;
                font-size: 1rem;
                transition: background-color 0.2s ease;
                top: -20px;
                right: -18px;
            }

            .custom-close-btn:hover {
                background-color: darkred;
            }

            .readonly {
                background-color: #e9ecef !important;
                color: #6c757d !important;
                cursor: not-allowed;
                border: 1px solid #ced4da !important;
            }

            .form-group{
                margin-bottom:1rem;
            }

        </style>
    @endpush
    <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> Deleted Invoice </h3>
        </div>
        <div class="row">
          <div class="col-md-12 ">
            <form class="forms-sample" id="FrmNewInvoice">
                @csrf
                <input type="hidden" name="id" value="{{ $invoice->id }}">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="company">Company</label>
                                <select class="form-select" id="company" name="company_id">
                                    <option value="">Choose one</option>

                                    @foreach($company as $c)
                                        <option value="{{ $c->id }}" {{ $invoice->company_id == $c->id ? "selected" : "" }}>{{ $c->name }}</option>
                                    @endforeach

                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="voucher_no">Voucher No</label>
                                <input type="text" class="form-control" value="{{ $invoice->voucher_no }}" id="voucher_no" name="voucher_no" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="added_date">Date</label>
                                <input type="date" class="form-control" id="added_date" value="{{ \Carbon\Carbon::parse($invoice->added_date)->toDateString() }}" name="added_date" placeholder="" required>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="invoice">Invoice #</label>
                                <input type="text" class="form-control" value="{{ $invoice->number }}" id="invoice" name="number" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="tin">Tin</label>
                                    <input type="text" class="form-control" id="tin" value="{{ $invoice->tin }}" name="tin" placeholder="">
                                    <span class="error text-danger mt-1"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="tin">Classification</label>
                                    <input type="text" class="form-control readonly" value="{{ $invoice->classification }}" id="classification" name="classification" placeholder="" readonly>
                                    <span class="error text-danger mt-1"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="supplier_name">Payee</label>
                                    <input type="text" class="form-control readonly" value="{{ $invoice->supplier }}" id="name" name="supplier_name" placeholder="" readonly>
                                    <span class="error text-danger mt-1"></span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control readonly" value="{{ $invoice->address }}" id="address" name="address" placeholder="" readonly>
                                    <span class="error text-danger mt-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="department">Department</label>
                                <select class="form-select" id="department" name="department_id">
                                    <option value="">Choose one</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ $invoice->department_id == $d->id ? "selected" : "" }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sales_category">Category</label>
                                <select class="form-select" id="sales_category" name="sales_category">
                                    <option value="">Choose one</option>
                                    @foreach($sales_category as $sc)
                                        <option value="{{ $sc->id }}" {{ $invoice->sales_category_id == $sc->id ? "selected" : "" }}>{{ $sc->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                        </div>
                        <div class="row" id="taxable-section">
                            <div class="form-group col-md-2">
                                <label for="taxable_amount">Net of Vat:</label>
                                <input type="text" class="form-control" value="{{ $invoice->vat_tax_amount }}" id="taxable_amount" name="taxable_amount" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="zero_rated">Zero rated</label>
                                <input type="text" name="zero_rated" value="{{ $invoice->vat_zero_rated }}" class="form-control" id="zero_rated" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="vat_exempt">Vat Exempt</label>
                                <input type="text" class="form-control" value="{{ $invoice->vat_exempt }}" id="vat_exempt" name="vat_exempt" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="vat_exempt">Input/ Output VAT</label>
                                <input type="text" class="form-control readonly" value="" id="input_output" name="" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="vat_exempt">Total Invoice</label>
                                <input type="text" class="form-control readonly" id="total_invoice"  name="" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <input type="hidden" class="form-control" id="percentage" name="percentage" placeholder="" value="12">
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                        <label for="account_code">Account Title</label>
                        <div class="account_title_section my-2">
                            @if( $invoice->invoiceOthers->count() > 0 )
                                @foreach ( $invoice->invoiceOthers as $i )
                                    @include('invoice.deleted.edit-section')
                                @endforeach
                            @endif
                        </div>
                        <div class="{{ $invoice->invoiceOthers->count() > 0 ? "" : "d-none" }} totalDebitCreditSection">
                            <div class="row ms-2 border-start ps-3">
                                <div class="form-group col-3">
                                </div>
                                <div class="form-group col-1">
                                </div>
                                <div class="form-group col-4 mb-0">
                                </div>
                                <div class="form-group col-2 mb-0">
                                    <label for="">Total Debit</label>
                                    <input type="text" value="" id="total_debit" class="form-control readonly" readonly>
                                </div>
                                <div class="form-group col-2">
                                    <label>Total Credit</label>
                                    <input type="text" class="form-control readonly" id="total_credit" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
      @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function(){
                $('select[data-toggle="select2"]').select2();

                const computeTotalDebitCredit = () => {
                    let totalDebit = 0;
                    let totalCredit = 0;

                    document.querySelectorAll('.debit-field').forEach(element => {
                        const value = parseFloat(element.value.trim()) || 0;
                        totalDebit += value;
                    });

                    document.querySelectorAll('.credit-field').forEach(element => {
                        const value = parseFloat(element.value.trim()) || 0;
                        totalCredit += value;
                    });

                    $('#total_debit').val(totalDebit.toFixed(2));
                    $('#total_credit').val(totalCredit.toFixed(2));
                }

                $(document).on('input', '.credit-field, .debit-field', function(){
                    computeTotalDebitCredit();
                });

                $(document).on('input', '#taxable_amount', function(){
                    let value = ( $(this).val() * 0.12 );
                    $('#input_output').val(value.toFixed(2));
                    computeTotalInvoice();
                })

                $(document).on('input', '#zero_rated, #vat_exempt', function(){
                    computeTotalInvoice();
                });

                const computeInputOutput = () => {
                    if( $('#taxable_amount').val() ){
                        let value = ( $('#taxable_amount').val() * 0.12 );
                        $('#input_output').val(parseFloat(value.toFixed(2)));
                    }
                }

                const computeTotalInvoice = () => {
                    let input = $('#input_output').val();
                    let zero  = $('#zero_rated').val();
                    let vat   = $('#vat_exempt').val();
                    let tax_amount = $('#taxable_amount').val();

                    let total = parseFloat(input || 0) + parseFloat(zero || 0) + parseFloat(vat || 0) + parseFloat(tax_amount || 0);

                    $('#total_invoice').val( total.toFixed(2) );
                }
                
                computeInputOutput();
                computeTotalInvoice();
                computeTotalDebitCredit();
            })
        </script>
      @endpush
</x-app-layout>