<div class="form-group">
    <label>{{ __('Title')}}</label>
    <input type="text" value="{{ $translation->title ?? 'New Post' }}" placeholder="News title" name="title" class="form-control">
</div>

<div class="form-group d-none">
    <label>{{ __('Number of beds')}}</label>
    <input type="text" value="{{ $row->bed }}" placeholder="beds" name="bed" class="form-control">
</div>


<div class="form-group d-none">
    <label>{{ __('Number of baths')}}</label>
    <input type="text" value="{{ $row->bath }}" placeholder="baths" name="bath" class="form-control">
</div>


<div class="form-group d-none">
    <label>{{ __('Acreage')}}</label>
    <input type="text" value="{{ $row->acreage }}" placeholder="acreage" name="acreage" class="form-control">
</div>

<div class="form-group d-none">
    <label>{{ __('Price')}}</label>
    <input type="text" value="{{ $row->price }}" placeholder="price" name="price" class="form-control">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Content')}} </label>
    <div class="">
        <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{$translation->content}}</textarea>
    </div>
</div>
 