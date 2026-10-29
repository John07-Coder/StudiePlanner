<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('grid')<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>@break
@case('calendar')<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18M8 15h2m4 0h2M8 18h2"/>@break
@case('book')<path d="M12 6c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V5c-3-1-6-1-9 1v15M6 9h3m6 0h3"/>@break
@case('check')<rect x="3" y="3" width="18" height="18" rx="4"/><path d="m7 12 3 3 7-7"/>@break
@case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
@case('logout')<path d="M9 4H4v16h5m5-12 4 4-4 4M8 12h12"/>@break
@case('plus')<path d="M12 5v14M5 12h14"/>@break
@case('arrow')<path d="M4 12h16m-6-6 6 6-6 6"/>@break
@case('alert')<path d="m12 3 10 18H2L12 3Z M12 9v5m0 3v.1"/>@break
@endswitch
</svg>

