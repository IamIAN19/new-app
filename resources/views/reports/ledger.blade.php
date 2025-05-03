<div class="d-flex justify-content-end">
    <form method="GET" action="{{ route('report.export') }}" class="mb-3">
    <input type="hidden" name="from" value="{{ $from }}">
    <input type="hidden" name="to" value="{{ $to }}">
    <input type="hidden" name="company" value="{{ $company }}">
    <button type="submit" class="btn btn-success">Generate Excel</button>
    </form>
</div>
<table class="table table-bordered">
  <thead>
      <tr>
          <th>Account Code</th>
          <th>Account Title</th>
          <th>Amount</th>
      </tr>
  </thead>
  <tbody>
      @php $grandTotal = 0; @endphp
      @foreach ($accountTitles as $title)
          @php
            $parentAmount = $parentAccounts[$title->id] ?? null;
            $childAccounts = $childSubs[$title->id] ?? collect();
          @endphp

          {{-- Show parent title row --}}
          <tr>
              <td>{{ $title->code }}</td>
              <td>{{ $title->title }}</td>
              <td>
                  @if ($parentAmount)
                      {{ number_format($parentAmount, 2) }}
                      @php $grandTotal += $parentAmount; @endphp
                  @else
                      -
                  @endif
              </td>
          </tr>

          {{-- Show child subs if any --}}
          @foreach ($childAccounts as $sub)
     
              <tr>
                  <td>—</td>
                  <td class="ps-4">└ {{ $sub->accountSub->name }}</td>
                  <td>{{ number_format($sub->total_amount, 2) }}</td>
                  @php $grandTotal += $sub->total_amount; @endphp
              </tr>
          @endforeach
      @endforeach
  </tbody>
  <tfoot>
      <tr class="table-dark">
          <td colspan="2" class="text-end fw-bold">Total</td>
          <td class="fw-bold">{{ number_format($grandTotal, 2) }}</td>
      </tr>
  </tfoot>
</table>
