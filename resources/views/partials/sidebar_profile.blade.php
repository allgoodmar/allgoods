
<div class="box">
    <h3 class="box-header">{{ __('main.profile_menu') }}</h3>
    <div class="list-group">
        @foreach($menu as $menuItem)
            <a href="{{ $menuItem->url }}" class="list-group-item black-link">{{ $menuItem->name }}</a>
        @endforeach
        <a href="javascript:;" class="list-group-item red-link" onclick="document.getElementById('profile-logout-form').submit()">{{ __('main.nav.logout') }}</a>
    </div>
    <form class="logout-form d-none" id="profile-logout-form" action="{{ route('logout') }}" method="post">
        @csrf
    </form>
</div>
