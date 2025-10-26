<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
?>
<header>
  <div class="header-inner">
    <img src="assets/logo.png" alt="Logo" class="logo">
    <div class="profile-area">
      <span class="username">
        <?= htmlspecialchars($_SESSION['user']['nama_depan'] ?? '') ?>
      </span>
      <div class="profile">
        <img src="assets/profile_icon.png" alt="Profil">
        <div class="profile-menu">
          <a href="profil.php">Profil</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
  const profile = document.querySelector(".profile");
  document.addEventListener("click", e => {
    if (profile.contains(e.target)) {
      profile.classList.toggle("active");
    } else {
      profile.classList.remove("active");
    }
  });
</script>
