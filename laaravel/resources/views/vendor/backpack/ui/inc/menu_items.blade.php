{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Events" icon="la la-calendar" :link="backpack_url('event')" />
<x-backpack::menu-item title="Appointments" icon="la la-clock" :link="backpack_url('appointment')" />
<x-backpack::menu-item title="Availabilities" icon="la la-user" :link="backpack_url('availability')" />
<x-backpack::menu-item title="Radios" icon="la la-microphone" :link="backpack_url('radio')" />
<x-backpack::menu-item title="Results" icon="la la-trophy" :link="backpack_url('result')" />
<x-backpack::menu-item title="Feedback" icon="la la-comment" :link="backpack_url('feedback')" />