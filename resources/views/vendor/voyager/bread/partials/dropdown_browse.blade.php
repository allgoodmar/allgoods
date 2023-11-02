@if($row->field == 'status')
    <div class="current-status-container">
        <div class="current-status-text">{!! $row->details->options->{$data->{$row->field}} ?? '' !!}</div>
        @if (empty($row->details->no_change_status_buttons))
        <div class="btn-group">
            <button class="btn btn-sm btn-success change-status-btn @if($data->{$row->field} == 1) disabled @endif" data-target="{{ route('voyager.status.activate', ['table' => $dataType->name, 'id' => ($data->id ?? 0) ]) }}" data-text="{{ $row->details->options->{1} }}"><i class="voyager-check"></i></button>

            <button class="btn btn-sm btn-danger change-status-btn @if($data->{$row->field} == 0) disabled @endif" data-target="{{ route('voyager.status.deactivate', ['table' => $dataType->name, 'id' => ($data->id ?? 0) ]) }}" data-text="{{ $row->details->options->{0} }}"><i class="voyager-x"></i></button>
        </div>
        @endif
    </div>
@else
    {!! $row->details->options->{$data->{$row->field}} ?? '' !!}
@endif
