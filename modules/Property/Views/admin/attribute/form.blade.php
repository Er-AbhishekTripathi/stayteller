<div class="form-group">
    <label>{{__("Name")}}</label>
    <input type="text" value="{{$translation->name}}" placeholder="{{__("Attribute name")}}" name="name" class="form-control">
</div>
@if(is_default_lang())
    

    <div class="form-group">
        <label>{{__('Add Room Propertity')}}</label>
        <br>
        <label>
            <input type="checkbox" name="room_Property" @if($row->room_Property) checked @endif value="1"> {{__("Enable Room Property")}}
        </label>
        <br>
        <label>{{__('Amenities Enable')}}</label>
        <br>
        <label>
            <input type="checkbox" name="features_enable" @if($row->features_enable) checked @endif value="1"> {{__("Enable Amenities Enable")}}
        </label>
        <br>
        <label>{{__('Amenities Choice')}}</label>
        <br>
        <label>
            <input type="checkbox" name="features_choice" @if($row->features_choice) checked @endif value="1"> {{__("Enable Amenities Choice")}}
        </label>
    </div>

    <div class="form-group">
        <label>{{__('Hide in detail service')}}</label>
        <br>
        <label>
            <input type="checkbox" name="hide_in_single" @if($row->hide_in_single) checked @endif value="1"> {{__("Enable hide")}}
        </label>
    </div>
@endif