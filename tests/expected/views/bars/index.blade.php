@extends('default')
@section('title', __('Index'))

@section('content')
<div class="container">
    <div class="row">
        <h1 class="pull-left"></h1>
        <div>{!! link_to_route('bars.create', __('Create'), [], ['class' => 'btn btn-primary']) !!}</div>
    </div>

    <div class="row">
    @if ($bars->isEmpty())
        <div class="well text-center">{{ __('No record found.') }}</div>
    @else
        @include('bars.table')
    @endif
    </div>

    <div class="row">
        {!! $bars->render() !!}
    </div>
</div>
@endsection