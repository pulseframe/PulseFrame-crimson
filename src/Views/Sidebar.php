<?php
$vendorDropdownOpen = \PulseFrame\Facades\Cookie::get('vendorDropdownOpen', '0') === '1'; // Check if dropdown should be open
?>

<div class="w-1/3 p-4 bg-gray-800 shadow rounded-lg overflow-auto mb-44 select-none">
  <h2 class="text-xl font-semibold">Stack Trace</h2>
  <ul class="mt-2">
    <?php
    $vendorFiles = [];
    $vendorCount = 0;

    // First pass: collect vendor files and count them
    foreach ($stackTraces as $traceItem) {
      if (strpos($traceItem['file'], 'vendor/') !== false) {
        $vendorCount++;
        $vendorFiles[] = $traceItem; // Store the whole item for later
      }
    }

    // Function to extract namespace from a file and append class name
    function getNamespaceFromFile($filePath)
    {
      if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
          $namespace = trim($matches[1]);
          $className = basename($filePath, '.php'); // Get the class name from the file name
          return $namespace . '\\' . $className; // Append the class name to the namespace
        }
      }
      return null; // Return null if no namespace found or file doesn't exist
    }

    // Function to format app files (controllers and middleware)
    function FormatNamespace($filePath)
    {
      $namespace = getNamespaceFromFile($filePath);
      return $namespace ? $namespace : str_replace(realpath(ROOT_DIR) . '/', '', realpath($filePath));
    }

    // Second pass: display files and the vendor button
    foreach ($stackTraces as $traceItem) {
      // Display the current trace item
      if (strpos($traceItem['file'], 'vendor/') === false) {
        $formattedFile = $traceItem['file'];

        $formattedFile = FormatNamespace($traceItem['file']);

        echo '<li class="mb-2">';
        echo '<a href="javascript:void(0);" onclick="selectFile(\'' . urlencode($traceItem['file']) . '\', ' . htmlspecialchars($traceItem['line']) . ');" class="flex gap-2 p-3 w-full h-10 items-center rounded-lg ' . ($fileToView === $traceItem['file'] && in_array($traceItem['line'], $highlightLines) ? 'bg-blue-500' : 'hover:bg-slate-900') . '">';
        echo '<p class="text-gray-300">' . $formattedFile . ':' . $traceItem['line'] . '</p>';
        echo '</a>';
        echo '</li>';
      }

      // Insert the "Show Vendors" button just once
      if (strpos($traceItem['file'], 'vendor/') === false && $vendorCount > 0) {
        echo '<li class="mb-2">';
        echo '<button class="flex gap-2 p-3 w-full h-10 items-center rounded-lg ' . ($vendorDropdownOpen ? 'bg-gray-700' : 'hover:bg-slate-900') . '" onclick="toggleVendors()">';
        echo '<p class="text-white">' . $vendorCount . ' vendor frames</p>';
        echo '<span class="text-gray-400"> ▾</span>';
        echo '</button>';
        echo '</li>';

        // After the button, display the vendor dropdown
        echo '<ul id="vendor-list" class="' . ($vendorDropdownOpen ? '' : 'hidden') . ' mb-2">'; // Use the cookie value to set visibility
        foreach ($vendorFiles as $vendorFile) {
          $formattedVendorPath = FormatNamespace($vendorFile['file']);

          echo '<li class="ml-2 mb-2">';
          echo '<a href="javascript:void(0);" onclick="selectFile(\'' . urlencode($vendorFile['file']) . '\', ' . $vendorFile['line'] . ');" class="flex gap-2 p-3 w-full h-10 items-center rounded-lg ' . ($fileToView === $vendorFile['file'] && in_array($vendorFile['line'], $highlightLines) ? 'bg-blue-500' : 'hover:bg-slate-900') . '">';
          echo '<p class="text-gray-300">' . $formattedVendorPath . ':' . $vendorFile['line'] . '</p>';
          echo '</a>';
          echo '</li>';
        }
        echo '</ul>';

        // Prevent adding the button again
        $vendorCount = 0; // Reset vendor count after inserting the button
      }
    }
    ?>
  </ul>
</div>

<script>
  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
  }

  function selectFile(file, line) {
    setCookie('file', file, 1);
    setCookie('line', line, 1);
    location.reload(); // Reload to reflect changes
  }

  function toggleVendors() {
    const vendorList = document.getElementById('vendor-list');
    const isOpen = !vendorList.classList.contains('hidden'); // Check current state
    vendorList.classList.toggle('hidden');

    // Set the cookie to remember the state using JavaScript
    const expires = new Date();
    expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 days
    document.cookie = "vendorDropdownOpen=" + (!isOpen ? '1' : '0') + ";expires=" + expires.toUTCString() + ";path=/";
  }
</script>