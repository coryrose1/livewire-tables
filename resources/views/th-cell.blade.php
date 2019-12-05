<th
    @if (array_key_exists('sortable', $field) && $field['sortable'])
    @if (!is_null($sortField) && $sortField == $field['name'])
    class="{{ trim($field['header_class'] . ' ' . $css['th'] . ' ' . $css['sorted']) }}"
    @else
    class="{{ trim($field['header_class'] . ' ' . $css['th']) }}"
    @endif
    wire:click="$emit('sortColumn', {{ $colNum }})"
    @else
    class="{{ trim($field['header_class'] . ' ' . $css['th']) }}"
    @endif
>
    {{ $slot }}
    @if (array_key_exists('sortable', $field) && $field['sortable'] && !is_null($sortField) && $sortField == $field['name'])
        @if ($sortDir == 'asc')
            <span style="float: right">&#9650;</span>
        @elseif ($sortDir == 'desc')
            <span style="float: right">&#9660;</span>
        @endif
    @endif
</th>
