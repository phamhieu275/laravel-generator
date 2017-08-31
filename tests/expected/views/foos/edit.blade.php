@extends('default')
@section('title', __('Edit'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::model($foo, ['route' => ['foos.update', $foo->id], 'method' => 'patch']) !!}

    @include('foos.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
