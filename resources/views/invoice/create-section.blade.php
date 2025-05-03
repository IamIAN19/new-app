@if( $account->subs->count() < 1 )
    <div class="mb-4 border p-2 rounded border-secondary bg-light position-relative">
        {{-- <button type="button" class="btn-close btn-del position-absolute m-2 rounded-circle" aria-label="Close" style="top: -20px;right: -18px; background-color: red; !important"></button> --}}
        <button type="button"
                class="custom-close-btn position-absolute m-2 rounded-circle border-0 d-flex justify-content-center align-items-center"
                aria-label="Close">
            &times;
        </button>
        <div class="row">
            <div class="form-group col-4 mb-0">
                <label for="">Title</label>
                <input type="text" value="{{ $account->title }}" class="form-control" disabled>
            </div>
            <div class="form-group col-1 mb-0">
                <label for="">Code</label>
                <input type="text" value="{{ $account->code }}" class="form-control" disabled>
            </div>
            <div class="form-group col-3 mb-0">
                <label for="">Particulars</label>
                <input type="text" value="" name="account[{{ $time }}][particulars]" class="form-control">
            </div>
            <div class="form-group col-2 mb-0">
                <label for="">Debit</label>
                <input type="text" value="" name="account[{{ $time }}][debit]" class="form-control">
            </div>
            <div class="form-group col-2 mb-0">
                <label for="">Credit</label>
                <input type="hidden" value="{{ $account->id }}" name="account[{{ $time }}][title_id]" class="form-control">
                <input type="text" value="" name="account[{{ $time }}][credit]" class="form-control">
            </div>
        </div>
    </div>
@else
    <div class="mb-3 border p-2 rounded border-secondary bg-light position-relative">
        <button type="button"
                class="custom-close-btn position-absolute m-2 rounded-circle border-0 d-flex justify-content-center align-items-center"
                aria-label="Close">
            &times;
        </button>
        <div class="row">
            <div class="form-group col-4 mb-0">
                <label>Title</label>
                <input type="text" value="{{ $account->title }}" class="form-control" disabled>
            </div>
            <div class="form-group col-2 mb-0">
                <label>Code</label>
                <input type="text" value="{{ $account->code }}" class="form-control" disabled>
            </div>
            {{-- <div class="form-group col-4 mb-0">
                <label>Amount</label>
                <input type="text" value="" class="form-control" disabled>
            </div> --}}
            <input type="hidden" value="{{ $account->id }}" name="account[{{ $time }}][title_id]" class="form-control">
        </div>
    
        <!-- Accordion Toggle Button -->
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#children-accordion-{{ $account->id }}" aria-expanded="true" aria-controls="children-accordion-{{ $account->id }}">
                Toggle Sub Category
            </button>
        </div>
    
        <!-- Collapsible Children (Visible by Default) -->
        <div class="collapse show mt-3" id="children-accordion-{{ $account->id }}">
            @foreach($account->subs as $key => $value)
                <div class="row ms-2 border-start ps-3">
                    <div class="form-group col-3">
                        <label>Title</label>
                        <input type="text" value="{{ $value->name }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-1">
                        <label>Code</label>
                        <input type="text" value="{{ $account->code.'-'.$value->code }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-4 mb-0">
                        <label for="">Particulars</label>
                        <input type="text" value="" name="account[{{ $time }}][sub][{{ $value->id }}][particulars]" class="form-control">
                    </div>
                    <div class="form-group col-2 mb-0">
                        <label for="">Debit</label>
                        <input type="text" value="" name="account[{{ $time }}][sub][{{ $value->id }}][debit]" class="form-control">
                    </div>
                    <div class="form-group col-2">
                        <label>Credit</label>
                        <input type="text" name="account[{{ $time }}][sub][{{ $value->id }}][credit]" class="form-control">
                        <input type="hidden" value="{{ $value->id }}" name="account[{{ $time }}][sub][{{ $value->id }}][id]" class="form-control">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif