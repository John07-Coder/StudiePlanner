# StudiePlanner — ontwikkelafspraken

- Laravel 13, PHP, SQLite, Eloquent en Blade.
- Gewone CSS en alleen kleine hoeveelheden vanilla JavaScript.
- Geen React, Vue, Livewire, Tailwind, repositories, serviceklassen of onnodige packages.
- Houd de code begrijpelijk op MBO 4-niveau. Applicatieteksten zijn Nederlands.
- Geef iedere methode één duidelijke verantwoordelijkheid. Splits validatie, ophalen, filteren, berekenen en sessiebeheer op. Controlleracties mogen deze stappen aanroepen en het resultaat naar een view of redirect sturen.
- Gebruik standaard Laravel Form Requests voor formuliervalidatie.
- Haal taken en vakken altijd via de ingelogde gebruiker op.
- Valideer subject_id ook op user_id. Neem user_id nooit uit een formulier over.
- Bij verwijderen van een vak moeten taken blijven bestaan zonder vak.
- Gebruik php artisan test voor de Feature Tests. Tests gebruiken uitsluitend SQLite :memory:.
- Controleer gewijzigde routes met php artisan route:list --except-vendor.
- CSS en JavaScript staan direct in public; er is geen npm- of Vite-build nodig.
