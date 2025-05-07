<div class="d-flex justify-content-end">
    <form method="GET" action="{{ route('report.export-purchases') }}" class="mb-3">
    <input type="hidden" name="from" value="{{ $from }}">
    <input type="hidden" name="to" value="{{ $to }}">
    <input type="hidden" name="company" value="{{ $company }}">
    <button type="submit" class="btn btn-success">Generate Excel</button>
    </form>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>DATE</th>
            <th>REF. NO.</th>
            <th>SEQUENCE NO.</th>
            <th>ACCOUNT CODE</th>
            <th>ACCOUNT TITLE</th>
            <th>PARTICULARS</th>
            <th class="text-end">DEBIT</th>
            <th class="text-end">CREDIT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
            @foreach($invoice->invoiceOthers as $expense)
                @if($expense->has_child)
                    @foreach($expense->invoiceSubs as $sub)
                        <tr>
                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                            <td>{{ $invoice->voucher_no }}</td>
                            <td>{{ $invoice->code }}</td>
                            <td>{{ $expense->accountTitle->code.'-'.$sub->accountSub->code }}</td>
                            <td>{{ $sub->accountSub->name }}</td>
                            <td>{{ $sub->particulars }}</td>
                            <td class="text-end">{{ number_format($sub->debit, 2) }}</td>
                            <td class="text-end">{{ number_format($sub->credit, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td>{{ $invoice->voucher_no }}</td>
                        <td>{{ $invoice->code }}</td>
                        <td>{{ $expense->accountTitle->code }}</td>
                        <td>{{ $expense->accountTitle->title }}</td>
                        <td>{{ $expense->particulars }}</td>
                        <td class="text-end">{{ number_format($expense->debit, 2) }}</td>
                        <td class="text-end">{{ number_format($expense->credit, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>
{{ $invoices->links() }}

