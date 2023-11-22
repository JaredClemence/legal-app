<div class="{{isset($box_width)?$box_width:''}}">
    <label for="{{$id}}">{{$label_text}}</label>
    <input id="{{$id}}" name="{{$name}}" value="{{isset($value)?$value:''}}" type="{{isset($type)?$type:'text'}}" class="form-control" />
</div>
