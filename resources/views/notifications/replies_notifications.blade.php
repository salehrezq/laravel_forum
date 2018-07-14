<div class="navbar-brand btn-notify btn-noti-released">
    <input class='noti-count' type="hidden" value="{{auth()->user()->unreadNotifications()->count()}}">
    <div class="noti-counter"></div>
    <div class="notifications">
        <div class="noti-head"><h3 class='noti-head'>Notifications</h3>
            <button type="button" class="btn btn-link padding-8">Mark All as Read</button></div>
        <input class="page" type="hidden" value="0">
        <div class="notis-list">
        </div>
    </div>
</div>
