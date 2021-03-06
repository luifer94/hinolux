@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            @include('admin.sidebar')

            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">Editar Producto #{{ $product->id }}</div>
                    <div class="panel-body">
                        <a href="{{ url('/products') }}" title="Back"><button class="btn btn-warning btn-xs"><i class="fa fa-arrow-left" aria-hidden="true"></i> Atras</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <form method="POST" action="{{ url('/products/'. $product->id) }}" class="form-horizontal">
                            <input type="hidden" name="_method" value="PATCH">

                        @include ('products.form', ['submitButtonText' => 'Update'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
