@extends('default')

@section('content')
<div class="container">
<!-- name Field -->
<div class="form-group">
    {!! Form::label('name', __('name') . ':') !!}
    <p>{!! $bar->name !!}</p>
</div>
<!-- content Field -->
<div class="form-group">
    {!! Form::label('content', __('content') . ':') !!}
    <p>{!! $bar->content !!}</p>
</div>
<!-- publish_date Field -->
<div class="form-group">
    {!! Form::label('publish_date', __('publish_date') . ':') !!}
    <p>{!! $bar->publish_date !!}</p>
</div>
<!-- author_id Field -->
<div class="form-group">
    {!! Form::label('author_id', __('author_id') . ':') !!}
    <p>{!! $bar->author_id !!}</p>
</div>
<!-- rate Field -->
<div class="form-group">
    {!! Form::label('rate', __('rate') . ':') !!}
    <p>{!! $bar->rate !!}</p>
</div>
<!-- score Field -->
<div class="form-group">
    {!! Form::label('score', __('score') . ':') !!}
    <p>{!! $bar->score !!}</p>
</div>
</div>
@endsection
