<div>
    <label for="{{$name}}">{{$label_text}}</label>   
    <select name="{{$name}}" id="{{$name}}" class="selectpicker" data-live-search="true">
        @foreach($options as $option)
            @php
                $value = $option;
                $text = $option;
            @endphp
            <option data-tokens="{{$value}}">{{$text}}</option>
        @endforeach
    </select>
</div>
