@props([
    'sidebar' => false,
])

@if($sidebar)
    <div class="">
        <img class="h-35 w-auto object-scale-down" src="{{ asset('images/logo.png') }}" alt="Probando">
    </div>
    
@else
    <img class="size-8" src="{{ asset('images/logo.png') }}" alt="Probando">
@endif
