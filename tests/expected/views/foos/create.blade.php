@extends('default')
@section('title', __('Create'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::open(['route' => 'foos.store']) !!}

    @include('foos.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
