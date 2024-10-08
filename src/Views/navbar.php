<nav class="bg-gray-800 rounded-lg mb-4 p-4 flex gap-2 items-center">
  <h2 class="text-xl font-semibold text-white"><?php echo htmlspecialchars(get_class($exception)); ?></h2>
  <p class="text-xl text-gray-300 break-words">
    <?php echo htmlspecialchars($exception->getMessage()); ?>
  </p>
  <p class="ml-auto"><?php echo $message; ?></p>
</nav>