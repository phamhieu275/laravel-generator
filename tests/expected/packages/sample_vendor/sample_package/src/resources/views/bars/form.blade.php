<div class="form-group {!! $errors->has('name') ? 'has-error' : '' !!}">
    {!! Form::label('name', __('Name'), ['class' => 'control-label']) !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! $errors->first('name', '<small class="help-block">:message</small>') !!}
</div>

<div class="form-group {!! $errors->has('content') ? 'has-error' : '' !!}">
    {!! Form::label('content', __('Content'), ['class' => 'control-label']) !!}
    {!! Form::textarea('content', null, ['class' => 'form-control']) !!}
    {!! $errors->first('content', '<small class="help-block">:message</small>') !!}
</div>

<div class="form-group {!! $errors->has('publish_date') ? 'has-error' : '' !!}">
    {!! Form::label('publish_date', __('Publish Date'), ['class' => 'control-label']) !!}
    {!! Form::date('publish_date', null, ['class' => 'form-control']) !!}
    {!! $errors->first('publish_date', '<small class="help-block">:message</small>') !!}
</div>

<div class="form-group {!! $errors->has('author_id') ? 'has-error' : '' !!}">
    {!! Form::label('author_id', __('Author Id'), ['class' => 'control-label']) !!}
    {!! Form::number('author_id', null, ['class' => 'form-control']) !!}
    {!! $errors->first('author_id', '<small class="help-block">:message</small>') !!}
</div>

<div class="form-group {!! $errors->has('rate') ? 'has-error' : '' !!}">
    {!! Form::label('rate', __('Rate'), ['class' => 'control-label']) !!}
    {!! Form::text('rate', null, ['class' => 'form-control']) !!}
    {!! $errors->first('rate', '<small class="help-block">:message</small>') !!}
</div>

<div class="form-group {!! $errors->has('score') ? 'has-error' : '' !!}">
    {!! Form::label('score', __('Score'), ['class' => 'control-label']) !!}
    {!! Form::text('score', null, ['class' => 'form-control']) !!}
    {!! $errors->first('score', '<small class="help-block">:message</small>') !!}
</div>

<!-- Submit Field -->
<div class="form-group">
    {!! Form::submit(__('Save'), ['class' => 'btn btn-primary']) !!}
</div>
