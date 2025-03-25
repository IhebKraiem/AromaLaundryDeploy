
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Pusher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
.notification-badge {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #ff4747;  /* Couleur rouge pour l'attention */
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    transform: translate(25%, -25%);
}

/* Conteneur avec défilement pour les notifications */
.notifications-container {
    max-height: 320px; /* Hauteur maximale pour environ 4 notifications */
    overflow-y: auto;  /* Ajoute un défilement vertical si nécessaire */
}

/* Style pour la barre de défilement */
.notifications-container::-webkit-scrollbar {
    width: 5px;
}

.notifications-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.notifications-container::-webkit-scrollbar-thumb {
    background: var(--bs-primary-300, #c7d2fe);
    border-radius: 10px;
}

.notifications-container::-webkit-scrollbar-thumb:hover {
    background: var(--bs-primary-400, #a5b4fc);
}

/* Style pour les notifications */
.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.2s ease;
    
}

.notification-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.notification-item.unread {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    border-left: 3px solid var(--bs-primary);
}

.notification-time {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
}

.no-notifications {
    text-align: center;
    color: #888;
}
    </style>

</head>
<body>
<div class="navbar-header">
    <div class="row align-items-center justify-content-between">
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                </button>
                <h6 class="fw-semibold mb-0">{{ $title ?? '' }}</h6>

            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex flex-wrap align-items-center gap-3" wire:ignore>
                @can('order_create')
                <button type="button" data-theme-toggle
                    class="w-40-px h-40-px bg-neutral-200 rounded-circle  justify-content-center align-items-center tw-hidden"></button>
                <a href="{{route('orders.pos')}}" type="button" 
                    class="w-40-px h-40-px btn-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center tw-relative tw-group"
                  
                    title="">
                    <iconify-icon icon="tabler:plus" class="icon text-xl"></iconify-icon>
                    <div class="group-hover:tw-flex tw-hidden tw-bg-black hover:tw-bg-neutral-300 tw-rounded-lg tw-px-2 tw-py-1 tw-text-xs tw-absolute tw-text-white tw-min-w-max -tw-bottom-8">
                      {{ $lang->data['add_new_order'] ?? 'Add New Order' }}
                    </div>
                </a>
                @endcan
                @can('service_create')
                <a href="{{route('service')}}"
                    class="w-40-px h-40-px btn-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center tw-relative tw-group">
                    <iconify-icon icon="mdi:tag" class="icon text-xl"></iconify-icon>
                    <div class="group-hover:tw-flex tw-hidden tw-bg-black hover:tw-bg-neutral-300 tw-rounded-lg tw-px-2 tw-py-1 tw-text-xs tw-absolute tw-text-white tw-min-w-max  -tw-bottom-8">
                      {{ $lang->data['manage_services'] ?? 'Manage Services' }}
                    </div>
                </a>
                
                @endcan
                @can('customer_create')
                <a href="{{route('customers')}}"
                    class="w-40-px h-40-px btn-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center tw-relative tw-group hover:tw-bg-neutral-300">
                    <iconify-icon icon="ion:people" class="icon text-xl"></iconify-icon>
                    <div class="group-hover:tw-flex tw-hidden tw-bg-black  tw-rounded-lg tw-px-2 tw-py-1 tw-text-xs tw-absolute tw-min-w-max  tw-text-white -tw-bottom-8">
                      {{ $lang->data['manage_customers'] ?? 'Manage Customers' }}
                    </div>
                </a>
                @endcan
                @if(count($languages) > 0)
                <div class="dropdown ">
                    <button class="tw-flex justify-content-center align-items-center rounded-circle lg:tw-hidden" type="button"
                        data-bs-toggle="dropdown">
                        <div alt="image"
                            class="w-40-px h-40-px tw-flex tw-items-center tw-justify-center btn-primary-600 text-white tw-rounded-full">
                            <iconify-icon icon="heroicons:language-16-solid" class="icon text-xl"></iconify-icon>
                          </div>
                    </button>
                    <button class="btn lg:tw-block tw-hidden btn-outline-neutral-600 not-active px-18 py-11 dropdown-toggle toggle-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false"> {{ $lang->name ?? 'Choose Language'}} </button>
                    <ul class="dropdown-menu tw-max-h-40 tw-overflow-y-auto" aria-labelledby="dropdownMenuButton" style="top:0rem!important;">
                        @foreach ($languages as $key => $item)
                            <li><a class="dropdown-item text-xs"
                                    wire:click.prevent="changeLanguage({{ $key }})">{{ $item }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="dropdown">
    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
        data-bs-toggle="dropdown" id="notificationIcon">
        <div alt="notification"
            class="w-40-px h-40-px tw-flex tw-items-center tw-justify-center btn-primary-600 text-white tw-rounded-full">
            <iconify-icon icon="mdi:bell" class="icon text-xl"></iconify-icon>
            <span class="notification-badge" id="notificationCount">0</span>
        </div>
    </button>
    
    <div class="dropdown-menu to-top dropdown-menu-sm" id="notificationDropdown">
        <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
            <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-2">Notifications</h6>
            </div>
            <a href="#" id="markAllRead" class="hover-text-primary">
                <span class="text-secondary-light fw-medium text-sm">Mark all as read</span>
            </a>
        </div>
        
        <div class="notifications-container">
            <div id="notifications-list" class="to-top-list">
                <div class="no-notifications px-16 py-12">Aucune notification</div>
            </div>
        </div>
    </div>
</div>
                <div class="dropdown">
                    <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                        data-bs-toggle="dropdown">
                        <div alt="image"
                            class="w-40-px h-40-px tw-flex tw-items-center tw-justify-center btn-primary-600 text-white tw-rounded-full">
                            <iconify-icon icon="material-symbols:person" class="icon text-xl"></iconify-icon>
                          </div>
                    </button>
                    
                    <div class="dropdown-menu to-top dropdown-menu-sm">
                        <div
                            class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <h6 class="text-lg text-primary-light fw-semibold mb-2">{{ Auth::user()->name }}</h6>
                                {{-- <span class="text-secondary-light fw-medium text-sm">Admin</span> --}}
                            </div>
                            <button type="button" class="hover-text-danger">
                                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                            </button>
                        </div>
                        
                        <ul class="to-top-list">
                            @can('setting_master')
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-primary d-flex align-items-center gap-3"
                                    href="{{ route('settings.master-settings') }}">
                                    <iconify-icon icon="icon-park-outline:setting-two"
                                        class="icon text-xl"></iconify-icon> Setting</a>
                            </li>
                            @endcan
                            <li>
                                <a class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                                    href="#" wire:click.prevent="logout">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log Out</a>
                            </li>
                        </ul>
                    </div>
                </div><!-- Profile dropdown end -->
            </div>
        </div>
    </div>
    @script
    <script>
      
        $wire.on('reloadpage', orderId => {
            window.location.reload()
        })
                      
    </script>
    @endscript
</div>



</body>
</html>