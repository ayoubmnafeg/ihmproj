@props(['attachments'])

@php
    $photoCount = $attachments->count();
    $displayAttachments = $photoCount >= 4 ? $attachments->take(4) : $attachments;
    $remainingPhotoCount = max($photoCount - 4, 0);
    $photoLayoutVariant = $photoCount >= 4 ? '4plus' : (string) $photoCount;
@endphp

@if($photoCount > 0)
    <div class="d-flex align-items-center mt-2 mb-2">
        <i class="feather-image text-grey-500 me-2 font-xss"></i>
        <span class="fw-600 text-grey-700 font-xssss">{{ $photoCount }} {{ $photoCount === 1 ? 'Photo' : 'Photos' }}</span>
    </div>

    <div class="mt-2 post-photos-grid photos-count-{{ $photoLayoutVariant }}">
        @foreach($displayAttachments as $index => $attachment)
            @php $isLastShownWithOverlay = $remainingPhotoCount > 0 && $index === 3; @endphp
            <div class="post-photo-item {{ $isLastShownWithOverlay ? 'has-more-overlay' : '' }}">
                <img src="{{ $attachment->url }}" alt="attachment" class="w-100 h-100">
                @if($isLastShownWithOverlay)
                    <span class="post-photo-more-count">+{{ $remainingPhotoCount }}</span>
                @endif
            </div>
        @endforeach
    </div>
@endif
