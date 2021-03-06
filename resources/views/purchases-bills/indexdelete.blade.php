@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">Bienvenido a la seccion de compras</div>
                    <div class="panel-body">

                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Cod</th><th>Fecha de compra</th><th>Total</th><th>Confirmada</th><th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($purchasesbills as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->purchasedate }}</td>
                                        <td>{{ $item->totalamount }}</td>
                                        <td>{{ $item->confirmed ? 'Si': 'No' }}</td>
                                        <td>
                                            <form method="POST" action="{{ url('/purchases-bills/'. $item->id) }}" style="display:inline">
                                                <input type="hidden" name="_method" value="DELETE">
                                                {!! csrf_field() !!}
                                                <button type="submit" class="btn btn-danger btn-xs" title="Delete Cliente" onclick="return confirm('Confirm delete?')"><i class="fa fa-trash-o" aria-hidden="true"></i> Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $purchasesbills->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection