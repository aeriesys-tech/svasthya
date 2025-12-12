<template>
  <div class="container py-5">

    <!-- MAIN HEADING -->
    <h1 class="text-center fw-bold mb-4">Help & Support</h1>

    <!-- SUB-HEADING -->
    <p class="text-center text-muted mb-5 fs-5">
      If you have any concerns, issues, or general queries, feel free to reach out.
      We are here to assist you with prompt and reliable support.
    </p>

    <!-- TELEMANAS SECTION -->
    <div class="card shadow-sm p-4 mx-auto support-card mb-4">
      <h4 class="fw-bold mb-3">Telemanas for Mental Health Assistance</h4>

      <h5 class="mb-2">Phone Numbers</h5>
      <div class="d-flex flex-column gap-3 mb-3">
        <button class="btn modern-btn d-flex align-items-center justify-content-between"
                @click="call('14416')">
          <span><i class="bi bi-telephone-fill me-2"></i> 14416 (Toll-Free)</span>
          <i class="bi bi-arrow-right"></i>
        </button>

        <button class="btn modern-btn d-flex align-items-center justify-content-between"
                @click="call('18008914416')">
          <span><i class="bi bi-telephone-fill me-2"></i> 18008914416 (Toll-Free)</span>
          <i class="bi bi-arrow-right"></i>
        </button>        
      </div>      
    </div>

    <!-- TECHNICAL ASSISTANCE SECTION -->
    <div class="card shadow-sm p-4 mx-auto support-card">
      <h4 class="fw-bold mb-3">Technical Assistance (BCP)</h4>

      <h5 class="mb-2">Phone Number</h5>
      <button class="btn modern-btn d-flex align-items-center justify-content-between"
              @click="call('+919480806012')">
        <span><i class="bi bi-telephone-fill me-2"></i> +91 9480806012</span>
        <i class="bi bi-arrow-right"></i>
      </button>

      <h5 class="mb-2">Email ID</h5>
      <button class="btn modern-email-btn d-flex align-items-center justify-content-between mb-3"
              @click="sendEmail('bcpsyadm@ksp.gov.in')">
        <span><i class="bi bi-envelope-fill me-2"></i> bcpsyadm@ksp.gov.in</span>
        <i class="bi bi-arrow-right"></i>
      </button>

    </div>

  </div>
</template>

<script setup>
const call = (number) => {
  window.location.href = `tel:${number}`;
};

const sendEmail = (email) => {
  window.location.href = `mailto:${email}`;
};
</script>

<style scoped>
* {
  -webkit-tap-highlight-color: transparent !important;
}

.support-card {
  max-width: 600px;
  border-radius: 15px;
}

/* MODERN BUTTON STYLE */
.modern-btn {
  padding: 12px 18px;
  font-size: 1rem;
  border-radius: 12px;
  background: linear-gradient(135deg, #f3f4f7, #e0e4eb);
  border: 1px solid #d0d5dd;
  color: #333;
  transition: transform 0.15s ease, background 0.25s ease;
}

.modern-btn:hover,
.modern-btn:active,
.modern-btn:focus {
  transform: scale(1.03);
  background: linear-gradient(135deg, #eef0f4, #dce0e9);
  outline: none !important;
  box-shadow: none !important;
}

/* EMAIL BUTTON – COOL BLUE ACCENT */
.modern-email-btn {
  padding: 12px 18px;
  font-size: 1rem;
  border-radius: 12px;
  background: linear-gradient(135deg, #e7f2ff, #d1e7ff);
  border: 1px solid #b6d7ff;
  color: #004a94;
  transition: transform 0.15s ease, background 0.25s ease;
}

.modern-email-btn:hover,
.modern-email-btn:active,
.modern-email-btn:focus {
  transform: scale(1.03);
  background: linear-gradient(135deg, #d9ebff, #c5defe);
  outline: none !important;
  box-shadow: none !important;
}

.modern-email-btn:focus-visible {
  outline: none !important;
  box-shadow: none !important;
}
</style>
