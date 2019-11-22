<th
    @if (array_key_exists('sortable', $field) && $field['sortable'])
    @if (!is_null($sortField) && $sortField == $field['name'])
    @if ($sortDir == 'asc')
    class="{{ trim($thClass . ' ' . (array_key_exists('sorted_class', $field) ? $field['sorted_class'] : "") . ' ' . (array_key_exists('sorted_asc_class', $field) ? $field['sorted_asc_class'] : "")) }}"
    @elseif ($sortDir == 'desc')
    class="{{ trim($thClass . ' ' . (array_key_exists('sorted_class', $field) ? $field['sorted_class'] : "") . ' ' . (array_key_exists('sorted_desc_class', $field) ? $field['sorted_desc_class'] : "")) }}"
    @else
    class="{{ trim($thClass . ' ' . (array_key_exists('sorted_class', $field) ? $field['sorted_class'] : "")) }}"
    @endif
    @else
    @if ($thClass)
    class="{{ $thClass }}"
    @endif
    @endif
    wire:click="$emit('sortColumn', {{ $colNum }})"
    @endif
>
    {{ $slot }}
</th>
