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
        </style>
    @endpush
    <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> New Invoice </h3>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="/invoices">Invoice</a></li>
              <li class="breadcrumb-item active" aria-current="page">New invoice</li>
            </ol>
          </nav>
        </div>
        <div class="row">
          <div class="col-md-12 ">
            <form class="forms-sample" id="FrmNewInvoice">
                @csrf
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="company">Company</label>
                                <select class="form-select" id="company" name="company_id">
                                    <option value="">Choose one</option>

                                    @foreach($company as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach

                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="voucher_no">Voucher No</label>
                                <input type="text" class="form-control" id="voucher_no" name="voucher_no" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="added_date">Date</label>
                                <input type="date" class="form-control" id="added_date" name="added_date" placeholder="" required>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="invoice">Invoice #</label>
                                <input type="text" class="form-control" id="invoice" name="number" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="tin">Tin</label>
                                <input type="text" class="form-control" id="tin" name="tin" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="tin">Classification</label>
                                <input type="text" class="form-control readonly" id="classification" name="classification" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="supplier_name">Payee</label>
                                <input type="text" class="form-control readonly" id="name" name="supplier_name" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control readonly" id="address" name="address" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
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
                                    <option value="1">Sales</option>
                                    <option value="2">Purchases</option>
                                    <option value="3">Other Reports</option>
                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="sales_category">Category</label>
                                <select class="form-select" id="sales_category" name="sales_category">
                                    <option value="">Choose one</option>
                                    @foreach($sales_category as $sc)
                                        <option value="{{ $sc->id }}">{{ $sc->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error text-danger mt-1"></span>
                            </div>
                        </div>
                        {{-- <div class="d-flex">
                            <div class="form-group">
                                <input type="checkbox" class="" id="is_vatable" name="is_vatable" placeholder=""> 
                                <label for="is_vatable">Vatable</label>
                            </div>
                        </div> --}}
                        <div class="row" id="taxable-section">
                            <div class="form-group col-md-2">
                                <label for="taxable_amount">Net of Vat:</label>
                                <input type="text" class="form-control" id="taxable_amount" name="taxable_amount" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="zero_rated">Zero rated</label>
                                <input type="text" name="zero_rated" class="form-control" id="zero_rated" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="vat_exempt">Vat Exempt</label>
                                <input type="text" class="form-control" id="vat_exempt" name="vat_exempt" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="vat_exempt">Input/ Output VAT</label>
                                <input type="text" class="form-control readonly" id="input_output" name="" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="vat_exempt">Total Invoice</label>
                                <input type="text" class="form-control readonly" id="total_invoice" name="" placeholder="" readonly>
                                <span class="error text-danger mt-1"></span>
                            </div>
                            <input type="hidden" class="form-control" id="percentage" name="percentage" placeholder="" value="12">
                            {{-- <div class="form-group col-md-6">
                                <label for="percentage">Percentage(%):</label>
                                <input type="text" class="form-control" id="percentage" name="percentage" placeholder="" value="12">
                                <span class="error text-danger mt-1"></span>
                            </div> --}}
                        </div>
                        {{-- <div class="row">
                            <div class="form-group col-md-6">
                                <label for="vat_exempt">Vat Exempt</label>
                                <input type="text" class="form-control" id="vat_exempt" name="vat_exempt" placeholder="">
                                <span class="error text-danger mt-1"></span>
                            </div>
                       
                        </div> --}}
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="account_code">Account Title</label>
                                <select data-toggle="select2" class="form-control" id="account_code" placeholder="">
                                    <option value="">Choose one</option>
                                    @foreach($account_titles as $ac)
                                        <option value="{{ $ac->id }}">{{ $ac->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-center mb-2">
                                <button type="button" class="btn btn-primary btnAddAccount">Add account title</button>
                            </div>
                        </div>
                        <div class="account_title_section my-2">
                       
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="/invoices" class="btn btn-light">Cancel</a>
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

                $(document).on('input', '#taxable_amount', function(){
                    let value = ( $(this).val() * 0.12 );
                    $('#input_output').val(value.toFixed(2));
                    computeTotalInvoice();
                })

                $(document).on('input', '#zero_rated, #vat_exempt', function(){
                    computeTotalInvoice();
                });

                const computeTotalInvoice = () => {
                    let input = $('#input_output').val();
                    let zero  = $('#zero_rated').val();
                    let vat   = $('#vat_exempt').val();

                    let total = parseFloat(input || 0) + parseFloat(zero || 0) + parseFloat(vat || 0);

                    $('#total_invoice').val( total.toFixed(2) );
                }

                $('#tin').on('blur', function () {
                    const tin = $(this).val().trim();

                    if (tin) {
                        $.ajax({
                            url: `/supplier/supplier-by-tin/${tin}`,
                            type: 'GET',
                            success: function (data) {
                                $('#name').val(data.name).prop('readonly', true);
                                $('#address').val(data.address).prop('readonly', true);
                                $('#classification').val(data.classification).prop('readonly', true);
                            },
                            error: function () {
                                // Clear and enable manual entry
                                $('#name').val('').prop('readonly', false);
                                $('#address').val('').prop('readonly', false);
                                $('#classification').val('').prop('readonly', false);

                                // Popup
                                Swal.fire({
                                    icon: 'warning',
                                    text: `The TIN you entered does not exist in our records. Please add the supplier in the Supplier Manager before proceeding..`,
                                    showCancelButton: true,
                                    confirmButtonText: "Yes",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $('#tin').val('');
                                        window.open ('/supplier', "_newtab" );   
                                    } 
                                });
                            }
                        });
                    } else {
                        $('#name').val('').prop('readonly', false);
                        $('#address').val('').prop('readonly', false);
                        $('#classification').val('').prop('readonly', false);
                    }
                });

                $(document).on('click', '.custom-close-btn', function(){
                    let _this = $(this);
                    Swal.fire({
                        icon: 'warning',
                        text: ` Are you sure you want to remove this section?`,
                        showCancelButton: true,
                        confirmButtonText: "Yes",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(_this).parent('div').remove();
                        } 
                    });
                })

                $(document).on('click', '#is_vatable', function(){
                    if( $(this).is(':checked') ){
                        $('#taxable-section').removeClass('d-none');
                    }else{
                        $('#taxable-section').addClass('d-none');
                        $('#taxable_amount').val('');
                        $('#percentage').val(12);
                    }
                })

                $(document).on('click', '.btnAddAccount', function(){
                    fetchAccountSection();
                })

                $(document).on('submit', '#FrmNewInvoice', function(e){
                    e.preventDefault();
                    let frm = $(this).serialize();
                    
                    $.blockUI();
                    clearAllError();
                    $.ajax({
                        url: "{{ route('invoices.store') }}",
                        type: 'POST',
                        data: frm,
                        dataType: 'json',
                        success: function (response){

                            popupMessage('Success', response.message, 'success');

                            setTimeout(() => {
                                window.location.href = "/invoices";
                            }, 1500);
                        },
                         error: function (XMLHttpRequest, textStatus, errorThrown) {
                            if( XMLHttpRequest.status == 422 ){

                                var errors = XMLHttpRequest.responseJSON.errors;

                                popupMessage('Error', 'Failed! Please check user data before saving again.', 'error' );

                                $.each(errors, function(i, e){
                                    $('[name="'+i+'"]').parent().find('span.error').html(`*${e}`);
                                });
                            }
                            else{
                                popupMessage('Error', 'Failed!', 'error');
                            }
                        },
                        complete: function ( response ){
                            $.unblockUI();
                        }
                    })
                })

                function fetchAccountSection(){
                    let value = $('#account_code').val();

                    if( value.trim() == ""){
                        popupMessage('Error', 'Choose account title', 'error' );
                        return false;
                    }  

                    $.blockUI();

                    $.ajax({
                        url: "{{ route('invoices.fetch-account-section') }}",
                        type: 'GET',
                        data: { id: value },
                        dataType: 'json',
                        success: function (response){
                            $('.account_title_section').append( response.html );
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            popupMessage('Error', 'Failed!', 'error');
                        },
                        complete: function ( response ){
                            $.unblockUI();
                        }
                    })
                }
            })
        </script>
      @endpush
</x-app-layout>