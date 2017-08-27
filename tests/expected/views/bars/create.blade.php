@extends('default')
@section('title', __('Create'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::open(['route' => 'bars.store']) !!}

    @include('bars.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
