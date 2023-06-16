@extends('pages.profile.myinfo')

@section('title')
    Đơn hàng chờ giao
@endsection

@section('my-info-iframe')
    <article class="orderWaiting">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered" id="myTable">
                <thead class="table-header">
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                        {{-- <th scope="col">Action</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                        {{-- <th>
                            <button type="button" class="btn btn-info badge-pill">
                                <i class="fa-solid fa-pen-to-square" style="color: #000000;"></i>
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger badge-pill">
                                <i class="fa-solid fa-trash" style="color: #000000;"></i>
                                Delete
                            </button>
                        </th> --}}
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
@endsection
