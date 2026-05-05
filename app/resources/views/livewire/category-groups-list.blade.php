<div
    x-data="{
        triggerLoad() {
            if ($wire.hasMore && !$wire.isLoadingMore) {
                $wire.loadMore();
            }
        },
        checkBottomLoader() {
            const loader = this.$refs.bottomLoader;
            if (!loader) return;
            const rect = loader.getBoundingClientRect();
            if (rect.top <= window.innerHeight + 60) {
                this.triggerLoad();
            }
        }
    }"
    x-init="checkBottomLoader()"
    x-on:scroll.window.throttle.250ms="checkBottomLoader()"
>
    <div class="row ps-2 pe-1">
        @forelse($spaces as $space)
            <div class="col-md-6 col-sm-6 pe-2 ps-2">
                <div
                    class="card d-block border-0 shadow-xss rounded-3 overflow-hidden mb-3"
                    style="cursor:pointer;"
                    onclick="window.location='{{ route('groups.show', $space->id) }}'"
                >
                    <div class="card-body position-relative h100 bg-image-cover bg-image-center" style="background-image: url('{{ asset('images/bb-16.png') }}');"></div>
                    <div class="card-body d-block w-100 pl-10 pe-4 pb-4 pt-0 text-left position-relative">
                        <figure class="avatar position-absolute w75 z-index-1" style="top:-40px; left: 15px;">
                            <img src="{{ $space->profile_image_path ? asset('storage/' . $space->profile_image_path) : asset('images/user-12.png') }}" alt="image" class="float-right p-1 bg-white rounded-circle w-100">
                        </figure>
                        <div class="clearfix"></div>
                        <h4 class="fw-700 font-xsss mt-3 mb-1">
                            <a href="{{ route('groups.show', $space->id) }}" class="text-dark" onclick="event.stopPropagation();">{{ $space->name }}</a>
                        </h4>
                        <p class="fw-500 font-xsssss text-grey-500 mt-0 mb-3">{{ \Illuminate\Support\Str::limit($space->description ?? 'No description yet.', 60) }}</p>
                        <span class="position-absolute right-15 top-0 d-flex align-items-center">
                            <button
                                type="button"
                                wire:click="follow('{{ $space->id }}')"
                                wire:loading.attr="disabled"
                                wire:target="follow('{{ $space->id }}')"
                                onclick="event.stopPropagation();"
                                class="btn-round-md border-0 {{ in_array($space->id, $followedCategoryIds, true) ? 'bg-success' : 'bg-primary-gradiant' }} text-white"
                                title="{{ in_array($space->id, $followedCategoryIds, true) ? 'Following' : __('Follow space') }}"
                                {{ in_array($space->id, $followedCategoryIds, true) ? 'disabled' : '' }}
                            >
                                <i class="feather-user-plus font-sm"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12 pe-2 ps-2">
                <div class="card w-100 text-center shadow-xss rounded-xxl border-0 p-4 mb-3 mt-0">
                    @if($followingOnly ?? false)
                        <h4 class="fw-700 text-grey-900 font-xssss mb-1">{{ __('No spaces yet') }}</h4>
                        <p class="fw-500 text-grey-500 font-xssss mb-3">{{ __("You haven't joined any spaces yet. Browse spaces to follow some.") }}</p>
                        <a href="{{ route('groups.index') }}" class="fw-600 font-xssss text-primary">{{ __('Browse all spaces') }}</a>
                    @else
                        <h4 class="fw-700 text-grey-900 font-xssss mb-1">{{ __('No spaces yet') }}</h4>
                        <p class="fw-500 text-grey-500 font-xssss mb-0">{{ __("Use \"Create new space\" to add your first one.") }}</p>
                    @endif
                </div>
            </div>
        @endforelse

        @if($hasMore)
            <div class="col-md-12 pe-2 ps-2" x-ref="bottomLoader">
                <div class="card w-100 text-center shadow-xss rounded-xxl border-0 p-4 mb-3 mt-0">
                    <div class="snippet mt-2 ms-auto me-auto" data-title=".dot-typing">
                        <div class="stage">
                            <div class="dot-typing"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
