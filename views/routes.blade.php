<div {!! admin_attrs($group_attrs) !!}>
    <label class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        @foreach($modules as $name => $module)
            <div class="role-access row">

                @if(is_string($module))
                    <div class="icheck-@color all-routes col-2">
                        <input type="checkbox" id="@id"
                            {{ in_array($module, $value ?: []) ? 'checked' : '' }}
                            name="routes[]"
                               value="{{ $module }}"
                        />
                        <label for="@id">&nbsp;{{ $name }}&nbsp;&nbsp;</label>
                    </div>
                @else
                    <div class="icheck-@color all-routes col-2">
                        <input type="checkbox" id="@id"
                            {{ count(array_intersect($value ?: [], array_column($module, 'value'))) == count($module) ? 'checked' : '' }}
                        />
                        <label for="@id">&nbsp;{{ $name }}&nbsp;&nbsp;</label>
                    </div>

                    <div class="col-10 border-left">
                    @foreach($module as $access)
                    <span class="icheck-@color route mt-2">
                        <input type="checkbox" id="@id" name="routes[]" value="{{ $access['value'] }}"
                            {{ in_array($access['value'], $value ?: []) ?'checked':'' }}/>
                        <label for="@id" class="mt-2">&nbsp;{{ $access['label'] }}&nbsp;&nbsp;</label>
                    </span>
                    @endforeach

                    </div>
                @endif
            </div>
            <hr class="mt-2 mb-2">
        @endforeach
        <input type="hidden" name="routes[]">
    </div>
</div>

<script>
    $('.role-access .all-routes input').change(function () {
        $(this).parents('.role-access')
            .find('.route input')
            .prop('checked', this.checked);
    });

    $('.role-access .route input').change(function () {
        if (!this.checked) {
            $(this).parents('.role-access')
                .find('.all-routes input')
                .prop('checked', false);
        }
    });
</script>
