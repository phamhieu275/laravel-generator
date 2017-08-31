@extends('default')
@section('title', __('Create'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::open(['route' => 'sample_package.bars.store']) !!}

    @include('sample_package::bars.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
