@pushonce('above_css')
<!-- JQUERY UI -->
<link href="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce

@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    $(document).on('datePickersInstance', function() {
        $('.datepicker').datepicker({
            dateFormat: "dd.mm.yy"
        });
    });
    $(document).trigger('datePickersInstance');
</script>
@endpushonceOnReady

@php
    $translationsDefault = trans('admin/periodable/periodable');
    $langs = isset($translations)?
        transOrOther($translations, 'admin/periodable/periodable', array_keys($translationsDefault)) : $translationsDefault;
    $periodable_bag = $periodable_bag ?? 'period';
    $defaults = [];
    $defaults['start_date'] = '';
    $defaults['start_hour'] = 0;
    $defaults['start_minute'] = 0;
    if(isset($default_start) && ($default_start instanceof \DateTime)) {
        $defaults['start_date'] = $default_start->format('d.m.Y');
        $defaults['start_hour'] = $default_start->format('H');
        $defaults['start_minute'] = $default_start->format('i');
    }
    $defaults['end_date'] = '';
    $defaults['end_hour'] = 0;
    $defaults['end_minute'] = 0;
    if(isset($default_end) && ($default_end instanceof \DateTime)) {
        $defaults['end_date'] = $default_end->format('d.m.Y');
        $defaults['end_hour'] = $default_end->format('H');
        $defaults['end_minute'] = $default_end->format('i');
    }
@endphp


<fieldset>
    <div class="form-inline mb-3 mt-2">
        <div class="form-group mr-2">
            <label for='{{$inputBag}}[{{$periodable_bag}}][start_at]' class="mr-2">{{$langs['period']}}:</label>
        </div>
        <div class="form-group mr-2">
            <input class="form-control datepicker @if($errors->$inputBag->has($periodable_bag.'.start_at.date')) is-invalid @endif"
                   name='{{$inputBag}}[{{$periodable_bag}}][start_at][date]'
                   id='{{$inputBag}}[{{$periodable_bag}}][start_at][date]'
                   placeholder="{{$langs['period_date']}}"
                   value="{{ old($inputBag.'.'.$periodable_bag.'.start_at.date', (($object && $object->start_at)? $object->start_at->format('d.m.Y') : $defaults['start_date'])) }}"
            />
        </div>
        @php
            $sPFhour = old($inputBag.'.'.$periodable_bag.'.start_at.hour', (($object && $object->start_at)? $object->start_at->format('H') : $defaults['start_hour']));
            $sPThour = old($inputBag.'.'.$periodable_bag.'.end_at.hour', (($object && $object->end_at)? $object->end_at->format('H') : $defaults['end_hour']));
            $sPFminute = old($inputBag.'.'.$periodable_bag.'.start_at.minutes', (($object && $object->start_at)? $object->start_at->format('i') : $defaults['start_minute']));
            $sPTminute = old($inputBag.'.'.$periodable_bag.'.end_at.minutes', (($object && $object->end_at)? $object->end_at->format('i') : $defaults['end_minute']));
        @endphp
        <div class="form-group mr-2">
            <select class="form-control"
                    name='{{$inputBag}}[{{$periodable_bag}}][start_at][hour]'
                    id='{{$inputBag}}[{{$periodable_bag}}][start_at][hour]'>
                @for($i=0;$i<24;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'
                            @if($sPFhour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group mr-2">&nbsp;:&nbsp;</div>
        <div class="form-group mx-2">
            <select class="form-control"
                    name='{{$inputBag}}[{{$periodable_bag}}][start_at][minutes]'
                    id='{{$inputBag}}[{{$periodable_bag}}][start_at][minutes]'>
                @for($i=0;$i<60;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'
                            @if($sPFminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group mr-2">&nbsp;{{$langs['period_to']}}&nbsp;</div>
        <div class="form-group mr-2">
            <input class="form-control datepicker @if($errors->$inputBag->has($periodable_bag.'.end_at.date')) is-invalid @endif"
                   name='{{$inputBag}}[{{$periodable_bag}}][end_at][date]'
                   id='{{$inputBag}}[{{$periodable_bag}}][end_at][date]'
                   placeholder="{{$langs['period_date']}}"
                   value="{{ old($inputBag.'.'.$periodable_bag.'.end_at.date', (($object && $object->end_at)? $object->end_at->format('d.m.Y') : $defaults['end_date'])) }}"
            />
        </div>
        <div class="form-group mr-2">
            <select class="form-control"
                    name='{{$inputBag}}[{{$periodable_bag}}][end_at][hour]'
                    id='{{$inputBag}}[{{$periodable_bag}}][end_at][hour]'
            >
                @for($i=0;$i<24;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'
                            @if($sPThour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group mr-2">&nbsp;:&nbsp;</div>
        <div class="form-group ml-2">
            <select class="form-control"
                    name='{{$inputBag}}[{{$periodable_bag}}][end_at][minutes]'
                    id='{{$inputBag}}[{{$periodable_bag}}][end_at][minutes]'>
                @for($i=0;$i<60;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'
                            @if($sPTminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>
    </div>
</fieldset>
