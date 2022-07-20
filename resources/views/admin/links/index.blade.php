@extends('layouts.admin')
@section('content')
@can('link_generate_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.links.create") }}">
                {{ trans('global.create') }} {{ trans('cruds.links.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.links.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Comment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.links.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.ticket_id') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.ticket_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.cost') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.remarks') }}
                        </th>
                        <th>
                            {{ trans('cruds.links.fields.link') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $key => $link)
                        <tr data-entry-id="{{ $link->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $link->id ?? '' }}
                            </td>
                            <td>
                                {{ $link->tickets->id ?? '' }}
                            </td>
                            <td>
                                {{ $link->tickets->title ?? '' }}
                            </td>
                            <td>
                                {{ $link->tickets->user->name ?? '' }}
                            </td>
                            <td>
                                {{ $link->cost ?? '' }}
                            </td>
                            <td>
                                {{ $link->remarks ?? '' }}
                            </td>
                            <td>
                                {{ $link->payment_url }} <button class="btn btn-primary" onclick="copyURL('{{ $link->payment_url }}')">Click to Copy</button>
                            </td>
                            <td>
                                @can('link_generate_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.links.show', $link->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

{{--                                @can('link_generate_edit')--}}
{{--                                    <a class="btn btn-xs btn-info" href="{{ route('admin.links.edit', $link->id) }}">--}}
{{--                                        {{ trans('global.edit') }}--}}
{{--                                    </a>--}}
{{--                                @endcan--}}

                                @can('link_generate_delete')
                                    <form action="{{ route('admin.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    function copyURL(val) {
        /* Copy the text inside the text field */
        navigator.clipboard.writeText(val);
        /* Alert the copied text */
        alert("Copied the text: " + val);
    }

    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('link_generate_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.links.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-Comment:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
