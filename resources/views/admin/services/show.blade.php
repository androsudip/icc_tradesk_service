@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.services.title') }}
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.id') }}
                        </th>
                        <td>
                            {{ $service->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.name') }}
                        </th>
                        <td>
                            {{ $service->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.description') }}
                        </th>
                        <td>
                            {!! $service->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.service_type') }}
                        </th>
                        <td>{{ $service->service_type }}</td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.attachments') }}
                        </th>
                        <th>
                            @foreach($service->attachments as $attachment)
                                <a href="{{ $attachment->getUrl() }}" target="_blank">{{ $attachment->file_name }}</a>
                            @endforeach
                        </th>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.category_name') }}
                        </th>
                        <td>{{ $service->category->name }}</td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.cost') }}
                        </th>
                        <td>{{ $service->cost }}</td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.contact_info') }}
                        </th>
                        <td>{!! $service->contact_info !!}</td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.services.fields.status') }}
                        </th>
                        <td>{{ $service->status == 1 ? 'Active' : 'In Active' }}</td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>

        <nav class="mb-3">
            <div class="nav nav-tabs">

            </div>
        </nav>
        <div class="tab-content">

        </div>
    </div>
</div>
@endsection
