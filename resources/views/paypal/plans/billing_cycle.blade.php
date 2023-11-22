@for($i=1; $i<=3; $i++)
<fieldset>
    <legend>Billing Cycle {{$i}}</legend>
    
    @include('bootstrap.form.control',
    ["type"=>"hidden","value"=>"{$i}", "name"=>"sequence{$i}", "id"=>"sequence{$i}", "label_text"=>"Sequence Order"])
    
    @include('bootstrap.form.radio',
    [
        'radio_group_label'=>'Is this cycle used?',
        'name'=>"cycle{$i}_used",
        'options'=>[
            (object)['label_text'=>'Yes','value'=>'TRUE', "is_checked"=>FALSE],
            (object)['label_text'=>'No','value'=>'FALSE', "is_checked"=>TRUE],
        ]
    ])
    
    @include('bootstrap.form.radio',
    [
        'radio_group_label'=>'Billing Cycle Type',
        'name'=>"cycle{$i}_tenure",
        'options'=>[
            (object)['label_text'=>'Trial Period','value'=>'TRIAL', 'is_checked'=>FALSE],
            (object)['label_text'=>'Active Plan Period','value'=>'REGULAR', 'is_checked'=>FALSE],
        ]
    ])
    @include('bootstrap.form.control',
    ["type"=>"text","value"=>"0.00", "name"=>"cycle{$i}_price", "id"=>"cycle{$i}_price", "label_text"=>"Cycle Price"])
    
    @include('bootstrap.form.control',
    ["type"=>"number","value"=>"1", "name"=>"cycle{$i}_repeat", "id"=>"cycle{$i}_repeat", "label_text"=>"Cycle Iterations (Collect X payments before ending this step)"])
    
    @include('bootstrap.form.control',
    ["type"=>"number","value"=>"1", "name"=>"cycle{$i}_length", "id"=>"cycle{$i}_length", "label_text"=>"Cycle Length"])
    @include('bootstrap.form.control',
    ["type"=>"text","value"=>"WEEK", "name"=>"cycle{$i}_unit", "id"=>"cycle{$i}_unit", "label_text"=>"Cycle Length Unit"])
    
    
</fieldset>
@endfor
