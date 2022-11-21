@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("All Voucher")}}</h1>
            <div class="title-actions">
                <a href="{{route('voucher.admin.create')}}" class="btn btn-primary">{{__("Add new voucher")}}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('admin/module/voucher/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            <option value="trash">{{__(" Move to Trash ")}}</option>
                            <option value="delete">{{__(" Delete ")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-left">
                <form method="post" action="{{url('/admin/module/voucher/')}} " class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    @csrf
                   
                    <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by Code')}}" class="form-control">
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <div class="header-status-control">
                <a href="{{ url("/admin/module/voucher") }}">{{__("All Voucher")}}
                    <span>({{ \Modules\Voucher\Models\Voucher::countReviewByStatus() }})</span> </a> -
               
                <a href="{{ url("/admin/module/voucher?status=trash") }}">{{__("Trash")}}
                    <span>({{ \Modules\Voucher\Models\Voucher::countReviewByStatus("trash") }})</span></a>
            </div>
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form class="bravo-form-item">
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60px"><input type="checkbox" class="check-all"></th>
                            <th width="150px"> {{ __('Property Name')}}</th>
                            <th> {{ __('Voucher Content')}}</th>
                            <th width="250px"> {{ __('Start Date')}}</th>
                            <th width="80px"> {{ __('End Date')}}</th>
                            <th width="100px"> {{ __('Status')}}</th>
                            <th width="140px"> {{ __('Submitted On')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($rows->total() > 0)
                            @foreach($rows as $row)
                                @php $service = $row->getService @endphp
                                <tr class="{{$row->status}}">
                                    <td><input type="checkbox" name="ids[]" class="check-item" value="{{$row->id}}">
                                    </td>
                                    <td>
                                       {{$service->title}}
                                    </td>
                                    <td>
                                        <strong>{{$row->code}}</strong>
                                       
                                       
                                    </td>
                                    <td>
                                    {{ display_date($row->start_date)}}
                                    </td>
                                    <td>
                                    {{ display_date($row->end_date)}}
                                    </td>
                                    <td>
                                       <span> {{(date($row->end_date) < date('y-m-d'))? 'Live' : 'Expired'}}<span>
                                    </td>
                                    <td>{{ display_datetime($row->updated_at)}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6">{{__("No data")}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection
