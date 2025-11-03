@php
    $groups = config('site.data_types.groups', []); 
@endphp

<div>
    <option value="">Select one</option>
    @foreach($groups as $groupLabel => $types)
        <optgroup label="{{ $groupLabel }}">
            @foreach($types as $laravelType => $sqlType)
                @php($displayName = strtoupper($sqlType))
                <option value="{{ $laravelType }}">{{ $displayName }}</option>
            @endforeach
        </optgroup>
    @endforeach
</div>
