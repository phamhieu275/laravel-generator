@extends('default')
@section('title', __('Edit'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::model($bar, ['route' => ['sample_package.bars.update', $bar->id], 'method' => 'patch']) !!}

    @include('sample_package::bars.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
