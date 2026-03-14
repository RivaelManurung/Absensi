@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
])

<div class="space-y-1">
    <x-input
        :label="$label"
        :name="$name"
        :type="$type"
        :value="$value"
        :placeholder="$placeholder"
        {{ $attributes }}
    />

    @error($name)
        <p class="text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
