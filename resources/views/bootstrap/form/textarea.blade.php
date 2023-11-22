<div class="{{isset($box_width)?$box_width:''}}">
  <label for="{{$id}}">{{$label_text}}</label>
  <textarea id="{{$id}}" name="{{$name}}" class="form-control" rows="3">{{isset($value)?$value:''}}</textarea>
</div>
