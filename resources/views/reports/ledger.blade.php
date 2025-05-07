<div class="d-flex justify-content-end">
    <form method="GET" action="{{ route('report.export') }}" class="mb-3">
    <input type="hidden" name="from" value="{{ $from }}">
    <input type="hidden" name="to" value="{{ $to }}">
    <input type="hidden" name="company" value="{{ $company }}">
    <input type="hidden" name="departments" value="{{ json_encode($departments) }}">
    <button type="submit" class="btn btn-success">Generate Excel</button>
    </form>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Account Code</th>
            <th>Account Title</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandTotalDebit = 0;
            $grandTotalCredit = 0;
        @endphp
  
        @foreach ($accountTitles as $title)
            @php
                $parent = $parentAccounts[$title->id] ?? null;
                $childAccounts = $childSubs[$title->id] ?? collect();
  
                $parentDebit = $parent->total_debit ?? 0;
                $parentCredit = $parent->total_credit ?? 0;
  
                $grandTotalDebit += $parentDebit;
                $grandTotalCredit += $parentCredit;
            @endphp
  
            {{-- Parent row --}}
            <tr>
                <td>{{ $title->code }}</td>
                <td><strong>{{ $title->title }}</strong></td>
                <td>{{ number_format($parentDebit, 2) }}</td>
                <td>{{ number_format($parentCredit, 2) }}</td>
            </tr>
  
            {{-- Child rows --}}
            @foreach ($childAccounts as $sub)
                @php
                    $grandTotalDebit += $sub->total_debit;
                    $grandTotalCredit += $sub->total_credit;
                @endphp
                <tr>
                    <td></td>
                    <td class="ps-4">└ {{ $sub->account_sub->name }}</td>
                    <td>{{ number_format($sub->total_debit, 2) }}</td>
                    <td>{{ number_format($sub->total_credit, 2) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr class="table-dark">
            <td colspan="2" class="text-end fw-bold">Grand Total</td>
            <td class="fw-bold">{{ number_format($grandTotalDebit, 2) }}</td>
            <td class="fw-bold">{{ number_format($grandTotalCredit, 2) }}</td>
        </tr>
    </tfoot>
  </table>
