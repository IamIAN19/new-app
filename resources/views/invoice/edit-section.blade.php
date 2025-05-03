@if( !$i->has_child  )
    <div class="mb-4 border p-2 rounded border-secondary bg-light position-relative">
        <button type="button"
                class="custom-close-btn position-absolute m-2 rounded-circle border-0 d-flex justify-content-center align-items-center"
                aria-label="Close">
            &times;
        </button>
        <div class="row">
            {{-- <div class="form-group col-4">
                <label for="">Title</label>
                <input type="text" value="{{ $i->accountTitle->title }}" class="form-control" disabled>
            </div>
            <div class="form-group col-4">
                <label for="">Code</label>
                <input type="text" value="{{ $i->accountTitle->code }}" class="form-control" disabled>
            </div>
            <div class="form-group col-4">
                <label for="">Amount</label>
                <input type="hidden" value="{{ $i->accountTitle->id }}" name="account[{{ $i->id  }}][title_id]" class="form-control">
                <input type="text" value="{{ $i->amount }}" name="account[{{ $i->id }}][amount]" class="form-control">
            </div> --}}

            <div class="form-group col-4 mb-0">
                <label for="">Title</label>
                <input type="text" value="{{ $i->accountTitle->title }}" class="form-control" disabled>
            </div>
            <div class="form-group col-1 mb-0">
                <label for="">Code</label>
                <input type="text" value="{{ $i->accountTitle->code }}" class="form-control" disabled>
            </div>
            <div class="form-group col-3 mb-0">
                <label for="">Particulars</label>
                <input type="text" value="{{ $i->particulars }}" name="account[{{ $i->id }}][particulars]" class="form-control">
            </div>
            <div class="form-group col-2 mb-0">
                <label for="">Debit</label>
                <input type="text" value="{{ $i->debit }}" name="account[{{ $i->id }}][debit]" class="form-control">
            </div>
            <div class="form-group col-2 mb-0">
                <label for="">Credit</label>
                <input type="hidden" value="{{ $i->accountTitle->id }}" name="account[{{ $i->id  }}][title_id]" class="form-control">
                <input type="text" value="{{ $i->credit }}" name="account[{{ $i->id }}][credit]" class="form-control">
            </div>
        </div>
    </div>
@else
    <div class="mb-4 border p-2 rounded border-secondary bg-light position-relative">
        <button type="button"
                class="custom-close-btn position-absolute m-2 rounded-circle border-0 d-flex justify-content-center align-items-center"
                aria-label="Close">
            &times;
        </button>
        <div class="row">
            <div class="form-group col-4">
                <label>Title</label>
                <input type="text" value="{{ $i->accountTitle->title }}" class="form-control" disabled>
            </div>
            <div class="form-group col-4">
                <label>Code</label>
                <input type="text" value="{{ $i->accountTitle->code }}" class="form-control" disabled>
            </div>
            {{-- <div class="form-group col-4">
                <label>Amount</label>
                <input type="text" value="" class="form-control" disabled>
                <input type="hidden" value="{{ $i->accountTitle->id }}" name="account[{{ $i->id }}][title_id]" class="form-control">
            </div> --}}
            <input type="hidden" value="{{ $i->accountTitle->id }}" name="account[{{ $i->id }}][title_id]" class="form-control">
        </div>
    
        <!-- Accordion Toggle Button -->
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#children-accordion-{{ $i->id }}" aria-expanded="true" aria-controls="children-accordion-{{ $i->id }}">
                Toggle Sub Category
            </button>
        </div>
    
        <!-- Collapsible Children (Visible by Default) -->
        <div class="collapse show mt-3" id="children-accordion-{{ $i->id }}">
            @foreach($i->invoiceSubs as $key => $value)
                <div class="row ms-2 border-start ps-3">
                    {{-- <div class="form-group col-4">
                        <label>Title</label>
                        <input type="text" value="{{ $value->accountSub->name }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-4">
                        <label>Code</label>
                        <input type="text" value="{{ $i->accountTitle->code.'-'.$value->accountSub->code }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-4">
                        <label>Amount</label>
                        <input type="text" value="{{ $value->amount }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][amount]" class="form-control">
                        <input type="hidden" value="{{ $value->accountSub->id }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][id]" class="form-control">
                    </div> --}}

                    <div class="form-group col-3">
                        <label>Title</label>
                        <input type="text" value="{{ $value->accountSub->name }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-1">
                        <label>Code</label>
                        <input type="text" value="{{ $i->accountTitle->code.'-'.$value->accountSub->code }}" class="form-control" disabled>
                    </div>
                    <div class="form-group col-4 mb-0">
                        <label for="">Particulars</label>
                        <input type="text" value="{{ $value->particulars }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][particulars]" class="form-control">
                    </div>
                    <div class="form-group col-2 mb-0">
                        <label for="">Debit</label>
                        <input type="text" value="{{ $value->debit }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][debit]" class="form-control">
                    </div>
                    <div class="form-group col-2">
                        <label>Credit</label>
                        <input type="text" value="{{ $value->credit }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][credit]" class="form-control">
                        <input type="hidden" value="{{ $value->accountSub->id }}" name="account[{{ $i->id }}][sub][{{ $value->accountSub->id }}][id]" class="form-control">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif