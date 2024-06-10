<x-main>
    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height:38px !important;
            width:221px !important;
        }
        .select2-container .select2-selection--single {
            height:38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow{
            top:6px !important;
        }
    </style>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container" style="max-width: 100%">
            <div class="page-title">
                <h3>Accounts</h3>
            </div>
            <div class="row">
                @if (Session::has('success'))
                    <div class="alert alert-success" id="success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::has('info'))
                    <div class="alert alert-danger" id="info" role="alert">
                        {{ Session::get('info') }}
                    </div>
                @endif
                <div class="row">
                    <div style="display: flex; justify-content: space-between;">
                        <div style="display: flex; justify-content: space-between; width: 175px">
                            <a href="{{ route('account.create') }}" class="btn btn-primary" style="padding-top: 7px; padding-left: 8px; height: 40px;">
                                <span class="fa-fw select-all fas"></span>
                                Create
                            </a>
                            <div class="deleteSelection" style="padding-bottom: 6px">
                                <a href="javascript:void(0);" class="btn btn-danger" id="deleteAll" style="display: none; height: 100%">Delete</a>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end">
                            <div class="col-md-3 me-2">
                                <select name="account_type" id="accountType" placeholder="Account Type" class="form-control all-select2_dropdown">
                                    <option value="">Select Account Type</option>
                                    @foreach($accountTypes as $key => $value)
                                        <option value="{{$key}}" {{ isset($_GET['type']) ? ($_GET['type'] == $key ? 'selected' : '') : '' }}>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <form method="get"  class="col-md-6">
                                <div class="col-md-12 col-lg-12 d-flex justify-content-between">
                                    <input type="search" id="search" name="search_account" value="{{ $search }}" class="form-control mb-2" placeholder="Search transaction..." style="width: 67%"/>
                                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                                    <a href="/account" class="btn btn-primary mb-2" >Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12">

                        {{-- <br /> --}}

                        <div class="card">
                            <div class="card-header">Account</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input select_all_ids" type="checkbox"
                                                            value="" id="flexCheckDefault">
                                                    </div>
                                                </th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Parent</th>
                                                <th>Type</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($accounts as $account)
                                                <tr>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input userDelete" name="single_ids"
                                                                type="checkbox" value="{{ $account->id }}"
                                                                id="flexCheckDefault">
                                                        </div>
                                                    </td>
                                                    <td scope="row">{{ $account->id }}</td>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->description }}</td>
                                                    <td>{{ $account->getParentName() }}</td>
                                                    <td>{{ $account->getType() }}</td>
                                                    <td><a href="{{ route('account.update', $account->id) }}"
                                                            class="btn btn-success"><span
                                                                class="fa-fw select-all fas"></span></a> <a
                                                            href="{{ route('account.delete', $account->id) }}"
                                                            class="btn btn-danger"><span
                                                                class="fa-fw select-all fas"></span></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $accounts->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

    <script>
        $(document).ready(function(){
            $('#accountType').select2();

            $('#accountType').on('change', function() {
                let val = $(this).val();
                window.location.href = "{{ route('account.index', ['type' => 'ACCOUNT_TYPE']) }}".replace('ACCOUNT_TYPE', val);
            });
        });


        $('.select_all_ids').click(function() {
            $('.userDelete').prop('checked', $(this).prop('checked'));
        });

        $('input[type=checkbox]').click(function() {
            var check = 0;
            $('input[type=checkbox]').each(function() {
                checked = $(this).is(":checked");
                console.log(checked);
                if (checked) {
                    check = 1
                }
            });


            if (check) {
                $('#deleteAll').show();
            } else {
                $('#deleteAll').hide();
            }
        });

        $('#deleteAll').click(function(e) {
            e.preventDefault();
            var all_ids = [];
            $('input:checkbox[name="single_ids"]:checked').each(function() {
                all_ids.push($(this).val());
            });

            $.ajax({
                type: 'post',
                url: "{{ route('account.accountDeleteAll') }}",
                data: "all_ids=" + all_ids + "",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = "/account";
                }
            });
        });
        setTimeout(function() {
            $('#success').hide();
        }, 3000);
        setTimeout(function() {
            $('#info').hide();
        }, 3000);


    </script>
</x-main>
