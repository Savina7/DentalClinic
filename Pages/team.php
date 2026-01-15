<?php 
include __DIR__ . '/../includes/nologin/header.php'; 
include __DIR__ . '/../auth/db.php'; // lidh databazën
?>

<section class="fees-hero">
  <div class="hero-bg">
    <img src="/smile-dental/image/teamPhoto.jpg" alt="Dental Clinic" />
  </div>
  <div class="white-diagonal"></div>
  <div class="hero-content">
    <h1 class="fees-title">Meet the Team</h1>
  </div>
</section>

<section class="team-section">
  <div class="team-grid">
    <?php
    // Merr dentistët nga databaza
    $stmt = $conn->prepare("SELECT * FROM dentists ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        while($dentist = $result->fetch_assoc()){

            // Kontrollo imazhin, nëse nuk ka vendos imazh default
            $image = !empty($dentist['image']) ? $dentist['image'] : 'default-doctor.jpg';

            ?>
            <div class="doctor-card">
              <div class="doctor-image-container">
                <img src="/smile-dental/image/<?php echo htmlspecialchars($image); ?>" 
                     alt="<?php echo htmlspecialchars($dentist['name']); ?>" />
                <div class="doctor-overlay">
                  <div class="info-text">
                    <p><?php echo nl2br(htmlspecialchars($dentist['description'])); ?></p>
                  </div>
                </div>
              </div>
              <div class="doctor-info-basic">
                <h3><?php echo htmlspecialchars($dentist['name']); ?></h3>
                <h4><?php echo htmlspecialchars($dentist['specialty']); ?></h4>
              </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="text-center py-4 text-muted">No dentists found.</p>';
    }
    ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/nologin/footer.php'; ?>
