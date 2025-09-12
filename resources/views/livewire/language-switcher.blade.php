<div class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700" onclick="document.getElementById('language-menu').classList.toggle('hidden')">
            @php
            $currentLocale = $this->getCurrentLocale();
            $locales = $this->getAvailableLocales();
            @endphp
            {{ $locales[$currentLocale]['flag'] }} {{ $locales[$currentLocale]['native'] }}
            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div id="language-menu" class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 dark:ring-gray-600 hidden">
        <div class="py-1">
            @foreach($this->getAvailableLocales() as $code => $locale)
            {{-- @if($code !== $currentLocale) --}}
            <button wire:click="switchLanguage('{{ $code }}')" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-gray-100">
                <span class="mr-2">{{ $locale['flag'] }}</span>
                {{ $locale['native'] }}
            </button>
            {{-- @endif --}}
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('language-menu');
        const button = event.target.closest('button');

        if (!button && menu && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

</script>
