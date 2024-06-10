<x-main>
    <!-- end of navbar navigation -->
    <div class="content">
        <div class="container" style="max-width: 100%">
            <div class="page-title">
                <h3>Banks</h3>
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
                            <a href="{{ route('bank.create') }}" class="btn btn-primary" style="padding-top: 7px; padding-left: 8px; height: 40px;">
                                <span class="fa-fw select-all fas"></span>
                                Create
                            </a>
                            <div class="deleteSelection" style="padding-bottom: 6px">
                                <a href="javascript:void(0);" class="btn btn-danger" id="deleteAll" style="display: none; height: 100%">Delete</a>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <form method="get">
                                <div class="col-md-12 col-lg-12 d-flex justify-content-between">
                                    <input type="search" id="search" name="search_bank" value="{{ $search }}" class="form-control mb-2" placeholder="Search transaction..." style="width: 67%"/>
                                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                                    <a href="/bank" class="btn btn-primary mb-2" >Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12">

                        {{-- <br /> --}}

                        <div class="card">
                            <div class="card-header">Banks</div>
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
                                                <th>Currency</th>
                                                <th>Running Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($banks as $bank)
                                                <tr>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input userDelete" name="single_ids"
                                                                type="checkbox" value="{{ $bank->id }}"
                                                                id="flexCheckDefault">
                                                        </div>
                                                    </td>
                                                    <td scope="row">{{ $bank->id }}</td>
                                                    <td>{{ $bank->name }}</td>
                                                    <td>{{ $bank->description }}</td>
                                                    <td>{{ $bank->getCurrency() }}</td>
                                                    <td>{{ $bank->running_balance }}</td>
                                                    <td><a href="{{ route('bank.update', $bank->id) }}"
                                                            class="btn btn-success"><span
                                                                class="fa-fw select-all fas"></span></a> <a
                                                            href="{{ route('bank.delete', $bank->id) }}"
                                                            class="btn btn-danger"><span
                                                                class="fa-fw select-all fas"></span></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $banks->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
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
                url: "{{ route('bank.bankDeleteAll') }}",
                data: "all_ids=" + all_ids + "",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = "/bank";
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
