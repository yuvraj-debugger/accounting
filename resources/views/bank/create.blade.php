<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container">
            <div class="page-title">
                <h3>Create Bank</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-header">Create Bank</div>
                        <div class="card-body">
                            <form accept-charset="utf-8" action="{{route('bank.store')}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Name</label>
                                    <input type="text" name="name" placeholder="Name" class="form-control" value="{{old('name')}}">

                                    @error('name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" placeholder="Description" class="form-control" value="{{old('description')}}"></textarea>
                                    @error('description')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select name="currency" placeholder="Name" class="form-control all-select2_dropdown">
                                        <option value="">Select Currency</option>
                                        @foreach($currencies as $currency)
                                        <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_type" class="form-label">Bank Account Type</label>
                                    <select name="bank_account_type" placeholder="Name" class="form-control all-select2_dropdown">
                                        <option value="">Select Bank Account Type</option>
                                        @foreach($bankAccountTypes as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_account_type')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="opening_balance" class="form-label">Opening Balance</label>
                                    <input type="text" name="opening_balance" placeholder="Opening Balance" class="form-control" value="{{old('opening_balance')}}">

                                    @error('opening_balance')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 row">
                                    <div class="col-sm-4 offset-sm-2">
                                        <a href="{{route('bank.index')}}" class="btn btn-secondary mb-2"><i class="fas fa-times"></i> Cancel</a>
                                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .error{
            color:red;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('.all-select2_dropdown').select2();
</script>
</x-main>
