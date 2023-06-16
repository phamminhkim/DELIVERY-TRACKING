@extends('profile.myinfo')

@section('title')
    Đơn hàng chờ giao
@endsection

@section('my-info-iframe')
    <article class="orderWaiting">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
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
