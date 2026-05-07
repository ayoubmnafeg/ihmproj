@props([
    'publicationId',
    'parentId' => null,
    'placeholder' => 'Add a comment...',
    'formId' => null,
    'hidden' => false,
    'wrapperClass' => '',
    'wireSubmit' => null,
    'wireModel' => null,
])

<form
    @if($formId) id="{{ $formId }}" @endif
    @if($wireSubmit)
        wire:submit="{{ $wireSubmit }}"
    @else
        method="POST"
        action="{{ route('comments.store', $publicationId) }}"
    @endif
    class="d-flex {{ $wrapperClass }} {{ $hidden ? 'd-none' : '' }}"
>
    @if(!$wireSubmit)
        @csrf
    @endif
    @if($parentId && !$wireSubmit)
        <input type="hidden" name="parent_id" value="{{ $parentId }}">
    @endif
    <figure class="avatar me-2 mb-0"><img src="{{ asset('images/profile-4.png') }}" alt="image" class="shadow-sm rounded-circle w30"></figure>
    <div class="form-group mb-0 flex-fill comment-input-wrap">
        <input
            type="text"
            @if($wireModel)
                wire:model.live="{{ $wireModel }}"
            @else
                name="text"
            @endif
            placeholder="{{ $placeholder }}"
            class="form-control rounded-xl bg-greylight border-0 font-xssss fw-500 ps-3 pe-5"
            required
        >
        <button type="submit" class="comment-send-btn"><i class="feather-send"></i></button>
    </div>
</form>
