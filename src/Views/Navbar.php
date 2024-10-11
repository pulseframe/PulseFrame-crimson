<nav class="bg-gray-800 rounded-lg mb-4 p-4 flex gap-2 items-center">
  <h2 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($exceptionName); ?></h2>
  <p class="text-xl text-gray-300 break-words">
    <?php echo htmlspecialchars($exception->getMessage()); ?>
  </p>
  <h3 class="flex items-center ml-auto gap-0 select-none">
    <p class="text-lg text-red-500">Error <?php echo $statusCode; ?></p>&nbsp;-&nbsp;
    <?php echo $message; ?>
  </h3>
</nav>