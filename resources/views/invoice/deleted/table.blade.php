<div class="table-responsive">
  <p class="mb-3">
    Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} entries
  </p>
  <table class="table table-bordered text-center align-middle">
      <thead>
        <tr>
          <th>Code</th>
          <th>Company Name</th>
          <th>Invoice#</th>
          <th>Amount</th>
          <th>Supplier</th>
          <th>Added By</th>
          <th>Created at</th>
          <th>Deleted By</th>
          <th>Action</th>
        </tr>
      </thead>    
      <tbody>
          @foreach( $invoices as $invoice )
              <tr>
                  <td>{{ $invoice->code }}</td>
                  <td>{{ @$invoice->company->name }}</td>
                  <td>{{ $invoice->number }}</td>
                  <td>₱{{ number_format($invoice->total_amount,2) }}</td>
                  <td>{{ $invoice->supplier }}</td>
                  <td>{{ $invoice->user->name }}</td>
                  <td>{{ \Carbon\Carbon::parse($invoice->added_date)->format('Y-m-d') }}</td>
                  <td>{{ $invoice->deletedBy->name ?? "-" }}</td>
                  <td data-id="{{ $invoice->id }}">
                      <a href="/invoices/show-deleted?id={{ $invoice->id }}" class="btn btn-sm btn-secondary btn-view">
                          View
                      </a>
                  </td>
              </tr>
          @endforeach
      </tbody>
    </table>
</div>
<div class="d-flex justify-content-end mt-5">
  {{ $invoices->links() }}
</div>