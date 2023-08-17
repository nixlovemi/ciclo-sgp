@php
/*
View variables:
===============
    - $Quote: ?Quote
    - $disabled: bool
    - $type: string [view | edit | add]
*/

$disabled = $disabled ?? true;
$type = $type ?? 'view';
@endphp

<div id="quote-show"> 
    @include('quote.partials.formRows', [
        'Quote' => $Quote,
        'disabled' => $disabled,
        'type' => $type
    ])
</div>