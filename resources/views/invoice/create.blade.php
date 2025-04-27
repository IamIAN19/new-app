<x-app-layout>  
    <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title"> New Invoice </h3>
          {{-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Forms</a></li>
              <li class="breadcrumb-item active" aria-current="page">Form elements</li>
            </ol>
          </nav> --}}
        </div>
        <div class="row">
          <div class="col-md-12 ">
            <form class="forms-sample">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputUsername1">Vatable</label>
                            <input type="text" class="form-control" id="exampleInputUsername1" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Invoice no.</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Supplier Name</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputConfirmPassword1">Address</label>
                            <input type="text" class="form-control" id="exampleInputConfirmPassword1" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="exampleSelectGender">Sale Category</label>
                            <select class="form-select" id="exampleSelectGender">
                            <option>Sale 1 </option>
                            <option>Sale 2</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="form-group">
                                <input type="checkbox" class="" id="is-vatable" placeholder="" checked> 
                                <label for="is-vatable">Vatable</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Taxable amount:</label>
                                <input type="text" class="form-control" id="" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputPassword1">Percentage(%):</label>
                                <input type="text" class="form-control" id="" placeholder="" value="12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputConfirmPassword1">Vat Exempt</label>
                                <input type="text" class="form-control" id="exampleInputConfirmPassword1" placeholder="">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputConfirmPassword1">Vat Exempt</label>
                                <input type="text" class="form-control" id="exampleInputConfirmPassword1" placeholder="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputUsername1">Account Code</label>
                            <input type="text" class="form-control" id="exampleInputUsername1" placeholder="">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
</x-app-layout>