@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Ambil user dari default guard atau guard customer
$user = Auth::check() ? Auth::user() : (Auth::guard('customer')->check() ? Auth::guard('customer')->user() : null);
$currentUserRole = $user ? strtolower($user->role ?? 'customer') : '';

// Ambil menu data
$menuData = $menuData ?? [];
@endphp

<style>
/* Update Font untuk Sidebar Menu */
#layout-menu {
  font-family: 'Inter', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  background: #ffffff !important;
  background-color: #ffffff !important;
  box-shadow: none !important;
  overflow: auto !important;
  scrollbar-width: none !important; /* Firefox */
  -ms-overflow-style: none !important; /* IE and Edge */
}

/* Hide scrollbar for Chrome, Safari and Opera */
#layout-menu::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

/* Hide scrollbar on menu-inner as well */
.menu-inner {
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
}

.menu-inner::-webkit-scrollbar {
  display: none !important;
  width: 0 !important;
  height: 0 !important;
}

/* Styling untuk menu aktif - Background Hitam, Text Putih */
.menu-item.active > .menu-link {
  background-color: #18181b !important;
  color: #fafafa !important;
  font-weight: 600;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.menu-item.active > .menu-link i {
  color: #fafafa !important;
}

.menu-item.active > .menu-link div {
  color: #fafafa !important;
}

/* Hover effect untuk menu yang tidak aktif */
.menu-item:not(.active) > .menu-link:hover {
  background-color: #f4f4f5;
  border-radius: 6px;
  transition: all 0.3s ease;
}

/* Styling untuk menu dropdown yang terbuka (open) */
.menu-item.open:not(.active) > .menu-link {
  background-color: #18181b !important;
  color: #fafafa !important;
  border-radius: 6px;
}

.menu-item.open:not(.active) > .menu-link i,
.menu-item.open:not(.active) > .menu-link div {
  color: #fafafa !important;
}

/* Jarak bottom untuk menu paling akhir */
.menu-inner {
  padding-bottom: 50px !important;
}

/* Font styling untuk menu items */
.menu-link {
  font-family: 'Inter', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  font-size: 15px;
  font-weight: 400;
}

/* Menu header font */
.menu-header-text {
  font-family: 'Inter', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Brand logo font */
.app-brand-text, .app-brand-logo {
  font-family: 'Inter', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
  font-weight: 700;
  font-size: 1.25rem;
  color: #18181b !important;
}

</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  {{-- Brand --}}
  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">
          JMK
        </span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8.47 11.72C8.12 12.07 8.12 12.65 8.47 13.01L12.07 16.61C12.46 17.00 12.46 17.63 12.07 18.02C11.68 18.41 11.05 18.41 10.66 18.02L5.83 13.19C5.37 12.74 5.37 11.99 5.83 11.53L10.66 6.71C11.05 6.32 11.68 6.32 12.07 6.71C12.46 7.10 12.46 7.73 12.07 8.12L8.47 11.72Z" fill-opacity="0.9" />
          <path d="M14.36 11.83C14.07 12.13 14.07 12.60 14.36 12.89L18.07 16.61C18.46 17.00 18.46 17.63 18.07 18.02C17.68 18.41 17.05 18.41 16.66 18.02L11.68 13.05C11.31 12.67 11.31 12.06 11.68 11.68L16.66 6.71C17.05 6.32 17.68 6.32 18.07 6.71C18.46 7.10 18.46 7.73 18.07 8.12L14.36 11.83Z" fill-opacity="0.4" />
        </svg>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @if (!empty($menuData) && isset($menuData[0]->menu))
      @foreach ($menuData[0]->menu as $menu)
        {{-- ?? Cek role user terhadap menu --}}
        @php
          $menuRoles = $menu->roles ?? null;
          $isAllowed = true;

          if ($menuRoles) {
            if (is_array($menuRoles)) {
              $isAllowed = in_array($currentUserRole, array_map('strtolower', $menuRoles));
            } else {
              $isAllowed = strtolower($menuRoles) === $currentUserRole;
            }
          }
        @endphp

        @if ($isAllowed)
          {{-- Menu Header --}}
          @if (isset($menu->menuHeader))
            <li class="menu-header mt-5">
              <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
          @else
            @php
              $activeClass = '';
              $currentRouteName = Route::currentRouteName();
              $slug = $menu->slug ?? '';

              // Aktif jika route name sama atau salah satu slug submenu muncul di route name
              $hasActiveChild = false;
              if (isset($menu->submenu)) {
                foreach ($menu->submenu as $sub) {
                  if (str_contains($currentRouteName, $sub->slug ?? '')) {
                    $hasActiveChild = true;
                    break;
                  }
                }
              }

              if ($currentRouteName === $slug || $hasActiveChild) {
                $activeClass = $hasActiveChild ? 'active open' : 'active';
              }

              // Generate unique menu ID
              $menuId = 'menu-' . Str::slug($menu->name ?? 'item');
            @endphp

            <li class="menu-item {{ $activeClass }}" data-menu-id="{{ $menuId }}">
              <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>

                @isset($menu->icon)
                  <i class="{{ $menu->icon }}"></i>
                @endisset

                <div>{{ $menu->name ?? '' }}</div>

                @isset($menu->badge)
                  <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">
                    {{ $menu->badge[1] }}
                  </div>
                @endisset
              </a>

              {{-- Submenu --}}
              @isset($menu->submenu)
                <ul class="menu-sub">
                  @foreach ($menu->submenu as $submenu)
                    @php
                      $submenuRoles = $submenu->roles ?? null;
                      $submenuAllowed = true;
                      $submenuId = 'submenu-' . Str::slug($submenu->name ?? ($submenu->slug ?? 'subitem'));

                      if ($submenuRoles) {
                        if (is_array($submenuRoles)) {
                          $submenuAllowed = in_array($currentUserRole, array_map('strtolower', $submenuRoles));
                        } else {
                          $submenuAllowed = strtolower($submenuRoles) === $currentUserRole;
                        }
                      }
                    @endphp

                    @if ($submenuAllowed)
                      @php
                        $isSubActive = str_contains($currentRouteName, $submenu->slug ?? '');
                      @endphp
                      <li class="menu-item {{ $isSubActive ? 'active' : '' }}" data-submenu-id="{{ $submenuId }}">
                        <a href="{{ url($submenu->url) }}" class="menu-link">
                          <div>{{ $submenu->name }}</div>
                        </a>
                      </li>
                    @endif
                  @endforeach
                </ul>
              @endisset
            </li>
          @endif
        @endif
      @endforeach
    @else
      <li class="menu-item">
        <a href="#" class="menu-link disabled">
          <i class="ti ti-alert-triangle"></i>
          <div>No menu data found</div>
        </a>
      </li>
    @endif
  </ul>
</aside>

{{-- JavaScript untuk menjaga dropdown tetap terbuka setelah reload --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Restore menu state dari localStorage
  let openMenus = JSON.parse(localStorage.getItem('openMenus') || '[]');

  @if(in_array($currentUserRole, ['admin', 'administrator']) && request()->routeIs('dashboard.welcome'))
      // Jika login sebagai admin/administrator dan berada di Dashboard, tutup semua menu dropdown
      openMenus = [];
      localStorage.setItem('openMenus', JSON.stringify([]));
  @endif

  openMenus.forEach(function(menuId) {
    const menuItem = document.querySelector('[data-menu-id="' + menuId + '"]');
    if (menuItem && !menuItem.classList.contains('active')) {
      menuItem.classList.add('open');
    }
  });

  // Save menu state ketika dropdown diklik
  document.querySelectorAll('.menu-toggle').forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
      const menuItem = this.closest('.menu-item');
      const menuId = menuItem.getAttribute('data-menu-id');
      let openMenus = JSON.parse(localStorage.getItem('openMenus') || '[]');

      // Tutup dropdown lain agar hanya satu terbuka
      document.querySelectorAll('.menu-item.open').forEach(function(item) {
        if (item !== menuItem) {
          item.classList.remove('open');
          const otherId = item.getAttribute('data-menu-id');
          openMenus = openMenus.filter(id => id !== otherId);
        }
      });

      if (menuItem.classList.contains('open')) {
        // Remove dari array jika menu ditutup
        openMenus = openMenus.filter(id => id !== menuId);
      } else {
        // Tambah ke array jika menu dibuka
        if (!openMenus.includes(menuId)) {
          openMenus.push(menuId);
        }
      }

      localStorage.setItem('openMenus', JSON.stringify(openMenus));
    });
  });

  // Tandai submenu yang diklik dan simpan
  document.querySelectorAll('.menu-sub .menu-link').forEach(function(link) {
    link.addEventListener('click', function() {
      // Tidak menyimpan highlight khusus, hanya biarkan state default
    });
  });
});
</script>
