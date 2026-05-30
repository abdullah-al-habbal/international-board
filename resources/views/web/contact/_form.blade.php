<form action="{{ route('web.contact.store') }}" method="POST" class="mt-5">
    @csrf

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="form-group">
        <label for="name">{{ __('web.contact.name') }}</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="email">{{ __('web.contact.email') }}</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="message">{{ __('web.contact.message') }}</label>
        <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
    </div>

    <button type="submit" class="btn btn-main">{{ __('web.contact.send') }}</button>
</form>
