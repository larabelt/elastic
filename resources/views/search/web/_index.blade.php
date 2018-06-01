<h3><a href="{{ $item->defaultUrl }}">{{ $item->name }}</a> <span class="pull-right>"><span class="badge">{{ $item->getMorphClass() }}</span></span></h3>

@if($item->meta_description)
    <div>
        <p>{{ $item->meta_description }}</p>
    </div>
@endif

<hr/>