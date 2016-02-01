@if (!empty($options['url']))
    <div id="uploader-{{ $id }}"
        data-options="{{ json_encode($options) }}"
        data-autostart="{{ intval($autoStart) }}"
        data-uploadbtn="uploader-{{ $id }}-upload">
        <div id="{{ $options['container'] }}" class="controls uploader">
            <div class="filelist"></div>
            <div class="upload-actions">
                {!! $buttons['pickFiles'] !!}
                @if (!$autoStart)
                    {!! $buttons['upload'] !!}
                @endif
            </div>
            <div class="uploaded row" style="margin-top: 10px"></div>
        </div>
    </div>
@else
    Missing URL option.
@endif