@extends('layouts.admin')
@section('content')
@can('bill_generate_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.bills.create") }}">
                {{ trans('global.create') }} {{ trans('cruds.bills.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.bills.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Comment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.bills.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.bills.fields.ticket_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.bills.fields.bill_cost') }}
                        </th>
                        <th>
                            {{ trans('cruds.bills.fields.remaining_cost') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $key => $bill)
                        <tr data-entry-id="{{ $bill->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $bill->id ?? '' }}
                            </td>
                            <td>
                                {{ $bill->ticket->title ?? '' }}
                            </td>
                            <td>
                                {{ $bill->bill_cost ?? '' }}
                            </td>
                            <td>
                                {{ $bill->remaining_cost ?? '' }}
                            </td>
                            <td>
                                @can('bill_generate_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.bills.show', $bill->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

{{--                                @can('bill_generate_edit')--}}
{{--                                    <a class="btn btn-xs btn-info" href="{{ route('admin.bills.edit', $bill->id) }}">--}}
{{--                                        {{ trans('global.edit') }}--}}
{{--                                    </a>--}}
{{--                                @endcan--}}

                                @can('bill_generate_delete')
                                    <form action="{{ route('admin.bills.destroy', $bill->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
        const sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = val; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
        /* Alert the copied text */
        alert('Copy Text '+ val);
    }

    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('bill_generate_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.bills.massDestroy') }}",
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
