@extends('default')
@section('title', __('Edit'))

@section('content')
<div class="row">
    <div class="col-md-6 col-xs-12">

    {!! Form::model($bar, ['route' => ['bars.update', $bar->id], 'method' => 'patch']) !!}

    @include('bars.form')

    {!! Form::close() !!}

    </div>
</div>
@endsection
