<div class="d-flex justify-content-end">
    <form method="GET" action="{{ route('report.export-journal') }}" class="mb-3">
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
            <th>Date</th>
            <th>Account Code</th>
            <th>Account Title</th>
            <th>Ref No</th>
            <th>JV No</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoices as $row)
        <tr>
            <td>{{ \Carbon\Carbon::parse($row->date)->format('M j') }}</td>
            <td>{{ $row->account_code  }}</td>
            <td>{{ $row->account_title }}</td>
            <td>{{ $row->ref_no }}</td>
            <td></td>
            <td>{{ number_format($row->debit, 2) }}</td>
            <td>{{ number_format($row->credit, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>