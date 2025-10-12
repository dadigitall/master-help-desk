<!-- Breadcrumb Navigation -->
<nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
    @foreach($items as $index => $item)
        @if($index > 0)
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
        @endif
        
        @if($loop->last)
            <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" 
               class="flex items-center space-x-1 text-gray-500 hover:text-purple-600 transition-colors">
                @if(isset($item['icon']))
                    <i class="{{ $item['icon'] }}"></i>
                @endif
                <span>{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
