<x-app-layout>  
    <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> Reports </h3>
        </div>
          <div class="d-flex flex-wrap align-items-center mb-5">
            <!-- Select Report Section -->
            <div class="d-flex flex-column me-3 w-25">
                <label for="exampleSelectGender">@lang('Select Report')</label>
                <select class="form-select" id="exampleSelectGender">
                    <option value="general_ledger">General Ledger</option>
                    <option value="slsl">SLSL</option>
                </select>
            </div>
        
            <!-- Date Filter Section -->
            <div class="d-flex flex-column me-3">
                <label>@lang('Start date - End date')</label>
                <input
                    name="customfilter"
                    data-range="true"
                    data-multiple-dates-separator=" - "
                    data-language="en"
                    class="customDateFilterInput form-control"
                    data-position='bottom right'
                    placeholder="@lang('Start date - End date')"
                    autocomplete="off"
                    value=""
                >
            </div>
        
            <!-- Submit Button Section -->
            <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-success mt-3">Start Search</button>
            </div>
        </div>
        <div class="row">
          <div class="col-md-12 mb-5">
            <div class="d-flex justify-content-end">
              <button class="btn btn-sm btn-success mb-2">Generate Excel</button>
            </div>
            <div class="card">
                <div class="card-body">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Account Title</th>
                        <th>Account Code</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Gasoline</td>
                        <td>001</td>
                        <td>$5000</td>
                      </tr>
                      <tr>
                        <td>Salaries</td>
                        <td>002</td>
                        <td>$8000</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
          <div class="col-md-12">
            <div class="d-flex justify-content-end">
              <button class="btn btn-sm btn-success mb-2">Generate Excel</button>
            </div>
            <div class="card">
                <div class="card-body">
                  <table class="table text-center">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Invoice#</th>
                        <th>Supplier</th>
                        <th>Tin</th>
                        <th>Address</th>
                        <th>Sales Category</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>4/27/2025</td>
                        <td>012345</td>
                        <td>Jolikod</td>
                        <td>123123131</td>
                        <td>Somewhere there</td>
                        <td>Category A</td>
                        <td>
                            <table class="table table-borderless text-center mb-0">
                                <thead class="fw-bold">
                                  <tr>
                                    <th class="border-end">VAT</th>
                                    <th class="border-end">Percenter</th>
                                    <th class="border-end">Zero</th>
                                    <th class="border-end">Exempt</th>
                                    <th>Total</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td class="border-end">10%</td>
                                    <td class="border-end">5%</td>
                                    <td class="border-end">0</td>
                                    <td class="border-end">0</td>
                                    <td>$115</td>
                                  </tr>
                                </tbody>
                              </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
        </div>
      </div>
</x-app-layout>