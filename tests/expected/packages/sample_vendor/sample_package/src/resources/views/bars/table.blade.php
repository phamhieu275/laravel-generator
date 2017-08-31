<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Content</th>
            <th>Publish Date</th>
            <th>Author Id</th>
            <th>Rate</th>
            <th>Score</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
        $offset = ($bars->currentPage() - 1) * $bars->perPage();
        $total = $bars->total();
        @endphp

        @foreach($bars as $index => $bar)
        <tr>
            <td>{{ $index + $offset + 1 }}</td>
            <td>{!! $bar->name !!}</td>
            <td>{!! $bar->content !!}</td>
            <td>{!! $bar->publish_date !!}</td>
            <td>{!! $bar->author_id !!}</td>
            <td>{!! $bar->rate !!}</td>
            <td>{!! $bar->score !!}</td>
            <td>
                {!! link_to_route(
                    'sample_package.bars.show',
                    __('Show'),
                    [$bar->id],
                    ['class' => 'btn btn-info pull-left']
                ) !!}

                {!! link_to_route(
                    'sample_package.bars.edit',
                    __('Edit'),
                    [$bar->id],
                    ['class' => 'btn btn-primary pull-left']
                ) !!}

                {!! Form::open([
                    'route' => ['sample_package.bars.destroy', $bar->id],
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