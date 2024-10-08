<div class="w-1/4 p-4 bg-gray-800 shadow rounded-lg overflow-auto mb-44">
  <h2 class="text-xl font-semibold">Stack Trace</h2>
  <ul class="mt-2">
    <?php foreach ($stackTraces as $traceItem): ?>
      <li class="mb-2">
        <a href="?file=<?php echo urlencode($traceItem['file']); ?>&line=<?php echo htmlspecialchars($traceItem['line']); ?>"
          class="flex gap-2 p-3 w-full h-10 items-center rounded-lg 
           <?php echo ($fileToView === $traceItem['file'] && in_array($traceItem['line'], $highlightLines) && count(array_filter($stackTraces, fn($item) => $item['file'] === $traceItem['file'] && $item['line'] === $traceItem['line'])) === 1) ? 'bg-blue-500' : 'hover:bg-slate-900'; ?>">
          <p class="text-white">
            <?php echo basename($traceItem['file']); ?>
          </p>
          <p class="text-gray-400">(Line: <?php echo $traceItem['line']; ?>)</p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>