<div class="d-flex justify-content-end">
    <form method="GET" action="{{ route('report.export-slsp') }}" class="mb-3">
        <input type="hidden" name="from" value="{{ $from }}">
        <input type="hidden" name="to" value="{{ $to }}">
        <input type="hidden" name="company" value="{{ $company }}">
        <input type="hidden" name="departments" value="{{ json_encode($departments) }}">
        <button type="submit" class="btn btn-success">Generate Excel</button>
    </form>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>TIN.NO</th>
                <th>CLASSIFICATION</th>
                <th>PAYEE</th>
                <th>ADDRESS</th>
                <th>CATEGORY</th>
                <th>NET OF VAT</th>
                <th>ZERO RATED</th>
                <th>EXEMPT</th>
                <th>INPUT VAT (12%)</th>
                <th>TOTAL INVOICE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagedGrouped as $item)
                <tr>
                    <td>{{ $item['tin'] }}</td>
                    <td>{{ $item['classification'] }}</td>
                    <td>{{ $item['payee'] }}</td>
                    <td>{{ $item['address'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ number_format($item['net'], 2) }}</td>
                    <td>{{ number_format($item['zero'], 2) }}</td>
                    <td>{{ number_format($item['exempt'], 2) }}</td>
                    <td>{{ number_format($item['vat'], 2) }}</td>
                    <td>{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $pagedGrouped->links() }}

