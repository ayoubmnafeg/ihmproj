@extends('layouts.app')

@section('title', 'Groups')

@section('content')
<div class="row">
    <div class="col-xl-12">
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3">
            <div class="card-body d-flex align-items-center p-0">
                <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">Group</h2>
                <div class="search-form-2 ms-auto">
                    <i class="ti-search font-xss"></i>
                    <input type="text" class="form-control text-grey-500 mb-0 bg-greylight theme-dark-bg border-0" placeholder="Search here.">
                </div>
                <a href="#" class="btn-round-md ms-2 bg-greylight theme-dark-bg rounded-3"><i class="feather-filter font-xss text-grey-500"></i></a>
            </div>
        </div>

        <livewire:category-groups-list />
    </div>
</div>
@endsection

@section('left_sidebar_extras')
<div class="nav-wrap bg-white bg-transparent-card rounded-xxl shadow-xss mb-2">
    <div class="card-body p-4">
        <h4 class="fw-700 mb-2 font-xssss text-grey-900">Create Category</h4>
        <p class="fw-500 text-grey-500 font-xssss mb-3">Create a new category to organize groups.</p>
        <button type="button" id="open-create-group-modal" class="p-2 lh-24 w-100 bg-current border-0 text-white text-center font-xssss fw-700 ls-2 rounded-xl">
            Create New Category
        </button>
    </div>
</div>
@endsection

@push('modals')
<div id="create-group-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display:none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-3 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-700 font-sm mb-0">Create New Category</h4>
                <button type="button" id="close-create-group-modal" class="btn-close" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-group-form" method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="group-name" class="fw-600 font-xssss text-grey-700 mb-1">Category Name</label>
                        <input id="group-name" name="name" type="text" class="form-control" placeholder="Enter category name" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="group-description" class="fw-600 font-xssss text-grey-700 mb-1">Description</label>
                        <textarea id="group-description" name="description" class="form-control" rows="4" placeholder="Describe your category">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="group-image" class="fw-600 font-xssss text-grey-700 mb-1">Profile Image</label>
                        <input id="group-image" name="profile_image" type="file" accept="image/*" class="form-control">
                    </div>

                    <div class="d-flex pt-2">
                        <button type="button" id="cancel-create-group-modal" class="bg-greylight border-0 text-grey-700 fw-600 font-xssss rounded-xl p-2 ps-4 pe-4 me-2">Cancel</button>
                        <button type="submit" class="bg-current border-0 text-white fw-700 font-xssss rounded-xl p-2 ps-4 pe-4">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@section('scripts')
<script>
(function () {
    var modal = document.getElementById('create-group-modal');
    var openBtn = document.getElementById('open-create-group-modal');
    var closeBtn = document.getElementById('close-create-group-modal');
    var cancelBtn = document.getElementById('cancel-create-group-modal');

    function openModal() {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    @if ($errors->any())
        openModal();
    @endif
})();
</script>
@endsection
