<x-app-layout>
    <!-- partial -->
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <nav aria-label="breadcrumb">
            </nav>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Total Invoices</th>
                                <th>Invoices Today</th>
                            </tr>
                        </thead>   
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->total_invoices }}</td>
                                    <td>{{ $company->invoices_today }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
