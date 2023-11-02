@if($row->field == 'status')
    <div class="current-status-container">
        <div class="current-status-text">
            @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                @if($data->{$row->field})
                    {{ $row->details->on }}
                @else
                    {{ $row->details->off }}
                @endif
            @else
                {{ $data->{$row->field} }}
            @endif
        </div>
        <div class="btn-group">
            <button class="btn btn-sm btn-success change-status-btn @if($data->{$row->field} == 1) disabled @endif" data-target="{{ route('voyager.status.activate', ['table' => $dataType->name, 'id' => ($data->id ?? 0) ]) }}" data-text="{{ $row->details->on ?? 'Да' }}"><i class="voyager-check"></i></button>

            <button class="btn btn-sm btn-danger change-status-btn @if($data->{$row->field} == 0) disabled @endif" data-target="{{ route('voyager.status.deactivate', ['table' => $dataType->name, 'id' => ($data->id ?? 0) ]) }}" data-text="{{ $row->details->off ?? 'Нет' }}"><i class="voyager-x"></i></button>
        </div>
    </div>
@else
    @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
        @if($data->{$row->field})
            <span class="label label-info">{{ $row->details->on }}</span>
        @else
            <span class="label label-primary">{{ $row->details->off }}</span>
        @endif
    @else
        {{ $data->{$row->field} }}
    @endif
@endif
