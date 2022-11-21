@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("All Rooms")}}</h1>
            <div class="title-actions">
                <a href="{{route('room.admin.create')}}" class="btn btn-primary">{{__("Add new Room")}}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('admin/module/review/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            <option value="approved">{{__(" Approved ")}}</option>
                            <option value="pending">{{__(" Pending ")}}</option>
                            <option value="spam">{{__(" Spam ")}}</option>
                            <option value="trash">{{__(" Move to Trash ")}}</option>
                            <option value="delete">{{__(" Delete ")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-left">
                <form method="post" action="{{url('/admin/module/review/')}} " class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    @csrf
                    @if(!empty($rows))
                        <?php
                        $user = !empty(Request()->vendor_id) ? App\User::find(Request()->vendor_id) : false;
                        \App\Helpers\AdminForm::select2('vendor_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url' => url('/admin/module/user/getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('-- Vendor --')
                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->name_or_email . ' (#' . $user->id . ')'
                        ] : false)
                        ?>
                    @endif
                    <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by title')}}" class="form-control">
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
          {{--  <div class="header-status-control">
                <a href="{{ url("/admin/module/review") }}">{{__("All Room")}}
                    <span>({{ \Modules\Review\Models\Review::countReviewByStatus() }})</span> </a> -
                <a href="{{ url("/admin/module/review?status=approved") }}">{{__("Approved")}}
                    <span>({{ \Modules\Review\Models\Review::countReviewByStatus("approved") }})</span></a> -
                <a href="{{ url("/admin/module/review?status=pending") }}">{{__("Pending")}}
                    <span>({{ \Modules\Review\Models\Review::countReviewByStatus("pending") }})</span></a> -
                <a href="{{ url("/admin/module/review?status=spam") }}">{{__("Spam")}}
                    <span>({{ \Modules\Review\Models\Review::countReviewByStatus("spam") }})</span></a> -
                <a href="{{ url("/admin/module/review?status=trash") }}">{{__("Trash")}}
                    <span>({{ \Modules\Review\Models\Review::countReviewByStatus("trash") }})</span></a>
            </div>--}}
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
                            <th> {{ __('Room Name')}}</th>
                            <th width="250px"> {{ __('No of Rooms')}}</th>
                            <th width="80px"> {{ __('Action')}}</th>
                            <th width="100px"> {{ __('Status')}}</th>
                            <th width="140px"> {{ __('Submitted On')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($rows->total() > 0)
                       
                            @foreach($rows as $row)
                            @php
                            $date  = date('Y-m-d');
                           
                            $availabiltyroom = \Modules\Room\Models\Availability::where('room_id',$row->roomid)->where('start_date',$date)->first();
                           
                            @endphp
                          
                            
                              
                                <tr class="{{$row->status}}">
                                    <td><input type="checkbox" name="ids[]" class="check-item" value="{{$row->roomid}}">
                                    </td>
                                    <td>
                                    {{$row->title}}
                                    </td>
                                    <td>{{$row->name}}</td>
                                    <td>
                                    <p>{{$availabiltyroom->available_room ?? 0}}</p>
                                    </td>
                                    <td>
                                    <a href="{{route('room.admin.edit',['id'=>$row->roomid])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> {{__('Edit')}}</a>
                                                
                                    </td>
                                    <td>
                                    <a href="{{route('room.admin.vacancyupdate',['id'=>$row->roomid])}}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> {{__('vacancy update')}}</a>
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
