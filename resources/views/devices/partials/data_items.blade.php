@foreach ($data as $item)
<div class="border rounded p-2 mb-2 small">
    <strong>{{ ucfirst($item->type) }}</strong> — {{ $item->value }} {{ $item->unit }}
    <br>
    <code>{{ json_encode($item->payload) }}</code>
    <div class="text-muted">{{ $item->observed_at }}</div>
</div>
@endforeach
