@props(['disabled' => false, 'rows' => 3])

<textarea @disabled($disabled) rows="{{ $rows }}"
    {{ $attributes->merge([
        'class' => 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-xl py-3 px-4 shadow-sm w-full transition-colors duration-200',
    ]) }}>{{ $slot }}</textarea>
