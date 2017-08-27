@extends('default')
@section('title', __('Index'))

@section('content')
<div class="container">
    <div class="row">
        <h1 class="pull-left"></h1>
        <div>{!! link_to_route('foos.create', __('Create'), [], ['class' => 'btn btn-primary']) !!}</div>
    </div>

    <div class="row">
    @if ($foos->isEmpty())
        <div class="well text-center">{{ __('No record found.') }}</div>
    @else
        @include('foos.table')
    @endif
    </div>

    <div class="row">
        {!! $foos->render() !!}
    </div>
</div>
@endsection