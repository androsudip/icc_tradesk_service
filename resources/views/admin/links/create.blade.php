@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.links.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.links.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('ticket_id') ? 'has-error' : '' }}">
                <label for="ticket_id">{{ trans('cruds.links.fields.ticket_name') }}*</label>
                <select name="ticket_id" id="ticket_id" class="form-control select2 ticket_id_change" required>
                    <option value="" selected>Select Ticket</option>
                    @foreach($tickets as $id => $ticket)
                        <option value="{{ $ticket->id }}" data-cost="{{ $ticket->service->cost }}" {{ (old('ticket_id') == $ticket->id) ? 'selected' : '' }}>{{ $ticket->title .' - '. $ticket->id }}</option>
                    @endforeach
                </select>
                @if($errors->has('ticket_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('ticket_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.links.fields.ticket_name_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('cost') ? 'has-error' : '' }}">
                <label for="cost">{{ trans('cruds.links.fields.cost') }}*</label>
                <input type="number" id="cost" name="cost" class="form-control cost_input" value="" required>
                @if($errors->has('cost'))
                    <em class="invalid-feedback">
                        {{ $errors->first('cost') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.links.fields.cost_helper') }}
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.create') }}">
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        function setCostValue(val){
            $(".cost_input").val(val);
        }
        let cost = $(".ticket_id_change").find(':selected').data('cost');
        setCostValue(cost);
        $(document).ready(function () {
           $(".ticket_id_change").on('change',function () {
                let cost = $(this).find(':selected').data('cost');
               setCostValue(cost);
           })
        });
    </script>
@endsection
