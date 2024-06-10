<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container">
            <div class="page-title">
                <h3>Update Account</h3>
            </div>
            <div class="row">
                <div class="col-lg-12">
                
                    <div class="card">
                        <div class="card-header">Update Account</div>
                        <div class="card-body">
                            <form accept-charset="utf-8" action="{{route('account.storeupdate',$accountData->id)}}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" name="account_name" placeholder="Account Name" class="form-control" value="{{!empty(old('account_name'))?old('account_name'):$accountData->name}}">
                                  
                                    @error('account_name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" placeholder="Description" class="form-control">{{!empty(old('description'))?old('description'):$accountData->description}}</textarea>
                                    @error('description')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="parent_account_name" class="form-label">Parent Account Name</label>

                                    <select name="parent_account_name" placeholder="Account Type" class="form-control all-select2_dropdown">
                                        <option value="">Select Account Type</option>
                                        @foreach($accounts as $account)
                                        <option value="{{$account->id}}" {{($accountData->parent_account_name==$account->id)?'selected':''}}>{{$account->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('parent_account_name')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="account_type" class="form-label">Account Type</label>
                                    <select name="account_type" placeholder="Account Type" class="form-control all-select2_dropdown">
                                        <option value="">Select Account Type</option>
                                        @foreach($accountTypes as $key => $value)
                                        <option value="{{$key}}" {{($accountData->account_type==$key)?'selected':''}}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                    @error('account_type')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="opening_balance" class="form-label">Opening Balance</label>
                                    <input type="text" name="opening_balance" placeholder="Opening Balance" class="form-control" value="{{!empty(old('opening_balance'))?old('opening_balance'):$accountData->opening_balance}}">
                                    @error('opening_balance')
                                    <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-4 offset-sm-2">
                                        <a href="{{route('account.index')}}" class="btn btn-secondary mb-2"><i class="fas fa-times"></i> Cancel</a>
                                        <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-save"></i> Update</button>
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