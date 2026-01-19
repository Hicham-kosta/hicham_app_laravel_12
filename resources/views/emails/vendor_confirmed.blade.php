<h2>Welcome {{ $vendor->name }}</h2>

<p>Your account has been confirmed and activated. You can now login to your account 
    and start selling your products on {{ config('app.name') }}.</p>

<a href="{{ route('user.login') }}" class="btn btn-primary">Login to Dashboard</a>
