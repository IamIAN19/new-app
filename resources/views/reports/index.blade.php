<x-app-layout>  
  <div class="content-wrapper">
      <div class="page-header">
        <h3 class="page-title"> Reports </h3>
      </div>
      <form action="" id="frmFilter">
        <div class="d-flex flex-wrap align-items-center mb-5">
          <div class="d-flex flex-column me-3 w-25">
              <label for="exampleSelectGender">@lang('Select Company')</label>
              <select class="form-select" name="company" id="company" style="height:45px;" required>
                <option value="">Choose one</option>
                  @foreach($company as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                  @endforeach
              </select>
          </div>
          <!-- Select Report Section -->
          <div class="d-flex flex-column me-3 w-25">
              <label for="exampleSelectGender">@lang('Select Report')</label>
              <select class="form-select" name="type" id="type" required style="height:45px;">
                  <option value="">Choose one</option>
                  <option value="ledger">General Ledger</option>
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
                  required
              >
          </div>
          <!-- Submit Button Section -->
          <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-success mt-3">Start Search</button>
          </div>
      </div>
    </form>

    <!-- Dynamic Content Here -->
    <div class="row report-body">
    </div>
  </div>
  
  @push('scripts')
    <script>
      $(document).ready(function(){

        $(document).on('submit', '#frmFilter', function(e){
          e.preventDefault();
          let frm = $(this).serialize();

          $.blockUI();

          $.ajax({
            url: "{{ route('reports.fetch-content') }}",
            type: 'GET',
            data: frm,
            dataType: 'json',
            success: function ( response ){
              $('.report-body').html( response.html );
            },
            error: function( response ){
              popupMessage('Error', 'Please fill out all the fields', 'error' );
            },
            complete: function( response ){
              $.unblockUI();
            },
          })
        })
      });
    </script>
  @endpush
</x-app-layout>