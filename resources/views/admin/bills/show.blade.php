@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bills.title') }}
    </div>

    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.bills.fields.id') }}
                        </th>
                        <td>
                            {{ $bills->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bills.fields.ticket_name') }}
                        </th>
                        <td>
                            {{ $bills->ticket->title }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bills.fields.bill_cost') }}
                        </th>
                        <td>
                            {{ $bills->bill_cost }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bills.fields.remaining_cost') }}
                        </th>
                        <td>
                            {{ $bills->remaining_cost }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a class="btn btn-default my-2" href="{{ route('admin.bills.index') }}">
            {{ trans('global.back_to_list') }}
        </a>

{{--        <a href="{{ route('admin.bills.edit', $bills->id) }}" class="btn btn-primary">--}}
{{--            @lang('global.edit') @lang('cruds.bills.title_singular')--}}
{{--        </a>--}}

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
    </div>
</div>
@endsection
