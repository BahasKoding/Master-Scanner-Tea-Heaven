@if (isset($items) && !empty($items))
    <div class="page-header-title">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @foreach ($items as $name => $url)
                @if ($loop->last)
                    <li class="breadcrumb-item active">{{ $name }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $url }}">{{ $name }}</a></li>
                @endif
            @endforeach
        </ol>
    </div>
@endif
