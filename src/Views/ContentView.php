<?php if ($fileContent): ?>
  <?php
  $relativeFilePath = str_replace(realpath(ROOT_DIR) . '/', '', realpath($fileToView));
  $lineNumber = !empty($highlightLines) ? $highlightLines[0] : '';
  $displayName = $lineNumber ? "{$relativeFilePath}:{$lineNumber}" : $relativeFilePath;
  ?>
  <div class="bg-gray-800 shadow rounded-lg p-4 pb-10 h-full overflow-hidden relative">
    <h2 class="w-max ml-auto pr-2 text-gray-400"><?php echo htmlspecialchars($displayName); ?></h2>
    <pre class="p-2 pt-0 rounded-b-lg overflow-hidden h-full" style="line-height: 1.2;"><?php include(__DIR__ . "/FileContent.php") ?></pre>
  </div>
<?php elseif (isset($fileToView)): ?>
  <div class="mt-4 bg-gray-800 shadow rounded-lg p-4 text-red-500">
    <p>File not found: <?php echo htmlspecialchars($fileToView); ?></p>
  </div>
<?php else: ?>
  <div class="mt-4 bg-gray-800 shadow rounded-lg p-4">
    <p>Select a file from the sidebar to view its content.</p>
  </div>
<?php endif; ?>