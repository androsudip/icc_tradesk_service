@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.bills.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.bills.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('ticket_id') ? 'has-error' : '' }}">
                <label for="ticket_id">{{ trans('cruds.bills.fields.ticket_name') }}*</label>
                <select name="ticket_id" id="ticket_id" class="form-control select2 ticket_id_change" required>
                    <option value="" selected>Select Ticket</option>
                    @foreach($tickets as $id => $ticket)
                        <option value="{{ $ticket->id }}" data-id="{{ $ticket->id }}"{{ (old('ticket_id') == $ticket->id) ? 'selected' : '' }}>{{ $ticket->user->name .' - '. $ticket->title .' - '. $ticket->id }}</option>
                    @endforeach
                </select>
                @if($errors->has('ticket_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('ticket_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.bills.fields.ticket_name_helper') }}
                </p>
            </div>

            <div class="form-group" id="service_div">
                <label>Services</label>

            </div>

            <div>
                <input class="btn btn-danger btn_submit" type="submit" value="{{ trans('cruds.bills.title_singular') }} Generate">
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        function setCostValue(services){
            let html = "";
            let serviceIds = '';
            for (let i = 0; i < services.length; i++) {
                serviceIds = serviceIds +','+ services[i].id;
                html += '<input type="hidden" name="service_ids" value="'+serviceIds+'" >' +
                    '<div class="border row">' +
                            '<div class="col-2 form-group">' +
                                '<label for="service">{{ trans('cruds.bills.fields.service') }}</label>'+
                                '<input type="service" id="service" name="service_'+services[i].id+'" class="form-control service_input" value="'+ services[i].name +'" disabled>'+
                            '</div>'+

                            '<div class="col-2 form-group">'+
                                '<label for="cost">{{ trans('cruds.bills.fields.cost') }}</label>'+
                                '<input type="number" name="cost_'+services[i].id+'" class="form-control cost_input" id="'+ "cost_input_"+services[i].id +'" value="'+ services[i].cost +'" min="100" disabled required>'+
                            '</div>'+

                            '<div class="col-2 form-group remarks_div" id="'+ "remarks_div_"+services[i].id +'" style="display: none;">'+
                                '<label for="remarks">{{ trans('cruds.bills.fields.remarks') }}</label>'+
                                '<input type="text" id="remarks" name="remarks_'+services[i].id+'" class="form-control remarks_input" value="" >'+
                            '</div>'+

                            '<div class="col-2 form-group my-auto btn_div">'+
                                '<button class="btn btn-primary btn_edit" onclick="showRemarks('+services[i].id+')" type="button" value="'+ services[i].id +'">{{ trans('global.edit') }} Service {{ trans('cruds.bills.fields.cost') }}</button>'+
                            '</div>'+
                        '</div>';
            }
            $("#service_div").append(html);
        }

        function showRemarks(id){
            $("#cost_input_"+id).attr('disabled',false);
            $("#remarks_div_"+id).show();
        }

        function getServices(id){
            $.ajax({
                url: "{{ route('admin.tickets.getServices') }}",
                data:{ id:id },
                cache: false,
                success: function(html){
                    setCostValue(html);
                }
            });
        }
        $(document).ready(function () {
           $(".ticket_id_change").on('change',function () {
                let id = $(this).find(':selected').data('id');
               getServices(id);
           })
        });
    </script>
@endsection
