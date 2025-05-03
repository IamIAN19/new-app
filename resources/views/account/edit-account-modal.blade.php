<form id="updateAccountForm" action="/accounts/{{ $data->id }}/update">
    <div class="">
      @csrf
      <input type="hidden" name="id" id="accountId" value="{{ $data->id }}">

      <div class="mb-3">
        <label class="form-label">Account Title Name</label>
        <input type="text" class="form-control" name="title" id="" value="{{ $data->title }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Account Code</label>
        <input type="text" class="form-control" name="code" id="" value="{{ $data->code }}"  required>
      </div>

      <div class="mb-3">
        <label class="form-label">Sub Accounts</label>
        <div id="editSubAccounts">
            @if($data->subs)
                @foreach($data->subs as $sub)
                    <div class="d-flex mb-2 align-items-center sub-account-item" data-id="${sub.id}">
                        <input type="text" name="subs[{{ $sub->id }}][name]" value="{{ $sub->name }}" class="form-control me-2" required>
                        <input type="text" name="subs[{{ $sub->id }}][code]" value="{{ $sub->code }}" class="form-control me-2" required>
                        {{-- <button type="button" class="btn btn-danger btn-sm btn-remove-sub">x</button> --}}
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btneditAddSub">Add Sub Account</button>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>