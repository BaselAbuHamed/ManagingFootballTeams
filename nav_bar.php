<div class="navigation">
  <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="create_team.php">Create New Team</a></li>
      <li><a href="edit_team.php">Edit Team Page</a></li>
      <li><a href="delete_team.php">Delete Team Page</a></li>
      <?php if (!empty($_SESSION['loginMessage'])) : ?>
          <span class="login-message"><?php echo $_SESSION['loginMessage']; ?></span>
          <?php unset($_SESSION['loginMessage']); ?>
      <?php endif; ?>
  </ul>
</div>