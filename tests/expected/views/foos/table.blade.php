<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
        $offset = ($foos->currentPage() - 1) * $foos->perPage();
        $total = $foos->total();
        @endphp

        @foreach($foos as $index => $foo)
        <tr>
            <td>{{ $index + $offset + 1 }}</td>
            <td>
                {!! link_to_route(
                    'foos.show',
                    __('Show'),
                    [$foo->id],
                    ['class' => 'btn btn-info pull-left']
                ) !!}

                {!! link_to_route(
                    'foos.edit',
                    __('Edit'),
                    [$foo->id],
                    ['class' => 'btn btn-primary pull-left']
                ) !!}

                {!! Form::open([
                    'route' => ['foos.destroy', $foo->id],
                    'method' => 'DELETE',
                    'onSubmit' => "return confirm('Are you sure wants to delete this record ?')",
                ]) !!}
                {!! Form::submit(__('Delete'), ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>