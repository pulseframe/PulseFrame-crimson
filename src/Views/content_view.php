<?php if ($fileContent): ?>
  <div class="bg-gray-800 shadow rounded-lg p-4 h-full overflow-hidden pb-12">
    <h2 class="text-xl font-semibold pb-2.5"><?php echo htmlspecialchars(basename($fileToView)); ?></h2>
    <pre class="bg-gray-900 p-2 rounded-lg overflow-auto h-full" style="line-height: 1.2;"><?php include(__DIR__ . "/file_content.php") ?></pre>
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