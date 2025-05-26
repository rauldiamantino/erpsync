<?php

$html = '<nav aria-label="Pagination" class="flex justify-center items-center gap-3 text-sm select-none p-4">';

// Tailwind Classes
$baseBtnClasses = 'min-w-[44px] h-11 flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-all duration-300 ease-in-out whitespace-nowrap';
$activeBtnClasses = 'bg-blue-600 text-white shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2';
$disabledBtnClasses = 'bg-gray-200 text-gray-500';
$navBtnClasses = 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100';

// SVGs
$firstPageSVG = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" /></svg>';
$prevPageSVG = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>';
$nextPageSVG = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>';
$lastPageSVG = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" /></svg>';


// --- First Page Button ---
if ($currentPage > 1) {
  $html .= '<a href="' . $baseUrl . '1" class="' . $baseBtnClasses . ' ' . $navBtnClasses . '" aria-label="Primeira página" title="Primeira página">' . $firstPageSVG . '</a>';
}
else {
  $html .= '<span class="' . $baseBtnClasses . ' ' . $disabledBtnClasses . '" aria-disabled="true">' . $firstPageSVG . '</span>';
}

// --- Previous Page Button ---
if ($currentPage > 1) {
  $html .= '<a href="' . $baseUrl . ($currentPage - 1) . '" class="' . $baseBtnClasses . ' ' . $navBtnClasses . '" aria-label="Página anterior" title="Página anterior">' . $prevPageSVG . '</a>';
}
else {
  $html .= '<span class="' . $baseBtnClasses . ' ' . $disabledBtnClasses . '" aria-disabled="true">' . $prevPageSVG . '</span>';
}

// --- Next Page Button ---
if ($currentPage < $totalPages) {
  $html .= '<a href="' . $baseUrl . ($currentPage + 1) . '" class="' . $baseBtnClasses . ' ' . $navBtnClasses . '" aria-label="Próxima página" title="Próxima página">' . $nextPageSVG . '</a>';
}
else {
  $html .= '<span class="' . $baseBtnClasses . ' ' . $disabledBtnClasses . '" aria-disabled="true">' . $nextPageSVG . '</span>';
}

// --- Last Page Button ---
if ($currentPage < $totalPages) {
  $html .= '<a href="' . $baseUrl . $totalPages . '" class="' . $baseBtnClasses . ' ' . $navBtnClasses . '" aria-label="Última página" title="Última página">' . $lastPageSVG . '</a>';
}
else {
  $html .= '<span class="' . $baseBtnClasses . ' ' . $disabledBtnClasses . '" aria-disabled="true">' . $lastPageSVG . '</span>';
}

$html .= '</nav>';

echo $html;